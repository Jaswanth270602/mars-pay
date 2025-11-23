@extends('agent.layout.header')
@section('content')

<script>
    function createLightspeedOrder() {
        $(".loader").show();
        var token = $("input[name=_token]").val();
        var amount = $("#amount").val();

        $.ajax({
            type: "POST",
            url: "{{ url('agent/add-money/v8/create-order') }}",
            data: {
                _token: token,
                amount: amount
            },
            success: function(response) {
                $(".loader").hide();
                if (response.status === 'success' && response.data.paymentLink) {
                    // Redirect to OUR wrapper page
                    window.location.href = response.data.paymentLink;
                } else {
                    alert(response.message || "Something went wrong. No payment link received.");
                }
            },
            error: function(xhr) {
                $(".loader").hide();
                let errMsg = "Unknown error";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                alert("Validation failed: " + errMsg);
            }
        });
    }
</script>

<div class="main-content-body">
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-1">{{ $page_title }}</h6>
                    <hr>
                    <div class="mb-4">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="amount" placeholder="Enter Amount">
                    </div>
                    <div class="modal-footer">
                        @csrf
                        <button class="btn btn-primary" type="button" onclick="createLightspeedOrder()">Proceed to Payment</button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection