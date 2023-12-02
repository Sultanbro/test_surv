<?php $__env->startSection('title', 'Табель'); ?>
<?php $__env->startSection('content'); ?>
<script type="application/json" id="async-page-data">
    {
        "activeTab": "nav-home-tab",
        "groups": <?php echo e(json_encode($groups)); ?>,
        "fines": <?php echo e(json_encode($fines)); ?>,
        "years": <?php echo e(json_encode($years)); ?>,
        "can_edit": true,
        "activeuserid": <?php echo e(json_encode(auth()->user()->id)); ?>,
        "activeuserpos": <?php echo e(json_encode(auth()->user()->position_id)); ?>

    }
</script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spa', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lemon/Development/Others/surv/resources/views/admin/reports.blade.php ENDPATH**/ ?>