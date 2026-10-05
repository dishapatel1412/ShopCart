<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ShopCart</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
    </head>

    <body class="d-flex flex-column min-vh-100">
        @include('layouts.header')
        @if(request()->is('orders*') || request()->is('cart*') || request()->is('wishlist*') || request()->is('inquiry*'))
            {{-- Panel pages → full width --}}
            <div class="flex-grow-1">
                @yield('content')
            </div>
        @else
            {{-- Normal pages → boxed layout --}}
            <div class="container mt-4 flex-grow-1">
                @yield('content')
            </div>
        @endif
        <div class="container mt-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        @include('layouts.footer')
        <script src="{{ asset('assets/js/script.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @yield('scripts')
    </body>
</html>