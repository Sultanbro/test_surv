<?php $__env->startSection('content'); ?>

<div class="cabinet-loader-bg">
    <div class="cabinet-loader"></div>
    <div class="cabinet-loader-message">
        Ваш кабинет создается ...
    </div>
</div>
<div class="login-page">

    <div class="inner">
        <div id="sub-wrapper">


            <div class="login">
                <div class="login-content">
                    <div class="login-panel">

                        <a href="/" class="">
                            <img src="https://bp.jobtron.org/admin/images/logo.png" style="max-width: 150px;">
                        </a>


                    </div>

                    <!-- <div style="margin: 10px;display:flex;justify-content:center">
                        <a href="/register" class="active">Регистрация </a>
                    </div> -->

                    <div class="tab-row fade-tabset">

                        <div class="tab-content">
                            <div id="tab-30" class="tab <?php if(!isset($_GET['tab'])): ?> active <?php else: ?> js-tab-hidden  <?php endif; ?>">

                                <h3 class="text-center">Регистрация</h3>
                                <form id="register-form" class="form-horizontal" method="POST" action="<?php echo e(route('register')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-subregistration">

                                        <div class="form-registration-row">
                                            <input id="name" type="text" class="form-control" name="name"
                                                value="<?php echo e(old('name')); ?>" required autofocus
                                                placeholder="Ваше имя">

                                        </div>
                                        <?php if($errors->has('name')): ?>
                                            <span class="help-block">
                                                <strong><?php echo e($errors->first('name')); ?></strong>
                                            </span>
                                        <?php endif; ?>

                                        <div class="form-registration-row">
                                            <input id="last_name" type="text" class="form-control" name="last_name"
                                                value="<?php echo e(old('last_name')); ?>" required autofocus
                                                placeholder="Ваша фамилия">

                                        </div>
                                        <?php if($errors->has('last_name')): ?>
                                            <span class="help-block">
                                                <strong><?php echo e($errors->first('last_name')); ?></strong>
                                            </span>
                                        <?php endif; ?>

                                        <div class="form-registration-row">
                                            <input id="email" type="text" class="form-control" name="email"
                                                value="<?php echo e(old('email')); ?>" required autofocus
                                                placeholder="Ваш email">

                                        </div>
                                        <?php if($errors->has('email')): ?>
                                            <span class="help-block">
                                                <strong><?php echo e($errors->first('email')); ?></strong>
                                            </span>
                                        <?php endif; ?>

                                        <div class="form-registration-row">
                                            <input id="phone" type="text" class="form-control" name="phone"
                                                value="<?php echo e(old('phone')); ?>" required autofocus
                                                placeholder="Ваш телефон">

                                        </div>
                                        <?php if($errors->has('phone')): ?>
                                            <span class="help-block">
                                                <strong><?php echo e($errors->first('phone')); ?></strong>
                                            </span>
                                        <?php endif; ?>

                                        <div class="form-registration-row">
                                            <input id="password" type="password" class="form-control" name="password"
                                                required placeholder="Введите пароль">
                                        </div>
                                        <div class="form-registration-row">
                                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                                                required placeholder="Введите пароль повторно">
                                        </div>
                                        <?php if($errors->has('password')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('password')); ?></strong>
                                        </span>
                                        <?php endif; ?>

                                        <?php if(config('services.recaptcha.key')): ?>
                                            <div class="g-recaptcha"
                                                data-sitekey="<?php echo e(config('services.recaptcha.key')); ?>">
                                            </div>
                                        <?php endif; ?>



                                        <button type="submit" class="btn-form-login">
                                            Зарегистрироваться
                                        </button>
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


<?php $__env->startSection('head'); ?>
<script src='https://www.google.com/recaptcha/api.js'></script>

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



.cabinet-loader::before, .cabinet-loader::after, .cabinet-loader {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}

.cabinet-loader {
  width: 200px;
  height: 200px;
  margin: auto;
  background: url("/static/img/logo-jobtron.png") no-repeat center rgba(0, 0, 0, 0.1);
  background-size: 80%;
  color: #42aaff;
  box-shadow: inset 0 0 0 1px rgba(66, 170, 255, 0.5);
}
.cabinet-loader::before, .cabinet-loader::after {
  content: "";
  z-index: -1;
  margin: -5%;
  box-shadow: inset 0 0 0 2px;
  animation: clipMe 8s linear infinite;
}
.cabinet-loader::before {
  animation-delay: -4s;
}

@keyframes clipMe {
  0%, 100% {
    clip: rect(0px, 220px, 2px, 0px);
  }
  25% {
    clip: rect(0px, 2px, 220px, 0px);
  }
  50% {
    clip: rect(218px, 220px, 220px, 0px);
  }
  75% {
    clip: rect(0px, 220px, 220px, 218px);
  }
}
.cabinet-loader-bg{
    position: fixed;
    z-index: 999;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #0f222b;

    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}
.cabinet-loader-bg.active{
    opacity: 1;
    visibility: visible;
}
.cabinet-loader-message{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, 150px);
    font-size: 24px;
    color: #fff;
}

.g-recaptcha {
    width: 100%;
    transform: scale(0.8) translate(-15px, -1px);
}
</style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<script></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/auth/register.blade.php ENDPATH**/ ?>