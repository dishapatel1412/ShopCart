<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Coupon;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with(['product', 'size', 'color'])
            ->where('user_id', Auth::id())
            ->get();

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

        $total = max(0, $subtotal - $discount);

        return view('cart', compact('cartItems', 'subtotal', 'discount', 'coupon', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->where('color_id', $request->color_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity ?? 1);
        } else {
            Cart::create([
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
                'size_id'    => $request->size_id,
                'color_id'   => $request->color_id,
                'quantity'   => $request->quantity ?? 1,
            ]);
        }

        if ($request->buy_now == 1) {
            return redirect()->route('cart.index');
        }
        
        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }

    public function remove($id)
    {
        Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed!');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return redirect()->route('cart.index')->with('error', 'Invalid or inactive coupon code!');
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return redirect()->route('cart.index')->with('error', 'Coupon usage limit reached!');
        }

        $cartTotal = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get()
            ->sum(fn($item) => $item->product ? $item->product->price * $item->quantity : 0);

        if ($cartTotal < $coupon->min_order) {
            return redirect()->route('cart.index')
                ->with('error', "Minimum order of ₹{$coupon->min_order} required for this coupon!");
        }

        session(['coupon' => $coupon->code]);
        return redirect()->route('cart.index')->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return response()->json(['success' => true]);
    }

    public function applyCouponAjax(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive coupon code!'
            ]);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon usage limit reached!'
            ]);
        }

        if ($coupon->expiry_date && \Carbon\Carbon::parse($coupon->expiry_date)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired!'
            ]);
        }

        $cartTotal = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get()
            ->sum(fn($item) => $item->product ? $item->product->price * $item->quantity : 0);

        if ($cartTotal < $coupon->min_order) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order of ₹{$coupon->min_order} required!"
            ]);
        }

        session(['coupon' => $coupon->code]);

        return response()->json([
            'success'  => true,
            'message'  => 'Coupon applied successfully!',
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
        ]);
    }
}
