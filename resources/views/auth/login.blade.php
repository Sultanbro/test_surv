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
                            <img src="https://admin.u-marketing.org/admin/images/logo.png" style="max-width: 150px;">
                        </a>


                    </div>

                    <div class="tab-row fade-tabset">
                        <ul class="tabset">
                            <li><a href="#tab-30" class="@if(!isset($_GET['tab'])) active @endif">Вход</a></li>

                            @if ($_SERVER['HTTP_HOST'] == 'cp.u-marketing.org')
                            <li><a href="#tab-31"
                                    class="@if(isset($_GET['tab']) && $_GET['tab'] == 'register') active  @endif">Регистрация</a>
                            </li>
                            @endif

                        </ul>
                        <div class="tab-content">
                            <div id="tab-30" class="tab @if(!isset($_GET['tab'])) active @else js-tab-hidden  @endif">
                                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    {{ csrf_field() }}
                                    <div class="form-subregistration">
                                        <div class="form-registration-row">
                                            <input id="email" type="email" class="form-control" name="email"
                                                value="{{ old('email') }}" required autofocus
                                                placeholder="olegivanov@mail.ru">
                                            @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
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
                            <div id="tab-31"
                                class="tab @if(isset($_GET['tab']) && $_GET['tab'] == 'register') active @else js-tab-hidden @endif">

                                <form class="form-registration" id="registration" action="{{ route('register') }}"
                                    method="post">
                                    <div class="form-subregistration">
                                        {{ csrf_field() }}
                                        <div class="message">
                                        </div>
                                        @if ($errors->has('email'))
                                        <div class="message">
                                            Пользователь с таким email существует
                                        </div>
                                        @endif

                                        <div class="form-registration-row form-registration-star">
                                            <input type="email" name="email" placeholder="olegivanov@mail.ru" required>

                                        </div>
                                        <div class="form-registration-row">
                                            <input type="text" name="name" placeholder="Ваше имя" required>
                                        </div>
                                        <div class="form-registration-row form-registration-star">
                                            <input type="text" name="phone" placeholder="Телефон" required>
                                        </div>
                                        <div class="form-registration-row form-registration-star">
                                            <input type="password" name="password" placeholder="Введите пароль"
                                                required>
                                        </div>
                                        <div class="form-registration-row form-registration-star">
                                            <input type="password" name="password_confirmation"
                                                placeholder="Повторите пароль" required>
                                        </div>
                                        <div class="form-registration-row form-registration-star">
                                            <select name="currency" required class="chosen-select">
                                                <option value="">Выберите Валюту</option>
                                                <option value="kzt">KZT</option>
                                                <option value="rub">RUB</option>
                                            </select>
                                        </div>
                                        <div class="form-registration-row form-registration-star">
                                            <select name="timezone" required class="chosen-select">
                                                <option value="">Выберите таймзону</option>
                                                @foreach(\App\Setting::TIMEZONES as $offset => $timezone)
                                                <option value="{{$offset}}">{{$timezone}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <br>
                                        <input type="submit" value="Зарегистрироваться" class="btn-form-registarion">

                                    </div>
                                    <div class="form-subregistration-text">
                                        <label for="pravila" class="control control-checkbox"
                                            style="font-weight: normal;">
                                            <input type="checkbox" name="pravila" id="pravila" checked="checked">
                                            <div class="control_indicator"></div>
                                            Я согласен(а) с правилами использования сервиса
                                        </label>
                                        <p>Регистрируясь, я подтверждаю, что принимаю Пользовательское соглашение,
                                            ознакомлен с договором оферты и Политикой конфеденциальности.
                                        </p>
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
                    <p class="copy">© 2022 surv.u-marketing.org</p>
                </div>

            </div>
        </div>
    </div>

</div>







@endsection