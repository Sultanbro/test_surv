<html lang="ru">
<head>
<meta charset="utf-8">
<title>JOBTRON</title>
<meta name="keywords" content="программа, сервис, голосовые, сообщения, рассылка, voice, обзвон, номера, телефоны, информатор, автообзвон, автопрозвон, автоинформатор, ивтоинформатор, информирование, звонки, автоматический, реклама, sip" />
<meta name="description" content="joytron.org - сервис автообзвона и голосовых рассылок. Автоматический обзвон" />

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="/admin/css/bootstrap.min.css">

@yield('tophead')


<link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.1" />

<style>
@import url('https://fonts.googleapis.com/css?family=Comfortaa&subset=latin-ext');

@font-face {
  font-family: 'Panton-BlackCaps';
  src: url('/fonts/Panton-BlackCaps.eot');
  src: url('/fonts/Panton-BlackCaps.eot?#iefix') format('embedded-opentype'),
       url('/fonts/Panton-BlackCaps.svg#Panton-BlackCaps') format('svg'),
       url('/fonts/Panton-BlackCaps.ttf') format('truetype'),
       url('/fonts/Panton-BlackCaps.woff') format('woff'),
       url('/fonts/Panton-BlackCaps.woff2') format('woff2');
  font-weight: normal;
  font-style: normal;
unicode-range: U+000-5FF; /* Latin glyphs */
}



</style>
<meta name="theme-color" content="#000">
<meta name="msapplication-navbutton-color" content="#000">
<meta name="apple-mobile-web-app-status-bar-style" content="#000">
<!-- <link rel="stylesheet" type="text/css" href="/images/survv/css/header-footer.css?v1.43">
<link rel="stylesheet" type="text/css" href="/images/survv/css/all.css?version=1.7523"> -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- <link rel="stylesheet" type="text/css" href="/images/survv/css/instr.min.css?v=211"> -->


<meta property="og:type"              content="article" />
<meta property="og:title"              content="Новое поколение Web сервиса для организации колл центра онлайн. Функции CRM и облачной IP ATC" />
<meta property="og:description"        content="" />
<meta property="og:image"              content="https://joytron.org/baner.jpg" />




@yield('styles')


<style>
.header {
  width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    background: #093659;
    height: 50px;
    color: #fff;
}
.jcsb {
  justify-content: space-between;
}
.logox {
  font-size: 20px;
  color:#fff;
  text-transform: uppercase;
}
.input-with-button button {
  width: 300px !important;
}
.btn {
  width: 150px;
  padding: 3px 15px !important;
}
</style>
</head>
<body>

 <div class="header mb-5">
  <div class="container">
    <div class="d-flex jcsb">
      <div class="logox">JOB TRON</div>
      <div class="ml-3 d-flex align-items-start" style="align-items:center">
        @auth
     
        <form action="/logout">
            <button class="btn btn-primary rounded">
                <i class="fas fa-sign-out-alt"></i>
                <span>Выйти</span>
            </button>
            @csrf
        </form>
        
        @else
        <a class="btn btn-primary rounded mr-2" href="/login">
          Войти
        </a>
        <a class="btn btn-primary rounded" href="/register">
          Регистрация
        </a>
        @endif

        
      </div>
    </div>
  </div>
 </div>

 

  @yield('content')




<footer id="footer">
  <!-- <div class="footer-area ">
    <div class="footer-area-info">
      <span>Отдел продаж: <a href="tel:87777880800" onclick="yaCounter47046267.reachGoal ('call'); return true; ga('send', 'event', 'Knopka', 'call');">
          <span class="callmd_phoneone">8 (777) 788-08-00</span>
        </a>
        <a href="tel:84951364282" onclick="yaCounter47046267.reachGoal ('call'); return true; ga('send', 'event', 'Knopka', 'call');">
          <span class="callmd">8 (495) 136-42-82</span>
        </a>
        <a style="white-space: nowrap;" href="mailto:sales@joytron.org">sales@joytron.org</a>
      </span>
      <span>Техподдержка: <a style="white-space: nowrap;" href="mailto:support@joytron.org">support@joytron.org</a>
      </span>
    </div>
  </div> -->
  <div class="footer-holder">
    <div class="footer-holder-content">
      <span class="copy">&copy; 2022 Joytron.org</span>
      <!-- <ul>
        <li>
          <a href="/contacts/">контакты</a>
        </li>
        <li>
          <a href="/privacy">политика конфидециальности</a>
        </li>
        <li>
          <a href="/terms/">договор оферты</a>
        </li>
        <li>
          <a href="/soglashenie/">соглашение</a>
        </li>
      </ul> -->
    </div>
  </div>
</footer>



      <!-- <div class="modal modes fade" id="call" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">joytron.org</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
              Сейчас Вы получите автозвонок
            </div>
            <div class="modal-footer">

            </div>
          </div>
        </div>
      </div> -->

      <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
  <link rel="stylesheet" type="text/css" href="/css/main.min.css?version1.8.1">

  <link rel="stylesheet" type="text/css" href="/css/animate.css">




  <link href="/images/surv/css/style.css" type="text/css"  rel="stylesheet" />

  <script  type="text/javascript" src="/js/bootstrap.min.js?v=1.1"></script>








  


  <style>
      #footer2 {
          padding: 10px 0 5px;
          background: #475155;
          width: 100%;
      }
      .footerareatext{
          color: white;
          margin-bottom: 0;
      }
      .footerareatext p{
          font-size: 14px;
          margin-bottom: 6px;
          line-height: normal;
      }
      #footer2 h2{
          font-size: 20px;
          margin-bottom: 10px;
      }
      .footerareatext strong{
          font-weight: bold;
      }
      .alert-success {
        background: rgba(0,128,0,0.19);
        text-align: center;
        padding: 10px 15px;
        margin-bottom: 15px;
      } 
  </style>
  <style>
    #footer {
      padding-top: 0;
    padding-bottom: 10px;
    }
  @media (max-width: 419px){
#footer {
    padding-right: 0 !important;
    
} 
}
</style>
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" >


  @yield('scripts')


<style>
  .grecaptcha-badge{
  display: none !important;
  overflow:hidden !important;
  width: 0 !important;
  height:0 !important;
  box-shadow:none!important 
}
  
</style>
  </body>
  </html>










