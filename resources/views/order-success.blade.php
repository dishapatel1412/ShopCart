@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <div class="mb-4">
        <span style="font-size: 80px;">🎉</span>
    </div>
    <h2 class="fw-bold mb-2">Order Placed Successfully!</h2>
    <p class="text-muted mb-1">Thank you for shopping with ShopCart.</p>
    <p class="text-muted">Your Order ID is <strong>#{{ $order->id }}</strong></p>

    <div class="card border-0 shadow-sm mx-auto mt-4 mb-4" style="max-width: 500px;">
        <div class="card-body text-start">
            <h6 class="fw-bold mb-3">Order Details</h6>

            @foreach($order->items as $item)
            <div class="d-flex justify-content-between mb-2">
                <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                <span>₹{{ $item->subtotal }}</span>
            </div>
            @endforeach

            <hr>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Subtotal</span>
                <span>₹{{ $order->subtotal }}</span>
            </div>
            @if($order->discount > 0)
            <div class="d-flex justify-content-between mb-1 text-success">
                <span>Discount</span>
                <span>- ₹{{ $order->discount }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">GST (18%)</span>
                <span>₹{{ number_format($order->tax, 2) }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <span>Total Paid</span>
                <span class="text-primary">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark">View My Orders</a>
        <a href="{{ route('dashboard') }}" class="btn btn-dark">Continue Shopping</a>
    </div>
</div>
@endsection