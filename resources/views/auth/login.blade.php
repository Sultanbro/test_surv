@extends('layouts.auth')

@section('content')



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
                            <img src="https://bp.jobtron.org/admin/images/logo.png" style="max-width: 150px;">
                        </a>


                    </div>
             
                    <!-- <div style="margin: 10px;display:flex;justify-content:center">
                        <a href="/register" class="active">Регистрация </a>
                    </div> -->

                    <div class="tab-row fade-tabset">
                       
                        <div class="tab-content">
                            <div id="tab-30" class="tab @if(!isset($_GET['tab'])) active @else js-tab-hidden  @endif">
                                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    {{ csrf_field() }}
                                    
                                    <div class="form-subregistration">
                                        <div class="form-registration-row">
                                            <input id="username" type="text" class="form-control" name="username"
                                                value="{{ old('username') }}" required autofocus
                                                placeholder="olegivanov@mail.ru">
                                            @if ($errors->has('username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('username') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-registration-row">
                                            <input id="password" type="password" class="form-control" name="password"
                                                required placeholder="Введите пароль">

                                            @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                            @endif

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







@endsection

@section('scripts')
<script>

</script>
@endsection