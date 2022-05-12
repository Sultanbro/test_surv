<!doctype html>
<html class="no-js" lang="ru"> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="description" content="Mediasend.kz Управление">
    <meta name="viewport" content="width=800, initial-scale=0.26">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/css/style.css">
    <link rel="stylesheet" href="/admin/css/custom.css">
    <link rel="stylesheet" href="/css/admin/app.css">
    <link rel="stylesheet" href="/admin/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>

    @yield('head')
    @yield('styles')

    
</head>
<body>



<div id="app" class="right-panel right-panel-app d-flex">

    @include('layouts.side_menu')
        
    <div class="page">

        <div id="header" class="header" style="display:none">

            <div class="header-menu d-flex justify-content-between">

                <div class="mr-3" style="width: 100%">
                    <div id="cabinetjs">
                        <timetracking activeuserid="{{json_encode(auth()->user()->ID)}}"
                                    usertype="{{auth()->user()->user_type}}"
                                    program="{{auth()->user()->program_id}}"
                                    user_type="{{auth()->user()->user_type}}"
                                    position_id="{{auth()->user()->position_id}}"></timetracking>

                    </div>

                </div>
                <div class=" d-flex justify-content-end">
                    @include('includes.admin_notifications', [
                            'unread_notifications' => $unread_notifications,
                            'read_notifications' => $read_notifications,
                            'unread' => $unread,
                            'head_users' => $head_users,
                            'bonus_notification' => $bonus_notification,
                        ])
                    <div class="user-area dropdown " style="display: flex; align-items: center;">
                        <profile user="{{json_encode(auth()->user())}}"></profile>
                    </div>

                </div>
            </div>
        </div>

        <div class="content-wrap">
             
         

            <div class="cont">
                @yield('content')
            </div>

                 
          
        </div>
    </div>
    <notifications group="foo" />


</div>



<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
<!-- <script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>  -->
<script src="/js/app.js?13"></script>


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