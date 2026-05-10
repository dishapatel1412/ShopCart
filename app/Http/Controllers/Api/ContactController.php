<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
    
        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile_num' => 'required|digits:10',
            'message' => 'required|string',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Store data
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_num' => $request->mobile_num,
            'message' => $request->message,
        ]);

        // Success response
        return response()->json([
            'success' => true,
            'message' => 'Contact submitted successfully',
            'data' => $contact
        ], 201);
    }

}
