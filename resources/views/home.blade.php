<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href=https://fonts.cdnfonts.com/css/gotham-pro rel=stylesheet>
    <link rel=stylesheet href=https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css>
    <link href=/js/home.js rel=preload as=script>
    <title>Jobtron</title>
</head>
<body>
    <div id="app"></div>
    @guest
    <input type="hidden" name="csrf" id="csrf" value="">
    @else
    @csrf
    @endguest
    <script src=/js/home.js></script>
</body>
</html>
