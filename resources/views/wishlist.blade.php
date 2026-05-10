@extends('layouts.panel')

@section('panelContent')
<h3 class="mb-4">My Wishlist</h3>

@if($wishlistItems->isEmpty())
    <div class="alert alert-info">
        Your wishlist is empty.
        <a href="{{ route('dashboard') }}" class="alert-link">Continue Shopping</a>
    </div>
@else
    <div class="row g-4">
        @foreach($wishlistItems as $item)
            @if($item->product)
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">

                    {{-- Image --}}
                    @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}"
                             class="card-img-top"
                             style="height: 220px; width: 100%; object-fit: contain; background: #ffffff;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                             style="height: 220px; width: 100%;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif

                    {{-- Details --}}
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-1">{{ $item->product->name }}</h6>
                        @if($item->product->category)
                            <span class="badge bg-secondary mb-2">{{ $item->product->category->name }}</span>
                        @endif
                        <p class="text-primary fw-bold mb-0">₹{{ $item->product->price }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="card-footer bg-white border-0 pb-0">
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                            <button class="btn btn-sm btn-dark w-100">🛒 Add to Cart</button>
                        </form>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <form action="{{ route('wishlist.remove', $item->id) }}"
                              method="POST"
                              onsubmit="return confirm('Remove from wishlist?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger w-100">🗑️ Remove</button>
                        </form>
                    </div>

                </div>
            </div>
            @endif
        @endforeach
    </div>
@endif
@endsection