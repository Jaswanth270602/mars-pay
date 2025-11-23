@extends('agent.layout.header')

@section('content')
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-3">Payment Confirmation</h3>
        <p>You are about to pay <strong>₹{{ $amount }}</strong></p>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="consentCheck">
            <label class="form-check-label" for="consentCheck">
                I confirm that this payment is being made on my own willingness.
            </label>
        </div>

        <button id="proceedBtn" class="btn btn-primary" disabled>
            Proceed to Payment
        </button>
    </div>
</div>

<script>
    document.getElementById('consentCheck').addEventListener('change', function () {
        document.getElementById('proceedBtn').disabled = !this.checked;
    });

    document.getElementById('proceedBtn').addEventListener('click', function () {
        window.location.href = "{{ $redirectUrl }}";
    });
</script>
@endsection
