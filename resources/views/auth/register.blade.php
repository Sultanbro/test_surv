@extends('layouts.auth')
@section('content')

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
                                            <input id="password" type="password" class="form-control" name="password_confirmation"
                                                required placeholder="Введите пароль повторно">
                                        </div>
                                        @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif

                                        @if(config('services.recaptcha.key'))
                                            <div class="g-recaptcha"
                                                data-sitekey="{{config('services.recaptcha.key')}}">
                                            </div>
                                        @endif



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

</style>
@endsection


@section('scripts')
<script></script>
@endsection