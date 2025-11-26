<?php $__env->startSection('content'); ?>
   
   <script src="https://cdn.ckeditor.com/4.14.1/full/ckeditor.js"></script>


    <div class="main-content-body">
        
        <div class="row row-sm">
            <div class="col-xl-12">
               <form action="<?php echo e(url('admin/update-content')); ?>" method="post">
                   <?php echo csrf_field(); ?>



                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-2 mt-2"><?php echo e($page_title); ?></h4>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                        </div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="navigation_id" value="<?php echo e($navigation_id); ?>">
                        <div class="form-body">
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Navigation Name</label>
                                       <input type="text" value="<?php echo e($navigation_name); ?>" class="form-control" disabled>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Navigation Slug (URL)</label>
                                        <input type="text" value="<?php echo e($navigation_slug); ?>" class="form-control" disabled>
                                    </div>

                                </div>


                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">Content : </label>
                                        <textarea class="form-control"  name="nav_content" id="nav_content"><?php echo e($content); ?></textarea>
                                    </div>
                                    <script>
                                        CKEDITOR.replace( 'nav_content' );
                                    </script>
                                </div>



                            </div>

                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger waves-effect waves-light">Update Content</button>
                    </div>
                </div>
               </form>
            </div>
            <!--/div-->
        </div>
        








    </div>
    </div>
    </div>




<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/admin/website-master/add_content.blade.php ENDPATH**/ ?>