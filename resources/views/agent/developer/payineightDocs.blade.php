@extends('agent.layout.header')
@section('content')

    <div class="main-content-body">
        <div class="row row-sm">

            @include('agent.developer.left_side')

            <div class="col-lg-8 col-xl-9">

                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Create Order</h6>
                        </div>
                        <hr>

                        <table class="table main-table-reference mt-0 mb-0">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                            <tr>
                                <td><code>api_token</code></td>
                                <td>string</td>
                                <td>The API token for authenticating the request (sent in Authorization header as Bearer token).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>amount</code></td>
                                <td>numeric</td>
                                <td>The order amount, which must fall between the minimum and maximum allowed amounts (Min: ₹10, Max: ₹20,000).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>client_id</code></td>
                                <td>string</td>
                                <td>A unique identifier for the client making the request.</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>callback_url</code></td>
                                <td>string</td>
                                <td>The URL to call after order processing is complete, typically used for order status updates.</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>customer_name</code></td>
                                <td>string</td>
                                <td>The name of the customer initiating the transaction (max 255 characters).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>mobile_number</code></td>
                                <td>string</td>
                                <td>The mobile number of the customer (exactly 10 digits).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>email</code></td>
                                <td>string</td>
                                <td>The email address of the customer (max 255 characters).</td>
                                <td>Yes</td>
                            </tr>
                        </table>

                    </div>
                    <div class="card-footer">
                        <pre>POST: {{url('api/add-money/v8/createOrder')}}</pre>
                        <hr>
                        <pre style="color: #0ba360;">Success Response : {
    "status": "success",
    "message": "Transaction initiated successfully",
    "data": {
        "paymentLink": "https://edge.lightspeedpay.in/payment/xyz123",
        "txnid": "12345",
        "provider_id": "lsp_abc123"
    }
}</pre>
                        <hr>
                        <pre style="color: #dc3545;">Error Response : {
    "status": "failure",
    "message": "Amount should be between 10 and 20000"
}</pre>
                    </div>
                </div>

                <hr>
                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Create UPI Intent Order</h6>
                        </div>
                        <hr>

                        <table class="table main-table-reference mt-0 mb-0">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                            <tr>
                                <td><code>api_token</code></td>
                                <td>string</td>
                                <td>The API token for authentication (Bearer token in Authorization header).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>amount</code></td>
                                <td>numeric</td>
                                <td>The order amount (Min: ₹10, Max: ₹20,000).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>client_id</code></td>
                                <td>string</td>
                                <td>Unique identifier for the transaction.</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>callback_url</code></td>
                                <td>string</td>
                                <td>URL for transaction status callbacks.</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>customer_name</code></td>
                                <td>string</td>
                                <td>Customer's full name (max 255 characters).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>mobile_number</code></td>
                                <td>string</td>
                                <td>10-digit mobile number.</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>email</code></td>
                                <td>string</td>
                                <td>Valid email address (max 255 characters).</td>
                                <td>Yes</td>
                            </tr>
                        </table>

                    </div>
                    <div class="card-footer">
                        <pre>POST: {{url('api/add-money/v8/createOrderUpiIntent')}}</pre>
                        <hr>
                        <pre style="color: #0ba360;">Success Response : {
    "status": "success",
    "code": 200,
    "message": "UPI Intent created successfully",
    "data": {
        "upi_intent": "upi://pay?pa=merchant@ybl&pn=MerchantName&am=100.00&tid=12345&tr=TXN12345",
        "deeplink": "upi://pay?pa=merchant@ybl&pn=MerchantName&am=100.00&tid=12345&tr=TXN12345",
        "txnid": "12345",
        "client_id": "your_client_id",
        "transaction_id": "lsp_txn_abc123",
        "amount": 100,
        "qr_string": "upi://pay?pa=merchant@ybl&pn=MerchantName&am=100.00&tid=12345&tr=TXN12345"
    }
}</pre>
                    </div>
                </div>

                <hr>
                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Generate UPI QR Code</h6>
                        </div>
                        <hr>

                        <table class="table main-table-reference mt-0 mb-0">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                            <tr>
                                <td><code>api_token</code></td>
                                <td>string</td>
                                <td>Authentication token (Bearer token).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>client_id</code></td>
                                <td>string</td>
                                <td>The client ID from UPI Intent order creation.</td>
                                <td>Yes</td>
                            </tr>
                        </table>

                    </div>
                    <div class="card-footer">
                        <pre>POST: {{url('api/add-money/v8/generateUpiQr')}}</pre>
                        <hr>
                        <pre style="color: #0ba360;">Success Response : {
    "status": "success",
    "message": "Use the deeplink from create order response to generate QR code",
    "data": {
        "client_id": "your_client_id",
        "txnid": "12345",
        "note": "Generate QR code using the deeplink received in createOrderUpiIntent response"
    }
}</pre>
                    </div>
                </div>

                <hr>
                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Check UPI Intent Status</h6>
                        </div>
                        <hr>

                        <table class="table main-table-reference mt-0 mb-0">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                            <tr>
                                <td><code>api_token</code></td>
                                <td>string</td>
                                <td>Authentication token (Bearer token).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>client_id</code></td>
                                <td>string</td>
                                <td>The client ID of the UPI Intent transaction.</td>
                                <td>Yes</td>
                            </tr>
                        </table>

                    </div>
                    <div class="card-footer">
                        <pre>POST: {{url('api/add-money/v8/checkUpiStatus')}}</pre>
                        <hr>
                        <pre style="color: #0ba360;">Success Response : {
    "status": "success",
    "message": "Transaction status retrieved successfully",
    "data": {
        "client_id": "your_client_id",
        "txnid": "12345",
        "amount": 100,
        "status": "success",
        "created_at": "2024-01-15 10:30:00",
        "utr": "123456789012",
        "report_id": "67890"
    }
}</pre>
                    </div>
                </div>

                <hr>
                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Status Enquiry</h6>
                        </div>
                        <hr>

                        <table class="table main-table-reference mt-0 mb-0">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                            <tr>
                                <td><code>api_token</code></td>
                                <td>string</td>
                                <td>Your API token for authentication (Bearer token).</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td><code>client_id</code></td>
                                <td>string</td>
                                <td>The unique client ID for the transaction.</td>
                                <td>Yes</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pre>POST: {{url('api/add-money/v8/status-enquiry')}}</pre>
                        <hr>
                        <pre style="color: #0ba360;">Success Response : {
    "status": true,
    "message": "Transaction record found successfully!",
    "data": {
        "client_id": "your_client_id",
        "report_id": "67890",
        "amount": 100,
        "utr": "123456789012",
        "status": "credit"
    }
}</pre>
                    </div>
                </div>

                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Callback Request</h6>
                        </div>
                        <hr>

                        <p>This callback notifies your system when a transaction is successfully processed. It includes key transaction data and a secure signature for verification.</p>
                        <table class="table main-table-reference mt-0 mb-0">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td>status</td>
                                <td>string</td>
                                <td>Yes</td>
                                <td>Transaction status ("credit" for successful transactions)</td>
                            </tr>
                            <tr>
                                <td>client_id</td>
                                <td>string</td>
                                <td>Yes</td>
                                <td>Your unique transaction identifier</td>
                            </tr>
                            <tr>
                                <td>amount</td>
                                <td>numeric</td>
                                <td>Yes</td>
                                <td>Transaction amount</td>
                            </tr>
                            <tr>
                                <td>utr</td>
                                <td>string</td>
                                <td>Yes</td>
                                <td>Bank UTR (Unique Transaction Reference) number</td>
                            </tr>
                            <tr>
                                <td>txnid</td>
                                <td>string</td>
                                <td>Yes</td>
                                <td>Our internal transaction ID</td>
                            </tr>
                            <tr>
                                <td>signature</td>
                                <td>string</td>
                                <td>Yes</td>
                                <td>HMAC-SHA256 signature for security verification</td>
                            </tr>
                        </table>

                        <br>
                        <h4>Signature Verification</h4>
                        <p>To verify the callback authenticity, compute the HMAC-SHA256 signature using your API Token as the secret key.</p>
                        <h5>Signature Verification Logic (PHP example):</h5>
                        <pre style="background:#1e1e1e; color:#d4d4d4; padding:1em; border-radius:8px; font-family:monospace; overflow:auto;">
<span style="color:#9cdcfe;">$receivedParams</span> = [
    <span style="color:#ce9178;">'status'</span> => <span style="color:#ce9178;">'credit'</span>,
    <span style="color:#ce9178;">'client_id'</span> => <span style="color:#ce9178;">'your_client_id'</span>,
    <span style="color:#ce9178;">'amount'</span> => <span style="color:#ce9178;">'100.00'</span>,
    <span style="color:#ce9178;">'utr'</span> => <span style="color:#ce9178;">'1234567890'</span>,
    <span style="color:#ce9178;">'txnid'</span> => <span style="color:#ce9178;">'TXN123456'</span>,
];

<span style="color:#9cdcfe;">$receivedSignature</span> = <span style="color:#9cdcfe;">$_GET</span>[<span style="color:#ce9178;">'signature'</span>];

<span style="color:#9cdcfe;">$signatureString</span> = <span style="color:#dcdcaa;">http_build_query</span>(<span style="color:#9cdcfe;">$receivedParams</span>);
<span style="color:#9cdcfe;">$calculatedSignature</span> = <span style="color:#dcdcaa;">hash_hmac</span>(<span style="color:#ce9178;">'sha256'</span>, <span style="color:#9cdcfe;">$signatureString</span>, <span style="color:#ce9178;">'your_api_token'</span>);

<span style="color:#c586c0;">if</span> (<span style="color:#dcdcaa;">hash_equals</span>(<span style="color:#9cdcfe;">$calculatedSignature</span>, <span style="color:#9cdcfe;">$receivedSignature</span>)) {
    <span style="color:#6a9955;">// Signature is valid - process the callback</span>
    <span style="color:#dcdcaa;">echo</span> <span style="color:#ce9178;">"SUCCESS"</span>;
} <span style="color:#c586c0;">else</span> {
    <span style="color:#6a9955;">// Invalid signature - reject the callback</span>
    <span style="color:#dcdcaa;">echo</span> <span style="color:#ce9178;">"INVALID SIGNATURE"</span>;
}
</pre>

                    </div>

                    <div class="card-footer">
                        <pre>GET: https://your-website.com/callback-url?status=credit&client_id=xyz&amount=100&utr=123&txnid=456&signature=abcd</pre>
                    </div>
                </div>

                <hr>
                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Authentication</h6>
                        </div>
                        <hr>
                        <p>All API requests must include your API token in the Authorization header:</p>
                        <pre style="background:#f8f9fa; padding:1em; border-radius:5px;">Authorization: Bearer YOUR_API_TOKEN</pre>
                        <p>Alternatively, you can send the token as a POST parameter:</p>
                        <pre style="background:#f8f9fa; padding:1em; border-radius:5px;">api_token: YOUR_API_TOKEN</pre>
                    </div>
                </div>

                <hr>
                <div class="card" id="basic-alert">
                    <div class="card-body">
                        <div>
                            <h6 class="card-title mb-1">Features & Integration Benefits</h6>
                        </div>
                        <hr>

                        <p><strong>Payin 8</strong> provides advanced payment gateway integration with comprehensive UPI support:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Payment Methods:</strong></h6>
                                <ul>
                                    <li>💳 <strong>Regular Payment Link</strong> - Web-based payment</li>
                                    <li>📱 <strong>UPI Intent</strong> - Direct UPI app integration</li>
                                    <li>🔗 <strong>Deep Linking</strong> - Seamless app-to-app payments</li>
                                    <li>📊 <strong>QR Code</strong> - Scan and pay functionality</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Key Features:</strong></h6>
                                <ul>
                                    <li>✅ Real-time transaction processing</li>
                                    <li>✅ Secure HMAC-SHA256 signatures</li>
                                    <li>✅ Instant webhook callbacks</li>
                                    <li>✅ Comprehensive status tracking</li>
                                    <li>✅ Multi-format UPI support</li>
                                    <li>✅ Robust error handling</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>💡 Integration Tip:</strong> Use UPI Intent for mobile applications to provide seamless payment experience. The deeplink allows users to pay directly through their preferred UPI app without manual entry.
                        </div>

                        <div class="alert alert-warning mt-3">
                            <strong>⚠️ Important:</strong> Always verify callback signatures to ensure transaction authenticity. Store your API token securely and never expose it in client-side code.
                        </div>
                    </div>
                </div>

            </div>
            <!--/div-->

        </div>
    </div>

@endsection