
<div class="container g-padding-y-80--xs g-padding-y-125--sm">
<h4>Результаты поиска: <i><?php echo e($query); ?> </i></h4>
<h5>Обнаружено: <?php echo e($total); ?> совпадений</h5>

 <?php $__currentLoopData = $autocall_search; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <h4 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md">
<?php echo e($search->name); ?></h4>
<h4 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md">
<?php echo e($search->description); ?></h4>
 <p class="g-font-size-18--xs"> <span class="glyphicon glyphicon-calendar"></span> <?php echo e($search->created_at ->format('d m Y')); ?></p>
</div>  
<h4 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md">
<?php echo e($search->status); ?></h4>             
<br>
<a  href="<?php echo e(URL::to('show/'.$search->id)); ?>" button type="button" class="text-uppercase s-btn s-btn--sm s-btn--primary-bg g-radius--50 g-padding-x-50--xs">Смотреть дальше</a></button>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
<?php echo $autocall_search->links(); ?>

</div><?php /**PATH /var/www/job/resources/views/includes/search.blade.php ENDPATH**/ ?>