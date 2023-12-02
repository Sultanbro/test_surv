<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href=https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css>
    <link href=/js/home.js rel=preload as=script>
    <title>Jobtron</title>
</head>
<body>
    <div id="app"></div>
    <?php if(auth()->guard()->guest()): ?>
    <input type="hidden" name="csrf" id="csrf" value="">
    <?php else: ?>
    <?php echo csrf_field(); ?>
    <?php endif; ?>
    
    <script>
        window.Laravel = <?php echo json_encode($laravelToVue, 15, 512) ?>;
    </script>
    <script src=/js/home.js></script>
    <script>
            (function(w,d,u){
                    var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                    var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
            })(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_12_koodzo.js');
    </script>
</body>
</html>
<?php /**PATH /home/lemon/Development/Others/surv/resources/views/home.blade.php ENDPATH**/ ?>