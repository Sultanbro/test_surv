<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobtron</title>
</head>
<body>
    <div id="app"></div>
    @guest
    <input type="hidden" name="csrf" id="csrf" value="">
    @else
    @csrf
    @endguest
</body>
</html>