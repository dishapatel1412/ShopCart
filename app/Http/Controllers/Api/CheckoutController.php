<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\FirebaseService;
use App\Services\WhatsappMessageService;

class CheckoutController extends Controller
{
    protected FirebaseService $firebaseService;
    protected WhatsappMessageService $whatsapp;

    public function __construct(FirebaseService $firebaseService, WhatsappMessageService $whatsapp)
    {
        $this->firebaseService = $firebaseService;
        $this->whatsapp = $whatsapp;
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:10',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|max:10',
            'payment_method' => 'required|in:cod,online',
            'selected_items' => 'required|array',
        ]);

        $selectedItems = $request->selected_items;

        $cartItems = Cart::with(['product', 'size', 'color'])
            ->where('user_id', $request->user()->id)
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        $subTotal = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        $discount = 0;
        // $coupon   = null;

        // if (session('coupon')) {
        //     $coupon = Coupon::where('code', session('coupon'))->first();
        //     if ($coupon) {
        //         if ($coupon->type == 'fixed') {
        //             $discount = $coupon->value;
        //         } else {
        //             $discount = ($subtotal * $coupon->value) / 100;
        //         }
        //         // Increment coupon used count
        //         $coupon->increment('used_count');
        //     }
        // }

        $tax   = ($subTotal - $discount) * 0.18;
        $total = ($subTotal - $discount) + $tax;

        $order = Order::create([
            'user_id'     => $request->user()->id,
            'subtotal'    => $subTotal,
            'discount'    => $discount,
            'tax'         => $tax,
            'total'       => $total,
            // 'coupon_code' => session('coupon'),
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'state'       => $request->state,
            'pincode'     => $request->pincode,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status'      => 'pending',
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            if ($item->product) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'size_id'       => $item->size_id,
                    'color_id'      => $item->color_id,
                    'product_name'  => $item->product->name,
                    'product_price' => $item->product->price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $item->product->price * $item->quantity,
                ]);
            }
        }

        // COD → success page directly
        if ($request->payment_method === 'cod') {
            $user = $order->user;

            $order->load('items.product');

            $messageBody = view('whatsapp_message', [
                'order' => $order,
            ])->render();

            try {
                \Log::info($order->phone);
                $firstItem = $order->items->first();

                $imageUrl = null;                
                if ($firstItem && $firstItem->product && $firstItem->product->image) {
                    $imageUrl = config('app.ngrok_url') .
                        '/storage/' .
                        $firstItem->product->image;
                }

                $this->whatsapp->sendMessage(
                    $order->phone,
                    $messageBody,
                    $imageUrl,
                );
                \Log::info($imageUrl);
            } catch (\Exception $e) {
                \Log::error('WhatsApp failed: ' . $e->getMessage());
            }

            $admin = User::where('role', 'admin')->first();
            
            if ($admin && $admin->device_token) {
                $this->firebaseService->sendNotification(
                    $admin->device_token,
                    'New Order Received',
                    'A customer has placed a new order.'
                );
            }

            // Cart::whereIn('id', $selectedItems)->delete();
            Cart::where('user_id', $request->user()->id)
                ->whereIn('id', $selectedItems)
                ->delete();

            Mail::to($order->email)->send(new OrderPlacedMail($order));
            // session()->forget(['coupon', 'selected_items']);
            // return redirect()->route('order.success', $order->id);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
            ]);
        }

        if ($request->payment_method === 'online') {
            $response = Http::withHeaders([
                'x-api-version' => '2022-09-01',
                'x-client-id' => config('services.cashfree.app_id'),
                'x-client-secret' => config('services.cashfree.secret_key'),
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->post('https://sandbox.cashfree.com/pg/orders', [
                'order_id' => 'order_'.$order->id,
                'order_amount' => $total,
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => (string) $request->user()->id,
                    'customer_name' => $request->name,
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                ],
                'order_meta' => [
                    'return_url' =>
                        url('/api/payment-success?order_id=' . $order->id)
                ]
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway error'
                ], 500);
            }

            $data = $response->json();

            $order->update([
                'cashfree_order_id' => $data['order_id']
            ]);

            $user = $order->user;

            $order->load('items.product');
    
            try {
                $messageBody = view('whatsapp_message', [
                    'order' => $order,
                ])->render();
    
                $firstItem = $order->items->first();
                $imageUrl = null;                
                if ($firstItem && $firstItem->product && $firstItem->product->image) {
                    $imageUrl = config('app.ngrok_url') .
                        '/storage/' .
                        $firstItem->product->image;
                }
                $this->whatsapp->sendMessage(
                    $order->phone, 
                    $messageBody,
                    $imageUrl
                );
                \Log::info($imageUrl);
            } catch (\Exception $e) {
                \Log::error("WhatsApp failed: " . $e->getMessage());
            }

            $admin = User::where('role', 'admin')->first();
            
            if ($admin && $admin->device_token) {
                $this->firebaseService->sendNotification(
                    $admin->device_token,
                    'New Order Received',
                    'A customer has placed a new order.'
                );
            }

            return response()->json([
                'success' => true,
                'payment_session_id' => $data['payment_session_id'],
                'cashfree_order_id' => $data['order_id'],
                'order_id' => $order->id
            ]);
        }
    }
}
