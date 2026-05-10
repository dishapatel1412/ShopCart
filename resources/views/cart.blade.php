@extends('layouts.panel')

@section('panelContent')
<h3 class="mb-4">🛒 My Cart</h3>

@if($cartItems->isEmpty())
    <div class="alert alert-info">
        Your cart is empty.
        <a href="{{ route('dashboard') }}" class="alert-link">Continue Shopping</a>
    </div>
@else
    {{-- Main Cart Form --}}
    {{-- <form method="GET" action="{{ route('checkout.index') }}" id="cartForm" 
            onsubmit="return validateSelection()"> --}}
    <div id="cartForm">
        <div class="d-flex flex-column gap-4">
            {{-- Select All --}}
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" id="selectAll" class="form-check-input mt-0"
                       style="width:20px; height:20px; cursor:pointer;">
                <label for="selectAll" class="form-check-label fw-bold">Select All</label>
            </div>

            {{-- Cart Items --}}
            <div class="d-flex flex-column gap-3">
                @foreach($cartItems as $item)
                    @if($item->product)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                {{-- Checkbox --}}
                                <div class="d-flex align-items-center" style="padding-top: 5px;">
                                    <input type="checkbox" name="selected_items[]"
                                           value="{{ $item->id }}"
                                           class="form-check-input item-checkbox mt-0"
                                           style="width:20px; height:20px; cursor:pointer;">
                                </div>

                                {{-- Product Image --}}
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                            width="90" height="90"
                                            style="object-fit: contain; border-radius: 8px; background:#f8f9fa; padding:5px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width:90px; height:90px; border-radius:8px;">
                                        <span class="text-muted small">No Image</span>
                                    </div>
                                @endif

                                {{-- Product Info --}}
                                <div class="d-flex flex-column flex-grow-1 gap-2">
                                    <h6 class="fw-bold mb-0">{{ $item->product->name }}</h6>
                                    {{-- Size & Color --}}
                                    <div class="d-flex gap-2">
                                        @if($item->size)
                                            <span class="badge bg-light text-dark border">
                                                {{ $item->size->name }}
                                            </span>
                                        @endif
                                        @if($item->color)
                                            <span class="badge bg-light text-dark border d-flex align-items-center gap-1">
                                                <span class="rounded-circle border"
                                                      style="width:12px; height:12px; background-color:{{ $item->color->hex_code ?? '#ccc' }}; display:inline-block;">
                                                </span>
                                                {{ $item->color->name }}
                                            </span>
                                        @endif
                                        @if(!$item->size && !$item->color)
                                            <span class="text-muted small">No variant selected</span>
                                        @endif
                                    </div>

                                    {{-- Price --}}
                                    <p class="text-primary fw-bold mb-0">₹{{ $item->product->price }}</p>

                                    {{-- Quantity --}}
                                    <div class="d-flex">
                                        <form action="{{ route('cart.update', $item->id) }}"
                                              method="POST"
                                              style="display: flex; align-items: center;">
                                            @csrf
                                            <input type="number" name="quantity"
                                                   value="{{ $item->quantity }}" min="1"
                                                   class="form-control form-control-sm"
                                                   style="width: 70px; margin-right: 8px;">
                                            <button class="btn btn-sm btn-outline-secondary">Update</button>
                                        </form>
                                    </div>

                                    {{-- Subtotal + Remove --}}
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-success">
                                            Subtotal: ₹{{ $item->product->price * $item->quantity }}
                                        </span>
                                        <form action="{{ route('cart.remove', $item->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Remove this item?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">🗑️ Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    {{-- Coupon Section --}}
                    <div class="mb-3" id="couponSection">
                        <div id="couponInputArea">
                            <label class="form-label small text-muted fw-bold">Have a coupon?</label>
                            <div class="input-group">
                                <input type="text" id="couponInput"
                                        class="form-control"
                                        placeholder="Enter coupon code">
                                <button type="button" class="btn btn-dark"
                                        onclick="applyCoupon()">Apply</button>
                            </div>
                            <small id="couponMessage" class="mt-1 d-block"></small>
                        </div>
                        <div id="couponAppliedArea" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-success bg-opacity-10 rounded">
                                <span>
                                    <strong id="appliedCouponCode"></strong>
                                    <span class="text-success ms-1" id="appliedCouponDiscount"></span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="removeCoupon()">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span id="summarySubtotal">₹0</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Shipping</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Items selected</span>
                        <span><span id="selectedCount">0</span> item(s)</span>
                    </div>

                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span class="text-primary" id="grandTotal">₹0</span>
                    </div>

                    <button type="button" class="btn btn-dark w-100"
                            onclick="validateSelection()">
                        Proceed to Checkout →
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- </form> --}}
@endif
@endsection

@section('scripts')
<script>
    const itemPrices = {
        @foreach($cartItems as $item)
            @if($item->product)
            {{ $item->id }}: {{ $item->product->price * $item->quantity }},
            @endif
        @endforeach
    };

    let appliedCoupon = null;

    // Restore coupon from session if already applied
    @if(session('coupon'))
        appliedCoupon = {
            code  : "{{ session('coupon') }}",
            type  : "{{ $coupon ? $coupon->type : '' }}",
            value : {{ $coupon ? $coupon->value : 0 }},
        };
        showCouponApplied(appliedCoupon);
        updateSummary();
    @endif

    // Select All toggle
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
        updateSummary();
    });

    // Individual checkbox change
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = [...document.querySelectorAll('.item-checkbox')]
                .every(c => c.checked);
            document.getElementById('selectAll').checked = allChecked;
            updateSummary();
        });
    });

    // Apply coupon via AJAX
    function applyCoupon() {
        const code  = document.getElementById('couponInput').value.trim();
        const msgEl = document.getElementById('couponMessage');

        if (!code) {
            msgEl.className   = 'text-danger mt-1 d-block';
            msgEl.textContent = 'Please enter a coupon code!';
            return;
        }

        fetch('{{ route("cart.coupon.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ coupon_code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appliedCoupon = {
                    code  : data.code,
                    type  : data.type,
                    value : parseFloat(data.value),
                };
                showCouponApplied(appliedCoupon);
                updateSummary();
                msgEl.textContent = '';
            } else {
                msgEl.className   = 'text-danger mt-1 d-block';
                msgEl.textContent = data.message;
            }
        })
        .catch(err => console.error(err));
    }

    // Remove coupon via AJAX
    function removeCoupon() {
        fetch('{{ route("cart.coupon.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appliedCoupon = null;
                document.getElementById('couponAppliedArea').style.display = 'none';
                document.getElementById('couponInputArea').style.display = 'block';
                document.getElementById('couponInput').value = '';
                updateSummary();
            }
        });
    }

    // Show applied coupon UI
    function showCouponApplied(coupon) {
        document.getElementById('couponInputArea').style.display = 'none';
        document.getElementById('couponAppliedArea').style.display = 'block';
        document.getElementById('appliedCouponCode').textContent = coupon.code;
    }

    // Calculate discount
    function calculateDiscount(subtotal) {
        if (!appliedCoupon) return 0;
        const value = parseFloat(appliedCoupon.value);
        if (appliedCoupon.type === 'fixed') {
            return value;
        } else {
            return (subtotal * value) / 100;
        }
    }

    // Update summary
    function updateSummary() {
        let subtotal = 0;
        let count = 0;

        document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
            subtotal += itemPrices[cb.value] || 0;
            count++;
        });

        const discount = count > 0 ? calculateDiscount(subtotal) : 0;
        const total = Math.max(0, subtotal - discount);

        document.getElementById('selectedCount').textContent = count;
        document.getElementById('summarySubtotal').textContent = '₹' + subtotal;
        document.getElementById('grandTotal').textContent = '₹' + total.toFixed(2);

        // Update discount display in coupon applied area
        if (appliedCoupon && count > 0) {
            document.getElementById('appliedCouponDiscount').textContent = '- ₹' + discount.toFixed(2);
        } else {
            document.getElementById('appliedCouponDiscount').textContent = '';
        }
    }

    // Validate selection
    function validateSelection() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one item to checkout!');
            return;
        }
        
        // Build URL with selected items
        let url = '{{ route("checkout.index") }}?';
        checkboxes.forEach(cb => {
            url += 'selected_items[]=' + cb.value + '&';
        });

        window.location.href = url;
    }
</script>
@endsection