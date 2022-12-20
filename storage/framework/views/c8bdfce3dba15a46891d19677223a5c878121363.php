<?php $__env->startSection('content'); ?>
    <div class="">
        <div class="row">
            <form action="" method="get">
                <div class="col-lg-3">
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
                <div class="col-lg-3">
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
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> применить
                    </button>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="report-table-new"  class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2">-</th>
                            <th colspan="4">TOTAL</th>
                            <?php for($i = 1; $i <= $show_days; $i++): ?>
                                <th colspan="4"><?php echo e($i); ?></th>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <th style="background: url(/static/images/beeline.png) no-repeat; background-size: 90%" height="20px"></th>
                            <th style="background: url(/static/images/kcell.png) no-repeat; background-size: 90%"></th>
                            <th style="background: url(/static/images/tele2.png) no-repeat; background-size: 90%"></th>
                            <th style="background: url(/static/images/altel.png) no-repeat; background-size: 90%"></th>

                            <?php for($i = 1; $i <= $show_days; $i++): ?>
                                <th style="background: url(/static/images/beeline.png) no-repeat; background-size: 90%;  border-left-width: 5px;" height="20px"></th>
                                <th style="background: url(/static/images/kcell.png) no-repeat; background-size: 90%"></th>
                                <th style="background: url(/static/images/tele2.png) no-repeat; background-size: 90%"></th>
                                <th style="background: url(/static/images/altel.png) no-repeat; background-size: 90%"></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $gates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email=>$data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <?php if($email == 'TOTAL'): ?>
                            <td><?php echo e($email); ?></td>
                            <td style="background: yellow; border-left-width: 5px;" data-order="<?php echo e($data[\App\SmsDailyReport::$beeline]+$data[\App\SmsDailyReport::$beeline_gate]); ?>">
                                <?php echo e($data[\App\SmsDailyReport::$beeline]); ?>/
                                <?php echo e($data[\App\SmsDailyReport::$beeline_gate]); ?>

                            </td>
                            <td style="background: #00b7f4" data-order="<?php echo e($data[\App\SmsDailyReport::$kcell]+$data[\App\SmsDailyReport::$kcell_gate]); ?>">
                                <?php echo e($data[\App\SmsDailyReport::$kcell]); ?>/
                                <?php echo e($data[\App\SmsDailyReport::$kcell_gate]); ?>

                            </td>
                            <td style="background: gainsboro">
                                <?php echo e($data[\App\SmsDailyReport::$tele2_gate]); ?>

                            </td>
                            <td style="background: #ff6666">
                                <?php echo e($data[\App\SmsDailyReport::$altel_gate]); ?>

                            </td>
                        <?php else: ?>
                            <td><?php echo e($email); ?></td>
                            <td style="background: yellow;" data-order="<?php echo e(isset($gates['TOTAL'][$email])?($gates['TOTAL'][$email][\App\SmsDailyReport::$beeline]+$gates['TOTAL'][$email][\App\SmsDailyReport::$beeline_gate]):0); ?>">
                                <?php echo e(isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$beeline]:''); ?>/
                                <?php echo e(isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$beeline_gate]:''); ?>

                            </td>
                            <td style="background: #00b7f4" data-order="<?php echo e(isset($gates['TOTAL'][$email])?($gates['TOTAL'][$email][\App\SmsDailyReport::$kcell]+$gates['TOTAL'][$email][\App\SmsDailyReport::$kcell_gate]):0); ?>">
                                <?php echo e(isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$kcell]:''); ?>/
                                <?php echo e(isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$kcell_gate]:''); ?>

                            </td>
                            <td style="background: gainsboro">
                                <?php echo e(isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$tele2_gate]:''); ?>

                            </td>
                            <td style="background: #ff6666">
                                <?php echo e(isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$altel_gate]:''); ?>

                            </td>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $show_days; $i++): ?>
                            <td style="background: yellow; border-left-width: 5px;" data-order="<?php echo e(isset($data[$i])?($data[$i][\App\SmsDailyReport::$beeline]+$data[$i][\App\SmsDailyReport::$beeline_gate]):0); ?>">
                                <?php echo e(isset($data[$i])?$data[$i][\App\SmsDailyReport::$beeline]:''); ?>/
                                <?php echo e(isset($data[$i])?$data[$i][\App\SmsDailyReport::$beeline_gate]:''); ?>

                            </td>
                            <td style="background: #00b7f4" data-order="<?php echo e(isset($data[$i])?($data[$i][\App\SmsDailyReport::$kcell]+$data[$i][\App\SmsDailyReport::$kcell_gate]):0); ?>">
                                <?php echo e(isset($data[$i])?$data[$i][\App\SmsDailyReport::$kcell]:''); ?>/
                                <?php echo e(isset($data[$i])?$data[$i][\App\SmsDailyReport::$kcell_gate]:''); ?>

                            </td>
                            <td style="background: gainsboro">
                                <?php echo e(isset($data[$i])?$data[$i][\App\SmsDailyReport::$tele2_gate]:''); ?>

                            </td>
                            <td style="background: #ff6666">
                                <?php echo e(isset($data[$i])?$data[$i][\App\SmsDailyReport::$altel_gate]:''); ?>

                            </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- .animated -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/report_user.blade.php ENDPATH**/ ?>