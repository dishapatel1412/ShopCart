<div class="d-flex flex-column p-3">

    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark fw-semibold {{ request()->is('admin/dashboard') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.dashboard') }}">
               Dashboard
            </a>
        </li>
    </ul>

    {{-- Orders --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Orders</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/orders') ? 'active bg-white border-start border-3 border-primary' : '' }}" 
                href="{{ route('admin.orders') }}">
                Orders
            </a>
        </li>
    </ul>

    {{-- Products --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Products</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/products') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.dashboard') }}">
               All Products
            </a>
        </li>
    </ul>

    {{-- Categories --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Categories</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/categories') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.categories.index') }}">
               All Categories
            </a>
        </li>
    </ul>

    {{-- Sizes --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Sizes</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/sizes') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.sizes.index') }}">
               All Sizes
            </a>
        </li>
    </ul>

    {{-- Colors --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Colors</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/colors') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.colors.index') }}">
               All Colors
            </a>
        </li>
    </ul>

    {{-- Coupons --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Coupons</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/coupons') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.coupons.index') }}">
               All Coupons
            </a>
        </li>
    </ul>

    {{-- Inquiry --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Product Inquiry</p>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/products') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.inquiries') }}">
               All Inquiries
            </a>
        </li>
    </ul>

    {{-- Contact --}}
    <p class="text-muted small text-uppercase fw-bold px-2 mb-1">Contact</p>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-dark {{ request()->is('admin/contact') ? 'active bg-white border-start border-3 border-primary' : '' }}"
               href="{{ route('admin.contact.index') }}">
               Contact
            </a>
        </li>
    </ul>

</div>