<?php $__env->startSection('content'); ?>


<div class="firstscreen">
  <div class="firstscreen-holder">
    <div class="firstscreen-textbox">
      <strong>404. Страница не найдена</strong>
    <p></p>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.apperror', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>