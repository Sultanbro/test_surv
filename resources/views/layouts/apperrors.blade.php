<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.1" />
        <meta name="viewport" content="width=1260">
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
            @keyframes widgetPulse {
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
                <span class="name">jobtron.org<span>.org</span></span>
              </a>
                <h1>Меню</h1>

      
                
   
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
                    @yield('content')



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







        <script>
        function openTab(t, e) {
          var i, n, s;
          for (n = document.getElementsByClassName("tabcontent-item"), i = 0; i < n.length; i++) n[i].style.display = "none", jQuery(n[i]).hasClass(e) && (n[i].style.display = "block");
          for (s = document.getElementsByClassName("tablinks"), i = 0; i < s.length; i++) s[i].className = s[i].className.replace(" active", "");
          t.currentTarget.className += " active"
        }
        $('.send-btn').click(function(e){
          e.preventDefault();
          window.open('https://cp.jobtron.org.org/sms/update?message_text='+$(this).parent().find('p').text(), '_self');
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
