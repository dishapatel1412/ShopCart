<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Cart;
use App\Models\Coupon;
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

    public function index(Request $request)
    {
        $selectedItems = $request->selected_items ?? [];

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')
                ->with('error', 'Please select at least one item!');
        }

        // Store selected items in session
        session(['selected_items' => $selectedItems]);

        $cartItems = Cart::with(['product', 'size', 'color'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        $discount = 0;
        $coupon   = null;

        if (session('coupon')) {
            $coupon = Coupon::where('code', session('coupon'))->first();
            if ($coupon) {
                if ($coupon->type == 'fixed') {
                    $discount = $coupon->value;
                } else {
                    $discount = ($subtotal * $coupon->value) / 100;
                }
            }
        }

        $tax   = ($subtotal - $discount) * 0.18; // 18% GST
        $total = ($subtotal - $discount) + $tax;

        $savedAddresses = Order::where('user_id', Auth::id())
            ->select('name', 'email', 'phone', 'address', 'city', 'state', 'pincode')
            ->latest()
            ->get()
            ->unique(fn($o) => $o->address . $o->pincode)
            ->values()
            ->take(5);

        return view('checkout', compact('cartItems', 'subtotal', 'discount', 'tax', 'total', 'coupon', 'savedAddresses'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:15',
            'address' => 'required|string',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'payment_method' => 'required|in:cod,online',
        ]);

        $selectedItems = session('selected_items', []);

        $cartItems = Cart::with(['product', 'size', 'color'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedItems)
            ->get();

        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        $discount = 0;
        $coupon   = null;

        if (session('coupon')) {
            $coupon = Coupon::where('code', session('coupon'))->first();
            if ($coupon) {
                if ($coupon->type == 'fixed') {
                    $discount = $coupon->value;
                } else {
                    $discount = ($subtotal * $coupon->value) / 100;
                }
                // Increment coupon used count
                $coupon->increment('used_count');
            }
        }

        $tax   = ($subtotal - $discount) * 0.18;
        $total = ($subtotal - $discount) + $tax;

        // Create order
        $order = Order::create([
            'user_id'     => Auth::id(),
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'tax'         => $tax,
            'total'       => $total,
            'coupon_code' => session('coupon'),
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

            // Customer Notification
            if ($user && $user->device_token) {
                $this->firebaseService->sendNotification(
                    $user->device_token,
                    'Order Placed Successfully',
                    'Your COD order has been placed successfully.'
                );
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
            Cart::where('user_id', Auth::id())
                ->whereIn('id', $selectedItems)
                ->delete();

            Mail::to($order->email)->send(new OrderPlacedMail($order));
            // session()->forget(['coupon', 'selected_items']);
            // return redirect()->route('order.success', $order->id);

            return response()->view(
                'order-processing',
                compact('order')
            );
        }

        // Online → Cashfree
        $baseUrl = 'https://sandbox.cashfree.com';
        $returnUrl = config('app.ngrok_url') 
                ? config('app.ngrok_url') . '/payment/success?order_id=' . $order->id 
                : route('payment.success', ['order_id' => $order->id]);

        $cashfreeOrderId = 'order_' . $order->id . '_' . time();
        $orderAmount = max(1, round($total, 2));
        
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-version' => '2022-09-01',
            'x-client-id'   => config('services.cashfree.app_id'),
            'x-client-secret'=> config('services.cashfree.secret_key'),
        ])
        ->withoutVerifying()
        ->post($baseUrl . '/pg/orders', [
            'order_id'     => $cashfreeOrderId,
            'order_amount' => $orderAmount,
            'order_currency'=> 'INR',
            'order_note' => 'Order #' . $order->id,
            'customer_details' => [
                'customer_id'    => 'user_' . Auth::id(),
                'customer_name'  => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
            ],

            'order_meta' => [
                // 'return_url' => 'https://bendwise-semibald-darlene.ngrok-free.dev/payment/success?order_id=' . $order->id,
                'return_url' => $returnUrl,
            ],
        ]);

        if ($response->failed()) {
            return redirect()->route('cart.index')
                ->with('error', 'Payment initiation failed. Please try again.');
        }

        $data = $response->json();

        // Store cashfree order id
        $order->update([
            'cashfree_order_id' => $cashfreeOrderId,
            'payment_id' => $data['payment_session_id'] ?? null,
            // 'status' => $request->status,
        ]);

        return view('cashfree-checkout', [
            'paymentSessionId' => $data['payment_session_id'] ?? null,
            'returnUrl' => $returnUrl,
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect()->route('cart.index')
                ->with('error', 'Invalid payment response. Missing order ID.');
        }

        $order = Order::findOrFail($orderId);
        $cfOrderId = $order->cashfree_order_id;

        if (!$cfOrderId) {
            return redirect()->route('cart.index')
                ->with('error', 'Cashfree order ID not found. Contact support.');
        }

        // Verify payment status
        $response = Http::withHeaders([
            'x-api-version' => '2022-09-01',
            'x-client-id' => config('services.cashfree.app_id'),
            'x-client-secret' => config('services.cashfree.secret_key'),
            'Accept' => 'application/json',
        ])
        ->withoutVerifying()
        ->get("https://sandbox.cashfree.com/pg/orders/{$cfOrderId}/payments");

        if ($response->failed()) {
            return redirect()->route('cart.index')
                ->with('error', 'Payment verification failed. Contact support.');
        }

        $payments = $response->json();

        // Find successful payment in the array
        $paid = collect($payments)->first(
            fn($p) => ($p['payment_status'] ?? '') === 'SUCCESS'
        );

        if (!$paid) {
            return redirect()->route('cart.index')
                ->with('error', 'Payment not confirmed yet. Contact support if amount was deducted.');
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_id' => $paid['cf_payment_id'] ?? null,
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

        if ($user && $user->device_token) {        
            $this->firebaseService->sendNotification(
                $user->device_token,
                'Payment Successful',
                'Your order has been confirmed successfully.'
            );
        }

        $admin = User::where('role', 'admin')->first();

        if ($admin && $admin->device_token) {
            $this->firebaseService->sendNotification(
                $admin->device_token,
                'New Order Received',
                'A customer has placed a new order.'
            );
        }

        Mail::to($order->email)->send(new OrderPlacedMail($order));

        return redirect('http://localhost:8000/order/success/' . $order->id)
            ->with('success', '🎉 Payment successful! Order #' . $order->id . ' confirmed.');
    }

    public function paymentCancel(Request $request)
    {
        if ($request->order_id) {
            Order::where('id', $request->order_id)
                ->update(['status' => 'cancelled']);
        }

        return redirect()->route('cart.index')
            ->with('error', 'Payment cancelled.');
    }

    public function paymentWebhook(Request $request)
    {
        // Handle Cashfree webhook
        $data = $request->all();

        if (isset($data['data']['order']['order_id'])) {
            $cashfreeOrderId = $data['data']['order']['order_id'];
            $order = Order::where('cashfree_order_id', $cashfreeOrderId)->first();

            if ($order && $data['data']['payment']['payment_status'] === 'SUCCESS') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_id'     => $data['data']['payment']['cf_payment_id'] ?? null,
                    'status'         => 'confirmed',
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function orderSuccess($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('order-success', compact('order'));
    }
}
