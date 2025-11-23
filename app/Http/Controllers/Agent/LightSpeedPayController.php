<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Library\BasicLibrary;
use App\Library\GetcommissionLibrary;
use App\Library\Commission_increment;
use App\Models\Apiresponse;
use App\Models\Balance;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gatewayorder;
use App\Models\Report;
use App\Models\User;
use App\Models\Traceurl;
use QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Helpers;
use Validator;


class LightSpeedPayController extends Controller
{
    private $provider_id;
    private $api_id;
    private $api_key;
    private $api_secret_key;
    private $bearer_token;

    public function __construct()
    {
        $this->provider_id = 339;
        $this->api_id = 15;
        $this->api_key = "0f3500cd-c14b-4a94-a71b-1ed47380c4c0";
        $this->api_secret_key = "lGFWxPl2Dp03VD7eiKUAkk5NvE2naKTT";
        $this->bearer_token = "l67IeAdk0aIx0KcUCeEVgBSn4S2QDwA4MRJNvsVhdpHJCfvvXZxWVNwlb84I";

        $providers = Provider::find($this->provider_id);
        $this->min_amount = (isset($providers->min_amount)) ? $providers->min_amount : 10;
        $this->max_amount = (isset($providers->max_amount)) ? $providers->max_amount : 20000;
    }

    /**
     * Validate API Token for API requests
     */
    private function validateApiToken(Request $request)
    {
        $api_token = $request->header('Authorization') ?? $request->input('api_token');

        // Remove 'Bearer ' prefix if present
        if (strpos($api_token, 'Bearer ') === 0) {
            $api_token = substr($api_token, 7);
        }

        if (empty($api_token)) {
            return [
                'status' => false,
                'message' => 'API token is required',
                'user' => null
            ];
        }

        $user = User::where('api_token', $api_token)->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Invalid API token',
                'user' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Valid token',
            'user' => $user
        ];
    }

    public function welcome()
    {
        $user_id = Auth::id();
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);

        if ($activeService['status_id'] == 1) {
            return view('agent.add-money.lightspeedpay', ['page_title' => 'Add Money - Payin 8']);
        } else {
            return redirect()->back();
        }
    }

    // LightspeedPay Web Order
    function createOrderWeb(Request $request)
    {
        $rules = array(
            'amount' => 'required|numeric|min:1',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $amount = $request->amount;
        $user_id = Auth::id();
        $mode = 'WEB';
        $callback_url = ''; // not required for WEB
        $client_id = '';
        $name = Auth::user()->name;
        $email = Auth::user()->email;
        $mobile = Auth::user()->mobile;

        return Self::createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $name, $email, $mobile);
    }

    // LightspeedPay API Order - NOW WITH PROPER API TOKEN VALIDATION
    function createOrderApi(Request $request)
    {
        // First validate API token
        $tokenValidation = $this->validateApiToken($request);
        if (!$tokenValidation['status']) {
            return response()->json([
                'status' => 'failure',
                'message' => $tokenValidation['message']
            ], 401);
        }

        $authenticatedUser = $tokenValidation['user'];

        $rules = array(
            'amount' => 'required|numeric|min:1',
            'client_id' => 'required',
            'callback_url' => 'required|url',
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|digits:10',
            'email' => 'required|email|max:255',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $amount = $request->amount;
        $mode = 'API';
        $user_id = $authenticatedUser->id; // Use authenticated user's ID
        $callback_url = $request->callback_url;
        $client_id = $request->client_id;
        $customer_name = $request->customer_name;
        $mobile_number = $request->mobile_number;
        $email = $request->email;

        return Self::createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $customer_name, $email, $mobile_number);
    }

    // Shared Middle Function (LightspeedPay)
    function createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $name, $email, $mobile)
    {
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);
        $serviceStatus = $activeService['status_id'];

        if ($serviceStatus != 1) {
            return response()->json(['status' => 'failure', 'message' => 'Service not active!']);
        }

        // Validate amount limits
        if ($amount < $this->min_amount || $amount > $this->max_amount) {
            return response()->json([
                'status' => 'failure',
                'message' => "Amount should be between {$this->min_amount} and {$this->max_amount}"
            ]);
        }

        $now = new \DateTime();
        $created_at = $now->format('Y-m-d H:i:s');

        // Get user email for database (for web mode, use Auth, for API mode get from user_id)
        $userEmail = ($mode === 'WEB') ? Auth::user()->email : User::find($user_id)->email;

        // Save order in DB
        $orderId = Gatewayorder::insertGetId([
            'user_id'     => $user_id,
            'purpose'     => 'Add Money',
            'amount'      => $amount,
            'email'       => $userEmail,
            'ip_address'  => request()->ip(),
            'created_at'  => $created_at,
            'status_id'   => 3, // Pending
            'callback_url' => $callback_url,
            'client_id'   => $client_id,
            'mode'        => $mode,
        ]);

        // Prepare request payload
        $url = "https://edge.lightspeedpay.in/api/v1/transaction/initiate-transaction";
        $parameters = [
            "apiKey"        => $this->api_key,
            "apiSecret"     => $this->api_secret_key,
            "amount"        => (float) $amount,
            "billId"        => (string) $orderId,
            "customerName"  => $name,
            "description"   => "Add Money Transaction",
            "mobileNumber"  => $mobile,
            "email"         => $email,
        ];

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Accept"       => "application/json"
            ])->post($url, $parameters);

            $res = $response->json();

            if (isset($res['status']) && $res['status'] === "success") {
                $paymentLink = $res['paymentLink'] ?? null;
                // save deeplink securely
                Gatewayorder::where('id', $orderId)->update([
                    'confirmation_url' => $paymentLink
                ]);

                $confirmationUrl = route('payment.confirmation', ['orderId' => $orderId]);

                $data = [
                    'paymentLink' => $confirmationUrl,
                    'txnid'       => $orderId,
                    'provider_id' => $res['data']['_id'] ?? null,
                ];

                return response()->json([
                    'status'  => 'success',
                    'message' => $res['message'] ?? '',
                    'data'    => $data
                ]);
            }

            return response()->json([
                'status'  => 'failure',
                'message' => $res['message'] ?? 'Transaction initiation failed',
                'raw'     => $res
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'failure',
                'message' => 'API call failed: ' . $e->getMessage()
            ]);
        }
    }

    function callbackUrl(Request $request)
    {
        Apiresponse::insertGetId([
            'message'     => $request->getContent(),
            'api_type'    => 1,
            'created_at'  => now(),
            'ip_address'  => request()->ip()
        ]);

        $authHeader = $request->header('Authorization');
        if (!$authHeader || $authHeader !== 'Bearer ' . $this->bearer_token) {
            return response()->json(['status' => 'failure', 'message' => 'Unauthorized'], 401);
        }

        $data = $request->json()->all();
        $status        = $data['status']        ?? null; // COMPLETED or FAILED
        $transactionId = $data['transactionId'] ?? null;
        $amount        = $data['amount']        ?? null;
        $clientId      = $data['billId']        ?? null;
        $utr           = $data['bank_ref_num']  ?? null;

        if (!$status || !$transactionId || !$clientId) {
            return response()->json(['status' => 'failure', 'message' => 'Invalid payload'], 400);
        }

        if ($status === 'COMPLETED') {
            $gatewayorder = Gatewayorder::where('client_id', $clientId)
                ->where('status_id', 3) // pending
                ->first();

            if (!$gatewayorder) {
                return response()->json(['status' => false, 'message' => 'Order not found or already processed']);
            }

            // prevent duplicates
            if (Report::where('txnid', $utr)->exists()) {
                return response()->json(['status' => false, 'message' => 'Duplicate transaction']);
            }

            $user = User::find($gatewayorder->user_id);
            $opening_balance = $user->balance->aeps_balance;

            $library    = new GetcommissionLibrary();
            $commission = $library->get_commission($user->scheme_id, $this->provider_id, $amount);
            $retailer   = $commission['retailer'];
            $incrementAmount = $amount - $retailer;

            Balance::where('user_id', $user->id)->increment('aeps_balance', $incrementAmount);
            $aeps_balance = Balance::where('user_id', $user->id)->value('aeps_balance');

            $reportId = Report::insertGetId([
                'number'          => $user->mobile,
                'provider_id'     => $this->provider_id,
                'amount'          => $amount,
                'api_id'          => $this->api_id,
                'status_id'       => 6, // success
                'created_at'      => now(),
                'user_id'         => $user->id,
                'profit'          => '-' . $retailer,
                'mode'            => $gatewayorder->mode,
                'txnid'           => $utr,
                'ip_address'      => $gatewayorder->ip_address,
                'description'     => 'Add Money',
                'opening_balance' => $opening_balance,
                'total_balance'   => $aeps_balance,
                'credit_by'       => $user->id,
                'wallet_type'     => 2,
                'client_id'       => $clientId,
            ]);

            Gatewayorder::where('id', $gatewayorder->id)
                ->update(['status_id' => 1, 'report_id' => $reportId]);

            if (!empty($gatewayorder->callback_url)) {
                $queryParams = [
                    'status'    => 'credit',
                    'client_id' => $clientId,
                    'amount'    => $amount,
                    'utr'       => $utr,
                    'txnid'     => $gatewayorder->id,
                ];

                $signatureString = http_build_query($queryParams);
                $signature = hash_hmac('sha256', $signatureString, $user->api_token);
                $queryParams['signature'] = $signature;

                $url = $gatewayorder->callback_url . '?' . http_build_query($queryParams);

                $response = Http::timeout(30)->withoutVerifying()->get($url);

                Traceurl::insertGetId([
                    'user_id'          => $user->id,
                    'url'              => $url,
                    'number'           => $user->mobile,
                    'response_message' => $response->body(),
                    'created_at'       => now(),
                ]);
            }

            return ['status' => 'success', 'message' => 'Transaction successful'];
        }

        if ($status === 'FAILED') {
            Gatewayorder::where('client_id', $clientId)->update(['status_id' => 2]);
            return ['status' => 'failure', 'message' => 'Transaction failed'];
        }

        return response()->json(['status' => 'failure', 'message' => 'Invalid status']);
    }

    // Status Enquiry API - NOW WITH PROPER API TOKEN VALIDATION
    function statusEnquiryApi(Request $request)
    {
        // First validate API token
        $tokenValidation = $this->validateApiToken($request);
        if (!$tokenValidation['status']) {
            return response()->json([
                'status' => 'failure',
                'message' => $tokenValidation['message']
            ], 401);
        }

        $authenticatedUser = $tokenValidation['user'];

        $rules = array(
            'client_id' => 'required|exists:gatewayorders,client_id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $client_id = $request->client_id;
        $user_id = $authenticatedUser->id; // Use authenticated user's ID

        // Only allow users to query their own transactions
        $gatewayorders = Gatewayorder::where('client_id', $client_id)
            ->where('user_id', $user_id)
            ->orderBy('id', 'DESC')
            ->first();

        if ($gatewayorders) {
            $report_id = $gatewayorders->report_id;
            $reports = Report::find($report_id);
            if ($reports) {
                $data = [
                    'client_id' => $client_id,
                    'report_id' => $report_id,
                    'amount' => $reports->amount,
                    'utr' => $reports->txnid,
                    'status' => 'credit',
                ];
                return Response()->json(['status' => true, 'message' => 'Transaction record found successfully!', 'data' => $data]);
            } else {
                return Response()->json(['status' => false, 'message' => 'No matching report found!']);
            }
        } else {
            return Response()->json(['status' => false, 'message' => 'No matching report found or access denied!']);
        }
    }

    public function showSuccessScreen()
    {
        return view('agent.paymentresult', ['status' => 'success']);
    }

    public function showFailureScreen()
    {
        return view('agent.paymentresult', ['status' => 'failure']);
    }

    /**
     * UPI Intent API Order - NOW WITH PROPER API TOKEN VALIDATION
     */
    function createOrderUpiIntent(Request $request)
    {
        // First validate API token
        $tokenValidation = $this->validateApiToken($request);
        if (!$tokenValidation['status']) {
            return response()->json([
                'status' => 'failure',
                'message' => $tokenValidation['message']
            ], 401);
        }

        $authenticatedUser = $tokenValidation['user'];

        $rules = array(
            'amount' => 'required|numeric|min:1',
            'client_id' => 'required',
            'callback_url' => 'required|url',
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|digits:10',
            'email' => 'required|email|max:255',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $amount = $request->amount;
        $mode = 'UPI_INTENT';
        $user_id = $authenticatedUser->id; // Use authenticated user's ID
        $callback_url = $request->callback_url;
        $client_id = $request->client_id;
        $customer_name = $request->customer_name;
        $mobile_number = $request->mobile_number;
        $email = $request->email;

        return Self::createOrderUpiIntentMiddle($amount, $user_id, $mode, $callback_url, $client_id, $customer_name, $email, $mobile_number);
    }

    /**
     * Shared Middle Function for UPI Intent (LightspeedPay)
     */
    function createOrderUpiIntentMiddle($amount, $user_id, $mode, $callback_url, $client_id, $name, $email, $mobile)
    {
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);
        $serviceStatus = $activeService['status_id'];

        if ($serviceStatus != 1) {
            return response()->json(['status' => 'failure', 'message' => 'Service not active!']);
        }

        // Validate amount limits
        if ($amount < $this->min_amount || $amount > $this->max_amount) {
            return response()->json([
                'status' => 'failure',
                'message' => "Amount should be between {$this->min_amount} and {$this->max_amount}"
            ]);
        }

        $now = new \DateTime();
        $created_at = $now->format('Y-m-d H:i:s');

        // Get user email from DB
        $userEmail = User::find($user_id)->email ?? $email;

        // Save order in DB
        $orderId = Gatewayorder::insertGetId([
            'user_id'     => $user_id,
            'purpose'     => 'Add Money UPI Intent',
            'amount'      => $amount,
            'email'       => $userEmail,
            'ip_address'  => request()->ip(),
            'created_at'  => $created_at,
            'status_id'   => 3, // Pending
            'callback_url' => $callback_url,
            'client_id'   => $client_id,
            'mode'        => $mode,
        ]);

        // Prepare request payload for UPI Intent
        $url = "https://edge.lightspeedpay.in/api/v1/transaction/deeplink-transaction";
        $parameters = [
            "apiKey"       => $this->api_key,
            "apiSecret"    => $this->api_secret_key,
            "amount"       => (float) $amount,
            "billId"       => (string) $orderId,
            "customerName" => $name,
            "description"  => "Add Money UPI Intent Transaction",
            "mobileNumber" => $mobile,
            "email"        => $email,
        ];

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Accept"       => "application/json"
            ])->post($url, $parameters);

            $res = $response->json();

            // Log response
            Log::info('UPI Intent API Response', [
                'order_id' => $orderId,
                'response' => $res
            ]);

            if (isset($res['status']) && $res['status'] === "success") {
                $result = $res['result'] ?? [];

                $deeplink = $result['deeplink'] ?? null;
                $transaction_id = $result['_id'] ?? null;
                $amount_response = $result['amount'] ?? $amount;

                if (!$deeplink) {
                    return response()->json([
                        'status'  => 'failure',
                        'message' => 'UPI deeplink not received from provider'
                    ]);
                }

                // save deeplink securely
                Gatewayorder::where('id', $orderId)->update([
                    'confirmation_url' => $deeplink
                ]);

                $confirmationUrl = route('payment.confirmation', ['orderId' => $orderId]);

                // Save the transaction info in the same Gatewayorder record if needed
                Gatewayorder::where('id', $orderId)->update([
                    'status_id' => 3 // still pending; update later on callback
                ]);

                $data = [
                    'upi_intent'     => $confirmationUrl,
                    'deeplink'       => $confirmationUrl,
                    'txnid'          => $orderId,
                    'client_id'      => $client_id,
                    'transaction_id' => $transaction_id,
                    'amount'         => $amount_response,
                    'qr_string'      => $deeplink,
                ];

                return response()->json([
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => $res['message'] ?? 'UPI Intent created successfully',
                    'data'    => $data
                ]);
            }

            return response()->json([
                'status'  => 'failure',
                'code'    => $res['code'] ?? 400,
                'message' => $res['message'] ?? 'UPI Intent creation failed',
                'raw'     => $res
            ]);
        } catch (\Exception $e) {
            Log::error('UPI Intent API Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => 'failure',
                'code'    => 500,
                'message' => 'API call failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate QR Code for UPI Intent - NOW WITH PROPER API TOKEN VALIDATION
     */
    function generateUpiQrCode(Request $request)
    {
        // First validate API token
        $tokenValidation = $this->validateApiToken($request);
        if (!$tokenValidation['status']) {
            return response()->json([
                'status' => 'failure',
                'message' => $tokenValidation['message']
            ], 401);
        }

        $authenticatedUser = $tokenValidation['user'];

        $rules = array(
            'client_id' => 'required|exists:gatewayorders,client_id',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $client_id = $request->client_id;
        $user_id = $authenticatedUser->id; // Use authenticated user's ID

        // Find the gateway order - only for authenticated user
        $gatewayorder = Gatewayorder::where('client_id', $client_id)
            ->where('user_id', $user_id)
            ->where('mode', 'UPI_INTENT')
            ->where('status_id', 3) // Pending
            ->first();

        if (!$gatewayorder) {
            return response()->json([
                'status' => 'failure',
                'message' => 'UPI Intent order not found, already processed, or access denied'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Use the deeplink from create order response to generate QR code',
            'data' => [
                'client_id' => $client_id,
                'txnid' => $gatewayorder->id,
                'note' => 'Generate QR code using the deeplink received in createOrderUpiIntent response'
            ]
        ]);
    }

    /**
     * Check UPI Intent Transaction Status - NOW WITH PROPER API TOKEN VALIDATION
     */
    function checkUpiIntentStatus(Request $request)
    {
        // First validate API token
        $tokenValidation = $this->validateApiToken($request);
        if (!$tokenValidation['status']) {
            return response()->json([
                'status' => 'failure',
                'message' => $tokenValidation['message']
            ], 401);
        }

        $authenticatedUser = $tokenValidation['user'];

        $rules = array(
            'client_id' => 'required|exists:gatewayorders,client_id',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $client_id = $request->client_id;
        $user_id = $authenticatedUser->id; // Use authenticated user's ID

        // Only allow users to query their own UPI Intent transactions
        $gatewayorder = Gatewayorder::where('client_id', $client_id)
            ->where('user_id', $user_id)
            ->where('mode', 'UPI_INTENT')
            ->first();

        if (!$gatewayorder) {
            return response()->json([
                'status' => 'failure',
                'message' => 'UPI Intent transaction not found or access denied'
            ]);
        }

        $status_map = [
            1 => 'success',
            2 => 'failed',
            3 => 'pending'
        ];

        $data = [
            'client_id' => $client_id,
            'txnid' => $gatewayorder->id,
            'amount' => $gatewayorder->amount,
            'status' => $status_map[$gatewayorder->status_id] ?? 'unknown',
            'created_at' => $gatewayorder->created_at,
        ];

        if ($gatewayorder->report_id) {
            $report = Report::find($gatewayorder->report_id);
            if ($report) {
                $data['utr'] = $report->txnid;
                $data['report_id'] = $report->id;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction status retrieved successfully',
            'data' => $data
        ]);
    }

    public function confirmationPage($orderId)
    {
        $order = Gatewayorder::findOrFail($orderId);

        return view('agent.paymentconfirmation', [
            'amount'      => $order->amount,
            'redirectUrl' => $order->confirmation_url, 
            'page_title' => 'Payment Confirmation'
        ]);
    }

}
