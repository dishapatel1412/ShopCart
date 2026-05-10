<h1>Products Purchased:</h1>
<h2>Billing Address: </h2>
<p>{{ $order->name }}</p>
<p>{{ $order->address }}</p>
<p>{{ $order->city }}, {{ $order->state }}</p>
<p>{{ $order->pincode }}</p>
<p>{{ $order->phone }}</p>
<p>{{ $order->email }}</p>

<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Sub Total</th>
    </tr>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->product_price }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->subtotal }}</td>
        </tr>
    @endforeach
    <h3>Discount: {{ $order->discount }}</h3>
    <h3>Tax: {{ $order->tax }}</h3>
    <h3>Total: {{ $order->total}}</h3>
    <h3>Status: {{ $order->status }}</h3>
</table>