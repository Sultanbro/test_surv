<?php $__env->startSection('title', 'Мой профиль'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="card p-3" id="card1">
            <h3>Мой профиль</h3>
        </div>
    </div>      
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/surv/profile.blade.php ENDPATH**/ ?>