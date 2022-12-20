<?php $__env->startSection('title', 'База знаний'); ?>
<?php $__env->startSection('content'); ?>

<div class="old__content">
<page-kb :auth_user_id="<?php echo e(auth()->user()->id); ?>" :can_edit="<?php echo e(auth()->user()->can('kb_edit') ? 'true' : 'false'); ?>"/>
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
    padding-right: 6rem !important;
}
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/books.blade.php ENDPATH**/ ?>