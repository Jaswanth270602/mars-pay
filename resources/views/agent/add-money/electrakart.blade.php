@extends('agent.layout.header')
@section('content')

<script>
    function createElectraOrder() {
        $(".loader").show();
        var token = $("input[name=_token]").val();
        var amount = $("#amount").val();

        $.ajax({
            type: "POST",
            url: "{{ url('agent/add-money/v6/create-order') }}",
            data: {
                _token: token,
                amount: amount
            },
            success: function (response) {
                $(".loader").hide();
                if (response.status === 'success') {
                    window.location.href = response.data.paymentUrl;
                } else {
                    alert(response.message || "Something went wrong");
                }
            },
            error: function (xhr) {
                $(".loader").hide();
                alert("Validation failed: " + xhr.responseJSON.message);
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
                        <input type="text" class="form-control" id="amount" placeholder="Enter Amount">
                    </div>
                    <div class="modal-footer">
                        @csrf
                        <button class="btn btn-primary" type="button" onclick="createElectraOrder()">Proceed to Payment</button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
