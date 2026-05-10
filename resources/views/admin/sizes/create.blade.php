@extends('admin.layout.app')

@section('content')
<h3>Add Size</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.sizes.store') }}" style="max-width:400px">
    @csrf
    <div class="mb-3">
        <label>Size Name</label>
        <input type="text" name="name" class="form-control"
               placeholder="e.g. S, M, L, XL" value="{{ old('name') }}">
    </div>
    <button class="btn btn-primary">Save Size</button>
    <a href="{{ route('admin.sizes.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection