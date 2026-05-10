@extends('admin.layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>All Colors</h3>
    <a href="{{ route('admin.colors.create') }}" class="btn btn-primary">+ Add Color</a>
</div>

@if($colors->isEmpty())
    <div class="alert alert-info">No colors found.</div>
@else
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Color</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colors as $color)
            <tr>
                <td>{{ $color->name }}</td>
                <td>
                    <span class="d-inline-block rounded-circle border"
                          style="width:28px; height:28px;">
                    </span>
                    {{ $color->hex_code }}
                </td>
                <td>
                    <a href="{{ route('admin.colors.edit', $color->id) }}"
                       class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.colors.destroy', $color->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this color?')">
                        @csrf
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection