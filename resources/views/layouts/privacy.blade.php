<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@400;600;&display=swap" rel="stylesheet">

    <style>
        p, a, h1, h2,  h3, h4, h5, h6, blockquote, div, span, ul, li, label, select, input {
            font-family: 'Open Sans', sans-serif;
        }
        .logo {
            margin: 0 auto;
            display: block;
        }
        .logo img {
            width: 15.25rem;
        }
        #app {
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .content {
            max-width: 1040px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    
    <div id="app">
        <header>
            <a href="" class="logo">
                <img src="/images/logo.svg?6df33a576a0198a236aa0ea4f4a1f13f" alt="logo-img" class="jNav-logo-img">
            </a>
        </header>

        <div class="content">
            <h1 class="text-center">{{ $title }}</h1>
            @yield('content')
        </div>

        <footer>
            <p class="text-center">© 2022-2024 «Jobtron», Все права защищены</p>
        </footer>
    </div>
  

</body>
</html>