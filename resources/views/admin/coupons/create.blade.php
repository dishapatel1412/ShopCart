@extends('admin.layout.app')

@section('content')
<h3>Add Coupon</h3>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.coupons.store') }}" style="max-width:500px">
    @csrf
    <div class="mb-3">
        <label>Coupon Code</label>
        <input type="text" name="code" class="form-control text-uppercase"
               placeholder="e.g. SAVE10" value="{{ old('code') }}">
    </div>
    <div class="mb-3">
        <label>Discount Type</label>
        <select name="type" class="form-select">
            <option value="fixed"      {{ old('type') == 'fixed'      ? 'selected' : '' }}>Fixed (₹)</option>
            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Discount Value</label>
        <input type="number" name="value" class="form-control"
               placeholder="e.g. 100 or 10" value="{{ old('value') }}">
    </div>
    <div class="mb-3">
        <label>Minimum Order Amount (₹)</label>
        <input type="number" name="min_order" class="form-control"
               placeholder="e.g. 500" value="{{ old('min_order', 0) }}">
    </div>
    <div class="mb-3">
        <label>Usage Limit <small class="text-muted">(leave empty for unlimited)</small></label>
        <input type="number" name="usage_limit" class="form-control"
               placeholder="e.g. 100" value="{{ old('usage_limit') }}">
    </div>
    <div class="mb-3">
        <label>Expiry Date <small class="text-muted">(leave empty for no expiry)</small></label>
        <input type="date" name="expiry_date" class="form-control"
               value="{{ old('expiry_date') }}"
               min="{{ date('Y-m-d') }}">
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" class="form-check-input"
               id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
    <button class="btn btn-primary">Save Coupon</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection