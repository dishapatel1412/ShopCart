@extends('admin.layout.app')

@section('content')
<h3>Edit Size</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.sizes.update', $size->id) }}" style="max-width:400px">
    @csrf
    <div class="mb-3">
        <label>Size Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $size->name) }}">
    </div>
    <button class="btn btn-success">Update Size</button>
    <a href="{{ route('admin.sizes.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection