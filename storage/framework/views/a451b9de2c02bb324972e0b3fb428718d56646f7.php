<!DOCTYPE html>
<html lang="<?php echo e(config('app.locale')); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Выбор времени собеседования</title>
        <link rel="stylesheet" href="/static/css/bootstrap.min.css">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

        <?php echo $__env->make('recruiting._css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>
    <body>
    <div class="container shadow bg-white">
        <div class="image">
            <img src="/images/logobp.png" alt="">
        </div>



        <?php if($view == '1'): ?>
        <div class="page-header">
            <h1 class="page-title">Запись на собеседование</h1>
        </div>
        <div class="page-content">

            <div class="row">


                
                <div class="col-md-12 mb">
                    <form action="/bp/choose_time" method="post">
                   
                        <div class="row mb items-center" style="flex-wrap:wrap">
                            <div class="col-12 col-lg-6">
                                <p><b>Выберите удобное для вас время:</b></p>
                            </div>
                            <div class="col-12 col-lg-6">

                                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="days">
                                    <input type="radio" name="time"  value="<?php echo e($day['value']); ?>" id="day<?php echo e($loop->iteration); ?>" <?php if($loop->index == 0): ?> selected <?php endif; ?>>
                                    <label for="day<?php echo e($loop->iteration); ?>"><?php echo e($day['text']); ?></label>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                            </div>
                        </div> 

                 
                        <?php echo e(csrf_field()); ?>


                        <input type="hidden" name="hash" value="<?php echo e($hash); ?>">

                        <button class="btm btn-success" id="submit">
                            Записаться
                        </button>
                    </form>
                    
                </div>
                
            </div>
        </div>
        <?php else: ?>    
        <div class="page-header">
            <h1 class="page-title">Успешно!</h1>
        </div>
        <div class="page-content">

            <div class="row">

                <div class="col-md-12">
                    <div><?php echo $msg; ?></div>
                </div>
                
            </div>
        </div>                

        <?php endif; ?>
        
        <script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
        <script>
            document.getElementById("submit").onclick = function(e) {
                if (document.getElementById("skype").value == "" || document.getElementById("skype").value.length < 8) {
                    e.preventDefault();
                    alert("Пожалуйста, напишите логин скайпа!");
                    return null;
                }
            }

            $('.days input[type=radio]').change(function(){

            });

            $('.times input[type=radio]').change(function(){
                
                

                var firstDate = new Date(); // Todays date
                var secondDate = new Date(2021,5,21, parseFloat($(this).val()) ,00,00);

                var diffDays = (firstDate.getHours() - secondDate.getHours()); 

                
                //console.log(diffDays)

                $(this).parent().find('button').prop('disabled', false);
            });
        </script>    

        
    </body>
</html>
<?php /**PATH /var/www/job/resources/views/recruiting/choose_time.blade.php ENDPATH**/ ?>