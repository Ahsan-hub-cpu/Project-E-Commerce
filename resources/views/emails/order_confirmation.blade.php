<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 5px 0;
        }
        .order-summary, .shipping-address, .items {
            margin-bottom: 20px;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Order Confirmation</h1>
    <p>Thank you for your order!</p>

    <div class="order-summary">
        <h2>Order Details:</h2>
        <ul>
            <li><strong>Order ID:</strong> {{ $order->id }}</li>
            <li><strong>Subtotal:</strong> PKR {{ number_format($order->subtotal, 2) }}</li>
            <li><strong>Discount:</strong> PKR {{ number_format($order->discount, 2) }}</li>
            <li><strong>Tax:</strong> PKR {{ number_format($order->tax, 2) }}</li>
            <li><strong>Total:</strong> PKR {{ number_format($order->total, 2) }}</li>
            <li><strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d H:i:s') }}</li>
        </ul>
    </div>

    <div class="shipping-address">
        <h2>Shipping Address:</h2>
        <p>
            <strong>{{ $order->name }}</strong><br>
            {{ $order->address }}, {{ $order->locality }}<br>
            {{ $order->city }}, {{ $order->state }} - {{ $order->zip }}<br>
            <strong>Phone:</strong> {{ $order->phone }}
        </p>
    </div>

    <div class="items">
        <h2>Items:</h2>
        <ul>
            @foreach ($order->orderItems as $item)
                <li>
                    <strong>{{ $item->product->name }}</strong> - 
                    PKR {{ number_format($item->price, 2) }} 
                    (Quantity: {{ $item->quantity }})
                    (Size: {{ $item->productVariation->size->name ?? 'N/A' }})
                </li>
            @endforeach
        </ul>
    </div>

    <p>If you have any questions, please contact us.</p>
</body>
</html>
