<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Mediasend.kz Управление">
    <meta name="viewport" content="width=500, initial-scale=0.26">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <title>Отметка стажеров</title>

    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>

    <style>
        .special {
            display: block;
            margin-bottom: 10px;
            border: 0;
            cursor: pointer;
            outline: 0 !important;
            font-size: 15px;
        }
        h1 {
            font-size: 1.5rem;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Присутствующие</h1>
            <p>Найдите себя в списке и нажмите для отметки:</p>
            <buttom>Старт отметки</buttom>
        </div>

     

        
        <div class="col-lg-12">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <form action="" method="post">
                <div class="row">
                    <div class="col-lg-4">
                        <button class="btn btn-primary btn-sm special" href="/autochecker/<?php echo e($group->id); ?>" ><?php echo e($group->name); ?></button>
                    </div>
                    <?php if($group->checktime): ?>
                    <div class="col-lg-6">
                        Закроется в: <b><?php echo e($group->checktime->format('H:i:s')); ?></b>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php echo e(csrf_field()); ?>

            </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        

    </div>

</div>


</body>
</html><?php /**PATH /var/www/job/resources/views/admin/autocheck/autochecker.blade.php ENDPATH**/ ?>