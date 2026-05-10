<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductInquiry;

class ProductInquiryController extends Controller
{
    public function index(Request $request)
    {
        $inquiries = ProductInquiry::where('user_id', $request->user()->id)
            ->get();
        return response()->json([
            'success' => true,
            'data' => $inquiries 
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            // 'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $query = ProductInquiry::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Query raised successfully',
            'data' => $query,
        ]);
    }
}
