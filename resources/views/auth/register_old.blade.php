@extends('layouts.auth')
@section('content')

    <div class="preloader">
        <div class="preloader__status">
            <div class="v-progress-circular v-progress-circular--indeterminate v-progress-circular--visible v-theme--dark text-primary" style="width: 50px; height: 50px;" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-v-67cad751="">
                <svg style="transform: rotate(calc(-90deg));" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43.47826086956522 43.47826086956522">
                    <circle class="v-progress-circular__underlay" fill="transparent" cx="50%" cy="50%" r="20" stroke-width="3.4782608695652177" stroke-dasharray="125.66370614359172" stroke-dashoffset="0"></circle>
                    <circle class="v-progress-circular__overlay" fill="transparent" cx="50%" cy="50%" r="20" stroke-width="3.4782608695652177" stroke-dasharray="125.66370614359172" stroke-dashoffset="125.66370614359172px"></circle>
                </svg>
            </div>
            <div class="preloader__status-text">Создаем портал</div>
        </div>
    </div>
    <div class="frontpage container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="frontpage__title">Включайся в работу и наслаждайся. Jobtron.org</h1>
            </div>
        </div>
    </div>
    <div class="login-page">

        <div class="inner">
            <div id="sub-wrapper">


                <div class="login">
                    <div class="login-content">
                        <div class="login-panel">

                            <a href="/" class="">
                                <img src="/images/logo.svg" style="max-width: 150px;">
                            </a>


                        </div>

                        <!-- <div style="margin: 10px;display:flex;justify-content:center">
                            <a href="/register" class="active">Регистрация </a>
                        </div> -->

                        <div class="tab-row fade-tabset">

                            <div class="tab-content">
                                <div id="tab-30" class="tab @if(!isset($_GET['tab'])) active @else js-tab-hidden  @endif">

                                    <h3 class="text-center">Регистрация</h3>
                                    <form id="register-form" class="form-horizontal" method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="form-subregistration">

                                            <div class="form-registration-row">
                                                <input id="name" type="text" class="form-control" name="name"
                                                       value="{{ old('name') }}" required autofocus
                                                       placeholder="Ваше имя">

                                            </div>
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif

                                            <div class="form-registration-row">
                                                <input id="last_name" type="text" class="form-control" name="last_name"
                                                       value="{{ old('last_name') }}" required autofocus
                                                       placeholder="Ваша фамилия">

                                            </div>
                                            @if ($errors->has('last_name'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                            @endif

                                            <div class="form-registration-row">
                                                <input id="email" type="text" class="form-control" name="email"
                                                       value="{{ old('email') }}" required autofocus
                                                       placeholder="Ваш email">

                                            </div>
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                            @endif

                                            <div class="form-registration-row">
                                                <input id="phone" type="text" class="form-control" name="phone"
                                                       value="{{ old('phone') }}" required autofocus
                                                       placeholder="Ваш телефон">

                                            </div>
                                            @if ($errors->has('phone'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                            @endif

                                            <div class="form-registration-row">
                                                <input id="password" type="password" class="form-control" name="password"
                                                       required placeholder="Введите пароль">
                                            </div>
                                            <div class="form-registration-row">
                                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                                                       required placeholder="Введите пароль повторно">
                                            </div>
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                            @endif

                                            <div class="form-registration-row">
                                                <select id="currency" name="currency" required class="chosen-select">
                                                    <option value="">Выберите Валюту</option>
                                                    <option value="kzt">KZT Казахстанский тенге</option>
                                                    <option value="rub">RUB Российский рубль</option>
                                                    <option value="usd">USD Американский доллар</option>
                                                </select>
                                            </div>

                                            @if(config('services.recaptcha.key'))
                                                <div class="g-recaptcha"
                                                     data-sitekey="{{ config('services.recaptcha.key') }}">
                                                </div>
                                            @endif

                                            <div class="form-subregistration-text">
                                                <label for="pravila" class="control control-checkbox" style="font-weight: normal;">
                                                    <input type="checkbox" name="pravila" id="pravila" checked="checked" required>
                                                    <div class="control_indicator"></div>
                                                    Я согласен(а) с правилами использования сервиса
                                                </label>
                                                <p>Регистрируясь, я подтверждаю, что принимаю <a href="https://jobtron.org/aggreement" target="_blank">Пользовательское соглашение</a>,
                                                    ознакомлен с <a href="https://jobtron.org/offer" target="_blank">договором оферты</a> и <a href="https://jobtron.org/terms" target="_blank">Политикой конфеденциальности.</a>
                                                </p>
                                            </div>

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
                    <div class="col-lg-12 col-xs-12">
                        <p class="copy">© 2022 jobtron.org</p>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection


@section('head')
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



        @font-face {
            font-family: 'Raleway';
            font-style: normal;
            font-weight: 400;
            src: url(https://fonts.gstatic.com/s/raleway/v28/1Ptxg8zYS_SKggPN4iEgvnHyvveLxVvaorCIPrQ.ttf) format('truetype');
        }
        .preloader {
            display: none;
            width: 100vw;
            height: 100vh;

            flex-direction: column;
            justify-content: center;
            align-items: center;

            position: fixed;
            z-index: 9995;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            background: #3498db;
            color: white;
            font-family: 'Raleway', sans-serif;
        }
        .preloader_active{
            display: flex;
        }
        .preloader__status {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .preloader__status-text {
            font-size: 40px;
            font-family: 'Raleway', sans-serif;
            margin-bottom: 20px;
        }
        .preloader__status-loader {
            width: 100%;
            height: 3px;
        }
        .preloader__status-bar {
            background: white;
            height: 100%;
        }
        .v-progress-circular{
            align-items: center;
            display: inline-flex;
            justify-content: center;
            position: relative;
            vertical-align: middle;
            color: rgb(48, 63, 159);
            margin: 1rem;
        }
        .v-progress-circular > svg{
            width: 100%;
            height: 100%;
            margin: auto;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 0;
        }

        @keyframes progress-circular-dash {
            0% {
                stroke-dasharray:1,200;
                stroke-dashoffset:0px
            }
            50% {
                stroke-dasharray:100,200;
                stroke-dashoffset:-15px
            }
            to {
                stroke-dasharray:100,200;
                stroke-dashoffset:-124px
            }
        }
        @keyframes progress-circular-rotate {
            to {
                transform:rotate(270deg)
            }
        }

        .v-progress-circular--indeterminate > svg{
            animation: progress-circular-rotate 1.4s linear infinite;
            transform-origin: center center;
            transition: all .2s ease-in-out;
        }
        .v-progress-circular__underlay{
            color: rgba(255, 255, 255, 0.12);
            stroke: currentColor;
            z-index: 1;

        }
        .v-progress-circular__overlay {
            stroke: currentColor;
            transition: all .2s ease-in-out,stroke-width 0s;
            z-index: 2;
            animation: progress-circular-dash 1.4s ease-in-out infinite;
            stroke-dasharray: 25,200;
            stroke-dashoffset: 0;
            stroke-linecap: round;
        }

        .frontpage {
            width: 100vw;
            height: 100vh;

            position: fixed;
            z-index: 9996;
            top: 100vh;
            left: 0;

            background: #34495e;
        }
        .frontpage__title {
            color: white;
        }

        /* captcha */
        .g-recaptcha {
            transform: scale(0.97) translate(-5px, 0);
        }
    </style>
@endsection


@section('scripts')
    <script></script>
@endsection
