<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductInquiry;

class ProductInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ProductInquiry::where('user_id', Auth::id())->get();

        return view('inquiry', compact('inquiries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'message' => 'required|string',
        ]);

        ProductInquiry::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Inquiry sent successfully!');
    }
}
