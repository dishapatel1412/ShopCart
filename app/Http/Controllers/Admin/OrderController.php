<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdatedMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        return view('admin.orders.index', compact('orders'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered'
        ]);

        // Only update if changed
        if ($order->status !== $request->status) {
            $order->update([
                'status' => $request->status
            ]);

            // Send mail
            Mail::to($order->email)->send(new OrderStatusUpdatedMail($order));
        }
        return back()->with('success', 'Order status updated');
    }

    public function downloadInvoice($id)
    {
        $order = Order::findOrFail($id);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        $dateTime = Carbon::now()->format('d-m-Y_H-i');

        return $pdf->download('invoice_'.$dateTime.'.pdf');
    }
}
