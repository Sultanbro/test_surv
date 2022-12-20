<?php $__env->startSection('title', 'Депремирование'); ?>
<?php $__env->startSection('content'); ?>

<div class="old__content">
<div class="container p-4">
  <div class="card p-3 content">
      <h5 class="mb-3"><strong>Система депремирования&nbsp;</strong></h5>
      <table cellspacing="0" cellpadding="0" class="table table-striped">
        <tbody>
          <?php $__currentLoopData = $fines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="pr-5 text-left"><span><strong><?php echo e($fine->name); ?></strong></span></td>
            <td class="p-3 text-right primary" style="background: #dc354573;
              font-weight: 700;">- <?php echo e($fine->penalty_amount); ?> тенге</td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
        </tbody>
      </table>
 
  </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
  ul,
  ol {
    padding-left: 30px;
  }

  .content p {
    color: #000;
  }

  .content a {
    color: #007bff;
    text-decoration: dashed;
  }
</style>
<script>
    (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_8_dzfbjh.js');
</script>
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/fines.blade.php ENDPATH**/ ?>