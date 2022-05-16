<?php $__env->startSection('title', 'Редактор книг'); ?>
<?php $__env->startSection('content'); ?>

<page-upbooks-edit token="<?php echo e(csrf_token()); ?>" access="read" />

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>

</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>