<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ShopCart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card border-0 shadow-lg rounded-4 w-100" style="max-width: 450px;">
            <div class="card-body p-4 p-md-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-2">
                        Create Account
                    </h2>
                    <p class="text-muted mb-0">
                        Create your ShopCart account
                    </p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div
                        class="alert alert-success alert-dismissible fade show"
                        role="alert"
                    >
                        {{ session('success') }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                        ></button>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div
                        class="alert alert-danger alert-dismissible fade show"
                        role="alert"
                    >
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

                <!-- Register Form -->
                <form method="POST" action="{{ url('/register') }}">
                    @csrf
                    <!-- Name -->
                    <div class="mb-3">
                        <label
                            for="name"
                            class="form-label fw-semibold"
                        >
                            Name
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control form-control-lg"
                            value="{{ old('name') }}"
                            placeholder="Enter your name"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label
                            for="email"
                            class="form-label fw-semibold"
                        >
                            Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control form-control-lg"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
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
                            placeholder="Create a password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label
                            for="password_confirmation"
                            class="form-label fw-semibold"
                        >
                            Confirm Password
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control form-control-lg"
                            placeholder="Confirm your password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <!-- Register Button -->
                    <button
                        type="submit"
                        class="btn btn-primary btn-lg w-100 fw-semibold"
                    >
                        Create Account
                    </button>
                </form>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <span class="text-muted">
                        Already have an account?
                    </span>
                    <a
                        href="{{ route('login') }}"
                        class="text-decoration-none fw-semibold text-primary"
                    >
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    >
    </script>
</body>
</html>