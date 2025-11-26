<?php $__env->startSection('content'); ?>


    <!-- inner banner -->
    <section class="w3l-inner-banner-main">
        <div class="about-inner">
            <div class="container">
                <ul class="breadcrumbs-custom-path">
                    <li><a href="<?php echo e(url('')); ?>">Home <span class="fa fa-angle-double-right" aria-hidden="true"></span></a></li>
                    <li class="active"><?php echo e($navigation_name); ?></li>
                </ul>
            </div>
        </div>
    </section>
    <!-- //covers -->
    <!---728x90--->

    <!-- features-4 block -->
    <section class="w3l-galleries-14">
        <div id="gallery14-block" class="py-5">
            <div class="container py-md-3">
                <div class="top-main-content">

                <?php echo $content; ?>

                    <!---728x90--->



            </div>
        </div>
    </section>
    <!-- features-4 block -->
    <!---728x90--->


    <!---728x90--->


<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.template1.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/front/template1/dynamic_page.blade.php ENDPATH**/ ?>