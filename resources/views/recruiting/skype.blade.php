<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Шаг 2: Ваш skype</title>
        <link rel="stylesheet" href="/static/css/bootstrap.min.css">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

        @include('recruiting._css')
    </head>
    <body>
    <div class="container shadow bg-white">
        <div class="image">
            <img src="/images/logobp.png" alt="">
        </div>



        @if($view == '1')
        <div class="page-header">
            <h1 class="page-title">Шаг 2: Напишите свой логин скайпа</h1>
        </div>
        <div class="page-content">

            <div class="row">


                
                <div class="col-md-12 mb">
                    <p>Остался последний шаг к завершению. Для того чтобы, вы смогли пройти обучение, вам нужно написать нам свой скайп.</p>
                    <form action="/bp/job/skype" method="post">
                        <div class="row mb items-center">
                            <label for="skype" class="col-md-12 col-lg-3">Ваш скайп</label>
                            <div class="col-md-12 col-lg-9">
                                <input type="text" name="skype" class="form-control" id="skype" placeholder="Пример: live:cid.96eb6esd56198h60">
                            </div>
                        </div> 
                        {{ csrf_field() }}

                        <input type="hidden" name="lead_id" value="{{ $lead_id }}">

                        <button class="btm btn-success" id="submit">
                            Отправить
                        </button>
                    </form>
                    
                </div>
                <div class="col-md-12 mt-2">
                    <p class="mb">Если вы не знаете, что такое скайп, посмотрите видео, где показано, как скачать и установить скайп.</p>
                    <!-- <iframe width="100%" height="320" src="https://www.youtube.com/embed/gmV0uU-IiY0?rel=0&loop=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->
                    <div id="player"></div>
                </div>
                
            </div>
        </div>
        @else    
        <div class="page-header">
            <h1 class="page-title">Поздравляем вас!</h1>
        </div>
        <div class="page-content">

            <div class="row">

                <div class="col-md-12">
                  <p>Вы завершили выполнение Важного пункта на пути к работе в нашей компании.</p>
                  <p>Совсем скоро Вам придет сообщение в вацап и там будет указано точное время Вашего первого дня обучения на должность оператора колл-центра.</p>
                  <p>А перед началом обучения Вы получите ссылку в сообщении на вацап.</p>
                </div>
                
            </div>
        </div>                

        @endif
            
        <script>
            document.getElementById("submit").onclick = function(e) {
                if (document.getElementById("skype").value == "" || document.getElementById("skype").value.length < 8) {
                    e.preventDefault();
                    alert("Пожалуйста, напишите логин скайпа!");
                    return null;
                }
            }
        </script>



    <script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '320',
          width: '100%',
          videoId: 'gmV0uU-IiY0',
          playerVars: {
            'playsinline': 1
          },
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
        event.target.playVideo();
      }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
      function onPlayerStateChange(event) {
          
        // if (event.data == YT.PlayerState.PLAYING && !done) {
        //   setTimeout(stopVideo, 6000);
        //   done = true;
        // }
        
        if (event.data == 0) {
            player = new YT.Player('player', {
          height: '320',
          width: '100%',
          videoId: 'gmV0uU-IiY0',
          playerVars: {
            'playsinline': 1
          },
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
        }
      }
      function stopVideo() {
        player.stopVideo();
      }
    </script>    
    </body>
</html>
