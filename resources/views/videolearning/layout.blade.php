@php
$assetsVersion = '65.1' // т
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
	
 
	<link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/css/font-awesome.min.css">
    <link rel="stylesheet" href="/admin/css/themify-icons.css">
    <link rel="stylesheet" href="/admin/css/flag-icon.min.css">
    <link rel="stylesheet" href="/admin/css/cs-skin-elastic.css">

    <link rel="stylesheet" href="/admin/css/lib/datatable/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/admin/css/lib/datatable/fixedColumns.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/css/lib/chosen/chosen.min.css">
 
    <link rel="stylesheet" href="/admin/css/style.css?v={{$assetsVersion}}">
    <link rel="stylesheet" href="/admin/css/custom.css?v={{$assetsVersion}}">
    <link rel="stylesheet" href="/css/admin/app.css?v={{$assetsVersion}}">


  	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
	<meta name="csrf-token" content="{{ csrf_token() }}">	
    @yield('head')
	
</head>
<body>


@if(auth()->user())
    @include('layouts.admin_left_menu')
@else
<style>
body{padding-left: 0 !important;}
</style>
@endif

@include('videolearning.css')
@include('videolearning.header')






<div class="wrap" id="right-panel">
	<!-- <div class="leftblock">
		@include('videolearning.leftnav')
	</div> -->
	<div class="rightblock row">
		@yield('content')
	</div>
</div>
<style>
.rightblock {
    padding: 15px 5px !important;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="/video_learning/bootstrap.min.js"></script>
<script src="/video_learning/playerjs.js" ></script>
<script>
$('.navbar-toggler').click(function() {
	$('.navbar-collapse').toggleClass('show');
});
$('#cats').click(function() {
	$('.list-group').toggleClass('show');
	$('svg', this).toggleClass('rotate');
});

////////////////////








</script>
<script>
$('#videomenu').click(function() {
    $('.side-menu').toggleClass('show');
});
</script>
<style>
aside.left-panel {padding: 0;}
.navbar .navbar-brand img {
    max-width: 166px;
    margin-top: -10px;
}
</style>
<style>

.side-menu {
    display: none;
    padding: 15px 0;
    position: fixed;
    left: 280px;
    bottom: 0;
    z-index: 999;
    background: #045e92;
    width: 280px;
    height: 100vh;
    list-style: none;
    transition: 0.3s all ease;
}
.side-menu.show {
    display: block;
}
.side-menu a {
    color: #fff;
}
</style>
<!-- <script>
        $(".sidemenu").on("click", function() {
            $(".sidemenu i.opener").toggleClass("show", 300);
            $(".sidemenu i.closer").toggleClass("show", 300);
            $("#left-panel").toggleClass("show-sidebar", 500);
            $("#right-panel").toggleClass("show-sidebar", 500);
        });

        $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
        }
        var $subMenu = $(this).next('.dropdown-menu');
        $subMenu.toggleClass('show');


        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.dropdown-submenu .show').removeClass('show');
        });


        return false;
        });
    </script> -->
@yield('scripts')
</body>
</html>