<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администрирование</title>
    <script type="module" src="/js/admino/assets/index.js"></script>
    <link rel="stylesheet" href="/js/admino/assets/index.css">
    <script>
        var userInfo = @json(auth()->user())
    </script>
</head>
<body>
    <div id="app">
        Добро пожаловать в Админку!
    </div>
</body>
</html>
