@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h4 class="text-center mb-3">
                        🔐 Two-Factor Authentication
                    </h4>

                    <p class="text-center text-muted mb-4">
                        Enter the 6-digit code from your authenticator app
                    </p>

                    {{-- Error Message --}}
                    @if ($errors->any())
                        <div class="alert alert-danger text-center">
                            {{ $errors->first('otp') }}
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('2fa.verify') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Authentication Code
                            </label>

                            <input 
                                type="text" 
                                name="otp" 
                                class="form-control text-center"
                                placeholder="Enter 6-digit code"
                                maxlength="6"
                                autofocus
                                required
                            >
                        </div>

                        <button 
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Verify
                        </button>
                    </form>

                    <div class="text-center mt-3 text-muted small">
                        Didn’t get access? Contact support.
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection