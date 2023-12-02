<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jobtron.org</title>
        <link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

         <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <link rel="stylesheet" href="/static/new/css/auth.min.css?ver1.4231">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <?php echo $__env->yieldContent('head'); ?>
    </head>
    <body>
        <?php echo $__env->yieldContent('content'); ?>
        <script src="/static/new/js/auth.js?v=5"></script>
    </body>

     <?php echo $__env->yieldContent('scripts'); ?>
</html>
<?php /**PATH /home/lemon/Development/Others/surv/resources/views/layouts/auth.blade.php ENDPATH**/ ?>