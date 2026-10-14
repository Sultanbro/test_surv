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
        .special.primary {
            background: #007bff;
        }
        .special {
            display: block;
            margin-bottom: 0px;
            border: 0;
            border-radius: 3px;
            cursor: pointer;
            outline: 0 !important;
            font-size: 14px;
            background: #ddd;
            color: white;
        }
        h1 {
            font-size: 1.5rem !important;
        }
        .row {
            background: aliceblue;
            padding: 20px;
            margin: 0 auto;
            margin-top: 15px;
        }
        .d-flex {
            display:flex;
            align-items:center;
        }
        form p {
            margin: 0;
            margin-left: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Отмечаемся</h1>
            <p>Найдите себя в списке, поставьте галочку и нажмите "Я присутствую" для отметки:</p>
        </div>
        <div class="col-lg-12">
            @foreach($users as $user)
            <form action="" method="post" class="d-flex my-2">
                <input type="checkbox" name="checker" class="checker" required onclick="check(this)">
                <input type="hidden" name="user_id" value="{{ $user['id'] }}">
                <p>{{ $user['name'] }}</p>
                <button class="special" disabled>Я присутствую</button>
                {{ csrf_field() }}
            </form>
            @endforeach
        </div>
    </div>

    </div>

<script>
function check(e) {
    var checkBox = e;

    var btn = checkBox.parentElement.querySelector("button");

    if (checkBox.checked == true){
        btn.classList.add("primary");
        btn.removeAttribute('disabled');
    } else {
        btn.classList.remove("primary");
        btn.setAttribute('disabled', true);
    }
}
</script>
</body>
</html>