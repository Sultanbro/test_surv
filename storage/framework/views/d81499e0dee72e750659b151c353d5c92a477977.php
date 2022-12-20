<?php $__env->startSection('content'); ?>
    <div class="">
        <div class="row">
            <form action="" method="get">
                <div class="col-lg-4">
                    <div class="row form-group">
                        <div class="col-lg-4">
                            <label for="select" class=" form-control-label">Год</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="year" class="form-control">
                                <?php for($y = 2018; $y <= (int)date('Y'); $y++): ?>
                                    <option <?php echo e($year == $y?'selected':''); ?> value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row form-group">
                        <div class="col-lg-6">
                            <label for="select" class=" form-control-label">Месяц</label>
                        </div>
                        <div class="col-lg-6">
                            <select name="month" class="form-control">
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option <?php echo e($month == $m?'selected':''); ?> value="<?php echo e($m); ?>"><?php echo e($m); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> применить
                    </button>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="report-table" class="table table-striped table-bordered" style="width:100%">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Линия</th>
                        <th scope="col">Сумма</th>
                        <?php for($i = 1; $i <= $show_days; $i++): ?>
                            <th scope="col"><?php echo e($i); ?></th>
                        <?php endfor; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $gates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gate=>$data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <th scope="row"><?php echo e(\App\SmsDailyReport::title($gate)); ?></th>
                        <th scope="row"><?php echo e($data['total']); ?></th>
                        <?php for($i = 1; $i <= $show_days; $i++): ?>
                            <td><?php echo e(isset($data[$i])?$data[$i]:''); ?></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- .animated -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/report.blade.php ENDPATH**/ ?>