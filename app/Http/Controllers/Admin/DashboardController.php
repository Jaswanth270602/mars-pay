<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use App\Models\Report;
use App\Models\Provider;
use App\Models\Balance;
use App\Models\User;
use Helpers;
use App\Models\Loginlog;
use App\Models\Sitesetting;
use App\Models\Service;
use App\Models\Company;
use App\Library\SmsLibrary;
use App\Library\MemberLibrary;
use App\Library\BasicLibrary;
use Carbon\Carbon;



class DashboardController extends Controller
{


    public function __construct()
    {
        $this->company_id = Helpers::company_id()->id;
        $companies = Helpers::company_id();
        $this->company_id = $companies->id;
        $sitesettings = Sitesetting::where('company_id', $this->company_id)->first();
        $this->brand_name = (empty($sitesettings)) ? '' :  $sitesettings->brand_name;
        $this->backend_template_id = (empty($sitesettings)) ? 1 :  $sitesettings->backend_template_id;
        $api = Api::where('vender_id', 10)->first();
        $this->key = (empty($api)) ? '' :  'Bearer ' . $api->api_key;
        // get company details
        $companies = Company::find($this->company_id);
        $this->cdnLink = (empty($companies)) ? '' : $companies->cdn_link;
    }

    function dashboard()
    {
        if (Auth::User()->role_id <= 7) {
            $data = array(
                'page_title' => 'Dashboard',
                'urls' => url('admin/top-seller'),
            );
            if ($this->backend_template_id == 1) {
                return view('admin.dashboard')->with($data);
            } elseif ($this->backend_template_id == 2) {
                return view('themes2.admin.dashboard')->with($data);
            } elseif ($this->backend_template_id == 3) {
                return view('themes3.admin.dashboard')->with($data);
            } elseif ($this->backend_template_id == 4) {
                return view('themes4.admin.dashboard')->with($data);
            } else {
                return redirect()->back();
            }
        } else {
            return redirect('agent/dashboard');
        }
    }


    function dashboard_data_api(Request $request)
    {
        self::send_balance_alert();
        if (Auth::User()->role_id == 1) {
            $balace = $this->getApiBalance();
            $api_balance = $balace['normal_balance'];
            $aeps_api_balance = $balace['aeps_balance'];
        } else {
            $api_balance = 0;
            $aeps_api_balance = 0;
        }
        $role_id = Auth::User()->role_id;
        $company_id = Auth::User()->company_id;
        $user_id = Auth::id();
        $library = new MemberLibrary();
        $my_down_member = $library->my_down_member($role_id, $company_id, $user_id);
        $normal_sale = Report::whereIn('user_id', $my_down_member)->whereIn('status_id', [1, 3, 8])->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        $aeps_sale = Report::whereIn('user_id', $my_down_member)->whereIn('status_id', [6])->whereIn('provider_id', [316,317,318,319,320])->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        if (Auth::User()->role_id == 8 || Auth::User()->role_id == 9 || Auth::User()->role_id == 10) {
            $today_profit = Report::where('user_id', Auth::id())->whereIn('status_id', [1])->whereDate('created_at', '=', date('Y-m-d'))->sum('profit');
        } elseif (Auth::User()->role_id == 1) {
            $provider_id = Provider::whereIn('service_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 15])->get(['id']);
            $user_profit = Report::whereIn('user_id', $my_down_member)->whereIn('provider_id', $provider_id)->whereIn('status_id', [1])->whereDate('created_at', '=', date('Y-m-d'))->sum('profit');
            $distributor_profit = Report::whereIn('user_id', $my_down_member)->whereIn('provider_id', $provider_id)->whereIn('status_id', [1])->whereDate('created_at', '=', date('Y-m-d'))->sum('distributor_comm');
            $super_profit = Report::whereIn('user_id', $my_down_member)->whereIn('provider_id', $provider_id)->whereIn('status_id', [1])->whereDate('created_at', '=', date('Y-m-d'))->sum('super_distributor_comm');
            $api_profit = Report::whereIn('user_id', $my_down_member)->whereIn('provider_id', $provider_id)->whereIn('status_id', [1])->whereDate('created_at', '=', date('Y-m-d'))->sum('api_comm');
            $today_profit = $api_profit - $user_profit - $distributor_profit - $super_profit;
        } else {
            $today_profit = Report::where('user_id', Auth::id())->whereIn('status_id', [6])->whereDate('created_at', '=', date('Y-m-d'))->sum('profit');
        }
        $sales = array(
            'today_sale' => number_format($normal_sale, 2),
            'aeps_sale' => number_format($aeps_sale, 2),
            'today_profit' => number_format($today_profit, 2),
        );
        $balaces = array(
            'api_balance' => $api_balance,
            'aeps_api_balance' => $aeps_api_balance,
        );
        return Response()->json([
            'status' => 'success',
            'balance' => $balaces,
            'sales' => $sales,
        ]);
    }


    function getApiBalance()
    {
        return ['normal_balance' => 0, 'aeps_balance' => 0];
        $api = Api::where('company_id', Auth::User()->company_id)->where('vender_id', 10)->first();
        $key = 'Bearer ' . $api->api_key;
        $url = "https://api.pay2all.in/user";
        $api_request_parameters = array();
        $method = 'GET';
        $header = ["Accept:application/json", "Authorization:" . $key];
        $response = Helpers::pay_curl_post($url, $header, $api_request_parameters, $method);
        $res = json_decode($response);
        $normal_balance = (empty($res->balance)) ? 0 : $res->balance;
        $aeps_balance = (empty($res->aeps_balance)) ? 0 : $res->aeps_balance;
        return ['normal_balance' => $normal_balance, 'aeps_balance' => $aeps_balance];
    }

    function dashboard_chart_api(Request $request)
    {
        $datefrom = date('Y-m-d', time());
        $dateto = date('Y-m-d', time());
        $role_id = Auth::User()->role_id;
        $company_id = Auth::User()->company_id;
        $user_id = Auth::id();
        $library = new MemberLibrary();
        $my_down_member = $library->my_down_member($role_id, $company_id, $user_id);

        $reports = Report::whereIn('user_id', $my_down_member)
            ->where('status_id', 1)
            ->whereDate('created_at', '>=', $datefrom)
            ->whereDate('created_at', '<=', $dateto)
            ->groupBy('provider_id')
            ->selectRaw('provider_id, sum(amount) as op_amount, sum(profit) as op_profit')
            ->orderBy('provider_id', 'DESC')
            ->get();

        $response = array();
        foreach ($reports as $value) {
            $product = array();
            $product["amount"] = $value->op_amount;
            $product["provider_name"] = $value->provider->provider_name;
            array_push($response, $product);
        }
        return Response()->json(['status' => 'success', 'provider' => $response]);
    }

    function dashboard_details_api()
    {

        $role_id = Auth::User()->role_id;
        $company_id = Auth::User()->company_id;
        $user_id = Auth::id();
        $library = new MemberLibrary();
        $my_down_member = $library->my_down_member($role_id, $company_id, $user_id);
        $today_success = Report::whereIn('user_id', $my_down_member)->where('status_id', 1)->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        $today_failure = Report::whereIn('user_id', $my_down_member)->whereIn('status_id', [2, 5])->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        $today_pending = Report::whereIn('user_id', $my_down_member)->where('status_id', 3)->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        $today_refunded = Report::whereIn('user_id', $my_down_member)->where('status_id', 4)->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        $today_credit = Report::where('user_id', Auth::id())->where('status_id', 6)->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        $today_debit = Report::where('user_id', Auth::id())->where('status_id', 7)->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');

        $normal_distributed_balance = Balance::whereIn('user_id', $my_down_member)->whereNotIn('user_id', [Auth::id()])->sum('user_balance');
        $aeps_distributed_balance = Balance::whereIn('user_id', $my_down_member)->whereNotIn('user_id', [Auth::id()])->sum('aeps_balance');
        $total_members = User::whereIn('id', $my_down_member)->whereNotIn('id', [Auth::id()])->count();
        $total_suspended_users = User::whereIn('id', $my_down_member)->whereNotIn('active', [1])->count();
        $balances = array(
            'normal_distributed_balance' => number_format($normal_distributed_balance, 2),
            'aeps_distributed_balance' => number_format($aeps_distributed_balance, 2),
            'my_balances' => number_format(Auth::User()->balance->user_balance, 2),
            'dashboard_total_members' => $total_members,
            'dashboard_total_suspended_users' => $total_suspended_users,

        );

        $total_row = Report::whereIn('user_id', $my_down_member)->whereIn('status_id', [1, 2, 3, 5])->whereDate('created_at', '=', date('Y-m-d'))->sum('amount');
        if (empty($today_success && $total_row)) {
            $percentage = array(
                'success_percentage' => 0,
                'failure_percentage' => 0,
                'pending_percentage' => 0,
            );
        } else {
            $percentage = array(
                'success_percentage' => number_format(100 * $today_success / $total_row, 2) . '%',
                'failure_percentage' => number_format(100 * $today_failure / $total_row, 2) . '%',
                'pending_percentage' => number_format(100 * $today_pending / $total_row, 2) . '%',
            );
        }

        $sales_overview = array(
            'today_success' => number_format($today_success, 2),
            'today_failure' => number_format($today_failure, 2),
            'today_pending' => number_format($today_pending, 2),
            'today_refunded' => number_format($today_refunded, 2),
            'today_credit' => number_format($today_credit, 2),
            'today_debit' => number_format($today_debit, 2),
        );
        return Response()->json([
            'status' => 'success',
            'sales_overview' => $sales_overview,
            'percentage' => $percentage,
            'balances' => $balances,
        ]);
    }

    function activity_logs(Request $request)
    {
        if ($request->fromdate && $request->todate) {
            $fromdate = $request->fromdate;
            $todate = $request->todate;
        } else {
            $fromdate = date('Y-m-d', time());
            $todate = date('Y-m-d', time());
        }
        $loginlogs = Loginlog::where('user_id', Auth::id())
            ->whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->orderBy('id', 'desc')
            ->get();
        $data = array(
            'page_title' => 'Activity Logs',
            'fromdate' => $fromdate,
            'todate' => $todate,
        );
        return view('admin.activity_logs', compact('loginlogs'))->with($data);
    }

    function send_balance_alert()
    {
        $company_id = Auth::User()->company_id;
        $sitesettings = Sitesetting::where('company_id', $company_id)->first();
        if ($sitesettings) {
            $alert_amount = $sitesettings->alert_amount;
        } else {
            $alert_amount = 500;
        }
        $balances = Balance::where('balance_alert', 1)->get();
        foreach ($balances as $value) {
            $user_id = $value->user_id;
            $user_balance = $value->user_balance;
            if ($alert_amount >= $user_balance) {
                Balance::where('user_id', $user_id)->update(['balance_alert' => 0]);
                $userdetails = User::find($user_id);
                $message = "Dear $userdetails->name $userdetails->last_name your balance is low : $user_balance kindly refill your wallet $this->brand_name";
                $template_id = 12;
                $library = new SmsLibrary();
                $library->send_sms($userdetails->mobile, $message, $template_id);
            }

        }
    }

    function top_seller(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $role_id = Auth::User()->role_id;
        $company_id = Auth::User()->company_id;
        $user_id = Auth::id();
        $library = new MemberLibrary();
        $my_down_member = $library->my_down_member($role_id, $company_id, $user_id);

        $totalRecords = 10;
        $totalRecordswithFilter = 10;

        // Fetch records
        $start = Carbon::now()->startOfMonth();
        $fromdate = $start->format('Y-m-d');
        $todate = date('Y-m-d', time());
        $records = Report::whereIn('user_id', $my_down_member)
            ->where('status_id', 1)
            ->whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->groupBy('user_id')
            ->selectRaw('user_id, sum(amount) as total_amount, sum(profit) as total_profit')
            ->orderBy('total_amount', 'DESC')
            ->paginate(10);

        $data_arr = array();
        $i = 1;
        foreach ($records as $value) {
            $data_arr[] = array(
                "sr_no" => $i++,
                "username" => $value->user->name . ' ' . $value->user->last_name,
                "total_amount" => number_format($value->total_amount, 2),
                "total_profit" => number_format($value->total_profit, 2),
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        echo json_encode($response);
        exit;
    }


    function getServiceWiseSales()
    {
        $library = new BasicLibrary();
        $companyActiveService = $library->getCompanyActiveService(Auth::id());
        $userActiveService = $library->getUserActiveService(Auth::id());
        $services = Service::whereIn('id', $companyActiveService)->whereIn('id', $userActiveService)->get();

        $results = '<div class="row row-sm">';
        foreach ($services as $value) {
            $toDaySale = Self::getServiceWiseTodaySales($value->id);
            $todaySuccess = $toDaySale['todaySuccess'];
            $todayFailure = $toDaySale['todayFailure'];

            $results .= '
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="card overflow-hidden project-card" style="
                    background: repeating-linear-gradient(135deg, #1c2e4a, #1c2e4a 14px, #273c5c 14px, #273c5c 28px) !important;
                    color: #ffffff !important;
                    border: none !important;
                    border-radius: 16px !important;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.25) !important;
                    transition: all 0.3s ease-in-out !important;
                " onmouseover="this.style.transform=\'translateY(-6px) scale(1.01)\'" onmouseout="this.style.transform=\'translateY(0) scale(1)\'">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="my-auto">
                                <img src="' . $this->cdnLink . $value->service_image . '" style="
                                    width: 80px !important;
                                    height: 80px !important;
                                    border-radius: 12px !important;
                                    object-fit: contain !important;
                                    background: #ffffff !important;
                                    padding: 6px !important;
                                    box-shadow: 0 3px 6px rgba(0,0,0,0.2) !important;
                                ">
                            </div>
                            <div class="project-content" style="margin-left: 10% !important;">
                                <h6 style="font-weight: 700 !important; font-size: 1.1rem !important; color: #ffffff !important; margin-bottom: 12px;">
                                    ' . $value->service_name . '
                                </h6>
                                <ul style="list-style: none !important; padding-left: 0 !important; margin: 0 !important; font-size: 0.95rem !important;">
                                    <li style="margin-bottom: 6px;">
                                        <strong style="color: #2ecc71 !important; font-weight: 700 !important;">Success</strong>
                                        <span style="margin-left: 8px; font-weight: 600 !important; color: #ffffff !important;">₹ ' . $todaySuccess . '</span>
                                    </li>
                                    <li>
                                        <strong style="color: #e74c3c !important; font-weight: 700 !important;">Failure</strong>
                                        <span style="margin-left: 8px; font-weight: 600 !important; color: #ffffff !important;">₹ ' . $todayFailure . '</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }

        $results .= '</div>';
        return $results;
    }

    function getServiceWiseTodaySales($service_id)
    {
        $provider_id = Provider::where('service_id', $service_id)->get(['id']);
        $role_id = Auth::User()->role_id;
        $company_id = Auth::User()->company_id;
        $user_id = Auth::id();
        $library = new MemberLibrary();
        $my_down_member = $library->my_down_member($role_id, $company_id, $user_id);
        $todaySuccess = Report::whereIn('user_id', $my_down_member)->whereIn('status_id', [1,6])->whereDate('created_at', '=', date('Y-m-d'))->whereIn('provider_id', $provider_id)->sum('amount');
        $todayFailure = Report::whereIn('user_id', $my_down_member)->where('status_id', 2)->whereDate('created_at', '=', date('Y-m-d'))->whereIn('provider_id', $provider_id)->sum('amount');
        return [
            'todaySuccess' => number_format($todaySuccess, 2),
            'todayFailure' => number_format($todayFailure, 2)
        ];
    }


}
