<?php $__env->startSection('title', 'База знаний'); ?>
<?php $__env->startSection('content'); ?>

<page-kb />

<?php $__env->stopSection(); ?> 
<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>