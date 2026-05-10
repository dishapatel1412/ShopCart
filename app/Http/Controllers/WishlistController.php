<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    // Show wishlist page
    public function index()
    {
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return view('wishlist', compact('wishlistItems'));
    }

    // Add to wishlist
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if (!$exists) {
            Wishlist::create([
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
            ]);
            return redirect()->back()->with('success', 'Added to wishlist!');
        }

        return redirect()->back()->with('info', 'Already in wishlist!');
    }

    // Remove from wishlist
    public function remove($id)
    {
        Wishlist::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('wishlist.index')->with('success', 'Removed from wishlist!');
    }
}
