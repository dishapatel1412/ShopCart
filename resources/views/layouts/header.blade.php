<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold text-white" href="{{ route('dashboard') }}">ShopCart</a>

    @auth
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            @auth
                @php
                    $image = Auth::user()->profile_image;
                @endphp
                @if(!empty($image))
                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" width="35" height="35" class="rounded-circle me-2">
                @else
                    <i class="bi bi-person-circle text-white me-2" style="font-size: 22px;"></i>
                @endif
            @endauth
        </a>

        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile.show') }}">My Profile</a></li>
            {{-- <li><a class="dropdown-item" href="{{ route('orders.index') }}">My Orders</a></li>
            <li><a class="dropdown-item" href="{{ route('cart.index') }}">Cart</a></li>
            <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">Wishlist</a></li> --}}
            <li><a class="dropdown-item" href="{{ route('panel.show') }}">My Panel</a></li>
            <hr class="m-0">
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item">Logout</button>
                </form>
            </li>
        </ul>
    </div>
    @endauth
</nav>