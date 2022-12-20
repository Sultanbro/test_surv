<?php $__env->startSection('title', 'HR Аналитика'); ?>
<?php $__env->startSection('content'); ?>

<div class="old__content">
    <div class="row">
        <div class="col-md-12 mt-4 mb-3">
            <nav>
                <div class="nav nav-tabs" id="nav-tab">

                    <?php if(auth()->user()->can('top_view')): ?><li class="nav-item"><a class="nav-link" id="nav-top-tab" href="/timetracking/top">TOП</a></li><?php endif; ?>
                    <?php if(auth()->user()->can('tabel_view')): ?><li class="nav-item"><a class="nav-link" id="nav-home-tab" href="/timetracking/reports" >Табель</a></li><?php endif; ?>
                        <?php if(auth()->user()->can('entertime_view')): ?><li class="nav-item"><a class="nav-link " id="nav-home-tab" href="/timetracking/reports/enter-report" >Время прихода</a></li><?php endif; ?>
                        <?php if(auth()->user()->can('hr_view')): ?><li class="nav-item"><a class="nav-link active" id="nav-profilex-tab" href="/timetracking/analytics">HR</a></li><?php endif; ?>
                        <?php if(auth()->user()->can('analytics_view')): ?><li class="nav-item"><a class="nav-link" id="nav-profile-tab" href="/timetracking/an">Аналитика</a></li><?php endif; ?>
                        <?php if(auth()->user()->can('salaries_view')): ?><li class="nav-item"><a class="nav-link " id="nav-salary-tab" href="/timetracking/salaries">Начисление</a></li><?php endif; ?>
                        <?php if(auth()->user()->can('quality_view')): ?><li class="nav-item"><a class="nav-link" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a></li><?php endif; ?>
                </div>
            </nav>    
        </div>
        
        <div class="col-md-12 analytics">
            <analytics :groups="<?php echo e(json_encode($groups)); ?>" activeuserid="<?php echo e(json_encode(auth()->user()->id)); ?>"></analytics>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 9rem !important;
}
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/analytics.blade.php ENDPATH**/ ?>