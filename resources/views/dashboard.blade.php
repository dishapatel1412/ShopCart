@extends('layouts.app')

@section('content')
    {{-- Search & Filter --}}
    <div class="d-flex justify-content-center mb-4">
        <div class="input-group" style="max-width: 500px;">
            <input type="text"
                    id="searchInput"
                    class="form-control"
                    placeholder="Search by name, category or price..."
                    value="{{ request('search') }}">
            @if(request('search'))
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">✕</a>
            @endif
        </div>
    </div>

    @if($products->isEmpty())
        <div class="alert alert-info">
            No products found for "{{ request('search')}}".
            <a href="{{ route('dashboard') }}" class="alert-link">Clear Search</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        {{-- Image --}}
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                    class="card-img-top"
                                    style="height: 220px; width: 100%; object-fit: contain; padding: 10px; background: #ffffff;"
                                    >
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height: 220px; width: 100%;">
                                <span class="text-muted">No Image</span>
                            </div>
                        @endif

                        {{-- Details --}}
                        <div class="card-body pb-0" style="min-height: 140px">
                            {{-- For displaying product details on card --}}
                            <h6 class="card-title fw-bold mb-1 product-name-link"
                                    style="min-height: 48px; cursor:pointer;"
                                    onclick="openProductModal({{ $product->id }})">
                                {{ $product->name }}
                            </h6>
                            {{-- For displaying product details on a new page --}}
                            {{-- <a href="{{ route('products.show', $product->id) }}"
                                    class="text-decoration-none text-dark">
                                <h6 class="card-title fw-bold mb-1" style="min-height: 48px">{{ $product->name }}</h6>
                            </a> --}}
                            @if($product->category)
                                <span class="badge bg-secondary mb-1" style="min-height: 20px">{{ $product->category->name }}</span>
                            @endif

                            <p class="fw-bold mb-2">₹{{ $product->price }}</p>

                            {{-- <div class="d-flex gap-3 mb-2" style="min-height: 38px">
                                @if($product->sizes->count() > 0)
                                    <div class="flex-fill">
                                        <select name="size_id" class="form-select form-select-sm"
                                                id="size_{{ $product->id }}">
                                            <option value="">Select Size</option>
                                            @foreach($product->sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                    
                                <div class="flex-fill">
                                    @if($product->colors->count() > 0)
                                        <select class="form-select form-select-sm"
                                                id="color_{{ $product->id }}">
                                            <option value="">Select Color</option>
                                            @foreach($product->colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="d-flex gap-3 mb-2" style="min-height: 38px">
                                {{-- Size --}}
                                <div class="flex-fill">
                                    @if($product->sizes->count() > 0)
                                        <select name="size_id"
                                                class="form-select form-select-sm"
                                                id="size_{{ $product->id }}">
                                            <option value="">Select Size</option>
                                                @foreach($product->sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                @endforeach
                                        </select>
                                    @else
                                        {{-- Placeholder to maintain layout --}}
                                        <select class="form-select form-select-sm" disabled>
                                            <option>No Size</option>
                                        </select>
                                    @endif
                                </div>

                                {{-- Color --}}
                                <div class="flex-fill">
                                    @if($product->colors->count() > 0)
                                        <select class="form-select form-select-sm"
                                                id="color_{{ $product->id }}">
                                            <option value="">Select Color</option>
                                                @foreach($product->colors as $color)
                                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                @endforeach
                                        </select>
                                    @else
                                        {{-- Placeholder to maintain layout --}}
                                        <select class="form-select form-select-sm" disabled>
                                            <option>No Color</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                            <form action="{{ route('cart.add') }}" method="POST"
                                    id="cartForm_{{ $product->id }}"
                                    onsubmit="setVariant({{ $product->id }})">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="size_id"  id="cart_size_{{ $product->id }}">
                                <input type="hidden" name="color_id" id="cart_color_{{ $product->id }}">
                                <button type="submit"
                                        class="btn rounded-circle border border-dark d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px;"
                                        title="Add to Cart">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="none" stroke="black" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h13M10 21a1 1 0 100-2 1 1 0 000 
                                                2zm7 0a1 1 0 100-2 1 1 0 000 2z" />
                                    </svg>
                                </button>
                            </form>
                        
                            <form action="{{ route('wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button class="btn rounded-circle border border-dark d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px;"
                                        title="Add to Wishlist">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="none" stroke="black" stroke-width="1.5" viewBox="0 0 16 16">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385
                                                .92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357
                                                .955-1.886.838-3.362.314-4.385C13.486.878 10.4.281 8.717 2.01L8 2.748z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add this after the products grid closing @endif --}}
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif

    {{-- Product Detail Modal --}}
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                {{-- Close Button --}}
                <button type="button" class="btn-close position-absolute"
                        style="top:16px; right:16px; z-index:10;"
                        data-bs-dismiss="modal">
                </button>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        {{-- Left — Image --}}
                        <div class="col-md-5 d-flex align-items-center justify-content-center p-4"
                                style="background:#f8f9fa; min-height:350px;">
                            <img id="modalImage" src="" alt=""
                                     style="max-height:300px; max-width:100%; object-fit:contain;">
                        </div>
                        {{-- Right — Details --}}
                        <div class="col-md-7 p-4">
                            {{-- Category --}}
                            <span id="modalCategory" class="badge bg-secondary mb-2"></span>
                            {{-- Name --}}
                            <h4 id="modalName" class="fw-bold mb-2"></h4>
                            {{-- Price --}}
                            <h5 id="modalPrice" class="text-primary fw-bold mb-3"></h5>
                            <hr>
                            <form action="{{ route('cart.add') }}" method="POST" id="modalForm">
                                @csrf
                                <input type="hidden" name="product_id" id="modalProductId">
                                <input type="hidden" name="buy_now"    id="modalBuyNow" value="0">
                                {{-- Sizes --}}
                                <div id="modalSizeSection" class="mb-3" style="display:none;">
                                    <label class="fw-bold mb-2 d-block">Select Size</label>
                                    <div id="modalSizes" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                {{-- Colors --}}
                                <div id="modalColorSection" class="mb-3" style="display:none;">
                                    <label class="fw-bold mb-2 d-block">Select Color</label>
                                    <div id="modalColors" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                {{-- Quantity --}}
                                <div class="mb-4">
                                    <label class="fw-bold mb-2 d-block">Quantity</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button"
                                                class="btn btn-outline-dark btn-sm"
                                                onclick="changeModalQty(-1)">−</button>
                                        <span id="modalQtyDisplay" class="fw-bold fs-5">1</span>
                                        <button type="button"
                                                class="btn btn-outline-dark btn-sm"
                                                onclick="changeModalQty(1)">+</button>
                                        <input type="hidden" name="quantity" id="modalQtyInput" value="1">
                                    </div>
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex gap-3 mb-3">
                                    <button type="submit"
                                            class="btn grow py-2 fw-bold d-flex align-items-center justify-content-center gap-2"
                                            style="background:#1a1a1a; color:white; border-radius:10px;"
                                            onclick="document.getElementById('modalBuyNow').value=0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h13
                                                    M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                                        </svg>
                                        Add to Cart
                                    </button>
                                    <button type="submit"
                                            class="btn grow py-2 fw-bold d-flex align-items-center justify-content-center gap-2"
                                            style="background:linear-gradient(135deg,#667eea,#764ba2); color:white; border-radius:10px; border:none;"
                                            onclick="document.getElementById('modalBuyNow').value=1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Buy Now
                                    </button>
                                </div>
                            </form>

                            {{-- Wishlist --}}
                            <form action="{{ route('wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" id="modalWishlistProductId">
                                <button type="submit"
                                        class="btn w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2"
                                        style="border:2px solid #e74c3c; color:#e74c3c; border-radius:10px; background:white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682
                                                a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318
                                                a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    Add to Wishlist
                                </button>
                            </form>
                            <button type="button"
                                    class="btn w-100 mt-3 fw-semibold"
                                    style="border:1px solid #6c757d; border-radius:12px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#inquiryModal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}">
                                Ask About Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inquiry modal --}}
    <div class="modal fade" id="inquiryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Inquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inquiry.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="mb-3">
                            <input type="text" name="subject" class="form-control"
                                    placeholder="Subject (optional)">
                        </div>
                    
                        <div class="mb-3">
                            <textarea name="message" class="form-control"
                                    rows="4" placeholder="Your message..."></textarea>
                        </div>
                    
                        <button class="btn btn-dark w-100">
                            Send Inquiry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function setVariant(productId) {
            const sizeSelect  = document.getElementById('size_' + productId);
            const colorSelect = document.getElementById('color_' + productId);
            if (sizeSelect)  document.getElementById('cart_size_' + productId).value  = sizeSelect.value;
            if (colorSelect) document.getElementById('cart_color_' + productId).value = colorSelect.value;
        }

        // Live search
        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimer);
            const query = this.value.trim();
            searchTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                url.searchParams.delete('page');
                if (!query) url.searchParams.delete('search');
                    window.location.href = url.toString();
                }, 
            500);
        });

        // For displaying product details on a card
        // Product data from PHP
        const products = @json($productsJson);

        let modalQty = 1;

        function openProductModal(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            // Reset qty
            modalQty = 1;
            document.getElementById('modalQtyDisplay').textContent = 1;
            document.getElementById('modalQtyInput').value         = 1;

            // Fill data
            document.getElementById('modalProductId').value          = product.id;
            document.getElementById('modalWishlistProductId').value   = product.id;
            document.getElementById('modalName').textContent          = product.name;
            document.getElementById('modalPrice').textContent         = '₹' + Number(product.price).toLocaleString('en-IN');
            document.getElementById('modalCategory').textContent      = product.cFategory;
            document.getElementById('modalImage').src                 = product.image;

            // Sizes
            const sizeSection = document.getElementById('modalSizeSection');
            const sizesDiv    = document.getElementById('modalSizes');
            sizesDiv.innerHTML = '';
            if (product.sizes.length > 0) {
                sizeSection.style.display = 'block';
                product.sizes.forEach((size, i) => {
                    sizesDiv.innerHTML += `
                        <input type="radio" name="size_id"
                               id="msize_${size.id}" value="${size.id}" class="btn-check"
                            ${i === 0 ? 'checked' : ''}>
                        <label class="btn btn-outline-dark btn-sm" for="msize_${size.id}">
                            ${size.name}
                        </label>`;
                });
            } else {
                sizeSection.style.display = 'none';
            }

            // Colors
            const colorSection = document.getElementById('modalColorSection');
            const colorsDiv    = document.getElementById('modalColors');
            colorsDiv.innerHTML = '';
            if (product.colors.length > 0) {
                colorSection.style.display = 'block';
                product.colors.forEach((color, i) => {
                    colorsDiv.innerHTML += `
                        <input type="radio" name="color_id"
                               id="mcolor_${color.id}" value="${color.id}" class="btn-check"
                            ${i === 0 ? 'checked' : ''}>
                        <label class="btn btn-outline-dark btn-sm d-flex align-items-center gap-2"
                               for="mcolor_${color.id}">
                            <span class="rounded-circle border"
                                style="width:13px;height:13px;background:${color.hex};display:inline-block;">
                            </span>
                            ${color.name}
                        </label>`;
                });
            } else {
                colorSection.style.display = 'none';
            }

            // Open modal
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }

        function changeModalQty(change) {
            modalQty = Math.max(1, modalQty + change);
            document.getElementById('modalQtyDisplay').textContent = modalQty;
            document.getElementById('modalQtyInput').value = modalQty;
        }

        const productModal = document.getElementById('productModal');

        productModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const productId = button.getAttribute('data-product-id');

            document.getElementById('modalWishlistProductId').value = productId;
        });

        window.userId = {{ Auth::id() }} // can be changed to auth()->id()
        window.userName = "{{ Auth::user()->name }}";
    </script>
@endsection