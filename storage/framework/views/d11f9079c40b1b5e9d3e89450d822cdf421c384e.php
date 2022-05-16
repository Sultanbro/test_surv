<!doctype html>
<html class="no-js" lang="ru"> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta name="description" content="Mediasend.kz Управление">
    <meta name="viewport" content="width=800, initial-scale=0.26">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/css/style.css">
    <link rel="stylesheet" href="/admin/css/custom.css">
    <link rel="stylesheet" href="/css/admin/app.css">
    <link rel="stylesheet" href="/admin/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>

    <?php echo $__env->yieldContent('head'); ?>
    <?php echo $__env->yieldContent('styles'); ?>

    
</head>
<body>



<div id="app" class="right-panel right-panel-app d-flex">

    <?php echo $__env->make('layouts.side_menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        
    <div class="page">

        <div id="header" class="header" style="display:none">

            <div class="header-menu d-flex justify-content-between">

                <div class="mr-3" style="width: 100%">
                    <div id="cabinetjs">
                        <timetracking activeuserid="<?php echo e(json_encode(auth()->user()->ID)); ?>"
                                    usertype="<?php echo e(auth()->user()->user_type); ?>"
                                    program="<?php echo e(auth()->user()->program_id); ?>"
                                    user_type="<?php echo e(auth()->user()->user_type); ?>"
                                    position_id="<?php echo e(auth()->user()->position_id); ?>"></timetracking>

                    </div>

                </div>
                <div class=" d-flex justify-content-end">
                    <?php echo $__env->make('includes.admin_notifications', [
                            'unread_notifications' => $unread_notifications,
                            'read_notifications' => $read_notifications,
                            'unread' => $unread,
                            'head_users' => $head_users,
                            'bonus_notification' => $bonus_notification,
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <div class="user-area dropdown " style="display: flex; align-items: center;">
                        <profile user="<?php echo e(json_encode(auth()->user())); ?>"></profile>
                    </div>

                </div>
            </div>
        </div>

        <div class="content-wrap">
             
         

            <div class="cont">
                <?php echo $__env->yieldContent('content'); ?>
            </div>

                 
          
        </div>
    </div>
    <notifications group="foo" />


</div>



<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
<!-- <script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>  -->
<script src="/js/app.js"></script>


<?php echo $__env->make('includes.admin_notifications_css_js', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->yieldContent('scripts'); ?>

<?php echo $__env->make('layouts.admin_scripts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


<?php if($reminder): ?>
  <?php echo $__env->make('includes.reminder', ['unread' => $unread], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>

<?php if($bonus_notification): ?>
  <?php echo $__env->make('includes.bonus_notification', ['bonus_notification' => $bonus_notification], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>
    
</body>
</html>