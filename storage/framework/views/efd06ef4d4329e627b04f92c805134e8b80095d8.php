<?php $__env->startSection('title', 'KPI'); ?>
<?php $__env->startSection('content'); ?>
<div class="old__content">
<?php if(auth()->user()->can('kpi_view')): ?>
<kpi-pages 
    page="<?php echo e($page); ?>"
    access="<?php echo e(auth()->user()->can('kpi_edit') ? 'edit' : 'view'); ?>"
>
<?php else: ?>
Нет доступа
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 9rem !important;
}
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/kpi.blade.php ENDPATH**/ ?>