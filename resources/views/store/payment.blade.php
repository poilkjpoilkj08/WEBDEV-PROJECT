@extends('base.base')

@section('content')
<div class="container text-center mt-5">
    <h2>Complete Your Payment</h2>
    <p>Please complete your payment using the Midtrans popup.</p>
    <button id="pay-button" class="btn btn-primary btn-lg mt-3">Pay Now</button>
</div>

<!-- Midtrans Snap JS setup -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
  document.getElementById('pay-button').onclick = function(){
    // SnapToken acquired from the StoreController
    window.snap.pay('{{ $snapToken }}', {
      onSuccess: function(result){
        window.location.href = "{{ route('payment_status', $order->id) }}";
      },
      onPending: function(result){
        window.location.href = "{{ route('payment_status', $order->id) }}";
      },
      onError: function(result){
        alert("Payment failed!");
        window.location.href = "{{ route('payment_status', $order->id) }}";
      },
      onClose: function(){
        alert('You closed the popup without finishing the payment');
        window.location.href = "{{ route('payment_status', $order->id) }}";
      }
    });
  };

  // Automatically trigger the payment modal on page load
  window.onload = function() {
      document.getElementById('pay-button').click();
  };
</script>
@endsection