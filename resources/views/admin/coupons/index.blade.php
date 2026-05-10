@extends('admin.layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>All Coupons</h3>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">+ Add Coupon</a>
</div>

@if($coupons->isEmpty())
    <div class="alert alert-info">No coupons found.</div>
@else
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Min Order</th>
                <th>Usage</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr>
                <td><span class="badge bg-dark">{{ $coupon->code }}</span></td>
                <td>{{ ucfirst($coupon->type) }}</td>
                <td>
                    @if($coupon->type == 'fixed')
                        ₹{{ $coupon->value }}
                    @else
                        {{ $coupon->value }}%
                    @endif
                </td>
                <td>₹{{ $coupon->min_order }}</td>
                <td>
                    {{ $coupon->used_count }}
                    @if($coupon->usage_limit)
                        / {{ $coupon->usage_limit }}
                    @else
                        / ∞
                    @endif
                </td>
                <td>
                    @if($coupon->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                       class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this coupon?')">
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