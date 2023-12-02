<?php $__env->startSection('title', 'Аналитика'); ?>
<?php $__env->startSection('content'); ?>
<script type="application/json" id="async-page-data">
    {
        "groups": <?php echo e(json_encode($groups)); ?>,
        "activeuserid": <?php echo e(json_encode(auth()->user()->id)); ?>,
        "isadmin": <?php echo e(json_encode(auth()->user()->is_admin)); ?>

    }
</script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spa', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lemon/Development/Others/surv/resources/views/admin/analytics-page.blade.php ENDPATH**/ ?>