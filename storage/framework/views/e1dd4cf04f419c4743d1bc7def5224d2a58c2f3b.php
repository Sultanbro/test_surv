<?php $__env->startSection('title', 'Курсы'); ?>
<?php $__env->startSection('content'); ?>
<page-courses />      
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>