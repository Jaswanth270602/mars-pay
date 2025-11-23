@extends('admin.layout.header')
@section('content')
<script>
    $(document).ready(function () {
        showGraph();
        dashboard_details();
        getServiceWiseSale();
    });

    function showGraph() {
        var id = 1;
        var dataString = 'id=' + id;
        $.ajax({
            type: "GET",
            url: "{{url('admin/dashboard-chart-api')}}",
            data: dataString,
            success: function (msg) {
                var provider_name = [];
                var amount = [];
                for (var i in msg.provider) {
                    provider_name.push(msg.provider[i].provider_name);
                    amount.push(msg.provider[i].amount);
                }
                var chartdata = {
                    labels: provider_name,
                    datasets: [
                        {
                            label: 'Provider Wise Chart',
                            backgroundColor: '#49e2ff',
                            borderColor: '#46d5f1',
                            hoverBackgroundColor: '#CCCCCC',
                            hoverBorderColor: '#666666',
                            data: amount
                        }
                    ]
                };
                var graphTarget = $("#graphCanvas");
                var barGraph = new Chart(graphTarget, {
                    type: 'bar',
                    data: chartdata
                });
            }
        });
    }

    function dashboard_details() {
        var id = 1;
        var dataString = 'id=' + id;
        $.ajax({
            type: "GET",
            url: "{{url('admin/dashboard-details-api')}}",
            data: dataString,
            success: function (msg) {
                if (msg.status == 'success') {
                    $("#dashboard_today_success").text(msg.sales_overview.today_success);
                    $("#dashboard_today_failure").text(msg.sales_overview.today_failure);
                    $("#dashboard_today_pending").text(msg.sales_overview.today_pending);
                    $("#dashboard_today_refunded").text(msg.sales_overview.today_refunded);
                    $("#dashboard_today_credit").text(msg.sales_overview.today_credit);
                    $("#dashboard_today_debit").text(msg.sales_overview.today_debit);

                    $("#normal_distributed_balance").text(msg.balances.normal_distributed_balance);
                    $("#aeps_distributed_balance").text(msg.balances.aeps_distributed_balance);
                    $("#my_balances").text(msg.balances.my_balances);
                    $("#dashboard_total_members").text(msg.balances.dashboard_total_members);
                    $("#dashboard_total_suspended_users").text(msg.balances.dashboard_total_suspended_users);

                    $("#success_percentage").text(msg.percentage.success_percentage);
                    $("#failure_percentage").text(msg.percentage.failure_percentage);
                    $("#pending_percentage").text(msg.percentage.pending_percentage);
                }
            }
        });
    }


    function getServiceWiseSale() {
        var token = $("input[name=_token]").val();
        var dataString = '_token=' + token;
        $.ajax({
            type: "GET",
            url: "{{url('admin/get-service-wise-sales')}}",
            data: dataString,
            success: function (msg) {
                $('#get-service-wise-sale').append(msg);
            }
        });
    }
</script>


<!-- main-content-body -->
<div class="main-content-body">


    {{--Dashboard popup start--}}
         @include('common.dashboard_popup')
    {{--Dashboard popup End--}}

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; max-width: 1200px; margin: 0 auto; margin-bottom: 20px !important;">

  <!-- Card 1 -->
  <div style="position: relative; height: 180px; border-radius: 20px; background: rgba(255,255,255,0.95); box-shadow: 0 8px 24px rgba(0,0,0,0.12); overflow: hidden; backdrop-filter: blur(8px); transition: all 0.4s ease; cursor: pointer;"
       onmouseover="
         this.querySelector('[data-content]').style.opacity='1';
         this.querySelector('[data-header]').style.opacity='0';
         this.querySelector('[data-arrow]').style.transform='rotate(45deg)';
       "
       onmouseout="
         this.querySelector('[data-content]').style.opacity='0';
         this.querySelector('[data-header]').style.opacity='1';
         this.querySelector('[data-arrow]').style.transform='rotate(0deg)';
       "
  >
    <div data-header style="height: 100%; padding: 30px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; transition: all 0.3s ease;">
      <div style="font-size: 26px; font-weight: bold; color: #2d3748; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-wallet" style="font-size: 28px; color: #4299e1;"></i> Distributed
      </div>
      <div style="font-size: 15px; color: #718096; margin-top: 6px;">Normal Balance</div>
    </div>
    <div data-arrow style="position: absolute; top: 18px; right: 20px; font-size: 22px; color: #a0aec0; transition: all 0.3s ease;">
      <i class="fas fa-arrow-right"></i>
    </div>
    <div data-content style="position: absolute; inset: 0; background: linear-gradient(135deg, #4299e1, #3182ce); color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 25px; text-align: center; opacity: 0; transition: all 0.4s ease;">
      <div style="font-size: 18px; margin-bottom: 10px;">Distributed Balance</div>
      <div style="font-size: 34px; font-weight: bold; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <i class="fas fa-wallet"></i> <span id="normal_distributed_balance">₹0</span>
      </div>
      <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Normal Wallet Balance</div>
    </div>
  </div>

  <!-- Card 2 -->
  <div style="position: relative; height: 180px; border-radius: 20px; background: rgba(255,255,255,0.95); box-shadow: 0 8px 24px rgba(0,0,0,0.12); overflow: hidden; backdrop-filter: blur(8px); transition: all 0.4s ease; cursor: pointer;"
       onmouseover="
         this.querySelector('[data-content]').style.opacity='1';
         this.querySelector('[data-header]').style.opacity='0';
         this.querySelector('[data-arrow]').style.transform='rotate(45deg)';
       "
       onmouseout="
         this.querySelector('[data-content]').style.opacity='0';
         this.querySelector('[data-header]').style.opacity='1';
         this.querySelector('[data-arrow]').style.transform='rotate(0deg)';
       "
  >
    <div data-header style="height: 100%; padding: 30px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; transition: all 0.3s ease;">
      <div style="font-size: 26px; font-weight: bold; color: #2d3748; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-wallet" style="font-size: 28px; color: #48bb78;"></i> My Balance
      </div>
      <div style="font-size: 15px; color: #718096; margin-top: 6px;">Wallet Balance</div>
    </div>
    <div data-arrow style="position: absolute; top: 18px; right: 20px; font-size: 22px; color: #a0aec0; transition: all 0.3s ease;">
      <i class="fas fa-arrow-right"></i>
    </div>
    <div data-content style="position: absolute; inset: 0; background: linear-gradient(135deg, #48bb78, #38a169); color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 25px; text-align: center; opacity: 0; transition: all 0.4s ease;">
      <div style="font-size: 18px; margin-bottom: 10px;">My Balance</div>
      <div style="font-size: 34px; font-weight: bold; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <i class="fas fa-wallet"></i> <span id="my_balances">₹0</span>
      </div>
      <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Wallet available balance</div>
    </div>
  </div>

  <!-- Card 3 -->
  <div style="position: relative; height: 180px; border-radius: 20px; background: rgba(255,255,255,0.95); box-shadow: 0 8px 24px rgba(0,0,0,0.12); overflow: hidden; backdrop-filter: blur(8px); transition: all 0.4s ease; cursor: pointer;"
       onmouseover="
         this.querySelector('[data-content]').style.opacity='1';
         this.querySelector('[data-header]').style.opacity='0';
         this.querySelector('[data-arrow]').style.transform='rotate(45deg)';
       "
       onmouseout="
         this.querySelector('[data-content]').style.opacity='0';
         this.querySelector('[data-header]').style.opacity='1';
         this.querySelector('[data-arrow]').style.transform='rotate(0deg)';
       "
  >
    <div data-header style="height: 100%; padding: 30px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; transition: all 0.3s ease;">
      <div style="font-size: 26px; font-weight: bold; color: #2d3748; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-users" style="font-size: 28px; color: #ed8936;"></i> Members
      </div>
      <div style="font-size: 15px; color: #718096; margin-top: 6px;">Total Users</div>
    </div>
    <div data-arrow style="position: absolute; top: 18px; right: 20px; font-size: 22px; color: #a0aec0; transition: all 0.3s ease;">
      <i class="fas fa-arrow-right"></i>
    </div>
    <div data-content style="position: absolute; inset: 0; background: linear-gradient(135deg, #ed8936, #dd6b20); color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 25px; text-align: center; opacity: 0; transition: all 0.4s ease;">
      <div style="font-size: 18px; margin-bottom: 10px;">Members</div>
      <div style="font-size: 34px; font-weight: bold; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <i class="fas fa-users"></i> <span id="dashboard_total_members">0</span>
      </div>
      <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Registered user count</div>
      <a href="{{ url('admin/all-user-list') }}" style="font-size: 13px; padding: 6px 12px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 20px; transition: all 0.3s ease;"
         onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
         onmouseout="this.style.background='rgba(255,255,255,0.15)'">View All</a>
    </div>
  </div>

  <!-- Card 4 -->
  <div style="position: relative; height: 180px; border-radius: 20px; background: rgba(255,255,255,0.95); box-shadow: 0 8px 24px rgba(0,0,0,0.12); overflow: hidden; backdrop-filter: blur(8px); transition: all 0.4s ease; cursor: pointer;"
       onmouseover="
         this.querySelector('[data-content]').style.opacity='1';
         this.querySelector('[data-header]').style.opacity='0';
         this.querySelector('[data-arrow]').style.transform='rotate(45deg)';
       "
       onmouseout="
         this.querySelector('[data-content]').style.opacity='0';
         this.querySelector('[data-header]').style.opacity='1';
         this.querySelector('[data-arrow]').style.transform='rotate(0deg)';
       "
  >
    <div data-header style="height: 100%; padding: 30px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; transition: all 0.3s ease;">
      <div style="font-size: 26px; font-weight: bold; color: #2d3748; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-times" style="font-size: 28px; color: #e53e3e;"></i> Suspended
      </div>
      <div style="font-size: 15px; color: #718096; margin-top: 6px;">Blocked Users</div>
    </div>
    <div data-arrow style="position: absolute; top: 18px; right: 20px; font-size: 22px; color: #a0aec0; transition: all 0.3s ease;">
      <i class="fas fa-arrow-right"></i>
    </div>
    <div data-content style="position: absolute; inset: 0; background: linear-gradient(135deg, #e53e3e, #c53030); color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 25px; text-align: center; opacity: 0; transition: all 0.4s ease;">
      <div style="font-size: 18px; margin-bottom: 10px;">Suspended Users</div>
      <div style="font-size: 34px; font-weight: bold; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <i class="fas fa-user-times"></i> <span id="dashboard_total_suspended_users">0</span>
      </div>
      <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Accounts currently blocked</div>
      <a href="{{ url('admin/suspended-users') }}" style="font-size: 13px; padding: 6px 12px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 20px; transition: all 0.3s ease;"
         onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
         onmouseout="this.style.background='rgba(255,255,255,0.15)'">View Suspended</a>
    </div>
  </div>

</div>


    <!-- row -->
    <div class="row row-sm ">
        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12">
            <div class="card overflow-hidden">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Today Sales</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body pd-y-7">
                    <!--<canvas id="graphCanvas"></canvas>-->
                    <div class="row row-sm">
                       <div id="get-service-wise-sale"></div>
                    </div>

                </div>
            </div>

            @if(Auth::User()->role_id == 1)
            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-2 mt-2">This month top 10 sellers</h4>
                                <i class="mdi mdi-dots-horizontal text-gray"></i>
                            </div>
                            <hr>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-md-nowrap" id="my_table">
                                    <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">Sr No</th>
                                        <th class="wd-15p border-bottom-0">User</th>
                                        <th class="wd-15p border-bottom-0">Total Sale</th>
                                        <th class="wd-15p border-bottom-0">Total Profit</th>
                                    </tr>
                                    </thead>
                                </table>

                                <script type="text/javascript">
                                    $(document).ready(function () {

                                        // DataTable
                                        var todate = $("#todate").val();
                                        $('#my_table').DataTable({
                                            "order": [[1, "desc"]],
                                            processing: true,
                                            serverSide: true,
                                            ajax: "{{ $urls }}",
                                            columns: [
                                                {data: 'sr_no'},
                                                {data: 'username'},
                                                {data: 'total_amount'},
                                                {data: 'total_profit'},

                                            ]
                                        });

                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/div-->
            </div>
            @endif
        </div>

        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
            <div class="card">
                <div class="card-header pb-0 pt-4">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Last 5 login records</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body p-0 m-scroll mh-350 mt-2">
                    <div class="list-group projects-list">

                        @foreach(App\Models\Loginlog::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(5) as $value)
                        <a href="{{ url('admin/activity-logs') }}" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">{{ $value->get_device }} - {{ $value->get_browsers }} - {{ $value->get_os }}</h6>
                                <small class="text-danger">{{ \Carbon\Carbon::parse($value->created_at)->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 text-muted mb-0 tx-12">IP Address: {{ $value->ip_address }}</p>
                            <small class="text-muted">Latitude: {{ $value->latitude }}, Longitude: {{ $value->longitude }}</small>
                        </a>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header pb-0 pt-4">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Today Overview</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body p-0 m-scroll mh-350 mt-2">
                    <div class="list-group projects-list">

                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">Success</h6>
                                <small class="text-success" id="dashboard_today_success"></small>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">Failure</h6>
                                <small class="text-danger" id="dashboard_today_failure"></small>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">Pending</h6>
                                <small class="text-warning" id="dashboard_today_pending"></small>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">Refunded</h6>
                                <small class="text-danger" id="dashboard_today_refunded"></small>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">Debit</h6>
                                <small class="text-warning" id="dashboard_today_debit"></small>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-semibold ">Credit</h6>
                                <small class="text-warning" id="dashboard_today_credit"></small>
                            </div>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->



    <!-- row -->


</div>
<!-- /row -->
</div>
<!-- /container -->
</div>
<!-- /main-content -->


@endsection