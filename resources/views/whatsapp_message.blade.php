Hello {{ $order->name }},

Your order #{{ $order->id }} has been placed successfully.

Order Details:
@foreach($order->items as $item)
  Product: {{ $item->product_name }}
  Qty: {{ $item->quantity }}
  Price: Rs. {{ $item->product_price }}
@endforeach

Shipping Address:
{{ $order->address }}
{{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}

Contact Details:
Phone: {{ $order->phone }}
Email: {{ $order->email }}

Total Amount: Rs. {{ $order->total }}

Thank you for shopping with us!