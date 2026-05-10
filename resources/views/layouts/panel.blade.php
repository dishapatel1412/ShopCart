@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <div class="col-md-3 bg-light border-end pt-4">
                @include('layouts.sidebar')
            </div>
            
            <div class="col-md-9 p-4">
                @yield('panelContent')
            </div>
        </div>
    </div>
@endsection