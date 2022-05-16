<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.1" />
        <meta name="viewport" content="width=1260">
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title>Страница не найдена</title>
        <link rel="stylesheet" href="/static/css/bootstrap.min.css">
        <link rel="stylesheet" href="/static/plugins/fancybox/src/css/core.css">
        <link rel="stylesheet" href="/static/plugins/datepicker/css/bootstrap-datepicker.css">
        <link rel="stylesheet" href="/static/plugins/datatables/datatables.min.css">
        <link rel="stylesheet" href="/static/css/chosen.css?v=1.4">
        <link rel="stylesheet" href="/static/css/audio.css?v=1.2">
        <link rel="stylesheet" href="/static/css/nprogress.css?v=1.1">
        <link rel="stylesheet" href="/static/css/jquery.mCustomScrollbar.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic" rel="stylesheet">
        <!-- <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/sunny/jquery-ui.css">-->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/static/new/css/chekboxandradio.css?v=1.2">
        <link rel="stylesheet" href="/static/new/css/tempusdominus-bootstrap-3.css">
        <link rel="stylesheet" href="/static/new/css/style.css?v=1.7">
        <style>


            @-webkit-keyframes blinktwo {
              50% {
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1
              }
              100% {
                -webkit-transform: scale(2, 2);
                transform: scale(2, 2);
                opacity: 0
              }
            }
            @keyframes  widgetPulse {
              50% {
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1
              }
              100% {
                -webkit-transform: scale(2, 2);
                transform: scale(2, 2);
                opacity: 0
              }
            }

            .blink-notification {
              box-shadow: 0 0 4px #61bfe9;
              border: 1px solid #61bfe9;
              -webkit-animation: blinktwo infinite 1.5s;
              animation: blinktwo infinite 1.5s;

            }
        </style>
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
                    <li class="root-item ">
                      <a href="#" class="opener-ac item-2 pener-ac"><span>Рассылки<i class="playvideo" title="Видео инструкция" data-fancybox="import" data-src="#videorassylka"></i></span>
                          <span class="names">автозвонки и смс</span>
                      </a>
                        <div class="slide-ac">
                            <ul>
                                <li><a href="/contact/index" class="">Контакты</a></li>
                                <li><a href="/sms/index" class="">Рассылка</a></li>
                                <li><a href="/stat/sms" class="">Статистика</a></li>
                                <li><a href="/sonic/index" class="">Интеграция</a></li>
                                <li><a class="" href="/robot/index">Роботы</a></li>
                                <li><a class="" href="/examples">Примеры СМС</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <ul class="custom-accordion service-files ">
                    <li class="root-item">
                        <a href="/setting/callibro?route=/obzvon/dashboard" class="hrefcall">
                            <span class="callls" style="margin-right: 0; padding-right: 0;">Cервис обзвона<i class="playvideo" title="Видео инструкция" data-fancybox="import" data-src="#videorassylka2"></i></span>
                            <span style="display:none;" class="calllstwo">сервис обзвона</span>
                        </a>
                        <a href="#" class="opener-ac item-7 pener-ac"><span>&nbsp;</span></a>
                        <div class="slide-ac">

                            <ul>
                                <li><a href="/setting/callibro?route=/obzvon/operator">Панель оператора</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/account/1">Пользователи</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/script">Сценарии обзвона</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/dialer">Диалеры/Очереди</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/contact">Контакты</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/report">Отчёт по звонкам</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/sip">SIP Телефония</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/integration">Интеграции</a></li>
                                <li><a href="/setting/callibro?route=/obzvon/notification">Уведомления</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <!-- <ul class="custom-accordion service-files intellect-files">
                <li class="root-item">
                    <a href="/intellect/index" class="hrefcall">
                        <span class="callls" style="margin-right: 0; padding-right: 0;">U-Mind</span>
                        <span class="calllstwo">интел. обзвон</span>
                    </a>
                    <a href="#" class="opener-ac item-71 pener-ac"><span>&nbsp;</span></a>
                    <div class="slide-ac">
                        <ul>
                            <li><a href="/intellect/contacts">Контакты</a></li>
                            <li><a href="/intellect/delivery">Рассылка</a></li>
                            <li><a href="/intellect/stats">Статистика</a></li>
                        </ul>
                    </div>
                </li>
            </ul> -->
                
                <ul class="custom-accordion settings">
                    <li class="root-item "><a href="#" class="opener-ac item-7 pener-ac"><span>Настройки</span></a>
                        <div class="slide-ac">
                            <ul>
                                <li><a href="/setting/profile" class="">Профиль</a></li>
                                <li><a href="/setting/transaction" class="">Денежные транзакции</a></li>
                                <li><a class="" href="/setting/payment">Пополнить баланс</a></li>
                                <li><a class="" href="/setting/price">Тарифы</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <ul class="custom-accordion partner">
                    <li class="root-item ">
                        <a href="#" class="opener-ac item-7 pener-ac"><span>Партнерская часть</span></a>
                        <div class="slide-ac">
                            <ul>
                                <?php if(Gate::allows('partner')): ?>
                                <li><a href="<?php echo e(route('partner.about')); ?>" class="">О партнерке</a></li>
                                <li><a href="<?php echo e(route('partner.edit')); ?>" class="">Карточка партнера</a></li>
                                <li><a href="<?php echo e(route('partner.info')); ?>" class="">Персональная ссылка</a></li>
                                <li><a href="<?php echo e(route('partner.dok')); ?>" class="">Маркетинговая поддержка</a></li>
                                <li><a href="<?php echo e(route('partner.index')); ?>" class="">Курс для менеджера</a></li>
                                <li><a href="<?php echo e(route('partner.faq')); ?>" class="">Частые вопросы</a></li>
                                <li><a href="<?php echo e(route('partner.statistic')); ?>" class="">Статистика продаж и выплат</a></li>
                                <?php else: ?>
                                <li><a href="<?php echo e(route('partner.add')); ?>" class="">Заявка на партнерство</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                </ul>

                <a class="opener" href="#">
                    <span>+ View More</span>
                    <em>- View Less</em>
                </a>
                <div id="contacts" class="">
                    <p>Поддержка:</p>
                    <a href="skype:live:support_49911?add" title="live:support_49911" class="skype">
                        <p>live:support_49911</p>
                    </a>
                    <a href="tel:+77057804343" class="call">
                        <p>8 (705) 780-43-43</p>
                    </a>
                    <a href="tel:74951364282" class="call">
                        <p>8 (495) 136-42-82</p>
                    </a>


                </div>
            </div>

            <div id="main" class="my_main ">



                <div id="content">
                    <?php echo $__env->yieldContent('content'); ?>



                </div>
            </div>
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
        <script type="text/javascript" src="/static/js/main.js?v=1.11"></script>
        <script type="text/javascript" src="/static/js/recorder.js"></script>
        <script type="text/javascript" src="/static/js/audio.js?v=16"></script>
        <script type="text/javascript" src="/static/js/app.js?v=652"></script>
        <script type="text/javascript" src="/static/js/audioplayer.min.js"></script>
        <script type="text/javascript" src="/static/js/jcfilter.js?1.1"></script>
        <script type="text/javascript" src="/static/js/app2.js?v=3.594"></script>



        <script type="text/javascript">$(document).ready(function(){
                $(window).scroll(function () {if ($(this).scrollTop() > 100) {$('#scroller').fadeIn();} else {$('#scroller').fadeOut();}});
                $('#scroller').click(function () {$('body,html').animate({scrollTop: 0}, 400); return false;});
            });</script>
<div id="scroller"  class="return-to-top" style="display: none;"><span style="transform: rotate(-90deg);display: inline-block;"> ➤ </span></div>


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

            (function() {
                var script = document.createElement('script');
                script.type = 'text/javascript'; script.async = true;
                script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
                document.getElementsByTagName('head')[0].appendChild(script);
            })();
        </script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a><a href="http://sendphone.reformal.ru">Oтзывы и предложения для mediasend проект</a></noscript>
        <!---reformal--->




        <script data-skip-moving="true">
            (function(w,d,u,b){
                s=d.createElement('script');r=(Date.now()/1000|0);s.async=1;s.src=u+'?'+r;
                h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
            })(window,document,'https://cdn.bitrix24.ru/b1734679/crm/site_button/loader_6_hbxvas.js');
        </script>
        <script>window.roistatCookieDomain = 'u-marketing.org';</script>
        <script>
            (function(w, d, s, h, id) {
                w.roistatProjectId = id; w.roistatHost = h;
                var p = d.location.protocol == "https:" ? "https://" : "http://";
                var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init";
                var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
            })(window, document, 'script', 'cloud.roistat.com', '92537ae8425b40443bb504e4503594fd');
        </script>



        <script>
        function openTab(t, e) {
          var i, n, s;
          for (n = document.getElementsByClassName("tabcontent-item"), i = 0; i < n.length; i++) n[i].style.display = "none", jQuery(n[i]).hasClass(e) && (n[i].style.display = "block");
          for (s = document.getElementsByClassName("tablinks"), i = 0; i < s.length; i++) s[i].className = s[i].className.replace(" active", "");
          t.currentTarget.className += " active"
        }
        $('.send-btn').click(function(e){
          e.preventDefault();
          window.open('https://cp.u-marketing.org/sms/update?message_text='+$(this).parent().find('p').text(), '_self');
        });
        </script>

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


    </body>
</html>
