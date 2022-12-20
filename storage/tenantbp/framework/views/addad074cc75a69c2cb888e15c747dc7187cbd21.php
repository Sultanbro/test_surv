<?php $__env->startSection('title', 'Настройка кабинета'); ?>
<?php $__env->startSection('content'); ?>

<div class="container my-3">
    <div class="row">

        <div class="card col-12 mb-3">
        <div class=" d-flex justify-content-between my-2">
            Мои проекты
       
        <?php if(auth()->id() == 1): ?>
        <a class="btn btn-primary " href="/projects/create">Создать</a>
        <?php endif; ?>
        </div>
        </div>
       
        <div class="col-12 card mb-3">
            <table class="table">
                <tr>
                    <th>Субдомен проекта</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            
                <?php $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($tenant->id); ?></td>
                    <td><?php echo e($tenant->created_at); ?></td>
                    <td>
                        <?php if(auth()->id() == $tenant->global_id): ?>
                        <a class="btn btn-primary mr-2" href="/projects/edit/<?php echo e($tenant->id); ?>">Изменить</a>
                        <?php endif; ?>
                        <a class="btn btn-primary" href="/enter/<?php echo e($tenant->id); ?>">Войти</a>
                    </td>
                </tr>

                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </div>
     
    </div>
    
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.tenant', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/tenants/index.blade.php ENDPATH**/ ?>