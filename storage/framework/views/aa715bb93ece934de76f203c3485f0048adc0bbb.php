<?php $__env->startSection('content'); ?>
    <div class="">
        <div class="row">
            <div class="col-lg-12">
                <table id="report-table-new" class="table table-striped table-bordered" data-url="/bonus" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Почта</th>
                        <th>Имя</th>
                        <th>Бонус</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($user->id); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->bonus); ?></td>
                            <td><a href="/bonus/update/<?php echo e($user->id); ?>">Добавить бонус</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/bonus.blade.php ENDPATH**/ ?>