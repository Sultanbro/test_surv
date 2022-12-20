<?php $__env->startSection('title', 'Плейлисты - Видео обучение'); ?>
<?php $__env->startSection('content'); ?>
<page-playlists token="<?php echo e(csrf_token()); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="/video_learning/playerjs.js" ></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/playlists/edit.blade.php ENDPATH**/ ?>