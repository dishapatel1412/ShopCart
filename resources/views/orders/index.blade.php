@extends('layouts.panel')

@section('panelContent')
<h3 class="mb-4">📦 My Orders</h3>

<div class="container px-3">
    @if($orders->isEmpty())
        <div class="alert alert-info">
            You have no orders yet.
            <a href="{{ route('dashboard') }}" class="alert-link">Start Shopping</a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">

                        {{-- Order Info --}}
                        <div>
                            <h6 class="fw-bold mb-1">Order #{{ $order->id }}</h6>
                            <p class="text-muted small mb-1">
                                {{ $order->created_at->format('d M Y, h:i A') }}
                            </p>
                            <p class="text-muted small mb-0">
                                {{ $order->items->count() }} item(s)
                            </p>
                        </div>

                        {{-- Status + Total --}}
                        <div class="text-end">
                            @php
                                $statusColors = [
                                    'pending'   => 'warning',
                                    'confirmed' => 'info',
                                    'shipped'   => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} mb-2">
                                {{ ucfirst($order->status) }}
                            </span>
                            <p class="fw-bold text-primary mb-0">
                                ₹{{ number_format($order->total, 2) }}
                            </p>
                        </div>

                    </div>

                    <hr class="my-2">

                    {{-- Quick Item Preview --}}
                    <div class="d-flex gap-2 flex-wrap mb-2">
                        @foreach($order->items->take(3) as $item)
                            <span class="badge bg-light text-dark border">
                                {{ $item->product_name }} × {{ $item->quantity }}
                            </span>
                        @endforeach
                        @if($order->items->count() > 3)
                            <span class="badge bg-light text-dark border">
                                +{{ $order->items->count() - 3 }} more
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('orders.show', $order->id) }}"
                       class="btn btn-sm btn-outline-dark">View Details →
                    </a>


                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection