<?php $__env->startSection('title', 'Аналитика'); ?>
<?php $__env->startSection('content'); ?>

<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab">
                    <?php if(auth()->user()->ID == 18 || auth()->user()->ID == 5): ?>
                        <a class="nav-item nav-link" id="nav-top-tab" href="/timetracking/top">TOП</a>
                    <?php endif; ?>
                    <a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports">Табель</a>
                    <a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports/enter-report">Время прихода</a>
                    <a class="nav-item nav-link" id="nav-profilex-tab" href="/timetracking/analytics">HR</a> 
                    <a class="nav-item nav-link active" id="nav-profile-tab" href="/timetracking/an">Аналитика</a>
                    <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/salaries">Начисление</a>
                    <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/exam">Повышение квалификации</a>
                    <a class="nav-item nav-link" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a>
                </div>
            </nav>    
        </div>
        
        <div class="col-md-12 analytics">
            <analytics-page :groups="<?php echo e(json_encode($groups)); ?>" activeuserid="<?php echo e(json_encode(auth()->user()->ID)); ?>"></analytics-page>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>