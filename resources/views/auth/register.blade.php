@extends('layouts.auth')
@section('content')

    <div class="preloader">
        <div class="preloader__status">
            <div class="v-progress-circular v-progress-circular--indeterminate v-progress-circular--visible v-theme--dark text-primary"
                 style="width: 50px; height: 50px;" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                 data-v-67cad751="">
                <svg style="transform: rotate(calc(-90deg));" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 43.47826086956522 43.47826086956522">
                    <circle class="v-progress-circular__underlay" fill="transparent" cx="50%" cy="50%" r="20"
                            stroke-width="3.4782608695652177" stroke-dasharray="125.66370614359172"
                            stroke-dashoffset="0"></circle>
                    <circle class="v-progress-circular__overlay" fill="transparent" cx="50%" cy="50%" r="20"
                            stroke-width="3.4782608695652177" stroke-dasharray="125.66370614359172"
                            stroke-dashoffset="125.66370614359172px"></circle>
                </svg>
            </div>
            <div class="preloader__status-text">Создаем Ваш портал</div>
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
                                <div id="tab-30"
                                     class="tab @if(!isset($_GET['tab'])) active @else js-tab-hidden  @endif">

                                    <h3 class="text-center">Регистрация</h3>
                                    <form id="register-form" class="form-horizontal">
                                        @csrf
                                        <div class="form-subregistration">

                                            <div class="form-registration-row">
                                                <input id="name" type="text"
                                                       class="form-control"
                                                       name="name"
                                                       value="{{ old('name') }}" required autofocus
                                                       placeholder="Ваше имя">
                                                <span class="req">*</span>
                                            </div>

                                            <div class="form-registration-row">
                                                <input id="last_name" type="text"
                                                       class="form-control"
                                                       name="last_name"
                                                       value="{{ old('last_name') }}" autofocus
                                                       placeholder="Ваша фамилия">
                                            </div>

                                            <div class="form-registration-row">
                                                <input id="email" type="text"
                                                       class="form-control"
                                                       name="email"
                                                       value="{{ old('email') }}" required autofocus
                                                       placeholder="Ваш email">
                                                <span class="req">*</span>
                                            </div>
                                            <div class="help-block email">
                                                <span>Значение поля email должно быть действительным электронным адресом.</span>
                                            </div>

                                            <div class="form-registration-row">
                                                <input id="phone" type="number"
                                                       class="form-control"
                                                       name="phone"
                                                       value="{{ old('phone') }}" required autofocus
                                                       placeholder="Ваш телефон">
                                                <span class="req">*</span>
                                            </div>
                                            <div class="help-block phone">
                                                <span>Количество цифр в номере телефона должно быть не меньше 11.</span>
                                                <b></b>
                                            </div>


                                            <div class="form-registration-row">
                                                <input id="password" type="password"
                                                       class="form-control"
                                                       name="password"
                                                       required placeholder="Введите пароль">
                                                <span class="req">*</span>
                                            </div>
                                            <div class="help-block password">
                                                <span>Количество символов в пароле должно быть не меньше 8.</span>
                                                <b></b>
                                            </div>
                                            <div class="form-registration-row">
                                                <input id="password_confirmation" type="password"
                                                       class="form-control"
                                                       name="password_confirmation"
                                                       required placeholder="Введите пароль повторно">
                                                <span class="req">*</span>
                                            </div>
                                            <div class="help-block password_confirmation">
                                                <span>Пароли не совпадают</span>
                                            </div>

                                            <div class="form-registration-row">
                                                <select id="currency" name="currency" required class="chosen-select">
                                                    <option value="">Выберите Валюту</option>
                                                    <option value="kzt">KZT Казахстанский тенге</option>
                                                    <option value="rub">RUB Российский рубль</option>
                                                    <option value="usd">USD Американский доллар</option>
                                                </select>
                                                <span class="req">*</span>
                                            </div>

                                            @if(config('services.recaptcha.key'))
                                                <div class="g-recaptcha"
                                                     data-sitekey="{{ config('services.recaptcha.key') }}">
                                                </div>
                                            @endif
                                            <br>

                                            <div class="form-subregistration-text">
                                                <label for="pravila" class="control control-checkbox"
                                                       style="font-weight: normal;">
                                                    <input type="checkbox" name="pravila" id="pravila" checked="checked"
                                                           required>
                                                    <div class="control_indicator"></div>
                                                    Я согласен(а) с правилами использования сервиса
                                                </label>
                                                <p>Регистрируясь, я подтверждаю, что принимаю <a
                                                            href="https://jobtron.org/aggreement" target="_blank">Пользовательское
                                                        соглашение</a>,
                                                    ознакомлен с <a href="https://jobtron.org/offer" target="_blank">договором
                                                        оферты</a> и <a href="https://jobtron.org/terms"
                                                                        target="_blank">Политикой
                                                        конфеденциальности.</a>
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
                        <p class="copy">© 2024 jobtron.org</p>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection


@section('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <style>
        input[name="phone"]::-webkit-outer-spin-button,
        input[name="phone"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
        .help-block {
            color: red;
            display: block;
            transition: 0.5s all ease-in-out;
            max-height: 0;
            overflow: hidden;
            margin: 0;
        }
        .help-block.show {
            max-height: 200px;
            padding-bottom: 10px;
        }
        .form-registration-row{
            position: relative;
        }
        .form-registration-row .req{
            color: red;
            position: absolute;
            top: 12px;
            left: -20px;
            font-size: 20px;
        }
        .form-registration-row::before {
            transition: 0.5s all ease;
        }
        .form-registration-row .form-control {
            transition: 0.5s all ease;
        }
        .form-registration-row.error::before {
            background: #ff695f !important;
        }
        .form-registration-row.error .form-control,
        .form-registration-row.error .chosen-select {
            border: 1px solid red !important;
        }
        .help-block strong {
            font-weight: 400 !important;
        }
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
            display: flex;
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
            opacity: 0;
            visibility: hidden;
            background: #3498db;
            color: white;
            font-family: 'Raleway', sans-serif;
            transition: 0.3s all ease;
        }
        .preloader_active {
            opacity: 1;
            visibility: visible;
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
        .v-progress-circular {
            align-items: center;
            display: inline-flex;
            justify-content: center;
            position: relative;
            vertical-align: middle;
            color: rgb(48, 63, 159);
            margin: 1rem;
        }
        .v-progress-circular > svg {
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
                stroke-dasharray: 1, 200;
                stroke-dashoffset: 0px
            }
            50% {
                stroke-dasharray: 100, 200;
                stroke-dashoffset: -15px
            }
            to {
                stroke-dasharray: 100, 200;
                stroke-dashoffset: -124px
            }
        }
        @keyframes progress-circular-rotate {
            to {
                transform: rotate(270deg)
            }
        }
        .v-progress-circular--indeterminate > svg {
            animation: progress-circular-rotate 1.4s linear infinite;
            transform-origin: center center;
            transition: all .2s ease-in-out;
        }
        .v-progress-circular__underlay {
            color: rgba(255, 255, 255, 0.12);
            stroke: currentColor;
            z-index: 1;
        }
        .v-progress-circular__overlay {
            stroke: currentColor;
            transition: all .2s ease-in-out, stroke-width 0s;
            z-index: 2;
            animation: progress-circular-dash 1.4s ease-in-out infinite;
            stroke-dasharray: 25, 200;
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