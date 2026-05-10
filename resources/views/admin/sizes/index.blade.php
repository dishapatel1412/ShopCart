@extends('admin.layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>All Sizes</h3>
    <a href="{{ route('admin.sizes.create') }}" class="btn btn-primary">+ Add Size</a>
</div>

@if($sizes->isEmpty())
    <div class="alert alert-info">No sizes found.</div>
@else
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sizes as $size)
            <tr>
                <td>{{ $size->name }}</td>
                <td>
                    <a href="{{ route('admin.sizes.edit', $size->id) }}"
                       class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.sizes.destroy', $size->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this size?')">
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