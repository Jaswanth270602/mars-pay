@extends('agent.layout.header')
@section('content')


    <script type="text/javascript">
        function createOrder() {
            $(".loader").show();
            var token = $("input[name=_token]").val();
            var amount = $("#amount").val();
            var dataString = 'amount=' + amount + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('agent/add-money/v3/create-order')}}",
                data: dataString,
                success: function (msg) {
                    $(".loader").hide();
                    if (msg.status == 'success') {
                        swal("Success", msg.message, "success");
                        window.open(msg.payment_url, '_blank');
                    } else if (msg.status == 'validation_error') {
                        $("#amount_errors").text(msg.errors.amount);
                    } else {
                        swal("Failed", msg.message, "error");
                    }
                }
            });
        }
    </script>

    <div class="main-content-body">

        <div class="row">
            <div class="col-lg-4 col-md-12">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <a class="close" data-dismiss="alert">×</a>
                        <strong>Alert </strong> {!!Session::get('success')!!}
                    </div>
                @endif

                @if(Session::has('failure'))
                    <div class="alert alert-danger">
                        <a class="close" data-dismiss="alert">×</a>
                        <strong>Alert </strong> {!!Session::get('failure')!!}
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">{{ $page_title }}</h6>
                            <hr>
                        </div>


                        <div class="mb-4">
                            <label>Amount</label>
                            <input type="text" class="form-control" placeholder="Amount" name="amount" id="amount">
                            <ul class="parsley-errors-list filled">
                                <li class="parsley-required" id="amount_errors"></li>
                            </ul>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button class="btn ripple btn-primary" type="button" onclick="createOrder()">Add Money</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                    </div>

                </div>
            </div>




        </div>


    </div>
    </div>
    </div>



@endsection