<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Order;
use App\Services\FirebaseService;

class OrderController extends Controller
{

    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }


    // All orders of logged in user
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    // Single order detail
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.size', 'items.color'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function downloadinvoice($id)
    {
        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $pdf = Pdf::loadView('orders.invoice', compact('order'));
        $dateTime = Carbon::now()->format('d-m-Y_H-i');

        return $pdf->download('invoice_'.$dateTime.'.pdf');
    }
}

