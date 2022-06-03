@php
$assetsVersion = '65.1' // т
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
  	<link rel="stylesheet" href="/video_learning/bootstrap.min.css">
  	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
	<meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/admin/css/font-awesome.min.css">
    <link rel="stylesheet" href="/admin/css/style.css?v={{$assetsVersion}}">
    <link rel="stylesheet" href="/admin/css/custom.css?v={{$assetsVersion}}">
    <link rel="stylesheet" href="/css/admin/app.css?v={{$assetsVersion}}">

    @yield('head')

</head>
<body class="admin">

@include('videolearning.css')

@if(auth()->user())
    @include('layouts.admin_left_menu')
@else
<style>
body{padding-left: 0 !important;}
</style>
@endif

<div class="container bg-white fh">
    <div class="row">
        <div class="col-lg-12">
            <div class="row p-4 body">
                @yield('content')
            </div>
        </div>
    </div>
    
</div> 

 
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="/video_learning/bootstrap.min.js"></script>
<script src="/video_learning/playerjs.js" ></script>
<script>
$.ajaxSetup({
headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
</script>
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
let timer = setInterval(() => {}); // upload progress async function
let approximateTimer = setInterval(() => {}); // show approximate time of end of uploading to ftp server
let seconds = 0; // counted seconds
let incrementor = setInterval(() => {seconds += 0.3}, 300); // count seconds of uploading file to server, not FTP,  by ajax

// Загрузка видео по FTP
$('#file').change(function(file) {
    var formData = new FormData();
    formData.append('file', document.forms['filer']['file'].files[0] );
    formData.append('folder', $('#folder').val());
    $('#status svg').show();
    $('#status span').text('Готовим файл к загрузке: 0%');
    $.ajax({
        url: '/videos/upload',
        type: 'POST',              
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        $('#status span').text('Готовим файл к загрузке: ' + parseFloat(percentComplete).toFixed(2) + '%');
                        if(percentComplete == 100) {
                            $('#status span').text('Файл готов. Загружаем на сервер...');
                            //uploadProgress();
                            setApproximateTimer();
                        }
                    }
            }, false);
            return xhr;
        },
        success: function(result)
        {
            clearInterval(timer);
            player = new Playerjs({ 
                id:"video",
                file: result['file'],
            }); 
            $('#link').val(result['file']);
            if(result['file']) {
                $('#status span').text('Видеофайл загружен!');
                document.getElementById("remaining_time").innerHTML = '';
                clearInterval(approximateTimer)
            } else {
                $('#status span').text('Ошибка связи. Файл не загрузился на удаленный сервер!');
            }
            $('#status svg').hide();
            $('#link_text').text(result['file']);
        },
        error: function(data)
        {
            $('#status span').text('Ошибка связи. Файл не загрузился на удаленный сервер!');
            clearInterval(timer);
            clearInterval(approximateTimer)
            document.getElementById("remaining_time").innerHTML = '';
            console.log(data);
        }
    });
    
});

function setApproximateTimer() {
    clearInterval(incrementor); 
    approximateTimer = setInterval(function() {
        
        var distance = parseFloat(seconds).toFixed(0);
        seconds--;
        // Output the result in an element with id="demo"
        if (seconds < 1) clearInterval(approximateTimer)
        document.getElementById("remaining_time").innerHTML = 'Примерно осталось: ' + new Date(seconds * 1000).toISOString().substr(11, 8)
    }, 1000)
}



</script>
@yield('scripts')
</body>
</html>