@extends('admin.layout.app')

@section('content')
<h3>Add Color</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.colors.store') }}" style="max-width:400px">
    @csrf
    <div class="mb-3">
        <label>Color Name</label>
        <input type="text" name="name" class="form-control"
               placeholder="e.g. Red, Blue, Black" value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label>Hex Code</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="color" name="hex_code" class="form-control form-control-color"
                   value="{{ old('hex_code', '#000000') }}">
            <span class="text-muted small">Pick a color</span>
        </div>
    </div>
    <button class="btn btn-primary">Save Color</button>
    <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection