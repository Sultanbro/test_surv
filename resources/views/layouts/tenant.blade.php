<!doctype html>
<html class="no-js" lang="ru"> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="description" content="Jobtron Управление">
    <meta name="viewport" content="width=800, initial-scale=0.26">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>

    @yield('head')
    @yield('styles')

    
</head>
<body>




<style>
table td {
    vertical-align: middle !important;
}
</style>
 
<div class="container my-3">
    <div class="row">
        <div class="col-12 card mb-3">
        <div class="d-flex justify-content-between">
            {{ auth()->id() }} {{ auth()->user()->email }} 
            
            <form action="/logout" method="post">
                <button class="btn btn-primary">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Выйти</span>
                </button>
                @csrf
            </form>
        </div>   
        
        </div>
    </div>
</div>
    @yield('content')
      
   




<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>

@yield('scripts')


</body>
</html>