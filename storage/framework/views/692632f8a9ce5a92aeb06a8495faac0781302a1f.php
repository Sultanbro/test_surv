<?php $__env->startSection('title', 'Новости'); ?>
<?php $__env->startSection('content'); ?>
    <div class="news-page">
        <news-pages
                page="<?php echo e($page); ?>"
                access="<?php echo e(auth()->user()->can('news_edit') ? 'edit' : 'view'); ?>"
        ></news-pages>
        <birthday-feed></birthday-feed>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>

    <link rel="stylesheet" href="<?php echo e(url('/css/news.css')); ?>">

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/news.blade.php ENDPATH**/ ?>