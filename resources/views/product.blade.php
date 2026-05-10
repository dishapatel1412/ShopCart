<h1>Products</h1>

@foreach($products as $product)

<div>
    <h3>{{ $product->name }}</h3>
    <p>Price: {{ $product->price }}</p>

    <strong>Sizes:</strong>
    @foreach($product->sizes as $size)
        {{ $size->name }}
    @endforeach

    <br>

    <strong>Colors:</strong>
    @foreach($product->colors as $color)
        {{ $color->name }}
    @endforeach

</div>

@endforeach