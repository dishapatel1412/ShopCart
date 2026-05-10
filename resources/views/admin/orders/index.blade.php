@extends('admin.layout.app')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">📦 Orders</h2>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->name }}</td>
                        <td class="text-success fw-bold">₹{{ $order->total }}</td>

                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="d-flex gap-2">
                                @csrf

                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                        </td>

                        <td>
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Update
                                    </button>
                                    <a 
                                        class="btn btn-info btn-sm "
                                        href="{{ route('admin.invoice.download', $order) }}">
                                            Download Invoice
                                    </a>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>
</div>

@endsection