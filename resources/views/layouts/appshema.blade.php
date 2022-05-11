<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.1" />
        <meta name="viewport" content="width=1260">
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{$title}}</title>
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="/static/new/css/style.css?v=1">
    </head>
    <body>

                    @yield('content')


        <script type="text/javascript" src="/js/app.js?v=1"></script>


    </body>
</html>
