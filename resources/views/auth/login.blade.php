<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5" style="max-width: 450px">
        <h3 class="mb-4">Login</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input type="text" name="login" class="form-control" value="{{ old('login') }}">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button class="btn btn-primary w-100">Login</button>
            <div class="d-flex justify-content-between align-items-center">
                <p class="mt-3">
                    <a href="{{ route('register') }}" class="text-decoration-none text-body">
                        New Here? Register
                    </a>
                </p>
                <p class="mt-3">
                    <a href="{{ route('password.request') }}" class="text-decoration-none text-body">
                        Reset Password
                    </a>
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <p class="mt-3">
                    <a href="{{ route('socialite.auth', 'google') }}" class="btn btn-body border border-black">
                        Login with Google
                    </a>
                </p>
                <p class="mt-3">
                    <a href="{{ route('socialite.auth', 'facebook') }}" class="btn btn-body border border-black">
                        Login with Facebook
                    </a>
                </p>
            </div>
        </form>

        @if(session('message'))
            <div style="color:red;">
                {{ session('message') }}
            </div>
        @endif
    </div>
</body>

</html>