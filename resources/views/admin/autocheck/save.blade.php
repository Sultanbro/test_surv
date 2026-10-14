<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Mediasend.kz Управление">
    <meta name="viewport" content="width=500, initial-scale=0.26">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <title>Отметка стажеров</title>

    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>

    <style>
        .special {
            display: block;
            margin-bottom: 10px;
            border: 0;
            cursor: pointer;
            outline: 0 !important;
            font-size: 15px;
        }
        h1 {
            font-size: 1.5rem !important;
        }
        .row {
            background: aliceblue;
            padding: 20px;
            /* width: 1000px; */
            margin: 0 auto;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Отмечаемся</h1>
           
        </div>
        <div class="col-lg-12">
            <span style="color:#007bff">{{ $user }}</span>
            <span style="color:#007bff">{{ $message }}</span>
        </div>
    </div>

</div>


</body>
</html>