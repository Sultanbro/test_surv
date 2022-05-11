<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.1"/>
    <meta name="viewport" content="width=1260">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/plugins/fancybox/src/css/core.css">
    <link rel="stylesheet" href="/static/plugins/datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/static/plugins/datatables/datatables.min.css">
    <link rel="stylesheet" href="/static/css/chosen.css?v=1.4">
    <link rel="stylesheet" href="/static/css/audio.css?v=1.2">
    <link rel="stylesheet" href="/static/css/nprogress.css?v=1.1">
    <link rel="stylesheet" href="/static/css/jquery.mCustomScrollbar.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic"
          rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/sunny/jquery-ui.css">-->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/static/new/css/chekboxandradio.css?v=1.2">
    <link rel="stylesheet" href="/static/new/css/tempusdominus-bootstrap-3.css">
    <link rel="stylesheet" href="/static/new/css/style.css?v=1.2">
</head>
<body>
<div id="wrapper">
    <div id="sidebar" class="active active_no_transition">
        <a href="/" class="logo">
            <span class="name">u-marketing<span>.org</span></span>
            <span class="slogan">Simplify your marketing!</span>
        </a>
        <h1>Меню</h1>

        <ul class="custom-accordion rassylki">
            <li class="root-item  {{$bigmenu=='rassylka'?'active':''}}">
                <a href="#" class="opener-ac item-2 pener-ac"><span>Рассылки<i class="playvideo"
                                                                               title="Видео инструкция"
                                                                               data-fancybox="import"
                                                                               data-src="#videorassylka"></i></span>
                    <span class="names">автозвонки и смс</span>
                </a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/contact/index" class="{{$menu=='contact'?'item-selected':''}}">Контакты</a></li>
                        <li><a href="/sms/index" class="{{$menu=='sms' || $menu=='autocall' ? 'item-selected':''}}">Рассылка</a>
                        </li>
                        <li><a href="/stat/sms" class="{{$menu=='stat' ? 'item-selected':''}}">Статистика</a></li>
                        <li><a href="/sonic/index" class="{{$menu=='sonic' || $menu=='message' ? 'item-selected':''}}">Интеграция</a>
                        </li>
                        <li><a class="{{$menu=='robot'?'item-selected':''}}" href="/robot/index">Роботы</a></li>
                        <li><a class="{{$menu=='examples'?'item-selected':''}}" href="/examples">Примеры СМС</a></li>
                    </ul>
                </div>
            </li>
        </ul>

        <ul class="custom-accordion service-files ">
            <li class="root-item">
                <a href="/setting/callibro?route=/obzvon/dashboard" class="hrefcall">
                    <span class="callls" style="margin-right: 0; padding-right: 0;">U-Calls<i class="playvideo"
                                                                                              title="Видео инструкция"
                                                                                              data-fancybox="import"
                                                                                              data-src="#videorassylka2"></i></span>
                    <span class="calllstwo">сервис обзвона</span>
                </a>
                <a href="#" class="opener-ac item-7 pener-ac"><span>&nbsp;</span></a>
                <div class="slide-ac">
                    @if (\App\User::bitrixUser()->menu)
                        <ul>
                            <li><a href="/setting/callibro?route=/obzvon/operator">Панель оператора</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/account/1">Пользователи</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/script">Сценарии обзвона</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/dialer">Диалеры/Очереди</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/contact">Контакты + CRM</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/report">Отчёт по звонкам</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/sip">SIP Телефония</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/integration">Интеграции</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/notification">Уведомления</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/settings">Общие настройки</a></li>
                        </ul>
                    @else
                        <ul>
                            <li><a href="/setting/callibro?route=/obzvon/operator">Панель оператора</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/account/1">Пользователи</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/script">Сценарий обзвона</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/dialer">Диалеры/Очереди</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/contact">Контакты + CRM</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/report">Отчёт по звонкам</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/sip">SIP Телефония</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/integration">Интеграция</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/notification">Уведомления</a></li>
                            <li><a href="/setting/callibro?route=/obzvon/settings">Общие настройки</a></li>
                        </ul>
                    @endif
                </div>
            </li>
        </ul>
        @if (\App\User::bitrixUser() && \App\User::bitrixUser()->ID == 5)
        <ul class="custom-accordion service-files intellect-files">
            <li class="root-item  {{$bigmenu=='intellect_calls'?'active':''}}">
                <a href="#" class="opener-ac item-71 pener-ac">
                    <span class="callls" style="margin-right: 0; padding-right: 0;">U-Mind</span>
                    <span class="calllstwo names">интел. обзвон</span>
                </a>
                <div class="slide-ac">
                    @if (\App\User::bitrixUser()->menu)
                        <ul>
                            <li><a href="/intellect_calls/contact/index" class="{{$menu=='contact'?'item-selected':''}}">Контакты</a></li>
                            <li><a href="/intellect_calls/delivery" class="{{$menu=='delivery'?'item-selected':''}}">Рассылка</a></li>
                            <li><a href="/intellect_calls/stats" class="{{$menu=='stats'?'item-selected':''}}">Статистика</a></li>
                        </ul>
                    @endif
                </div>
            </li>
        </ul> 
        @endif
        <!-- <ul class="custom-accordion rent_numbers">
            <li class="root-item">
                <a href="/setting/callibro?route=/obzvon/rent_numbers"
                   class="{{$menu=='rent_numbers'?'item-selected':''}} item-7">Аренда номеров</a>
            </li>
        </ul> -->
        <ul class="custom-accordion settings">
            <li class="root-item {{($menu=='price' || $menu=='stop_list' || $menu=='profile' || $menu=='setting' || $menu=='transaction' || $menu=='payment' || $menu=='uniqname') ? 'active':''}}">
                <a href="#" class="opener-ac item-7 pener-ac"><span>Настройки</span></a>
                <div class="slide-ac">
                    <ul>
                        <li><a href="/setting/profile"
                               class="{{$menu=='profile' || $menu=='uniqname'?'item-selected':''}}">Профиль</a></li>
                        <li><a href="/setting/transaction" class="{{$menu=='transaction'?'item-selected':''}}">Денежные
                                транзакции</a></li>
                        <li><a class="{{$menu=='payment'?'item-selected':''}}" href="/setting/payment">Пополнить
                                баланс</a></li>
                        <li><a class="{{$menu=='price'?'item-selected':''}}" href="/setting/price">Тарифы</a></li>
                        <li><a class="{{$menu=='stop_list'?'item-selected':''}}" href="/setting/stop_list">Стоп-лист</a>
                        </li>
                    <!--      <li><a class="{{$menu=='stop_list'?'item-selected':''}}" href="/setting/stop_list">Стоп лист</a></li>
                            -->
                    </ul>
                </div>
            </li>
        </ul>

        <ul class="custom-accordion partner">
            <li class="root-item {{($menu=='partner' || $menu=='partner_add' || $menu=='partner_edit' || $menu=='partner_info' || $menu=='partner_dok' || $menu=='partner_faq' || $menu=='partner_index' || $menu=='partner_stat' || $menu=='partner_about') ? 'active':''}}">
                <a href="#" class="opener-ac item-7 pener-ac"><span>Партнерская часть</span></a>
                <div class="slide-ac">
                    <ul>
                        @if(Gate::allows('partner'))
                            <li><a href="{{route('partner.about')}}"
                                   class="{{$menu=='partner_about'?'item-selected':''}}">О партнерке</a></li>
                            <li><a href="{{route('partner.edit')}}"
                                   class="{{$menu=='partner_edit'?'item-selected':''}}">Карточка партнера</a></li>
                            <li><a href="{{route('partner.info')}}"
                                   class="{{$menu=='partner_info'?'item-selected':''}}">Персональная ссылка</a></li>
                            <li><a href="{{route('partner.dok')}}" class="{{$menu=='partner_dok'?'item-selected':''}}">Маркетинговая
                                    поддержка</a></li>
                            <li><a href="{{route('partner.index')}}"
                                   class="{{$menu=='partner_index'?'item-selected':''}}">Курс для менеджера</a></li>
                            <li><a href="{{route('partner.faq')}}" class="{{$menu=='partner_faq'?'item-selected':''}}">Частые
                                    вопросы</a></li>
                            <li><a href="{{route('partner.statistic')}}"
                                   class="{{$menu=='partner_stat'?'item-selected':''}}">Статистика продаж и выплат</a>
                            </li>
                        @else
                            <li><a href="{{route('partner.add')}}" class="{{$menu=='partner_add'?'item-selected':''}}">Заявка
                                    на партнерство</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>

        <a class="opener" href="#">
            <span>+ View More</span>
            <em>- View Less</em>
        </a>
        <div id="contacts"
             class="{{($menu=='price' || $menu=='stop_list' || $menu=='examples' || $menu=='autocall' || $menu=='contact' || $menu=='sms'  || $menu=='stat' || $menu=='sonic' || $menu=='message' || $menu=='robot' || $menu=='partner_edit' || $menu=='partner_info' || $menu=='partner_dok' || $menu=='partner_faq' || $menu=='partner_index' || $menu=='partner_stat' || $menu=='partner_about')?'cl':''}}">
            <p>Поддержка:</p>
            <a href="skype:live:support_49911?add" title="live:support_49911" class="skype">
                <p>live:support_49911</p>
            </a>
            <a href="tel:87057804343" class="call">
                <p>8 (705) 780-43-43</p>
            </a>
            <a href="tel:74951364282" class="call">
                <p>8 (495) 136-42-82</p>
            </a>


        </div>
    </div>

    <div id="main"
         class="my_main {{($menu=='partner' || $menu=='partner_add' || $menu=='partner_edit' || $menu=='partner_info' || $menu=='partner_dok' || $menu=='partner_faq' || $menu=='partner_index' || $menu=='partner_stat' || $menu=='partner_about') ? 'partnerpages':''}}">
        <div class="panel">
            <strong>
                {{$title}}
            </strong>

            <a href="{{ route('logout') }}" class="exit" title="Выход"
               onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">exit</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
            <span class="time">{{ date('H:i', time())}}</span>
            <span class="time" style="width: 200px; display: none">timezone {{ Auth::user()->timezone}}</span>
            <span class="time" style="width: 200px; display: none">php {{ \App\User::now_from_php()}}</span>
            <span class="time" style="width: 200px; display: none">db {{ \App\User::now_from_db()}}</span>

            <div class="schef" title="Пожаловаться или поблагодарить руководителя" data-toggle="modal"
                 data-target="#complaint"></div>

            <a href="/setting/transaction" class="money_money" title="Денежные транзакции">
                        <span class="balanse">Баланс:
                            <em><span id="balance_value">{{number_format(\App\User::balance(), 2, '.', '')}}</span> {{auth()->user()->currency=='kzt'?'&#8376;':'₽'}}</em>

                            <em id="my-bonus" data-bonus="{{\App\User::bitrixUser()->bonus}}">
                                <span style="color: #797c80;font-size: 13px;">Бонусы: </span>{{number_format(\App\User::bitrixUser()->bonus, 0, '.', '')}} Б</em>

                        </span>
            </a>

            <a href="/setting/payment" title="Пополнить баланс" class="balansetwo"
               style="{{auth()->user()->currency=='kzt'?'':'background: url("/static/images/ico-balanser.png") no-repeat 50% 50%;'}}">&nbsp;</a>

            <span class="id" title="Ваш id">
                        <em>Ваш ID: </em>{{\App\User::bitrixUser()->ID}}</span>
            <div class="panel-logo" title="Ваш логотип">
                <div>
                    <a href="#" data-toggle="modal" data-target="#logo-modal"><img src="{{\App\User::logo()}}"
                                                                                   alt="logo"></a>
                </div>
            </div>
            <div class="kolokolchik">
                <div class="inf">
                    <a href="#" title="Ваши уведомления" class="tooglenotifi "><i class="fa fa-bell"
                                                                                  aria-hidden="true"></i>
                        <div class="{{$unread?'blink-notification':''}}" style="border-color: #00bef6;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    border-radius: 50%;"></div>
                        <span class="numb"
                              style="font-weight:800">{{ $unread }}</span>
                    </a>
                </div>
                <div class="bgpanel"></div>
                <div class="panel" style="display:none;">
                    <div class="tail"></div>
                    <div class="panel_head">
                        <div class="panel_in active" data-tab="1">Уведомления</div>
                        <div class="panel_in " data-tab="2">Уведомления прочитанные</div>
                    </div>
                    <div class="panel_body">
                        <div class="panel_out active" data-id="1">
                            <div class="notification_list">
                                @foreach ($unread_notifications as $item)
                                    <div class="notification_item">
                                        <div class="notifi_top">
                                            <div class="label-wrapper {{$item->type=='important'?'':'hidden'}}"><span
                                                        class="label-wrapper_text">{{$item->type=='important'?'ВАЖНОЕ':''}}</span>
                                            </div>
                                            <span class="notification-date">{{$item->formattedDate()}}</span>
                                            <span class="notification-projectId">U-marketing</span>
                                        </div>
                                        <div class="notification-title">{{$item->title}}</div>
                                        <div class="notification-text">
                                            {{$item->message}}
                                        </div>
                                        <a href="{{ route('notification.read', ['id'=>$item->id]) }}">
                                            <div class="notification-change"><i class="fa fa-check"></i></div>
                                        </a>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="panel_out " data-id="2">
                            <div class="notification_list">
                                @foreach ($read_notifications as $item)
                                    <div class="notification_item">
                                        <div class="notifi_top">
                                            <div class="label-wrapper {{$item->type=='important'?'':'hidden'}}"><span
                                                        class="label-wrapper_text">{{$item->type=='important'?'ВАЖНОЕ':''}}</span>
                                            </div>
                                            <span class="notification-date">{{$item->formattedDate()}}</span>
                                            <span class="notification-projectId">U-marketing</span>
                                        </div>
                                        <div class="notification-title">{{$item->title}}</div>
                                        <div class="notification-text">
                                            {{$item->message}}
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                    <div class="panel_foot">
                        <a href="{{ route('notification.read') }}">
                            <button><i class="fa fa-check"></i>Отметить все, как прочитанные</button>
                        </a>
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
                        <input type="file" data-jcf='{"buttonText": "Custoasd", "placeholderText": "Загрузить логотип"}'
                               name="logo">
                    </div>
                </div>
                <div class="edit-row">
                    <input type="submit" name="saveLogo" value="Сохранить" class="btn btn-blue">
                </div>
            </form>
        </div>

        <div id="content">
            @yield('content')

            <div class="modal fade" id="logo-modal" tabindex="-1" role="dialog" aria-labelledby="logoModalText">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="logoModalText">Загрузка логотипа</h4>
                        </div>
                        <div class="modal-body">
                            <form action="/setting/logo" method="post" enctype="multipart/form-data" id="logo-form">
                                {{ csrf_field() }}
                                <div class="message">
                                </div>


                                <div class="boxlogo">
                                    <input type="file" name="logo" id="file-2" class="inputfile inputfile-2"
                                           data-multiple-caption="{count} files selected" style="display: none;"
                                           accept="image/*" required>

                                    <label for="file-2">
                                        <svg xmlns="//www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"
                                             class="iconsfile">
                                            <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"
                                                  style=""></path>
                                        </svg>

                                        <span>
                                              Загрузите Ваш логотип.
                                            </span></label>
                                </div>


                                <input style="margin: 10px auto; display: table;" type="submit" name="saveLogo"
                                       value="Сохранить" class="btn btn-blue">
                            </form>
                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@if ($unread > 0)

    <div class="modal fade" id="notifi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document" style="    padding-top: 47px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Новое уведомление</h4>
                </div>
                <div class="modal-body">
                    У Вас новое уведомление <i class="fa fa-arrow-up" aria-hidden="true"></i>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary shownotifi" data-dismiss="modal">Показать</button>
                </div>
            </div>
        </div>
    </div>
@endif


<div class="modal fade" id="complaint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Пожаловаться или поблагодарить руководителя</h4>
            </div>
            <div class="modal-body">
                <form class="form_complaint">
                    <div class="form-group">

                                <textarea style="resize: vertical; max-height: 200px;
                                                 min-height: 80px;" class="form-control" id="sms" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="add_complaint btn btn-default" type="submit">Отправить</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div id="videorassylka" style="display: none; ">
    <iframe src="https://www.youtube.com/embed/zKuPuJ7Zc-k?showinfo=0;modestbranding=1;rel=0;" frameborder="0"
            allowfullscreen=""
            style="width:650px; height:390px;"></iframe>
</div>
<div id="videorassylka2" style="display: none; ">
    <iframe src="https://www.youtube.com/embed/ZLVZmI_44CI?showinfo=0;modestbranding=1;rel=0;" frameborder="0"
            allowfullscreen=""
            style="width:650px; height:390px;"></iframe>
</div>
<script type="text/javascript" src="/static/js/jquery-1.11.2.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/nprogress.js?v=1.1"></script>
<script type="text/javascript" src="/static/js/startprogress.js?v=1.5"></script>
<script type="text/javascript" src="/static/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="/static/plugins/fancybox/src/js/core.js?v=1.2"></script>
<script type="text/javascript" src="/static/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/static/plugins/datepicker/locales/bootstrap-datepicker.ru.min.js"></script>
<script type="text/javascript" src="/static/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="/static/js/common.js?v=1.2"></script>
<script type="text/javascript" src="/static/js/mask.js"></script>
<script type="text/javascript" src="/static/js/main.js?v=1.1111"></script>
<script type="text/javascript" src="/static/js/recorder.js"></script>
<script type="text/javascript" src="/static/js/audio.js?v=16"></script>
<script type="text/javascript" src="/static/js/app.js?v=9.6"></script>
<script type="text/javascript" src="/static/js/audioplayer.min.js"></script>
<script type="text/javascript" src="/static/js/jcfilter.js?1.1"></script>
<script type="text/javascript" src="/static/js/app2.js?v=4"></script>
@if(isset($js_file))
    <script type="text/javascript" src="/static/js/{{$js_file}}?v=3.4113"></script>
@endif

@if ($unread > 0)
    <script type="text/javascript">
        $(document).ready(function () {
            $('#notifi').modal('show');

            $('.shownotifi').click(function() {
                $('.kolokolchik .panel').toggle();
                $('.bgpanel').toggleClass('active');
            })
        });
    </script>
@endif

<script type="text/javascript">$(document).ready(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#scroller').fadeIn();
            } else {
                $('#scroller').fadeOut();
            }
        });
        $('#scroller').click(function () {
            $('body,html').animate({scrollTop: 0}, 400);
            return false;
        });
    });</script>
<div id="scroller" class="return-to-top" style="display: none;"><span
            style="transform: rotate(-90deg);display: inline-block;"> ➤ </span></div>


<!---reformal--->
<script type="text/javascript">
    var reformalOptions = {
        project_id: 977304,
        project_host: "sendphone.reformal.ru",
        tab_orientation: "left",
        tab_indent: "50%",
        tab_bg_color: "#2fc5f7",
        tab_border_color: "#FFFFFF",
        tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/FFFFFF/4bfb34d91c8d7fb481972ca3c84aec38/left/0/tab.png",
        tab_border_width: 2
    };

    (function () {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script>
<noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png"/></a><a
            href="http://sendphone.reformal.ru">Oтзывы и предложения для mediasend проект</a></noscript>
<!---reformal--->


@if(isset($menu))
    @if($menu != 'sms' and $menu != 'autocall')



    @endif

    @if($menu == 'examples')
        <script>
            function openTab(t, e) {
                var i, n, s;
                for (n = document.getElementsByClassName("tabcontent-item"), i = 0; i < n.length; i++) n[i].style.display = "none", jQuery(n[i]).hasClass(e) && (n[i].style.display = "block");
                for (s = document.getElementsByClassName("tablinks"), i = 0; i < s.length; i++) s[i].className = s[i].className.replace(" active", "");
                t.currentTarget.className += " active"
            }

            $('.send-btn').click(function (e) {
                e.preventDefault();
                window.open('https://cp.u-marketing.org/sms/update?message_text=' + $(this).parent().find('p').text(), '_self');
            });
        </script>
    @endif
    @if($menu == 'price')
        <script>
            $('.ruspricebutton').click(function (e) {
                e.preventDefault();
                $('.rusprice').show();
                $('.kazprice').hide();
            });
            $('.kazpricebutton').click(function (e) {
                e.preventDefault();
                $('.rusprice').hide();
                $('.kazprice').show();
            });
            $('.dropdown-menu .dropdown-item').click(function (e) {
                e.preventDefault();
                $('#dropdownMenuButton span').text($(this).text());
            });
        </script>
    @endif
@endif







@if(\App\User::bitrixUser()->ID != 2195)
    <script data-skip-moving="true">
        (function (w, d, u, b) {
            s = d.createElement('script');
            r = (Date.now() / 1000 | 0);
            s.async = 1;
            s.src = u + '?' + r;
            h = d.getElementsByTagName('script')[0];
            h.parentNode.insertBefore(s, h);
        })(window, document, 'https://cdn.bitrix24.ru/b1734679/crm/site_button/loader_6_hbxvas.js');
    </script>
@endif

<script>window.roistatCookieDomain = 'u-marketing.org';</script>
<script>
    (function (w, d, s, h, id) {
        w.roistatProjectId = id;
        w.roistatHost = h;
        var p = d.location.protocol == "https:" ? "https://" : "http://";
        var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/" + id + "/init";
        var js = d.createElement(s);
        js.charset = "UTF-8";
        js.async = 1;
        js.src = p + h + u;
        var js2 = d.getElementsByTagName(s)[0];
        js2.parentNode.insertBefore(js, js2);
    })(window, document, 'script', 'cloud.roistat.com', '92537ae8425b40443bb504e4503594fd');
</script>
<style>
.kolokolchik .panel .panel_body::-webkit-scrollbar {width: 7px;}
.kolokolchik .panel .panel_body::-webkit-scrollbar-track {background: #f1f1f1; }
.kolokolchik .panel .panel_body::-webkit-scrollbar-thumb {background: rgba(33,169,229,.58); }
.kolokolchik .panel .panel_body::-webkit-scrollbar-thumb:hover {background: #21a9e5; }
</style>
</body>
</html>
