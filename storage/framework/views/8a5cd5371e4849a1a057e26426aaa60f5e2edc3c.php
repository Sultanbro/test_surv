<?php $__env->startSection('content'); ?>
<div>
	<?php echo $__env->make('larecipe::partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	
	<div class="documentation is-<?php echo e(config('larecipe.ui.code_theme')); ?>" :class="{'expanded': ! sidebar}">
		<?php echo $content; ?>

		<?php echo $__env->make('larecipe::plugins.forum', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('larecipe::default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/vendor/binarytorch/larecipe/resources/views/docs.blade.php ENDPATH**/ ?>