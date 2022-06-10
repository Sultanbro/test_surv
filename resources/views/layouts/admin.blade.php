<!doctype html>
<html class="no-js" lang="ru"> 
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.1"/>
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
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>

    @yield('head')
    @yield('styles')

    
</head>
<body>

    <script src="js/jquery-1.11.2.min.js"></script>
    <script src="js/jquery.iMissYou.min.js"></script>
    <script>
        jQuery(document).ready(function($){
           $.iMissYou({
               title: "Вернись :(",
               favicon: {
                   enabled: true,
                   src:'/IMissYouFavicon.ico'
               }
           });
       });
    </script>

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
<!-- 
<script src="js/jquery-1.11.2.min.js"></script> -->
  

<!-- <script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>  -->
<script src="{{ url('/js/app.js') }}"></script>


@include('includes.admin_notifications_css_js')
@yield('scripts')

@include('layouts.admin_scripts')


@if($reminder)
  @include('includes.reminder', ['unread' => $unread])
@endif

@if($bonus_notification)
  @include('includes.bonus_notification', ['bonus_notification' => $bonus_notification])
@endif
    
</body>
</html>