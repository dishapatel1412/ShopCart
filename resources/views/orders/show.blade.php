@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Order #{{ $order->id }}</h3>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Orders</a>
</div>

<div class="row g-4">

    {{-- Order Items --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Items Ordered</h5>

                @foreach($order->items as $item)
                <div class="d-flex align-items-start gap-3 mb-3">

                    {{-- Image --}}
                    @if($item->product && $item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}"
                             width="70" height="70"
                             style="object-fit:contain; background:#f8f9fa; border-radius:8px; padding:5px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                             style="width:70px; height:70px; border-radius:8px;">
                            <span class="text-muted small">No Image</span>
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $item->product_name }}</h6>
                        <div class="d-flex gap-2 mb-1">
                            @if($item->size)
                                <span class="badge bg-light text-dark border">
                                    📐 {{ $item->size->name }}
                                </span>
                            @endif
                            @if($item->color)
                                <span class="badge bg-light text-dark border d-flex align-items-center gap-1">
                                    <span class="rounded-circle border"
                                          style="width:10px; height:10px; background-color:{{ $item->color->hex_code ?? '#ccc' }}; display:inline-block;">
                                    </span>
                                    {{ $item->color->name }}
                                </span>
                            @endif
                        </div>
                        <small class="text-muted">
                            ₹{{ $item->product_price }} × {{ $item->quantity }}
                        </small>
                    </div>

                    {{-- Subtotal + Status --}}
                    <div class="text-end">
                        <p class="fw-bold mb-1">₹{{ $item->subtotal }}</p>
                    </div>

                </div>
                @if(!$loop->last) <hr> @endif
                @endforeach

            </div>
        </div>

        {{-- Delivery Details --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Delivery Details</h5>
                <div class="row g-2">
                    <div class="col-md-6">
                        <small class="text-muted">Name</small>
                        <p class="fw-bold mb-0">{{ $order->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Phone</small>
                        <p class="fw-bold mb-0">{{ $order->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Email</small>
                        <p class="fw-bold mb-0">{{ $order->email }}</p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Address</small>
                        <p class="fw-bold mb-0">
                            {{ $order->address }}, {{ $order->city }},
                            {{ $order->state }} - {{ $order->pincode }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Order Summary</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order ID</span>
                    <span>#{{ $order->id }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <span>{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>₹{{ $order->subtotal }}</span>
                </div>
                @if($order->discount > 0)
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount
                        @if($order->coupon_code)
                            <small>({{ $order->coupon_code }})</small>
                        @endif
                    </span>
                    <span>- ₹{{ $order->discount }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">GST (18%)</span>
                    <span>₹{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-primary">₹{{ number_format($order->total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-center">
                    <a 
                        class="btn btn-info p-2"
                        href="{{ route('orders.invoice.download', $order) }}">
                            Download Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection