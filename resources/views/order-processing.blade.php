<!DOCTYPE html>
<html>
<head>
    <title>Processing Order</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

    </style>
</head>

<body>

    <h2>Order placed successfully 🎉</h2>

    <p>Redirecting...</p>

    <script>

        setTimeout(() => {

            window.location.href =
                "{{ route('order.success', $order->id) }}";

        }, 3000);

    </script>

</body>
</html>