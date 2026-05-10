@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Contact Us</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <input type="text" name="name" class="form-control mb-3" placeholder="Name">
                <input type="email" name="email" class="form-control mb-3" placeholder="Email">
                <input type="text" name="mobile_num" class="form-control mb-3" placeholder="Mobile Number">

                <textarea name="message" class="form-control mb-3" placeholder="Message"></textarea>

                <button class="btn btn-primary">Send</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back</a>

            </form>

        </div>
    </div>
</div>

@endsection