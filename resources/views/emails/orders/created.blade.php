<div>
    <h1>Order Confirmation #{{ $order->id }}</h1>
    
    <p>Thank you for your order!</p>
    
    <h2>Order Details:</h2>
    <ul>
    @foreach($order->products as $product)
        <li>{{ $product->name }} - ${{ number_format($product->price, 2) }}</li>
    @endforeach
    </ul>
</div>