<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Str;
use App\Models\Gatewayorder;
use App\Models\Provider;
use App\Models\User;
use App\Models\Balance;
use App\Models\Report;
use App\Models\Sitesetting;
use App\Models\Api;
use App\Library\SmsLibrary;
use Helpers;
use App\Models\Apiresponse;
use App\Models\Traceurl;
use QrCode;

use App\Library\BasicLibrary;
use App\Library\GetcommissionLibrary;
use App\Library\RefundLibrary;
use App\Library\Commission_increment;

class FingoMoneyController extends Controller
{
    private $authToken;
    private $tokenExpiry;
    
    public function __construct()
    {
        $this->api_id = 16;
        $this->provider_id = 340;
        $providers = Provider::find($this->provider_id);
        $this->min_amount = (isset($providers->min_amount)) ? $providers->min_amount : 10;
        $this->max_amount = (isset($providers->max_amount)) ? $providers->max_amount : 2000;

        $credentials = json_decode(optional(Api::find($this->api_id))->credentials);
        $this->base_url = optional($credentials)->base_url ?? 'https://app.fingomoney.com//';
        $this->username = optional($credentials)->username ?? 'TP2482';
        $this->password = optional($credentials)->password ?? '4cq&!1zd';
    }

    function welcome()
    {
        $user_id = Auth::id();
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);
        $serviceStatus = $activeService['status_id'];
        if ($serviceStatus == 1) {
            $data = array('page_title' => 'Fingomoney Payin');
            return view('agent.add-money.fingomoney')->with($data);
        } else {
            return redirect()->back();
        }
    }

    private function getAuthToken()
    {
        // Check if token exists and is not expired
        if ($this->authToken && $this->tokenExpiry && now() < $this->tokenExpiry) {
            return $this->authToken;
        }

        $url = $this->base_url . 'payinapi/Auth/1.0/getAuthToken';
        $parameters = array(
            'username' => $this->username,
            'password' => $this->password,
        );
        $method = 'POST';
        $header = ["Content-Type: application/json", "Accept: application/json"];
        
        // Log the auth request
        Apiresponse::insertGetId([
            'message' => json_encode(['url' => $url, 'request' => $parameters]), 
            'api_type' => 2, 
            'created_at' => now(), 
            'ip_address' => request()->ip()
        ]);
        
        $response = Helpers::pay_curl_post($url, $header, json_encode($parameters), $method);
        
        // Log the auth response
        Apiresponse::insertGetId([
            'message' => 'Auth Response: ' . $response, 
            'api_type' => 2, 
            'created_at' => now(), 
            'ip_address' => request()->ip()
        ]);
        
        $res = json_decode($response, true);
        
        if (isset($res['token'])) {
            $this->authToken = $res['token'];
            $this->tokenExpiry = now()->addHours(24); // Token valid for 24 hours
            return $this->authToken;
        }
        
        return false;
    }

    function createOrderWeb(Request $request)
    {
        $rules = array(
            'amount' => 'required|numeric|between:' . $this->min_amount . ',' . $this->max_amount,
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }
        $amount = $request->amount;
        $user_id = Auth::id();
        $mode = 'WEB';
        $callback_url = '';
        $client_id = '';
        $name = Auth::User()->name;
        $email = Auth::User()->email;
        $mobile = Auth::User()->mobile;
        return Self::createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $name, $email, $mobile);
    }

    function createOrderApi(Request $request)
    {
        $rules = array(
            'amount' => 'required|numeric|between:' . $this->min_amount . ',' . $this->max_amount,
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
        $user_id = Auth::id();
        $callback_url = $request->callback_url;
        $client_id = $request->client_id;
        $customer_name = $request->customer_name;
        $mobile_number = $request->mobile_number;
        $email = $request->email;
        return Self::createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $customer_name, $email, $mobile_number);
    }

    function createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $name, $email, $mobile)
    {
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);
        $serviceStatus = $activeService['status_id'];
        if ($serviceStatus == 1) {
            // Get authentication token
            $token = $this->getAuthToken();
            if (!$token) {
                return Response()->json(['status' => 'failure', 'message' => 'Unable to authenticate with payment gateway']);
            }

            $now = new \DateTime();
            $created_at = $now->format('Y-m-d H:i:s');
            $orderId = Gatewayorder::insertGetId([
                'user_id' => $user_id,
                'purpose' => 'Add Money',
                'amount' => $amount,
                'email' => Auth::User()->email,
                'ip_address' => request()->ip(),
                'created_at' => $created_at,
                'status_id' => 3,
                'callback_url' => $callback_url,
                'client_id' => $client_id,
                'mode' => $mode,
            ]);

            // Generate merchant request ID (must start with merchant ID, 12-30 chars)
            $merchantId = 'TP8034'; // This should come from your merchant config
            $requestId = $merchantId . 'ord' . $orderId . time();

            $url = $this->base_url . 'payinapi/payin/1.0/registerIntent';
            $parameters = array(
                'requestid' => $requestId,
                'merchantid' => $merchantId,
                'amount' => (float)$amount,
                'remark' => 'Add Money Transaction',
                'customerInfo' => [
                    'deviceLocation' => 'Online',
                    'customerName' => $name,
                    'mobileNo' => $mobile
                ]
            );
            
            $method = 'POST';
            $header = [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer " . $token
            ];
            
            // Log the payin request
            Apiresponse::insertGetId([
                'message' => json_encode(['url' => $url, 'request' => $parameters]), 
                'api_type' => 3, 
                'created_at' => now(), 
                'ip_address' => request()->ip()
            ]);
            
            $response = Helpers::pay_curl_post($url, $header, json_encode($parameters), $method);
            
            // Log the payin response
            Apiresponse::insertGetId([
                'message' => 'Payin Response: ' . $response, 
                'api_type' => 3, 
                'created_at' => now(), 
                'ip_address' => request()->ip()
            ]);
            
            $res = json_decode($response, true);
            
            if (isset($res['reqStatus']['result']) && $res['reqStatus']['result'] == true) {
                $qrString = $res['upiData']['intent'] ?? '';
                
                // Update order with request ID for status tracking
                Gatewayorder::where('id', $orderId)->update(['gateway_txn_id' => $requestId]);
                
                if ($mode == 'API') {
                    $data = [
                        'qrString' => $qrString,
                        'txnid' => $orderId,
                        'expires_at' => $res['upiData']['expiryTs'] ?? '',
                    ];
                    return Response(['status' => 'success', 'message' => $res['reqStatus']['message'] ?? 'Intent created successfully', 'data' => $data]);
                }
                
                $qrCodeUrl = url('agent/add-money/fingomoney/view-qrcode') . '?upi_string=' . urlencode($qrString);
                $data = [
                    'qrCodeUrl' => $qrCodeUrl,
                    'qrString' => $qrString,
                    'txnid' => $orderId,
                    'expires_at' => $res['upiData']['expiryTs'] ?? '',
                ];
                return Response(['status' => 'success', 'message' => $res['reqStatus']['message'] ?? 'Intent created successfully', 'data' => $data]);
            } else {
                return Response()->json(['status' => 'failure', 'message' => $res['reqStatus']['message'] ?? 'Unable to create payment intent']);
            }
        } else {
            return Response()->json(['status' => 'failure', 'message' => 'Service not active!']);
        }
    }

    function viewQrcode(Request $request)
    {
        $upi_string = $request->upi_string;
        // Generate the QR code as an image
        return response(QrCode::size(300)->generate($upi_string), 200)
            ->header('Content-Type', 'image/svg+xml');
    }

    function callbackUrl(Request $request)
    {
        // Log incoming callback
        Apiresponse::insertGetId([
            'message' => 'Callback received: ' . $request->getContent(), 
            'api_type' => 1, 
            'created_at' => now(), 
            'ip_address' => request()->ip()
        ]);

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        // Since Fingomoney doesn't have direct callback in docs, 
        // we'll handle status checks via polling or webhook if configured
        // This is a placeholder for webhook handling if Fingomoney adds it later
        
        return response()->json(['status' => 'received'], 200);
    }

    function statusEnquiryApi(Request $request)
    {
        $rules = array(
            'client_id' => 'required|exists:gatewayorders,client_id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }
        
        $client_id = $request->client_id;
        $user_id = Auth::id();
        $gatewayorders = Gatewayorder::where('client_id', $client_id)->where('user_id', $user_id)->orderBy('id', 'DESC')->first();
        
        if ($gatewayorders) {
            // Get current status from Fingomoney API
            $status = $this->checkTransactionStatus($gatewayorders->gateway_txn_id, $gatewayorders->id);
            
            if ($status['success']) {
                if (isset($status['data'])) {
                    return Response()->json(['status' => true, 'message' => 'Transaction status retrieved successfully!', 'data' => $status['data']]);
                } else {
                    return Response()->json(['status' => false, 'message' => 'Transaction pending or not found']);
                }
            } else {
                return Response()->json(['status' => false, 'message' => $status['message']]);
            }
        } else {
            return Response()->json(['status' => false, 'message' => 'No matching order found!']);
        }
    }

    private function checkTransactionStatus($originalRequestId, $orderId)
    {
        // Get authentication token
        $token = $this->getAuthToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Unable to authenticate with payment gateway'];
        }

        // Generate new request ID for status check
        $merchantId = 'TP8034';
        $statusRequestId = $merchantId . 'sts' . $orderId . time();

        $url = $this->base_url . 'payinapi/payin/1.0/checkStatus';
        $parameters = array(
            'requestid' => $statusRequestId,
            'org_requestid' => $originalRequestId
        );
        
        $method = 'POST';
        $header = [
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Bearer " . $token
        ];
        
        // Log the status check request
        Apiresponse::insertGetId([
            'message' => json_encode(['url' => $url, 'request' => $parameters]), 
            'api_type' => 4, 
            'created_at' => now(), 
            'ip_address' => request()->ip()
        ]);
        
        $response = Helpers::pay_curl_post($url, $header, json_encode($parameters), $method);
        
        // Log the status check response
        Apiresponse::insertGetId([
            'message' => 'Status Response: ' . $response, 
            'api_type' => 4, 
            'created_at' => now(), 
            'ip_address' => request()->ip()
        ]);
        
        $res = json_decode($response, true);
        
        if (isset($res['transactionInfo']) && $res['transactionInfo']['isAvailable']) {
            $transactionInfo = $res['transactionInfo'];
            
            if ($transactionInfo['status'] == 'success') {
                // Process successful transaction
                $this->processSuccessfulTransaction($orderId, $transactionInfo);
                
                $data = [
                    'client_id' => Gatewayorder::find($orderId)->client_id,
                    'report_id' => Gatewayorder::find($orderId)->report_id,
                    'amount' => $transactionInfo['amountRecieved'],
                    'utr' => $transactionInfo['utrNo'],
                    'status' => 'credit',
                ];
                
                return ['success' => true, 'data' => $data];
            } elseif ($transactionInfo['status'] == 'failed') {
                // Update order status to failed
                Gatewayorder::where('id', $orderId)->update(['status_id' => 2]);
                return ['success' => false, 'message' => 'Transaction failed: ' . ($transactionInfo['bankMessage'] ?? 'Payment failed')];
            }
        }
        
        // Transaction still pending or not found
        return ['success' => false, 'message' => 'Transaction status pending'];
    }

    private function processSuccessfulTransaction($orderId, $transactionInfo)
    {
        $gatewayorders = Gatewayorder::where('id', $orderId)->where('status_id', 3)->first();
        if (!$gatewayorders) {
            return false; // Already processed or invalid order
        }

        $amount = $transactionInfo['amountRecieved'];
        $utr = $transactionInfo['utrNo'];
        
        // Check for duplicate transaction
        $reports = Report::where('txnid', $utr)->first();
        if ($reports) {
            return false; // Duplicate transaction
        }

        $user_id = $gatewayorders->user_id;
        $userDetails = User::find($user_id);
        $opening_balance = $userDetails->balance->aeps_balance;
        $provider_id = $this->provider_id;
        $scheme_id = $userDetails->scheme_id;
        
        // Calculate commission
        $library = new GetcommissionLibrary();
        $commission = $library->get_commission($scheme_id, $provider_id, $amount);
        $retailer = $commission['retailer'];
        $d = $commission['distributor'];
        $sd = $commission['sdistributor'];
        $st = $commission['sales_team'];
        $rf = $commission['referral'];
        
        $incrementAmount = $amount - $retailer;
        Balance::where('user_id', $user_id)->increment('aeps_balance', $incrementAmount);
        $balance = Balance::where('user_id', $user_id)->first();
        $aeps_balance = $balance->aeps_balance;
        
        $description = "Add Money";
        $now = new \DateTime();
        $ctime = $now->format('Y-m-d H:i:s');
        
        $insert_id = Report::insertGetId([
            'number' => $userDetails->mobile,
            'provider_id' => $provider_id,
            'amount' => $amount,
            'api_id' => $this->api_id,
            'status_id' => 6,
            'created_at' => $ctime,
            'user_id' => $user_id,
            'profit' => '-' . $retailer,
            'mode' => $gatewayorders->mode,
            'txnid' => $utr,
            'ip_address' => $gatewayorders->ip_address,
            'description' => $description,
            'opening_balance' => $opening_balance,
            'total_balance' => $aeps_balance,
            'credit_by' => $user_id,
            'wallet_type' => 2,
            'client_id' => $gatewayorders->client_id ?? '',
        ]);
        
        if ($gatewayorders->mode != 'API') {
            Report::where('id', $insert_id)->update(['client_id' => $insert_id]);
        }
        
        $number = $userDetails->mobile;
        $library = new Commission_increment();
        $library->parent_recharge_commission($user_id, $number, $insert_id, $provider_id, $amount, $this->api_id, $retailer, $d, $sd, $st, $rf);

        Gatewayorder::where('id', $orderId)->update(['status_id' => 1, 'report_id' => $insert_id]);
        
        // Handle callback URL if present
        if (!empty($gatewayorders->callback_url)) {
            $clientId = $gatewayorders->client_id;
            $apiToken = $userDetails->api_token;
            
            $queryParams = [
                'status' => 'credit',
                'client_id' => $clientId,
                'amount' => $amount,
                'utr' => $utr,
                'txnid' => $orderId,
            ];
            
            $signatureString = http_build_query($queryParams);
            $signature = hash_hmac('sha256', $signatureString, $apiToken);
            $queryParams['signature'] = $signature;
            
            $url = $gatewayorders->callback_url . '?' . http_build_query($queryParams);
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30,
            ]);
            $response = curl_exec($curl);
            $curlError = curl_error($curl);
            curl_close($curl);
            
            Traceurl::insertGetId([
                'user_id' => $user_id,
                'url' => $url,
                'number' => $userDetails->mobile,
                'response_message' => $curlError ?: $response,
                'created_at' => $ctime
            ]);
        }
        
        return true;
    }

    public function checkOrderStatus($txnId)
    {
        $gatewayOrder = Gatewayorder::find($txnId);
        if (!$gatewayOrder) {
            return response()->json(['status' => 'error', 'message' => 'Order not found']);
        }
        
        if ($gatewayOrder->status_id == 1 && $gatewayOrder->report_id) {
            $report = Report::find($gatewayOrder->report_id);
            return response()->json([
                'status' => 'success', 
                'data' => [
                    'utr' => $report->txnid,
                    'amount' => $report->amount
                ]
            ]);
        } elseif ($gatewayOrder->status_id == 2) {
            return response()->json(['status' => 'failed', 'message' => 'Payment failed']);
        }
        
        // Check with Fingomoney API for latest status
        $status = $this->checkTransactionStatus($gatewayOrder->gateway_txn_id, $txnId);
        
        if ($status['success'] && isset($status['data'])) {
            return response()->json(['status' => 'success', 'data' => $status['data']]);
        }
        
        return response()->json(['status' => 'pending']);
    }
}