<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function show()
    {
        return redirect()->route('orders.index');
    }
}
