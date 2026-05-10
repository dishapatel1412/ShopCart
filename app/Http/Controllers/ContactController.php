<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
{
    return view('contact');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'mobile_num' => 'required|digits:10',
        'message' => 'required',
    ]);

    // Contact::create($request->all());
    Contact::create([
       'name' => $request->name,
       'email' => $request->email,
       'mobile_num' => $request->mobile_num,
       'message' => $request->message,
    ]);

    return back()->with('success', 'Message sent successfully!');
}
}
