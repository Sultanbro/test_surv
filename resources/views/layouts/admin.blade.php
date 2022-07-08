<!doctype html>
<html class="no-js" lang="ru"> 
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.2"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="description" content="Mediasend.kz Управление">
    <meta name="viewport" content="width=800, initial-scale=0.26">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="/admin/css/style.css"> -->
    <link rel="stylesheet" href="/admin/css/custom.css">
    <link rel="stylesheet" href="/css/admin/app.css?6">
    <link rel="stylesheet" href="/admin/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/croppie.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    @yield('head')
    @yield('styles')
 
    
</head>
<body>

<div id="app" class="right-panel right-panel-app d-flex">

    @include('layouts.side_menu', [
                            'unread_notifications' => $unread_notifications,
                            'read_notifications' => $read_notifications,
                            'unread' => $unread,
                            'head_users' => $head_users,
                            'bonus_notification' => $bonus_notification,
                        ])
    @include('layouts.right_menu', [
        'unread' => $unread,
    ])    
    <div class="page">

      

        <div class="content-wrap">
             
         

            <div class="cont">
                @yield('content')
            </div>

                 
          
        </div>
    </div>
    <notifications group="foo" />


</div>



<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>

@if(auth()->user() && !in_array(auth()->id(), [5,4444]))
<script src="/js/jquery.iMissYou.min.js"></script>
<script>
$(document).ready(function(){
    $.iMissYou({
        title: "@yield('title') - Вернись  :(",
        favicon: {
            enabled: true,
            src:'/IMissYouFavicon.ico'
        }
    });
});
</script>
@endif
<!-- 
<script src="js/jquery-1.11.2.min.js"></script> -->
  

<!-- <script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>  -->
<script src="https://unpkg.com/vue-upload-multiple-image@1.1.6/dist/vue-upload-multiple-image.js"></script>
<script src="{{ url('/js/app.js') }}"></script>
<script src="{{ url('/js/croppie.js') }}"></script>
<script src="{{ url('/js/croppie.min.js') }}"></script>


@include('includes.admin_notifications_css_js')
@yield('scripts')

@include('layouts.admin_scripts')


@if($reminder)
  @include('includes.reminder', ['unread' => $unread])
@endif

@if($bonus_notification)
  @include('includes.bonus_notification', ['bonus_notification' => $bonus_notification])
@endif
    

@if(auth()->id() == 13865)
<img src="/users_img/homer.gif" id="homer">
<img src="/users_img/pikachu-running.gif" id="pikachu">
<img src="/users_img/mouse.gif" id="mouse" style="display:none;">
<audio id="myAudio">
  <source src="/users_img/elevator.mp3" type="audio/mpeg">
</audio>
<script>
let show = true;
document.getElementById("app").addEventListener("click",() => {
    let time = 20;
    let num = Math.floor((Math.random() * time) + 1);
    if(show &&  num == time) {
        document.getElementById("myAudio").play()
        document.getElementById("homer").classList.add("show");
        
        document.getElementById("dance").style.display = 'block';
        setTimeout(() => {
            document.getElementById("homer").classList.remove("show");
            document.getElementById("pikachu").classList.add("show");
        }, 2700);
        setTimeout(() => {
            document.getElementById("pikachu").classList.remove("show");
            document.getElementById("mouse").classList.add("show");
        }, 6000);
        setTimeout(() => {
            document.getElementById("myAudio").pause()
            document.getElementById("mouse").classList.remove("show");
            document.getElementById("dance").style.display = 'none';
        }, 10000);
        show = false;
    }   
  

})

</script>

<style>
#homer {
    width: 300px;
    display: none;
    position: absolute;
    bottom: 0;
    right: -30px;
    z-index: 99999;
}
#homer.show{
    display: block;
}
#pikachu {
    width: 200px;
    position: absolute;
    bottom: 0;
    left: -200px;
    z-index: 99999;
    
}
#pikachu.show{
    animation: 3s pikachu ease;
}
#mouse {
    width: 200px;
    display: none;
    position: absolute;
    bottom: 50%;
    left: -26px;
    z-index: 99999;
}
#mouse.show{
    display: block !important;
}
@keyframes pikachu {
    from {
        left: -200px;
    }
    to {
        left:100%;
    }
}
</style>
@endif

<style>
.corpbook a {
    word-break: break-word;
}
.corpbook table,
.corpbook img {
    max-width:100%;
}
</style>
</body>
</html>