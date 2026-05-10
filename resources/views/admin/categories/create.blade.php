@extends('admin.layout.app')

@section('content')
<h3>Add Category</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.categories.store') }}" style="max-width:400px">
    @csrf
    <div class="mb-3">
        <label>Category Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
    </div>
    <button class="btn btn-primary">Save Category</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection