@extends('agent.layout.header')
@section('content')

<script>
    function createFingmoneyOrder() {
        $(".loader").show();
        var token = $("input[name=_token]").val();
        var amount = $("#amount").val();

        // Basic validation
        if (!amount || amount <= 0) {
            $(".loader").hide();
            alert("Please enter a valid amount");
            return;
        }

        $.ajax({
            type: "POST",
            url: "{{ url('agent/add-money/fingomoney/create-order') }}",
            data: {
                _token: token,
                amount: amount
            },
            success: function(response) {
                $(".loader").hide();
                if (response.status === 'success' && response.data) {
                    // Hide the form and show payment details
                    $("#payment-form").hide();
                    $("#payment-details").show();
                    
                    // Display QR Code
                    if (response.data.qrCodeUrl) {
                        $("#qr-code").attr('src', response.data.qrCodeUrl);
                        $("#qr-section").show();
                    }
                    
                    // Display UPI Intent Link
                    if (response.data.qrString) {
                        $("#upi-intent-link").attr('href', response.data.qrString);
                        $("#upi-intent-text").text(response.data.qrString);
                        $("#upi-section").show();
                    }
                    
                    // Display transaction details
                    $("#transaction-id").text(response.data.txnid);
                    $("#transaction-amount").text(amount);
                    if (response.data.expires_at) {
                        $("#expiry-time").text(response.data.expires_at);
                        $("#expiry-section").show();
                    }
                    
                    // Start status checking
                    startStatusCheck(response.data.txnid);
                    
                } else {
                    alert(response.message || "Something went wrong. No payment details received.");
                }
            },
            error: function(xhr) {
                $(".loader").hide();
                let errMsg = "Unknown error";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                alert("Error: " + errMsg);
            }
        });
    }

    function startStatusCheck(txnId) {
        // Check status every 10 seconds
        var statusInterval = setInterval(function() {
            checkPaymentStatus(txnId, statusInterval);
        }, 10000);
        
        // Stop checking after 10 minutes
        setTimeout(function() {
            clearInterval(statusInterval);
            if ($("#payment-status").text() === 'Checking...') {
                $("#payment-status").text('Payment timeout. Please contact support.');
                $("#payment-status").removeClass('text-warning').addClass('text-danger');
            }
        }, 600000);
    }

    function checkPaymentStatus(txnId, interval) {
        $.ajax({
            type: "GET",
            url: "{{ url('agent/add-money/fingomoney/check-status') }}/" + txnId,
            success: function(response) {
                if (response.status === 'success') {
                    clearInterval(interval);
                    $("#payment-status").text('Payment Successful!');
                    $("#payment-status").removeClass('text-warning').addClass('text-success');
                    $("#success-details").show();
                    $("#utr-number").text(response.data.utr || 'N/A');
                    
                    // Redirect after 3 seconds
                    setTimeout(function() {
                        window.location.href = "{{ url('agent/dashboard') }}";
                    }, 3000);
                } else if (response.status === 'failed') {
                    clearInterval(interval);
                    $("#payment-status").text('Payment Failed');
                    $("#payment-status").removeClass('text-warning').addClass('text-danger');
                }
            },
            error: function() {
                // Continue checking on error
            }
        });
    }

    function copyUpiLink() {
        var upiLink = $("#upi-intent-text").text();
        navigator.clipboard.writeText(upiLink).then(function() {
            alert('UPI link copied to clipboard!');
        });
    }

    function resetPaymentForm() {
        $("#payment-details").hide();
        $("#payment-form").show();
        $("#amount").val('');
        $("#payment-status").text('Checking...').removeClass('text-success text-danger').addClass('text-warning');
        $("#success-details").hide();
        $("#qr-section").hide();
        $("#upi-section").hide();
        $("#expiry-section").hide();
    }
</script>

<div class="main-content-body">
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <!-- Payment Form -->
            <div class="card" id="payment-form">
                <div class="card-body">
                    <h6 class="card-title mb-1">{{ $page_title }}</h6>
                    <hr>
                    <div class="mb-4">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="amount" placeholder="Enter Amount" min="10" max="2000">
                        <small class="text-muted">Minimum: ₹10, Maximum: ₹2000</small>
                    </div>
                    <div class="modal-footer">
                        @csrf
                        <button class="btn btn-primary" type="button" onclick="createFingmoneyOrder()">
                            <i class="fa fa-credit-card"></i> Proceed to Payment
                        </button>
                        <button class="btn btn-secondary" type="button" onclick="window.history.back()">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card" id="payment-details" style="display: none;">
                <div class="card-header">
                    <h6 class="card-title mb-0">Payment Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Transaction ID:</strong> <span id="transaction-id"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Amount:</strong> ₹<span id="transaction-amount"></span>
                            </div>
                            <div class="mb-3" id="expiry-section" style="display: none;">
                                <strong>Expires At:</strong> <span id="expiry-time" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong> <span id="payment-status" class="text-warning">Checking...</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- QR Code Section -->
                            <div id="qr-section" style="display: none;">
                                <div class="text-center mb-3">
                                    <h6>Scan QR Code to Pay</h6>
                                    <img id="qr-code" src="" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UPI Intent Link Section -->
                    <div id="upi-section" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="fa fa-mobile"></i> UPI Payment Link</h6>
                            <p class="mb-2">Click the link below to open in your UPI app:</p>
                            <div class="d-flex align-items-center">
                                <a id="upi-intent-link" href="#" class="btn btn-success btn-sm me-2">
                                    <i class="fa fa-external-link"></i> Open in UPI App
                                </a>
                                <button class="btn btn-outline-secondary btn-sm" onclick="copyUpiLink()">
                                    <i class="fa fa-copy"></i> Copy Link
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                UPI Link: <span id="upi-intent-text" class="text-break"></span>
                            </small>
                        </div>
                    </div>

                    <!-- Success Details -->
                    <div id="success-details" style="display: none;" class="alert alert-success">
                        <h6><i class="fa fa-check-circle"></i> Payment Successful!</h6>
                        <p><strong>UTR Number:</strong> <span id="utr-number"></span></p>
                        <p class="mb-0">Redirecting to dashboard in 3 seconds...</p>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-secondary" onclick="resetPaymentForm()">
                            <i class="fa fa-arrow-left"></i> Back to Payment Form
                        </button>
                        <button class="btn btn-info" onclick="location.reload()">
                            <i class="fa fa-refresh"></i> Refresh Page
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Panel -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Payment Instructions</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fa fa-qrcode text-primary"></i> Using QR Code</h6>
                        <p class="small text-muted">1. Open any UPI app (PhonePe, GPay, Paytm, etc.)<br>2. Scan the QR code<br>3. Complete the payment</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="fa fa-mobile text-success"></i> Using UPI Link</h6>
                        <p class="small text-muted">1. Click on "Open in UPI App" button<br>2. Choose your preferred UPI app<br>3. Complete the payment</p>
                    </div>
                    <div class="alert alert-warning">
                        <small><strong>Note:</strong> Payment will be automatically verified. Please don't refresh or close this page during payment.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loader -->
<div class="loader" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<style>
.loader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
}
</style>

@endsection