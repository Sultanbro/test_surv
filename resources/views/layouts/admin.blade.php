<!doctype html>
<html class="no-js" lang="ru"> 
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.2"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>

    @auth
        <meta name="user_id" content="{{ auth()->id() }}" />

        @if(isset(auth()->user()->img_url) && !is_null(auth()->user()->img_url))
            <meta name="avatar" content="/users_img/{{auth()->user()->img_url}}" />
        @else
            <meta name="avatar" content="https://cp.callibro.org/files/img/8.png" />
        @endif
    @endauth

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

<div class="profile__progressbar" style="display:none;">
                    <svg class="progress-ring" width="80" height="80">
                        <circle stroke="#fff" stroke-width="8" cx="40" cy="40" r="30" fill="#8FAF00"/>
                        <circle class="progress-ring__circle" stroke="rgba(96,142,233,0.5)" stroke-width="4" cx="40" cy="40" r="36" fill="transparent"/>
                    </svg>
                    <div class="profile__progressbar-number">
                        <span>87</span>%
                    </div>
                </div>

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


<script>
    window.Laravel = [];
</script>
<!-- <script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>  -->
<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
<script src="https://unpkg.com/vue-upload-multiple-image@1.1.6/dist/vue-upload-multiple-image.js"></script>
<script src="{{ url('/js/app.js') }}"></script>




<script src="{{ url('/js/jquery.maskedinput.js') }}"></script>

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