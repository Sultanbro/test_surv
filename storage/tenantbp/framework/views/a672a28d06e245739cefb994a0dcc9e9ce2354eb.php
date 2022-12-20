<?php $__env->startSection('title', 'Настройка кабинета'); ?>
<?php $__env->startSection('content'); ?>

<div class="old__content">
<cabinet auth_role="<?php echo e(auth()->user()); ?>" />
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 7rem !important; 
}
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/cabinet.blade.php ENDPATH**/ ?>