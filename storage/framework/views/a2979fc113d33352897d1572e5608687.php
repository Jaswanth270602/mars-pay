<?php $__env->startSection('content'); ?>
    <script type="text/javascript">
        function update_navigation() {
            $(".loader").show();
            var token = $("input[name=_token]").val();
            var title = $("#title").val();
            var keyword = $("#keyword").val();
            var description = $("#description").val();
            var navigation_name = $("#navigation_name").val();
            var navigation_slug = $("#navigation_slug").val();
            var type = $("#type").val();
            var navigation_id = $("#navigation_id").val();
            var dataString = 'title=' + encodeURIComponent(title) + '&keyword=' + encodeURIComponent(keyword) +  '&description=' + encodeURIComponent(description) + '&navigation_name=' + navigation_name + '&navigation_slug=' + navigation_slug + '&type=' + type + '&navigation_id=' + navigation_id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('admin/update-navigation')); ?>",
                data: dataString,
                success: function (msg) {
                    $(".loader").hide();
                    if (msg.status == 'success') {
                        swal("Success", msg.message, "success");
                        setTimeout(function () { location.reload(1); }, 3000);
                    } else if(msg.status == 'validation_error'){
                        $("#title_errors").text(msg.errors.title);
                        $("#keyword_errors").text(msg.errors.keyword);
                        $("#description_errors").text(msg.errors.description);
                        $("#navigation_name_errors").text(msg.errors.navigation_name);
                        $("#navigation_slug_errors").text(msg.errors.navigation_slug);
                        $("#type_errors").text(msg.errors.type);
                    }else{
                        swal("Faild", msg.message, "error");
                    }
                }
            });
        }
    </script>


    <div class="main-content-body">
        
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

                        <input type="hidden" id="navigation_id" value="<?php echo e($navigation_id); ?>">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Title</label>
                                        <textarea type="text" id="title" class="form-control" placeholder="Enter Title"><?php echo e($title); ?></textarea>
                                        <ul class="parsley-errors-list filled">
                                            <li class="parsley-required" id="title_errors"></li>
                                        </ul>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Keyword</label>
                                        <textarea type="text" id="keyword" class="form-control" placeholder="Enter Keyword"><?php echo e($keyword); ?></textarea>
                                        <ul class="parsley-errors-list filled">
                                            <li class="parsley-required" id="keyword_errors"></li>
                                        </ul>

                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">Description</label>
                                        <textarea type="text" id="description" class="form-control" placeholder="Enter Description"><?php echo e($description); ?></textarea>
                                        <ul class="parsley-errors-list filled">
                                            <li class="parsley-required" id="description_errors"></li>
                                        </ul>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Navigation Name</label>
                                        <input type="text" id="navigation_name" class="form-control" placeholder="Navigation Name" value="<?php echo e($navigation_name); ?>">
                                        <ul class="parsley-errors-list filled">
                                            <li class="parsley-required" id="navigation_name_errors"></li>
                                        </ul>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Slug (Navigation URL)</label>
                                        <input type="text" id="navigation_slug" class="form-control" placeholder="Slug (Navigation URL)" value="<?php echo e($navigation_slug); ?>">
                                        <ul class="parsley-errors-list filled">
                                            <li class="parsley-required" id="navigation_slug_errors"></li>
                                        </ul>

                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">Navigation Type</label>
                                        <select class="form-control" id="type">
                                            <option value="1" <?php if($type == 1): ?> selected <?php endif; ?>>Header</option>
                                            <option value="2" <?php if($type == 2): ?> selected <?php endif; ?>>Footer</option>
                                        </select>
                                        <ul class="parsley-errors-list filled">
                                            <li class="parsley-required" id="type_errors"></li>
                                        </ul>

                                    </div>
                                </div>




                            </div>

                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger waves-effect waves-light" onclick="update_navigation()">Update Navigation</button>
                    </div>
                </div>
            </div>
            <!--/div-->
        </div>
        





    </div>
    </div>
    </div>




<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/admin/website-master/edit_navigation.blade.php ENDPATH**/ ?>