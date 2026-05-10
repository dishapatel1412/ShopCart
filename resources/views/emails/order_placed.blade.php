<h2>Order Received</h2>
<p>
    Hi  <strong>{{ $order->name }}</strong>,
</p>
<p>
    Your order has been placed successfully.
</p>
<p>
    <strong>Sub Total:</strong> ₹{{ $order->subtotal }}
</p>
<p>
    <strong>Discount:</strong> ₹{{ $order->discount }}
</p>
<p>
    <strong>Tax:</strong> ₹{{ $order->tax }}
</p>
<p>
    <strong>Total Amount:</strong> ₹{{ $order->total }}
</p>

<hr>

<h4>Items:</h4>
<ul>
    @foreach($order->items as $item)
        <li>{{ $item->product_name }} × {{ $item->quantity }}</li>
    @endforeach
</ul>

<p>Thanks for shopping with us 🚀</p>