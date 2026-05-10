@extends('admin.layout.app')

@section('content')
<h3>Edit Category</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.categories.update', $category->id) }}" style="max-width:400px">
    @csrf
    <div class="mb-3">
        <label>Category Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $category->name) }}">
    </div>
    <button class="btn btn-success">Update Category</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection