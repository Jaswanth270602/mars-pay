@extends('agent.layout.header')
@section('content')

<script type="text/javascript">

    function createRmsTradeOrder (){
        $(".loader").show();
        var token = $("input[name=_token]").val();
        var amount = $("#amount").val();

        $.ajax({
            type: "POST",
            url: "{{ url('agent/add-money/v7/create-order') }}", // API call
            data: {
                _token: token,
                amount: amount
            },
            success: function (response) {
                $(".loader").hide();
                if (response.status === 'success') {
                    if (response.data && response.data.qrcode) {
                        // inject QR + deep link
                        $("#qrCodeUrl").attr('src', 'data:image/png;base64,' + response.data.qrcode);
                        if (response.data.qrString) {
                            $("#qrStringBtn").attr('href', response.data.qrString);
                        }
                        $("#amountString").text(amount);
                        $("#view-qrcode-model").modal('show');
                    } else {
                        swal("Failed", "Order created, but no QR code returned!", "error");
                    }
                } else {
                    swal("Failed", response.message || "Something went wrong", "error");
                }
            },
            error: function (xhr) {
                $(".loader").hide();
                swal("Error", (xhr.responseJSON?.message || "Unknown error"), "error");
            }
        });
    }

</script>

<div class="main-content-body">

    <div class="row">
        <div class="col-lg-6 col-md-12">

            <div class="card">
                <div class="card-body">
                    <div>
                        <h6 class="card-title mb-1">{{ $page_title }}</h6>
                        <hr>
                    </div>

                    <div class="mb-4">
                        <label>Amount</label>
                        <input type="text" class="form-control" placeholder="Enter Amount" id="amount">
                        <ul class="parsley-errors-list filled">
                            <li class="parsley-required" id="amount_errors"></li>
                        </ul>
                    </div>

                </div>

                <div class="modal-footer">
                    @csrf
                    <button class="btn ripple btn-primary" type="button" onclick="createRmsTradeOrder()">Generate Qrcode</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- QR Modal -->
<div class="modal fade" id="view-qrcode-model" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Scan & Pay</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <b>
                    <h4>Open any UPI or Bank's mobile app and scan this QR code</h4>
                    <h5>You will be prompted to enter your UPI PIN on the app</h5>
                </b>
                <br>
                <img src="" class="qr_code" id="qrCodeUrl" style="width: 200px;">

                <hr>
                <p>Post successful payment, balance will reflect in your {{ $company_name }} within 5 minutes</p>

                <a class="btn btn-primary btn-lg btn-block" href="" role="button" id="qrStringBtn" target="_blank">
                    Pay <span id="amountString"></span> Using App
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
