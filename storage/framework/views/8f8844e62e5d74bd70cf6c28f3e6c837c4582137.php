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

@media (max-width: 450px) {
.login-page .inner {
    width: 100%;
}
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


            <div class="login login-height">
                <div class="login-content">
                    <div class="login-panel">

                        <a href="/" class="">
                            <img src="/images/logo.svg" style="max-width: 220px;">
                        </a>


                    </div>

                    <!-- <div style="margin: 10px;display:flex;justify-content:center">
                        <a href="/register" class="active">Регистрация </a>
                    </div> -->

                    <div class="tab-row fade-tabset">

                        <div class="tab-content">
                            <div id="tab-30" class="tab <?php if(!isset($_GET['tab'])): ?> active <?php else: ?> js-tab-hidden  <?php endif; ?>">
                                <form id="login" class="form-horizontal" method="POST" action="<?php echo e(route('login')); ?>">
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
                                <form class="form-registration" id="forget" action="#">
                                    <div class="form-subregistration">
                                        <div

class="message">
                                        </div>
                                    </div>

                                    <div id="sub-footer">
                                        <div class="sub-footer">
                                            <div class="col-lg-12 col-xs-12">
                                                <p class="copy">© 2023 jobtron.org</p>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>







                            <?php $__env->stopSection(); ?>

                            <?php $__env->startSection('scripts'); ?>
                            <script>
	                            document.addEventListener("DOMContentLoaded", () => {
		                            if (window.location.pathname === '/login' && window.location.protocol === 'https:') {
			                            const hostArr = window.location.hostname.split('.');
			                            if(hostArr.length > 2 && !~['bp', 'admin'].indexOf(hostArr[0])){
				                            window.location.href = `https://${hostArr[1]}.${hostArr[2]}/login`;
			                            }
		                            }
	                            });
                            delete_cookie('XSRF-TOKEN', '/', '.jobtron.org');
                            function delete_cookie( name, path, domain ) {
                                document.cookie = name + "=" +
                                  ((path) ? ";Max-Age=0;path="+path:"")+
                                  ((domain)?";domain="+domain:"") +
                                  ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
                            }
                            (function(){
                                var $loginForm = $('#login');
                                var loginUrl = '<?php echo e(route('login')); ?>';
                                var $loginButton = $loginForm.find('.btn-form-login');
                                function createLinks(links){
                                    return links.reduce((result, item) => {
                                        return `${result}<a href="${item.link}" class="list-group-item">${item.id}</a>`
                                    }, '')
                                }
                                $loginForm.on('submit', function(event){
                                    event.preventDefault();
                                    var formData = new FormData($loginForm.get(0));
                                    var data = {};
                                    formData.forEach(function(value, key){
                                        data[key] = value;
                                    });
                                    $('.help-block').remove();
                                    $loginButton.prop({disabled: true});
                                    $.ajax({
                                        url: loginUrl,
                                        data: data,
                                        processData: true,
                                        type: 'POST',
                                        cache: false,
                                        success: function (dataset) {
                                            if(dataset.link) return window.location.replace(dataset.link);
                                            if(dataset.links) {
                                                $loginForm.after('<div class="list-group mt-4">' + createLinks(dataset.links) + '</div>');
                                            }
                                        },
                                        error: function (response) {
                                            if(response.status === 422) {
                                                for(var inputName in response.responseJSON.errors){
                                                    var errorMessage = response.responseJSON.errors[inputName];
                                                    $('#' + inputName)
                                                        .closest('.form-registration-row')
                                                        .append('<span class="help-block"><strong>' + errorMessage + '</strong></span>');
                                                }
                                                return;
                                            }
                                            if(response.status === 401) {
                                                alert('Введенный email или пароль не совпадает');
                                                return;
                                            }
                                            alert('Ошибка на стороне сервера');
                                        },
                                    });
                                });
                            })();
                            </script>
                            <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lemon/Development/Others/surv/resources/views/auth/login.blade.php ENDPATH**/ ?>