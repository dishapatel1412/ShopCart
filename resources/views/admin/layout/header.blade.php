<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">ShopCart</a>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('products.index') }}" class="nav-link">Products</a>
        @if(Auth::check())
            <span class="text-white">Welcome, {{ Auth::user()->name }}!</span>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">Register</a>
        @endif
    </div>
</nav>