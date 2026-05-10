@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4>👤 My Profile</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label>Profile Image</label>
                    <input type="file" name="profile_image" class="form-control">
                </div>
                @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" width="80" class="mb-2 rounded">
                @endif
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                </div>

                <div class="mb-3">
                    <label>Phone</label>
                    <input type="text" name="mobile_num" class="form-control" value="{{ $user->mobile_num }}">
                </div>

                <div class="mb-3">
                    <label>Address</label>
                    {{-- <input type="text" name="mobile_num" class="form-control" value="{{ $user->address }}"> --}}
                    <textarea name="address" class="form-control" cols="10" rows="10">{{ $user->address }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Profile
                </button>
            </form>
            <div class="mt-4">
                @if(auth()->user()->google2fa_enabled)
                    <form method="POST" action="{{ route('2fa.disable') }}">
                        @csrf
                        <button class="btn btn-danger text-white px-4 py-2 rounded">
                            Disable 2FA
                        </button>
                    </form>
                @else
                    <a href="{{ route('2fa.enable.form') }}" 
                       class="btn btn-info px-4 py-2 rounded">
                        Enable 2FA
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection