<?php if(Auth::User()->role_id == 2): ?>
    <?php echo $__env->make('admin.layout.staff_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('admin.layout.main_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/admin/layout/header.blade.php ENDPATH**/ ?>