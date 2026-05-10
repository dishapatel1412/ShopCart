@extends('admin.layout.app')

@section('content')
<h3>Edit Coupon</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}" style="max-width:500px">
    @csrf
    <div class="mb-3">
        <label>Coupon Code</label>
        <input type="text" name="code" class="form-control text-uppercase"
               value="{{ old('code', $coupon->code) }}">
    </div>
    <div class="mb-3">
        <label>Discount Type</label>
        <select name="type" class="form-select">
            <option value="fixed"      {{ old('type', $coupon->type) == 'fixed'      ? 'selected' : '' }}>Fixed (₹)</option>
            <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Discount Value</label>
        <input type="number" name="value" class="form-control"
               value="{{ old('value', $coupon->value) }}">
    </div>
    <div class="mb-3">
        <label>Minimum Order Amount (₹)</label>
        <input type="number" name="min_order" class="form-control"
               value="{{ old('min_order', $coupon->min_order) }}">
    </div>
    <div class="mb-3">
        <label>Usage Limit <small class="text-muted">(leave empty for unlimited)</small></label>
        <input type="number" name="usage_limit" class="form-control"
               value="{{ old('usage_limit', $coupon->usage_limit) }}">
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" class="form-check-input"
               id="is_active" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
    <button class="btn btn-success">Update Coupon</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection