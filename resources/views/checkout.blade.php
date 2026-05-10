@extends('layouts.app')

@section('content')
<h3 class="mb-4">Checkout</h3>

<form method="POST" action="{{ route('checkout.place') }}">
@csrf
<div class="row g-4">
    {{-- Left — Delivery Details --}}
    <div class="col-md-7">
        @if($savedAddresses->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Saved Addresses</h6>
                    <div class="d-flex flex-column gap-2">
                        @foreach($savedAddresses as $index => $addr)
                            <div class="border rounded p-2 d-flex justify-content-between align-items-start saved-address-card"
                                    style="cursor:pointer;"
                                    onclick="fillAddress({{ $index }})">
                                <div>
                                    <p class="mb-0 fw-bold small">{{ $addr->name }}</p>
                                    <small class="text-muted">
                                        {{ $addr->address }}, {{ $addr->city }}, 
                                        {{ $addr->state }} - {{ $addr->pincode }}
                                    </small>
                                    <br>
                                    <small class="text-muted">📞 {{ $addr->phone }}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-dark ms-2">Use</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Delivery Details</h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', session('user_name')) }}"
                               placeholder="Enter your full name">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}"
                               placeholder="Enter your email">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="Enter your phone number">
                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-12">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="3"
                                  placeholder="House no, Street, Area">{{ old('address') }}</textarea>
                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>City</label>
                        <input type="text" name="city" class="form-control"
                               value="{{ old('city') }}"
                               placeholder="City">
                        @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>State</label>
                        <input type="text" name="state" class="form-control"
                               value="{{ old('state') }}"
                               placeholder="State">
                        @error('state') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Pincode</label>
                        <input type="text" name="pincode" class="form-control"
                               value="{{ old('pincode') }}"
                               placeholder="Pincode">
                        @error('pincode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Payment Method</h5>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="payment_method"
                           id="cod" value="cod" onchange="togglePayment('cod')" checked>
                    <label class="form-check-label" for="cod">
                        Cash on Delivery
                    </label>
                    <p class="text-muted small mb-0">Pay when your order arrives</p>
                </div>
                <div class="form-check text-muted">
                    <input class="form-check-input" type="radio" name="payment_method"
                           id="online" value="online" onchange="togglePayment('online')">
                    <label class="form-check-label" for="online">
                        Online Payment
                    </label>
                    <p class="text-muted small mb-0">UPI, Card, Net Banking, Wallets</p>
                </div>
                <div id="onlineInfo" class="mt-3 p-3 rounded"
                        style="background:#f0f7ff; display:none;">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-white border text-dark">📱 UPI</span>
                        <span class="badge bg-white border text-dark">💳 Credit Card</span>
                        <span class="badge bg-white border text-dark">🏦 Net Banking</span>
                        <span class="badge bg-white border text-dark">👛 Wallets</span>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Secured by Cashfree Payments
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Right — Order Summary --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Order Summary</h5>

                {{-- Cart Items --}}
                @foreach($cartItems as $item)
                    @if($item->product)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : '' }}"
                             width="55" height="55"
                             style="object-fit:contain; background:#f8f9fa; border-radius:8px; padding:3px;">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold small">{{ $item->product->name }}</p>
                            <small class="text-muted">
                                @if($item->size) {{ $item->size->name }} @endif
                                @if($item->color) · {{ $item->color->name }} @endif
                            </small>
                            <p class="mb-0 small">Qty: {{ $item->quantity }}</p>
                        </div>
                        <span class="fw-bold">₹{{ $item->product->price * $item->quantity }}</span>
                    </div>
                    @endif
                @endforeach

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>₹{{ $subtotal }}</span>
                </div>
                @if($discount > 0)
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount
                        @if($coupon)
                            <small>({{ $coupon->code }})</small>
                        @endif
                    </span>
                    <span>- ₹{{ number_format($discount, 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">GST (18%)</span>
                    <span>₹{{ number_format($tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                    <span>Total</span>
                    <span class="text-primary">₹{{ number_format($total, 2) }}</span>
                </div>

                <button type="submit" class="btn btn-dark w-100">
                    Place Order 🎉
                </button>
                <a href="{{ route('cart.index') }}"
                   class="btn btn-outline-secondary w-100 mt-2">← Back to Cart</a>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('scripts')
<script>
    function togglePayment(method) {
        const onlineInfo = document.getElementById('onlineInfo');
        onlineInfo.style.display = method === 'online' ? 'block' : 'none';
    }

    const savedAddresses = @json($savedAddresses->values());
    function fillAddress(index) {
        const addr = savedAddresses[index];

        document.querySelector('[name="name"]').value    = addr.name    ?? '';
        document.querySelector('[name="email"]').value   = addr.email   ?? '';
        document.querySelector('[name="phone"]').value   = addr.phone   ?? '';
        document.querySelector('[name="address"]').value = addr.address  ?? '';
        document.querySelector('[name="city"]').value    = addr.city    ?? '';
        document.querySelector('[name="state"]').value   = addr.state   ?? '';
        document.querySelector('[name="pincode"]').value = addr.pincode  ?? '';

        // Highlight selected card
        document.querySelectorAll('.saved-address-card').forEach((card, i) => {
            card.classList.toggle('border-dark', i === index);
            card.classList.toggle('bg-light',    i === index);
        });


    }
</script>
@endsection