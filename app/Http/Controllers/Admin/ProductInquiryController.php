<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
// use App\Mail\InquiryReplyMail;

class ProductInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ProductInquiry::with(['user', 'product'])
            ->latest()
            ->get();

        return view('admin.products.product-inquiry', compact('inquiries'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string'
        ]);

        $inquiry = ProductInquiry::with('user', 'product')->findOrFail($id);

        $inquiry->update([
            'reply_message' => $request->reply_message,
            'replied_at' => now(),
            'replied_by' => Auth::id(),
            'status' => 'replied'
        ]);

        // Send email
        // Mail::to($inquiry->user->email)
        //     ->send(new InquiryReplyMail($inquiry));

        return back()->with('success', 'Reply sent successfully');
    }
}
