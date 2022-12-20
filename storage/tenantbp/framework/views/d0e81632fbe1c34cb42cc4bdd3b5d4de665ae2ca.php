<?php $__env->startSection('content'); ?>
<style>
.login-page {
    background: #efefef;
    background: url('/images/login.jpg');
    background-size: cover;
}
.login-page .inner {
    background: #fefefe;
    width: 20%;
    min-width: 360px;
   
}
#sub-wrapper .login .login-content {
    width: 100%;
}
#sub-wrapper .login {
    padding: 0 !important;
}
#sub-wrapper .btn-form-login, #sub-wrapper .btn-form-registarion {
    width: 100%;
    max-width: 300px;
    border-radius: 5px;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 400;
}
</style>
<div class="login-page">

    <div class="inner">
        <div id="sub-wrapper">


            <div class="login">
                <div class="login-content">
                    <div class="login-panel">

                        <a href="/" class="">
                            <img src="/images/logo.png" style="max-width: 220px;">
                        </a>


                    </div>
             
                    <!-- <div style="margin: 10px;display:flex;justify-content:center">
                        <a href="/register" class="active">Регистрация </a>
                    </div> -->

                    <div class="tab-row fade-tabset">
                       
                        <div class="tab-content">
                            <div id="tab-30" class="tab <?php if(!isset($_GET['tab'])): ?> active <?php else: ?> js-tab-hidden  <?php endif; ?>">
                                <form class="form-horizontal" method="POST" action="<?php echo e(route('login')); ?>">
                                    <?php echo e(csrf_field()); ?>

                                    
                                    <div class="form-subregistration">
                                        <div class="form-registration-row">
                                            <input id="username" type="text" class="form-control" name="username"
                                                value="<?php echo e(old('username')); ?>" required autofocus
                                                placeholder="olegivanov@mail.ru">
                                            <?php if($errors->has('username')): ?>
                                            <span class="help-block">
                                                <strong><?php echo e($errors->first('username')); ?></strong>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-registration-row">
                                            <input id="password" type="password" class="form-control" name="password"
                                                required placeholder="Введите пароль">

                                            <?php if($errors->has('password')): ?>
                                            <span class="help-block">
                                                <strong><?php echo e($errors->first('password')); ?></strong>
                                            </span>
                                            <?php endif; ?>

                                        </div>
                                        <div class="form-registration-line">
                                            <input type="hidden" name="remember" value="1">


                                            <a id="openForgetPass">Восстановление пароля</a>
                                        </div>


                                        <button type="submit" class="btn-form-login">
                                            Войти
                                        </button>
                                    </div>
                                </form>
                            </div>
                    
                            <div id="forgetPass" class="js-tab-hidden">

                                <form class="form-registration" id="forget" action="#">
                                    <div class="form-subregistration">
                                        <div class="message">
                                        </div>
                                        <div class="form-registration-row form-registration-star">
                                            <input type="email" name="email" placeholder="Введите e-mail">
                                        </div>
                                        <br>
                                        <input type="submit" value="Восстановить пароль" class="btn-form-registarion">
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div id="sub-footer">
            <div class="sub-footer">
                <div class="col-lg-12 col-md-2 col-sm-2 col-xs-12">
                    <p class="copy">© 2022 jobtron.org</p>
                </div>

            </div>
        </div>
    </div> 

</div>



 



<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
delete_cookie('XSRF-TOKEN', '/', '.jobtron.org')

function delete_cookie( name, path, domain ) {
    document.cookie = name + "=" +
       
      ((path) ? ";Max-Age=0;path="+path:"")+
      ((domain)?";domain="+domain:"") +
      ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
}
</script> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/auth/login.blade.php ENDPATH**/ ?>