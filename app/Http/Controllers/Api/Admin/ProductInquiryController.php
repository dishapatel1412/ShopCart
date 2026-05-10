<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductInquiry;
use Illuminate\Http\Request;

class ProductInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ProductInquiry::get();

        return response()->json([
            'success' => true,
            'data' => $inquiries,
        ]);
    }

    public function store(Request $request, ProductInquiry $inquiry)
    {
        $request->validate([
            'reply_message' => 'required|string'
        ]);

        $inquiry->update([
            'reply_message' => $request->reply_message,
            'replied_at' => now(),
            'replied_by' => $request->user()->id,
            'status' => 'replied',
        ]);

        return response()->json([
            'message' => 'Query addressed successfully',
            'data' => $inquiry
        ]);
    }
}
