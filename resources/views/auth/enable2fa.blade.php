@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-body text-center">

                    <h4 class="mb-3">Enable 2FA</h4>

                    <p class="text-muted">
                        Scan this QR using Google Authenticator
                    </p>

                    {{-- QR --}}
                    <div class="mb-4">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" width="200">
                    </div>

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="text-danger mb-2">
                            {{ $errors->first('otp') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('2fa.enable') }}">
                        @csrf

                        <input 
                            type="text" 
                            name="otp" 
                            class="form-control mb-3"
                            placeholder="Enter 6-digit code"
                            required
                        >

                        <button class="btn btn-success w-100">
                            Verify & Enable
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection