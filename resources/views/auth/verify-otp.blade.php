@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-header">Verify OTP</div>
        {{-- @if(session('otp_type') === 'email')
            <p>OTP sent to your email</p>
        @else
            <p>OTP sent to your mobile number</p>
        @endif --}}
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <input type="text" name="otp" class="form-control mb-3" placeholder="Enter OTP">
                <button class="btn btn-primary">Verify</button>
                <a href="{{ route('login') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

@endsection