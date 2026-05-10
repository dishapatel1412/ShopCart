<div class="d-flex p-3">
    {{-- My Orders --}}
    <ul class="nav flex-column gap-3">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('orders') ? 'active bg-white border-start border-3 border-primary fw-semibold' : '' }}"
                href="{{ route('orders.index') }}">Orders</a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('cart') ? 'active bg-white border-start border-3 border-primary fw-semibold' : '' }}"
                href="{{ route('cart.index') }}">Cart</a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('wishlist') ? 'active bg-white border-start border-3 border-primary fw-semobold' : '' }}"
                href="{{ route('wishlist.index') }}">Wishlist</a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('inquiry') ? 'active bg-white border-start border-3 border-primary fw-semobold' : '' }}"
                href="{{ route('inquiry.index') }}">Inquiry</a>
        </li>
    </ul>
</div>