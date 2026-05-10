<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->first();

        if ($cart) {
            // update quantity
            $cart->quantity += $request->quantity;
            $cart->save();
        } else {
            // create new
            $cart = Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart',
            'data' => $cart
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->first();

        if (!$cart) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $cart->quantity = $request->quantity;
        if ($request->has('size_id')) {
            $cart->size_id = $request->size_id;
        }

        if ($request->has('color_id')) {
            $cart->color_id = $request->color_id;
        }
        $cart->save();

        return response()->json([
            'message' => 'Cart updated successfully',
            'data' => $cart
        ]);
    }

    public function destroy($id, Request $request)
    {
        $cart = Cart::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->first();

        if (!$cart) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $cart->delete();

        return response()->json([
            'message' => 'Item removed from cart'
        ]);
    }
}
