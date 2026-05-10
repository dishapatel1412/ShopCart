@extends('layouts.app')

@section('content')
    <div class="container py-4">
        {{-- Back Button --}}
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm mb-4">
            ← Back to Products
        </a>

        <div class="card border-0 shadow-sm">
            <div class="row g-0">
                {{-- Left — Product Image --}}
                <div class="col-md-5 d-flex align-items-center justify-content-center p-4"
                         style="background:#f8f9fa; border-radius: 12px 0 0 12px;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                                 style="max-height:400px; max-width:100%; object-fit:contain;">
                    @else
                        <div class="text-muted">No Image Available</div>
                    @endif
                </div>

                {{-- Right — Product Details --}}
                <div class="col-md-7">
                    <div class="card-body p-4">
                        {{-- Category --}}
                        @if($product->category)
                            <span class="badge bg-secondary mb-2">{{ $product->category->name }}</span>
                        @endif

                        {{-- Name --}}
                        <h3 class="fw-bold mb-2">{{ $product->name }}</h3>

                        {{-- Price --}}
                        <h4 class="text-primary fw-bold mb-3">₹{{ number_format($product->price, 2) }}</h4>

                        {{-- Description --}}
                        @if($product->description)
                            <p class="text-muted mb-4">{{ $product->description }}</p>
                        @endif
                        <hr>
                        <form action="{{ route('cart.add') }}" method="POST" id="productForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="buy_now" id="buyNowInput" value="0">

                            {{-- Size Selection --}}
                            @if($product->sizes->isNotEmpty())
                                <div class="mb-3">
                                    <label class="fw-bold mb-2">Select Size</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($product->sizes as $size)
                                            <input type="radio" name="size_id"
                                                    id="size_{{ $size->id }}"
                                                    value="{{ $size->id }}"
                                                    class="btn-check">
                                            <label class="btn btn-outline-dark btn-sm"
                                                    for="size_{{ $size->id }}">
                                                {{ $size->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Color Selection --}}
                            @if($product->colors->isNotEmpty())
                                <div class="mb-3">
                                    <label class="fw-bold mb-2">Select Color</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($product->colors as $color)
                                            <input type="radio" name="color_id"
                                                    id="color_{{ $color->id }}"
                                                    value="{{ $color->id }}"
                                                    class="btn-check">
                                            <label class="btn btn-outline-dark btn-sm d-flex align-items-center gap-2"
                                                    for="color_{{ $color->id }}">
                                                <span class="rounded-circle border"
                                                    style="width:14px; height:14px; background:{{ $color->hex_code ?? '#ccc' }}; display:inline-block;">
                                                </span>
                                                {{ $color->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Quantity --}}
                            <div class="mb-4">
                                <label class="fw-bold mb-2">Quantity</label>
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn btn-outline-dark btn-sm"
                                            onclick="changeQty(-1)">−</button>
                                    <span id="qtyDisplay" class="fw-bold fs-5">1</span>
                                    <button type="button" class="btn btn-outline-dark btn-sm"
                                            onclick="changeQty(1)">+</button>
                                    <input type="hidden" name="quantity" id="qtyInput" value="1">
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex gap-3 mb-3">
                                <button type="submit"
                                        class="btn flex-grow-1 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                                        style="background:#1a1a1a; color:white; border-radius:12px; font-size:15px;"
                                        onclick="document.getElementById('buyNowInput').value=0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h13
                                                M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                                    </svg>
                                    Add to Cart
                                </button>

                                <button type="submit"
                                        class="btn flex-grow-1 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                                        style="background: linear-gradient(135deg, #667eea, #764ba2); color:white; border-radius:12px; font-size:15px; border:none;"
                                        onclick="document.getElementById('buyNowInput').value=1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Buy Now
                                </button>
                            </div>
                        </form>
                            <form action="{{ route('wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit"
                                        class="btn w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                                        style="border: 2px solid #e74c3c; color:#e74c3c; border-radius:12px; font-size:15px; background:white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682
                                                a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318
                                                a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    Add to Wishlist
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
<script>
    let qty = 1;

    function changeQty(change) {
        qty = Math.max(1, qty + change);
        document.getElementById('qtyDisplay').textContent = qty;
        document.getElementById('qtyInput').value = qty;
    }
</script>
@endsection