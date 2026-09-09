<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ShopCart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card border-0 shadow-lg rounded-4 w-100" style="max-width: 450px;">
            <div class="card-body p-4 p-md-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-2">
                        Welcome
                    </h2>
                    <p class="text-muted mb-0">
                        Login to your ShopCart account
                    </p>
                </div>

                <!-- Error Message -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                        >
                        </button>
                    </div>
                @endif

                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                        >
                        </button>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                        >
                        </button>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ url('/login') }}">
                    @csrf
                    <!-- Email -->
                    <div class="mb-3">
                        <label
                            for="login"
                            class="form-label fw-semibold"
                        >
                            Email
                        </label>
                        <input
                            type="email"
                            id="login"
                            name="login"
                            class="form-control form-control-lg"
                            value="{{ old('login') }}"
                            placeholder="Enter your email"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label
                            for="password"
                            class="form-label fw-semibold"
                        >
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control form-control-lg"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="btn btn-primary btn-lg w-100 fw-semibold"
                    >
                        Login
                    </button>
                </form>

                <!-- Links -->
                <div class="text-center mt-4">
                    <div class="mb-3">
                        <a
                            href="{{ route('register') }}"
                            class="text-decoration-none text-body"
                        >
                            New Here?
                            <span class="fw-semibold text-primary">
                                Register
                            </span>
                        </a>
                    </div>

                    <div class="mb-3">
                        <a
                            href="{{ route('password.request') }}"
                            class="text-decoration-none text-body"
                        >
                            Forgot your password?
                        </a>
                    </div>

                    <!-- Divider -->
                    <div class="d-flex align-items-center my-4">
                        <hr class="flex-grow-1">
                        <span class="px-3 text-muted small">
                            OR
                        </span>
                        <hr class="flex-grow-1">
                    </div>

                    <!-- Google Login -->
                    <a
                        href="{{ route('socialite.auth', 'google') }}"
                        class="btn btn-outline-dark btn-lg w-100"
                    >
                        <span class="fw-semibold">
                            Continue with Google
                        </span>
                    </a>
                </div>

                <!-- Additional Message -->
                @if(session('message'))
                    <div class="alert alert-danger mt-4 mb-0">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    >
    </script>
</body>
</html>
