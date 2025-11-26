<?php $__env->startSection('content'); ?>
    <script type="text/javascript">
        function delete_navigation(id) {
            if (confirm("Are you sure? Delete this banner") == true) {
                $(".loader").show();
                var token = $("input[name=_token]").val();
                var dataString = 'id=' + id +  '&_token=' + token;
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(url('admin/delete-navigation')); ?>",
                    data: dataString,
                    success: function (msg) {
                        $(".loader").hide();
                        if (msg.status == 'success') {
                            swal("Success", msg.message, "success");
                            setTimeout(function () { location.reload(1); }, 3000);
                        }else{
                            swal("Faild", msg.message, "error");
                        }
                    }
                });
            }
        }
    </script>

    <div class="main-content-body">
        <div class="row row-sm">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-2 mt-2"><?php echo e($page_title); ?></h4>
                            <a href="<?php echo e(url('admin/create-navigation')); ?>" class="btn btn-danger btn-sm">Add Navigation</a>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                        </div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-md-nowrap" id="example1">
                                <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">navigation name</th>
                                    <th class="wd-15p border-bottom-0">navigation slug</th>
                                    <th class="wd-15p border-bottom-0">Type</th>
                                    <th class="wd-15p border-bottom-0">Content</th>
                                    <th class="wd-15p border-bottom-0">Action</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $navigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($value->navigation_name); ?></td>
                                            <td><?php echo e($value->navigation_slug); ?></td>
                                            <td> <?php if($value->type == 1): ?>Header <?php else: ?>  Footer <?php endif; ?></td>
                                            <td><a href="<?php echo e(url('admin/add-content')); ?>/<?php echo e($value->id); ?>" class="btn btn-success btn-sm"><i class="fas fa-plus-square"></i> Content</a> </td>
                                            <td>
                                                <a href="<?php echo e(url('admin/edit-navigation')); ?>/<?php echo e($value->id); ?>" class="btn btn-info btn-sm"><i class="fas fa-pen-square"></i> Edit</a>
                                                <button class="btn btn-danger btn-sm" onclick="delete_navigation(<?php echo e($value->id); ?>)"><i class="far fa-trash-alt"></i> Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--/div-->

        </div>

    </div>
    </div>
    </div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/admin/website-master/dynamic_page.blade.php ENDPATH**/ ?>