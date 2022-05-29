<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="/bitrix/templates/account/favicon.ico" />
    <meta name="viewport" content="width=1260">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Автозвонки</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/plugins/fancybox/src/css/core.css">
    <link rel="stylesheet" href="/static/plugins/datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/static/plugins/datatables/datatables.min.css">
    <link rel="stylesheet" href="/static/css/chosen.css?v=1.4">
    <link rel="stylesheet" href="/static/css/audio.css?v=1.2">
    <link rel="stylesheet" href="/static/css/adaptive.css?v=16">
    <link rel="stylesheet" href="/static/css/app.css?v=15">
    <link rel="stylesheet" href="/static/css/jquery.mCustomScrollbar.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic" rel="stylesheet">
    <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/sunny/jquery-ui.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
          crossorigin="anonymous">
</head>

<body>
<div id="wrapper">
    <div id="sidebar" class="active active_no_transition">
        <a href="/" class="logo">logo</a>
        <strong>Меню</strong>
        <ul class="custom-accordion">
            <li class=""><a href="/contact/index" class="item-1 root-item">Контакты</a></li>
            <li class="root-item {{$menu=='autocall'?'active':''}}"><a href="/account/forwards/" class="opener-ac item-2 pener-ac "><span>Рассылка</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/sms/index">SMS рассылка</a></li>
                        <li><a href="/autocalls/index" class="{{$menu=='autocall'?'item-selected':''}}">Автозвонки</a></li>
                    </ul></div></li>
            <li class="root-item"><a href="/account/statistics/" class="opener-ac item-3 pener-ac "><span>Статистика</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/stat/sms">SMS рассылка</a></li>
                        <li><a href="/stat/autocall">Автозвонки</a></li>
                        <li><a href="/stat/message">SMS интеграция</a></li>
                        <li><a href="/stat/sonic">Голосовая интеграция</a></li>
                        <li><a href="/stat/robot">Роботы</a></li>
                    </ul></div></li>
            <li class="root-item"><a href="/account/integration/" class="opener-ac item-4 pener-ac "><span>Интеграция</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/sonic/index">Голосовые интеграции</a></li>
                        <li><a href="/message/index">SMS интеграции</a></li>
                        <li><a href="/account/integration/document.php">Документы</a></li>
                    </ul></div></li>
            <!--<li class="{{$menu=='validator'?'active':''}}">
                <a href="/validator/index" class="item-7 root-item">
                    Проверка номеров
                </a>
            </li>-->

            <li class="root-item robotx"><a href="/robot/index" class="item-9 root-item">Роботы</a></li>
            <li class="root-item"><a href="/account/settings/" class="opener-ac item-5 pener-ac "><span>Настройки</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/account/settings/profile.php">Профиль</a></li>
                        <li><a href="/setting/transaction">Денежные транзакции</a></li>
                        <li><a href="/account/settings/payment.php">Пополнить баланс</a></li>
                        <li><a href="https://mediasend.kz/sms/">Примеры СМС</a></li>
                    </ul></div></li>
        </ul>
        <a class="opener" href="#">
            <span>+ View More</span>
            <em>- View Less</em>
        </a>
        <div id="contacts">
            <p>Поддержка:</p>
            <a href="skype:live:support_49911?add" class="skype">
                <p>live:support_49911</p>
            </a>
            <a href="tel:+77057804343" class="call">
                <p>8 (705) 780-43-43</p>
            </a>
        </div>
    </div>

    <div id="main" class="my_main">
        <div class="container-fluid">
            <div class="row no-gutters">
                <div class="panel">
                    <div class="col-md-4">
                        <strong>
                            {{$title}}
                        </strong>
                    </div>
                    <div class="col-md-8">
                        <a href="/auth/logout/" class="exit">exit</a>
                        <span class="time">{{ date('H:i', time())}}</span>
                        <a href="/account/settings/transactions.php">
								<span class="balanse">Баланс:
									<em>{{number_format(\App\User::balance(), 2, '.', '')}} &#8376;</em>
                                    @if(\App\User::bitrixUser()->bonus)
									    <em id="my-bonus" data-bonus="{{\App\User::bitrixUser()->bonus}}">
                                   <span style="color: #797c80;font-size: 13px;">Бонусы: </span>{{number_format(\App\User::bitrixUser()->bonus, 2, '.', '')}} Б</em>
                                    @endif
								</span>
                        </a>
                        <span class="id">
								<em>Ваш ID: </em>{{\App\User::bitrixUser()->id}}</span>
                        <div class="panel-logo">
                            <div>
                                <a data-fancybox="modal" data-src="#editLogo" href="javascript:;">
                                    <img src="{{\App\User::logo()}}" alt="logo">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="editLogo" style="display: none; padding: 50px 5vw; max-width: 800px; text-align: center;">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="message">
                </div>
                <div class="edit-row">
                    <div class="dispatch-sectionbox-line">
                        <input type="file" data-jcf='{"buttonText": "Custoasd", "placeholderText": "Загрузить логотип"}' name="logo">
                    </div>
                </div>
                <div class="edit-row">
                    <input type="submit" name="saveLogo" value="Сохранить" class="btn btn-blue">
                </div>
            </form>
        </div>

        <div id="content">
            @yield('content')
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.11.2.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="/static/plugins/fancybox/src/js/core.js"></script>
<script type="text/javascript" src="/static/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/static/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript" src="/static/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="/static/js/common.js?v=1.1"></script>
<script type="text/javascript" src="/static/js/mask.js"></script>
<script type="text/javascript" src="/static/js/main.js"></script>
<script type="text/javascript" src="/static/js/recorder.js"></script>
<script type="text/javascript" src="/static/js/audio.js?v=13"></script>
<script type="text/javascript" src="/static/js/app.js?v=442"></script>
<script type="text/javascript" src="/static/js/audioplayer.min.js"></script>
</body>
</html>
