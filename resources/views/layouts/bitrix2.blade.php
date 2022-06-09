<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=800, initial-scale=1.0">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Автозвонки</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/plugins/datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/static/plugins/fancybox/src/css/core.css">
    <link rel="stylesheet" href="/static/plugins/fancybox/src/css/core.css">
    <link rel="stylesheet" href="/static/css/adaptive.css">
    <link rel="stylesheet" href="/static/css/jcf.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic" rel="stylesheet">
</head>
<body>
<div id="wrapper">
    <div id="sidebar" class=" active">
        <a href="/account" class="logo">logo</a>
        <strong>Меню</strong>
        <ul class="custom-accordion">
            <li class=""><a href="/account/contacts" class="item-1 root-item">Контакты</a></li>
            <li class="active"><a href="/account/forwards/" class="opener-ac item-2 pener-ac "><span>Рассылка</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/account/forwards/sms.php">SMS рассылка</a></li>
                        <li><a href="/autocalls/index" class="item-selected">Автозвонки</a></li>
                    </ul></div></li>
            <li class="root-item"><a href="/account/statistics/" class="opener-ac item-3 pener-ac "><span>Статистика</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/account/statistics/sms-forwards.php">SMS рассылка</a></li>
                        <li><a href="/account/statistics/sms.php">Интеграция SMS</a></li>
                        <li><a href="/account/statistics/voice.php">Голосовые интеграции</a></li>
                    </ul></div></li>
            <li class="root-item"><a href="/account/integration/" class="opener-ac item-4 pener-ac "><span>Интеграция</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/account/integration/voice.php">Голосовые интеграции</a></li>
                        <li><a href="/account/integration/sms.php">SMS интеграции</a></li>
                    </ul></div></li>
            <li class="root-item"><a href="/account/settings/" class="opener-ac item-5 pener-ac "><span>Настройки</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/account/settings/profile.php">Профиль</a></li>
                        <li><a href="/account/settings/transactions.php">Денежные транзакции</a></li>
                        <li><a href="/account/settings/payment.php">Пополнить баланс</a></li>
                    </ul></div></li>
        </ul>

        <a class="opener" href="#"><span>+ View More</span><em>- View Less</em></a>
        <div id="contacts">
            <p>Поддержка:</p>
            <a href="skype:live:support_49911?add" class="skype"><p>live:support_49911</p></a>
            <a href="tel:+77057804343" class="call"><p>8 (705) 780-43-43</p></a>
        </div>
    </div>
    <div id="main" class="my_main">
        <div class="panel">
            <strong>
                Добавить обзвон
            </strong>
            <a href="/auth/logout/" class="exit">exit</a>
            <span class="time">{{date('H:i')}}</span>
            <a href="/account/settings/transactions.php"><span class="balanse">Баланс: <em>{{\App\User::balance()}} Т.</em></span></a>
            <span class="id"><em>Ваш ID: </em>{{\App\User::bitrixUser()->id}}</span>
            <div class="panel-logo">
                <div>
                    <a data-fancybox="modal" data-src="#editLogo" href="javascript:;"><img src="/static/images/logo-1.png" alt="logo"></a>
                </div>
            </div>
        </div>
        <div id="editLogo" style="display: none; padding: 50px 5vw; max-width: 800px; text-align: center;">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="message">
                </div>
                <div class="edit-row">
                    <div class="dispatch-sectionbox-line"><input type="file" data-jcf='{"buttonText": "Custoasd", "placeholderText": "Загрузить логотип"}' name="logo"></div>
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
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/plugins/fancybox/src/js/core.js"></script>
<script type="text/javascript" src="/static/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/mask.js"></script>
<script type="text/javascript" src="/static/js/main.js"></script>
<script type="text/javascript" src="/static/js/app.js?v=2"></script>
<script src="/static/js/jcf.radio.js"></script>
<!-- <script src="js/jcf.checkbox.js"></script> -->
<script src="/static/js/jcf.select.js"></script>

</body>
</html>
