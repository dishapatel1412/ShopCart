<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopCart | Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
</head>

<body class="d-flex flex-column min-vh-100">
    @include('admin.layout.header')

    <div class="container-fluid flex-grow-1">
        <div class="row">

            {{-- Sidebar --}}
            <div class="col-md-2 bg-light min-vh-100 pt-4 px-0 border-end">
                @include('admin.layout.sidebar')
            </div>

            {{-- Main Content --}}
            <div class="col-md-10 pt-4 px-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @yield('content')
            </div>

        </div>
    </div>

    @include('admin.layout.footer')
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    </body>
</html>