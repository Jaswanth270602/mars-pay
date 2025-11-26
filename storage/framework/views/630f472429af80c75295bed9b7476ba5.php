<?php $__env->startSection('content'); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#download_optional1").select2();
            $("#download_optional2").select2();
            $("#download_optional3").select2();
            $("#download_optional4").select2();
            $("#fromdate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: ('yy-mm-dd'),
            });
            $("#todate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: ('yy-mm-dd'),
            });
        });



    </script>



    <div class="main-content-body">

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">

                        <form action="<?php echo e(url('admin/report/v1/all-transaction-report')); ?>" method="get">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">From: <span class="tx-danger">*</span></label>
                                        <input class="form-control fc-datepicker" value="<?php echo e($fromdate); ?>" type="text" id="fromdate" name="fromdate" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">To: <span class="tx-danger">*</span></label>
                                        <input class="form-control fc-datepicker" value="<?php echo e($todate); ?>" type="text" id="todate"  name="todate" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Status:</label>
                                        <select class="form-control select2" id="download_optional1" name="status_id" style="width: 100%;">
                                            <option value="0" <?php if($status_id == 0): ?> selected <?php endif; ?>> All Status</option>
                                            <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value->id); ?>" <?php if($status_id == $value->id): ?> selected <?php endif; ?>> <?php echo e($value->status); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>



                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Select User:</label>
                                        <select class="form-control select2" id="download_optional2" name="child_id" style="width: 100%;">
                                            <option value="0" <?php if($child_id == 0): ?> selected <?php endif; ?>> All Users</option>
                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value->id); ?>" <?php if($child_id == $value->id): ?> selected <?php endif; ?>><?php echo e($value->name); ?> <?php echo e($value->last_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-lg-3 col-md-8 form-group mg-b-0">
                                    <label class="form-label">Select Provider:</label>
                                    <select class="form-control select2" id="download_optional3" name="provider_id" style="width: 100%;">
                                        <option value="0" <?php if($provider_id == 0): ?> selected <?php endif; ?>> All Provider</option>
                                        <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value->id); ?>" <?php if($provider_id == $value->id): ?> selected <?php endif; ?>> <?php echo e($value->provider_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <?php if(Auth::User()->role_id == 1): ?>
                                    <div class="col-lg-3 col-md-8 form-group mg-b-0">
                                        <label class="form-label">Select Api:</label>
                                        <select class="form-control select2" id="download_optional4" name="api_id" style="width: 100%;">
                                            <option value="0" <?php if($api_id == 0): ?> selected <?php endif; ?>> Select Api</option>
                                            <?php $__currentLoopData = $apis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value->id); ?>" <?php if($api_id == $value->id): ?> selected <?php endif; ?>> <?php echo e($value->api_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                <?php endif; ?>


                                <div class="col-lg-3 col-md-4 mg-t-10 mg-sm-t-25">
                                    <button class="btn btn-primary pd-x-20" type="submit"><i class="fas fa-search"></i> Search</button>
                                    <button class="btn btn-danger pd-x-20" type="button"  data-toggle="modal" data-target="#transaction_download_model"><i class="fas fa-download"></i> Download</button>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>





        <div class="row row-sm">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-2 mt-2"><?php echo e($page_title); ?></h4>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                        </div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="<?php if(Auth::User()->company->table_format == 1): ?>table text-md-nowrap <?php else: ?> display responsive nowrap <?php endif; ?>" id="my_table">
                                <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">Report ID</th>
                                    <th class="wd-15p border-bottom-0">Date</th>
                                    <th class="wd-15p border-bottom-0">User Name</th>
                                    <th class="wd-15p border-bottom-0">Provider Name</th>
                                    <th class="wd-15p border-bottom-0">Number</th>
                                    <th class="wd-15p border-bottom-0">Opening Balance</th>
                                    <th class="wd-15p border-bottom-0">Amount</th>
                                    <th class="wd-15p border-bottom-0">Profit</th>
                                    <th class="wd-15p border-bottom-0">Closing Balance</th>
                                    <th class="wd-15p border-bottom-0">Status</th>
                                    <th class="wd-15p border-bottom-0">Mode</th>
                                    <th class="wd-15p border-bottom-0">Vendor</th>
                                    <th class="wd-15p border-bottom-0">UTR</th>
                                    <th class="wd-15p border-bottom-0">Action</th>
                                    <th class="wd-15p border-bottom-0">failure reason</th>
                                    <th class="wd-15p border-bottom-0">Wallet</th>
                                    <th class="wd-15p border-bottom-0">client id</th>
                                    <th class="wd-15p border-bottom-0">Receiver Mobile</th>
                                    <th class="wd-15p border-bottom-0">Receiver Email</th>
                                </tr>
                                </thead>
                            </table>

                            <script type="text/javascript">
                                $(document).ready(function(){

                                    // DataTable
                                    var todate = $("#todate").val();
                                    $('#my_table').DataTable({
                                        "order": [[ 1, "desc" ]],
                                        processing: true,
                                        serverSide: true,
                                        ajax: "<?php echo e($urls); ?>",
                                        columns: [
                                            { data: 'id' },
                                            { data: 'created_at' },
                                            { data: 'user' },
                                            { data: 'provider' },
                                            { data: 'number' },
                                            { data: 'opening_balance' },
                                            { data: 'amount' },
                                            { data: 'profit' },
                                            { data: 'total_balance' },
                                            { data: 'status' },
                                            { data: 'mode' },
                                            { data: 'vendor' },
                                            { data: 'txnid' },
                                            { data: 'view' },
                                            { data: 'failure_reason' },
                                            { data: 'wallet_type' },
                                            { data: 'client_id' },
                                            { data: 'receiver_mobile' },
                                            { data: 'receiver_email' },
                                        ]
                                    });
                                    $("input[type='search']").wrap("<form>");
                                    $("input[type='search']").closest("form").attr("autocomplete","off");
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <!--/div-->

        </div>



    </div>
    </div>
    </div>



    <?php echo $__env->make('admin.report.transaction_refund_model', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/admin/report/all_transaction_report.blade.php ENDPATH**/ ?>