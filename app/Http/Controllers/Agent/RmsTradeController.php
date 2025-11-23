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
use Validator;

class RmsTradeController extends Controller
{
    private $provider_id;
    private $api_id;
    private $api_token;

    public function __construct()
    {
        $this->provider_id = 338;
        $this->api_id = 14;
        $this->api_token = "EcUrXEl5EuZUrLfdbXHjMIRoyVsSWv0DxzDILT3axkOEUnUSOi3tpsX10vBX";

        $providers = Provider::find($this->provider_id);
        $this->min_amount = (isset($providers->min_amount)) ? $providers->min_amount : 10;
        $this->max_amount = (isset($providers->max_amount)) ? $providers->max_amount : 20000;
    }

    public function welcome()
    {
        $user_id = Auth::id();
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);

        if ($activeService['status_id'] == 1) {
            return view('agent.add-money.rmstrade', ['page_title' => 'Add Money - Payin 7']);
        } else {
            return redirect()->back();
        }
    }

    public function createOrderWeb(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|between:' . $this->min_amount . ',' . $this->max_amount,
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }
        $redirectUrl = 'https://mars-pay.in/agent/add-money/v7/welcome';
        $callbackUrl = 'https://mars-pay.in/call-back/rmstrade';

        return $this->createOrderMiddle(
            $request->amount,
            Auth::id(),
            'WEB',
            $callbackUrl,
            '',
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->mobile,
            $redirectUrl,
        );
    }

    public function createOrderApi(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|between:' . $this->min_amount . ',' . $this->max_amount,
            'client_id' => 'required',
            'callback_url' => 'required|url',
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|digits:10',
            'email' => 'required|email|max:255',
            'redirect_url' => 'required|url'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        return $this->createOrderMiddle(
            $request->amount,
            Auth::id(),
            'API',
            $request->callback_url,
            $request->client_id,
            $request->customer_name,
            $request->email,
            $request->mobile_number,
            $request->redirect_url
        );
    }

    private function createOrderMiddle($amount, $user_id, $mode, $callback_url, $client_id, $name, $email, $mobile, $redirect_url = '')
    {
        $library = new BasicLibrary();
        $activeService = $library->getActiveService($this->provider_id, $user_id);
        if ($activeService['status_id'] != 1) {
            return response()->json(['status' => 'failure', 'message' => 'Service not active!']);
        }

        $orderId = Gatewayorder::insertGetId([
            'user_id' => $user_id,
            'purpose' => 'Add Money',
            'amount' => $amount,
            'email' => $email,
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'status_id' => 3, // pending
            'api_id' => $this->api_id,
            'callback_url' => $callback_url,
            'client_id' => $client_id,
            'mode' => $mode,
        ]);

        $customOrderId = 'rmstrade' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        Gatewayorder::where('id', $orderId)->update(['client_id' => $customOrderId]);

        // Call RmsTrade API
        $response = Http::post("https://rmstrade.online/api/add-money/v2/upiIntent/generateQR", [
            "api_token"     => $this->api_token,
            "amount"        => $amount,
            "client_id"     => $customOrderId,
            "redirect_url"  => $redirect_url,
            "callback_url"  => $callback_url,
            "customer_name" => $name,
            "mobile_number" => $mobile,
            "email"         => $email,
        ]);

        if ($response->failed()) {
            return response()->json(['status' => 'failure', 'message' => 'Failed to connect to RmsTrade']);
        }

        $data = $response->json();

        if ($mode == 'API') {
            return response()->json(['status' => $data['status'], 'message' => $data['message'], 'data' => $data]);
        }

        return response()->json(['status' => 'success', 'message' => 'Order created successfully!', 'data' => $data]);
    }

    public function callbackUrl(Request $request)
    {
        Apiresponse::insertGetId([
            'message'    => json_encode($request->all()),
            'api_type'   => 1,
            'created_at' => now(),
            'ip_address' => request()->ip()
        ]);

        $data    = $request->all();
        $status  = $data['status'] ?? $data['Status'] ?? null;
        $orderid = $data['orderid'] ?? $data['OrderId'] ?? $data['payin_ref'] ?? null;
        $amount  = $data['amount'] ?? $data['Amount'] ?? null;
        $utr     = $data['utr'] ?? $data['rrn'] ?? $data['transactionId'] ?? null;

        Log::info('RmsTrade Callback Hit', compact('status', 'orderid', 'amount', 'utr'));

        if (!$status || !$orderid || !$amount || !$utr) {
            Log::warning('Missing required fields in callback', compact('data'));
            return response()->json(['status' => 'failure', 'message' => 'Missing required fields']);
        }

        return DB::transaction(function () use ($status, $orderid, $amount, $utr) {
            $gatewayorders = Gatewayorder::where('client_id', $orderid)
                ->whereIn('status_id', [3, 9]) // 3 = pending, 9 = processing
                ->lockForUpdate()
                ->first();

            if (!$gatewayorders) {
                Log::warning('Order not found or already processed', ['orderid' => $orderid]);
                return response()->json(['status' => 'failure', 'message' => 'Invalid or already processed']);
            }

            if (!empty($gatewayorders->report_id)) {
                Log::warning('Duplicate callback: report already exists', [
                    'orderid'   => $orderid,
                    'report_id' => $gatewayorders->report_id
                ]);
                return response()->json(['status' => false, 'message' => 'Transaction already processed']);
            }

            if (Report::where('txnid', $utr)->exists()) {
                Log::warning('Duplicate txn found', ['txnid' => $utr]);
                return response()->json(['status' => false, 'message' => 'Duplicate transaction']);
            }

            Gatewayorder::where('id', $gatewayorders->id)->update(['status_id' => 9]);

            if (strtolower($status) === 'success') {
                $user_id     = $gatewayorders->user_id;
                $userDetails = User::find($user_id);

                if (!$userDetails || !$userDetails->balance) {
                    Log::error('User or balance not found', ['user_id' => $user_id]);
                    return response()->json(['status' => false, 'message' => 'User or balance missing']);
                }

                $opening_balance = $userDetails->balance->aeps_balance;
                $provider_id     = $this->provider_id;
                $scheme_id       = $userDetails->scheme_id;

                try {
                    $library    = new GetcommissionLibrary();
                    $commission = $library->get_commission($scheme_id, $provider_id, $amount);

                    $retailer = $commission['retailer'] ?? 0;
                    $d        = $commission['distributor'] ?? 0;
                    $sd       = $commission['sdistributor'] ?? 0;
                    $st       = $commission['sales_team'] ?? 0;
                    $rf       = $commission['referral'] ?? 0;

                    $incrementAmount = $amount - $retailer;

                    Balance::where('user_id', $user_id)->increment('aeps_balance', $incrementAmount);
                    $aeps_balance = Balance::where('user_id', $user_id)->value('aeps_balance');

                    $description = "Add Money via RmsTrade";
                    $ctime       = now()->format('Y-m-d H:i:s');

                    $insert_id = Report::insertGetId([
                        'number'          => $userDetails->mobile,
                        'provider_id'     => $provider_id,
                        'amount'          => $amount,
                        'api_id'          => $this->api_id,
                        'status_id'       => 6,
                        'created_at'      => $ctime,
                        'user_id'         => $user_id,
                        'profit'          => '-' . $retailer,
                        'mode'            => $gatewayorders->mode,
                        'txnid'           => $utr,
                        'ip_address'      => $gatewayorders->ip_address,
                        'description'     => $description,
                        'opening_balance' => $opening_balance,
                        'total_balance'   => $aeps_balance,
                        'credit_by'       => $user_id,
                        'wallet_type'     => 2,
                        'client_id'       => $gatewayorders->client_id ?? '',
                    ]);

                    if ($gatewayorders->mode != 'API') {
                        Report::where('id', $insert_id)->update(['client_id' => $insert_id]);
                    }

                    Gatewayorder::where('id', $gatewayorders->id)->update([
                        'status_id' => 1, // success
                        'report_id' => $insert_id
                    ]);

                    (new Commission_increment())->parent_recharge_commission(
                        $user_id,
                        $userDetails->mobile,
                        $insert_id,
                        $provider_id,
                        $amount,
                        $this->api_id,
                        $retailer,
                        $d,
                        $sd,
                        $st,
                        $rf
                    );

                    if (!empty($gatewayorders->callback_url)) {
                        $queryParams = [
                            'status'    => 'credit',
                            'client_id' => $gatewayorders->client_id,
                            'amount'    => $amount,
                            'utr'       => $utr,
                            'txnid'     => $gatewayorders->id,
                        ];
                        $signature = hash_hmac('sha256', http_build_query($queryParams), $userDetails->api_token);
                        $queryParams['signature'] = $signature;

                        $url      = $gatewayorders->callback_url . '?' . http_build_query($queryParams);
                        $response = @file_get_contents($url);

                        Traceurl::insertGetId([
                            'user_id'         => $user_id,
                            'url'             => $url,
                            'number'          => $userDetails->mobile,
                            'response_message' => $response ?: 'No response',
                            'created_at'      => $ctime
                        ]);
                    }

                    Log::info('RmsTrade transaction processed successfully', ['report_id' => $insert_id]);
                    return response()->json(['status' => 'success', 'message' => 'Transaction successful']);
                } catch (\Throwable $e) {
                    Log::error('Processing failed', ['error' => $e->getMessage()]);
                    return response()->json(['status' => false, 'message' => 'Processing error']);
                }
            } elseif (in_array(strtolower($status), ['failed', 'failure'])) {
                Gatewayorder::where('id', $gatewayorders->id)->update(['status_id' => 2]); // failed
                Log::warning('Payment failed', ['orderid' => $orderid]);
                return response()->json(['status' => 'failure', 'message' => 'Payment failed']);
            }

            return response()->json(['status' => 'failure', 'message' => 'Invalid status']);
        });
    }

    public function statusEnquiryApi(Request $request)
    {
        $rules = [
            'client_id' => 'required|exists:gatewayorders,client_id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(['status' => 'failure', 'message' => $validator->messages()->first()]);
        }

        $client_id = $request->client_id;
        $user_id   = Auth::id();

        $gatewayorders = Gatewayorder::where('client_id', $client_id)
            ->where('user_id', $user_id)
            ->orderBy('id', 'DESC')
            ->first();

        if ($gatewayorders) {
            $report_id = $gatewayorders->report_id;

            if ($report_id) {
                $reports = Report::find($report_id);
                if ($reports) {
                    $data = [
                        'client_id' => $client_id,
                        'report_id' => $report_id,
                        'amount'    => $reports->amount,
                        'utr'       => $reports->txnid,
                        'status'    => 'credit',
                    ];
                    return Response()->json(['status' => true, 'message' => 'Transaction record found successfully!', 'data' => $data]);
                }
            }

            if ($gatewayorders->status_id == 1) {
                $data = [
                    'client_id' => $client_id,
                    'amount'    => $gatewayorders->amount,
                    'status'    => 'credit',
                ];
                return Response()->json(['status' => true, 'message' => 'Transaction successful!', 'data' => $data]);
            } elseif ($gatewayorders->status_id == 2) {
                $data = [
                    'client_id' => $client_id,
                    'amount'    => $gatewayorders->amount,
                    'status'    => 'failed',
                ];
                return Response()->json(['status' => false, 'message' => 'Transaction failed!', 'data' => $data]);
            } else {
                $data = [
                    'client_id' => $client_id,
                    'amount'    => $gatewayorders->amount,
                    'status'    => 'pending',
                ];
                return Response()->json(['status' => false, 'message' => 'Transaction pending!', 'data' => $data]);
            }
        } else {
            return Response()->json(['status' => false, 'message' => 'No matching record found!']);
        }
    }
}
