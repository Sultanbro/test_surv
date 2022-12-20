<?php $__env->startSection('title', 'Плейлисты - Видео обучение'); ?>
<?php $__env->startSection('content'); ?>

<div class="old__content">
    <page-playlists 
    token="<?php echo e(csrf_token()); ?>" 
    :can_edit="<?php echo e(auth()->user()->can('videos_edit') ? 'true' : 'false'); ?>"
    :category="<?php echo e(isset($category) ? $category : 1); ?>"
    :playlist="<?php echo e(isset($playlist) ? $playlist : 0); ?>"
    :video="<?php echo e(isset($video) ? $video : 0); ?>" 
    />
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/playlists/index.blade.php ENDPATH**/ ?>