@extends('layouts.app2')
@section('content')


UUUUUUUURA


<section class="py-4 hero">
  <div class="container mb-4">
    <div class="row align-center flex-wrap">
        <div class="col-md-6">
          <div class="cover__content">
          <!-- <img src="/images/logomail.png" class="log" alt=""> -->
              <h1 class="pretitle text-left font-bold mb-2 leading-1 text-36">Сервис контроля рабочего времени сотрудников</h1>
              <h1 class="pretitle text-left font-bold mb-2 leading-1 text-36">Сервис контроля рабочего времени сотрудников</h1>
                <p class="text-lg mb-4">Контроль рабочего времени по сотрудникам, Дашборды, База знаний, Регламенты</p>
              
                
              <form class="form-box d-flex justify-start  flex-wrap mailform-1"  method="post" action="/surv_post">
                  @if(isset($_GET['utm_source']))<input type="hidden" name="utm_source" value="{{ $_GET['utm_source'] }}">@endif
                  @if(isset($_GET['utm_medium']))<input type="hidden" name="utm_medium" value="{{ $_GET['utm_medium'] }}">@endif 
                  @if(isset($_GET['utm_term']))<input type="hidden" name="utm_term" value="{{ $_GET['utm_term'] }}">@endif 
                  @if(isset($_GET['utm_content']))<input type="hidden" name="utm_content" value="{{ $_GET['utm_content'] }}">@endif 
                  @if(isset($_GET['utm_campaign']))<input type="hidden" name="utm_campaign" value="{{ $_GET['utm_campaign'] }}">@endif 
                <div class="input-with-button">
                  <input name="phone" class="input js-input-email" placeholder="Ваш телефон">
                  <button class="button submit" data-id="1">Попробовать →</button>
                </div>
                {{ csrf_field() }}
              </form>
              
          </div> 
        </div>
        <div class="col-md-6 visible-lg; video-block">
         
           <img class="img-fluid cover__content__image rounded" src="/images/survv/images/11.png">
         
          
        </div>
    </div>
  </div> 


  
</section>




<section class="start">
  <div class="container mb-2">
    <div class="row align-center flex-wrap">
        <div class="col-md-12 mb-4">
          <p class="pretitle text-left font-bold text-36">Возможности сервиса</p>
        </div>
        <div class="col-md-5 mb-4 lg:mb-0">
          <div class="cover__content">
            <p class="pretitle text-left font-bold ">Полный контроль рабочего времени ваших сотрудников.</p>
            <p class="text-lg mb-2">Отслеживайте реальные часы работы в режиме реального времени</p>
            <p class="text-lg mb-2">Управляйте цифрами, а не словами и ощущениями</p>
            <ul class="text-lg ul">
              <li>Удобно печатать</li>
              <li>Гибкая настройка</li>
              <li>Любой уровень иерархии</li>
              <li>Экспорт/импорт</li>
            </ul>
            <form class="form-box d-flex justify-start  flex-wrap mt-3 mailform-2"  method="post" action="/surv_post">
                  @if(isset($_GET['utm_source']))<input type="hidden" name="utm_source" value="{{ $_GET['utm_source'] }}">@endif
                  @if(isset($_GET['utm_medium']))<input type="hidden" name="utm_medium" value="{{ $_GET['utm_medium'] }}">@endif 
                  @if(isset($_GET['utm_term']))<input type="hidden" name="utm_term" value="{{ $_GET['utm_term'] }}">@endif 
                  @if(isset($_GET['utm_content']))<input type="hidden" name="utm_content" value="{{ $_GET['utm_content'] }}">@endif 
                  @if(isset($_GET['utm_campaign']))<input type="hidden" name="utm_campaign" value="{{ $_GET['utm_campaign'] }}">@endif 
                <div class="input-with-button">
                  <input name="phone" class="input js-input-email" placeholder="Ваш телефон">
                  <button class="button submit" data-id="2">Попробовать →</button>
                </div>
                {{ csrf_field() }}
              </form>
            <!-- <button class="btn mt-3">Попробовать →</button> -->
          </div> 
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 mb-4 lg:mb-0">
          <a href="/images/survv/images/12.png" data-lightbox="image-1" data-title="Полный контроль рабочего времени ваших сотрудников">
            <img class="img-fluid cover__content__image rounded shadow" src="/images/survv/images/12.png">
          </a>
          <a href="/images/survv/images/11.png" data-lightbox="image-1" data-title="Полный контроль рабочего времени ваших сотрудников"></a>
        </div>
    </div>
  </div> 
</section>



<section class="">
  <div class="container mb-2">
    <div class="row align-center flex-wrap">
        <div class="col-md-5 mb-4 lg:mb-0">
          <div class="cover__content">
            <p class="pretitle text-left font-bold ">Регламенты и база знаний</p>
            <p class="text-lg mb-2">Храните все знания компании и обучающие материалы в едином месте</p>
            <ul class="text-lg ul">
              <li>Система доступов в привязке к должности</li>
              <li>Возможность задать любую структуру документов и папок</li>
              <li>Автоматическое формирование оглавления</li>
              <li>Встраивание видео, iframe, картинок, файлов, аудио и пр.</li>
              <li>Совместная работа над документам</li>
            </ul>
            <form class="form-box d-flex justify-start  flex-wrap mt-3 mailform-3"  method="post" action="/surv_post">
                 @if(isset($_GET['utm_source']))<input type="hidden" name="utm_source" value="{{ $_GET['utm_source'] }}">@endif
                  @if(isset($_GET['utm_medium']))<input type="hidden" name="utm_medium" value="{{ $_GET['utm_medium'] }}">@endif 
                  @if(isset($_GET['utm_term']))<input type="hidden" name="utm_term" value="{{ $_GET['utm_term'] }}">@endif 
                  @if(isset($_GET['utm_content']))<input type="hidden" name="utm_content" value="{{ $_GET['utm_content'] }}">@endif 
                  @if(isset($_GET['utm_campaign']))<input type="hidden" name="utm_campaign" value="{{ $_GET['utm_campaign'] }}">@endif 
                <div class="input-with-button">
                <input name="phone" class="input js-input-email" placeholder="Ваш телефон">
                  <button class="button submit" data-id="3">Попробовать →</button>
                </div>
                {{ csrf_field() }}
              </form>
            <!-- <button class="btn mt-3">Попробовать →</button> -->
          </div> 
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 mb-4 lg:mb-0">
          <a href="/images/survv/images/21.png" data-lightbox="image-2" data-title="Регламенты и база знаний">
           <img class="img-fluid cover__content__image rounded shadow" src="/images/survv/images/21.png">
          </a>
          <a href="/images/survv/images/22.png" data-lightbox="image-2" data-title="Регламенты и база знаний"></a>
        </div>
    </div>
  </div> 
</section>






<section class="">
  <div class="container mb-2">
    <div class="row align-center flex-wrap">
        <div class="col-md-6 mb-4 lg:mb-0">
          <a href="/images/survv/images/31.png" data-lightbox="image-3" data-title="Начисления ЗП по каждому сотруднику">
            <img class="img-fluid cover__content__image rounded shadow" src="/images/survv/images/31.png">
          </a>
          <a href="/images/survv/images/32.png" data-lightbox="image-3" data-title="Аналитика работы сотрудников"></a>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5 mb-4 lg:mb-0">
          <div class="cover__content">
            <p class="pretitle text-left font-bold ">Аналитика работы сотрудников</p>
            <p class="text-lg mb-2">Управляйте цифрами, а не ощущениями</p>
            <ul class="text-lg ul">
              <li>Рабочий стол руководителя</li>
              <li>Начисления ЗП по каждому сотруднику</li>
              <li>Ежедневные, еженедельные и месячные</li>
              <li>Общий отчет по всем метрикам компании</li>
            </ul>
            <form class="form-box d-flex justify-start  flex-wrap mt-3 mailform-4"  method="post" action="/surv_post">
                 @if(isset($_GET['utm_source']))<input type="hidden" name="utm_source" value="{{ $_GET['utm_source'] }}">@endif
                  @if(isset($_GET['utm_medium']))<input type="hidden" name="utm_medium" value="{{ $_GET['utm_medium'] }}">@endif 
                  @if(isset($_GET['utm_term']))<input type="hidden" name="utm_term" value="{{ $_GET['utm_term'] }}">@endif 
                  @if(isset($_GET['utm_content']))<input type="hidden" name="utm_content" value="{{ $_GET['utm_content'] }}">@endif 
                  @if(isset($_GET['utm_campaign']))<input type="hidden" name="utm_campaign" value="{{ $_GET['utm_campaign'] }}">@endif 
                <div class="input-with-button">
                  <input name="phone" class="input js-input-email" placeholder="Ваш телефон">
                  <button class="button submit" data-id="4">Попробовать →</button>
                </div>
                {{ csrf_field() }}
              </form>
            <!-- <button class="btn mt-3">Попробовать →</button> -->
          </div> 
        </div>
    </div>
  </div> 
</section>
































<div class="modal modes fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-a" role="document">
      <div class="modal-content">
        <div class="modal-body" style="text-align: center;">
          <iframe width="100%" height="450" src="https://www.youtube.com/embed/LG53Vxum0as" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
  

  <div class="modal modes fade" id="modal_info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">U-marketing.org</h4>
        </div>
        <div class="modal-body" style="text-align: center;">
           <div id="modaltext"></div>
        </div>
        <div class="modal-footer">

        </div>
      </div>
    </div>
  </div>
  
  




@endsection
@section('tophead')

@endsection
@section('scripts')

@endsection
@section('styles')
<style>
  .pointer {
    cursor:pointer;
  }
.hero {
  position: relative;
  background: linear-gradient(
164deg, white, white, transparent);
}
.hero:before {
  content: "";
  display: block;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  z-index: -1;
  background: url(https://images.pexels.com/photos/1831234/pexels-photo-1831234.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260);
  background-size: cover;
}


.rc-anchor-invisible{
  display: none !important;
  overflow:hidden !important;
  width: 0 !important;
  height:0 !important;
  box-shadow:none!important 
}
.log {
    margin-top: 30px;
    height: 65px;
    display: block;margin-bottom: 15px;
  }
.logo {
  display: block;
    background: url(/images/logomail.png);
    width: 168px;
    height: 39px;
    float: left;
    margin-top: 8px;
}
.bg-grey{background:#f9f9f9}
.img-fluid {width: 100%;}
.font-bold {font-weight:bold}
.text-left{text-align:left;}
.text-lg{font-size:18px;}
.text-salad{color:#9bcf04}
.text-60{font-size:60px;}
.border-none {border:none}
.d-flex {display:flex}
.flex-wrap{flex-wrap:wrap}
.justify-start{justify-content:flex-start;}
.justify-center{justify-content:center;}
.justify-end{justify-content:flex-end;}
.align-center{align-items:center}
.col-20{flex:0 0 25%;}
.row {display: flex;}
.icon-s {width: 35px;}
section{padding-top: 80px;padding-bottom:80px;}
.form-box {width: 100%;padding: 0;margin: 0;}
.form-box input{width:auto;height: 42px;margin-bottom: 0;border:1px solid #99ce03;}
.btn-3 {width: auto;padding: 0 15px;margin: 0;}
.border {border:1px solid #9cd005;}
.border-2 {border:2px solid #9cd005;}
.border-b {border-bottom: 1px solid rgba(0,0,0,.05);}
.cover-padding{padding: 47px 80px 53px;}
.container-big {width: 1300px !important;max-width:100% !important;border: 1px solid #f4f4f4;   
    border-radius: 4px;}
.border-none{border:none;}
.text-24 {font-size: 24px;}
.text-36 {font-size: 36px;}
.text-42 {font-size: 42px;}
.leading-1{line-height: 1em;}
.font-exo{font-family: 'Exo 2', sans-serif;}
.shadow {box-shadow: 0 30px 40px rgb(73,228,254,0.1);}
.boxies {display: flex;align-items:flex-start;justify-content: space-between;}
.boxies .boxy {
  flex: 0 0 47%;
  margin-bottom: 25px;
  padding: 32px;
  background-color: #fff;
  border-radius: 4px;
}

.steps-bg {
  width: 488px;
    width: 33.88889vw;
    max-width: auto;
    position: absolute;
    top: 0;
    right: 0;
    margin: 0; 
    border-radius: 0;
}

.free_calls {height: 45px;}

.pretitle, .protitle{margin-bottom: 30px;}
.ml-0 {margin-left:0;}
.mb-1 {margin-bottom:1rem;}
.mb-1i {margin-bottom:1rem !important;}
.mb-2 {margin-bottom:2rem;}
.mb-3 {margin-bottom:3rem;}
.mb-4 {margin-bottom:4rem;}
.mb-6 {margin-bottom: 6rem;}
.ml-2 {margin-left:2rem !important;}
.mr-2 {margin-right:2rem;}
.mt-2{margin-top: 2rem;}
.mt-3{margin-top: 3rem;}
.mt-4{margin-top: 4rem;}
.pt-0{padding-top:0}
.p-0{padding: 0;}
.pr-4{padding-right: 30px;}
.pr-4{padding-left: 30px;}
.pb-2{padding-bottom: 2rem;}
.py-0{padding: 0;}
.py-8{padding-top: 8rem;padding-bottom: 8rem;}
.text-blue {color: #0798dc}
input.mr-2{margin-right: 1rem !important;}

@media(max-width:1023px) { 
  
}
@media(max-width:767px) { 
  .video-block {
    margin-top: 30px;
  }
  .hero {
    min-height: 550px;
    display: flex;
    align-items: center;
  }
  .log {
    margin: 30px 0;
}
  .start {
    margin-top: 30px;
  }
  section{padding-top: 40px;padding-bottom:40px;}
  .boxies .boxy {flex: 0 0 100%}
  .col-20{flex:0 0 100%;display: flex;align-items:flex-start;}
  .cover-padding {
      padding: 35px 5px 35px;
  }
  .col-20 img {margin-right: 15px;}
  .col-20 p{text-align:center}


  .text-36{font-size: 32px;}
  .form-box{justify-content:center !important}
  .form-box input {
    margin-right: 0 !important;
    width: 254px;
  }
  .btn-3 {
    padding: 1px 15px;
    width: 254px;
  }
  .sections {padding: 15px;}
  .jcc {justify-content: center;}
  .fcl {flex-direction: column-reverse;}
}

@media(min-width:1023px) {
  .lg\:mb-0 {margin-bottom: 0;}
  .lg\:mt-0 {margin-top: 0;}
  .lg\:mb-0i {margin-bottom: 0 !important;}
  .lg\:ml-2 {margin-left: 2rem;}
  .lg\:py-8 {padding-top: 8rem; padding-bottom: 8rem;}
  .md\:flex-nowrap {flex-wrap:unset}
  .container-small{max-width:960px;}
}

.img-check {
  display: inline-block;
    width: 1em;
    height: 1em;
    vertical-align: middle;
    background-size: contain;
    background-position: 50%;
    background-repeat: no-repeat;
  background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUiIGhlaWdodD0iMjEiIHZpZXdCb3g9IjAgMCAxNSAyMSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwKSI+CjxwYXRoIGQ9Ik0wLjAxNzc4OTkgMTIuNDk3MUMtMC4xNTcyMzkgMTIuMTY0NSAxLjQ1MzAzIDExLjgxNDUgMi41NzMyMiAxMi40MjcxQzMuNzEwOTEgMTMuMDIyMiA1Ljc0MTI0IDE0LjYxNDkgNi43MjE0MSAxNS45NjI3QzcuNDc0MDMgMTIuNzI0NiAxMC43ODIxIDQuMzU4MjMgMTUuMDAwMyAwQzE1LjE1NzggMC4xMDUwMTggMTMuOTUwMSAxLjczMjc5IDEyLjY3MjQgNC4yODgyMkMxMS4xODQ3IDcuMjgxMjEgOC42MTE3MiAxMy45NjczIDguMzg0MTggMTguMTMzQzguMjI2NjYgMjAuODQ2IDcuMzUxNTEgMjAuNjUzNCA3LjE1ODk4IDIwLjQwODRDNi43OTE0MiAxOS44ODMzIDYuNTYzODggMTguNTE4MSAzLjk5MDk1IDE1LjgyMjZDMS4yOTU1IDEyLjkzNDcgMC40NzI4NjYgMTMuMzE5NyAwLjAxNzc4OTkgMTIuNDk3MVoiIGZpbGw9IiNGRTc4NDkiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMCI+CjxyZWN0IHdpZHRoPSIxNSIgaGVpZ2h0PSIyMC41NDg0IiBmaWxsPSJ3aGl0ZSIvPgo8L2NsaXBQYXRoPgo8L2RlZnM+Cjwvc3ZnPgo=);
  width: 15px;
  height: 21px;
  filter: hue-rotate(82deg);
}
.filter {filter: hue-rotate(82deg);}

.relative{position: relative;}
.play {
  display: block;
    cursor: pointer;
    width: 0;
    height: 0;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-left: 12px solid #b9e415;
    position: relative;
    z-index: 1;
    transition: all 0.3s;
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    left: 10px;
    margin-right:10px
}
.play:before {
  content: "";
  position: absolute;
  top: -15px;
  left: -23px;
  bottom: -15px;
  right: -7px;
  border-radius: 50%;
  border: 2px solid #b9e415;
  z-index: 2;
  transition: all 0.3s;
  -webkit-transition: all 0.3s;
  -moz-transition: all 0.3s;
}
.play:after {
  content: "";
  opacity: 0;
  transition: opacity 0.6s;
  -webkit-transition: opacity 0.6s;
  -moz-transition: opacity 0.6s;
}
.play:hover:before, .play:focus:before {
  transform: scale(1.1);
  -webkit-transform: scale(1.1);
  -moz-transform: scale(1.1);
}
.play.active {
  border-color: transparent;
}
.play.active:after {
  content: "";
    opacity: 1;
    width: 11px;
    height: 13px;
    background: #b9e415;
    position: absolute;
    right: 2px;
    top: -7px;
    border-left: 3px solid #b9e415;
    box-shadow: inset 5px 0 0 0 #f9f9f9;
}
.shadow {
  box-shadow:0px 7px 29px 1px rgba(110,208,255,0.14);
}
.rounded {
  border-radius: 8px;
}
.input-with-button {
  display: flex;
  width: auto !important;
  padding: 0 !important;
}
.input-with-button input {
  width: 100%;
  border-radius: 8px 0 0 8px;
  font-size: 15px;
  font-weight:400;
}
.input-with-button button {
  color: #337ab7;
  border: none;
  outline: none;
    text-decoration: none;
    background: #acd735;
    text-align: center;
    display: flex;
    width: 200px;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    border-radius: 0 7px 8px 0;
}
.ul {
  list-style: initial;
  padding-left: 20px
}
.btn {
  border: none;
    outline: none;
    text-decoration: none;
    background: #acd735;
    text-align: center;
    display: flex;
    width: 200px;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    border-radius: 8px;
    padding: 8px 15px;
}

@media (min-width: 768px) {.modal-dialog-a {width: 600px !important;}}
@media (min-width: 1024px) {.modal-dialog-a {width: 900px !important;}}

</style>
@endsection