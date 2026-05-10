@extends('admin.layout.app')

@section('content')
<h3>Edit Color</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.colors.update', $color->id) }}" style="max-width:400px">
    @csrf
    <div class="mb-3">
        <label>Color Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $color->name) }}">
    </div>
    <div class="mb-3">
        <label>Hex Code</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="color" name="hex_code" class="form-control form-control-color"
                   value="{{ old('hex_code', $color->hex_code) }}">
            <span class="text-muted small">Pick a color</span>
        </div>
    </div>
    <button class="btn btn-success">Update Color</button>
    <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection