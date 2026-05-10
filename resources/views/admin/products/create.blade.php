@extends('admin.layout.app')

@section('content')
<div class="container mt-4" style="max-width:600px">
    <h3>Add Product</h3>

    @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Price (₹)</label>
            <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}">
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label>Sizes</label>
            <select name="sizes[]" class="form-select select2" multiple>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}"
                        {{ in_array($size->id, old('sizes', [])) ? 'selected' : '' }}>
                        {{ $size->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Colors</label>
            <select name="colors[]" class="form-select select2" multiple>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}"
                        {{ in_array($color->id, old('colors', [])) ? 'selected' : '' }}>
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-5 d-flex gap-2">
            <button class="btn btn-success">Update Product</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection