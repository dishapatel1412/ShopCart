@extends('layouts.app')

@section('content')
    <p>Redirecting to payment gateway, please wait...</p>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        const cashfree = Cashfree({ mode: "sandbox" });

        cashfree.checkout({
            paymentSessionId: "{{ $paymentSessionId }}",
            returnUrl: "{{ $returnUrl }}",
        });
    </script>
@endsection