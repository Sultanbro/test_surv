<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Выбор времени собеседования</title>
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
            <h1 class="page-title">Запись на собеседование</h1>
        </div>
        <div class="page-content">

            <div class="row">


                
                <div class="col-md-12 mb">
                    <form action="/bp/choose_time" method="post">
                   
                        <div class="row mb items-center" style="flex-wrap:wrap">
                            <div class="col-12 col-lg-6">
                                <p><b>Выберите удобное для вас время:</b></p>
                            </div>
                            <div class="col-12 col-lg-6">

                                @foreach($days as $day)
                                <div class="days">
                                    <input type="radio" name="time"  value="{{ $day['value'] }}" id="day{{ $loop->iteration }}" @if($loop->index == 0) selected @endif>
                                    <label for="day{{ $loop->iteration }}">{{ $day['text'] }}</label>
                                </div>
                                @endforeach
                                
                            </div>
                        </div> 

                 
                        {{ csrf_field() }}

                        <input type="hidden" name="hash" value="{{ $hash }}">

                        <button class="btm btn-success" id="submit">
                            Записаться
                        </button>
                    </form>
                    
                </div>
                
            </div>
        </div>
        @else    
        <div class="page-header">
            <h1 class="page-title">Успешно!</h1>
        </div>
        <div class="page-content">

            <div class="row">

                <div class="col-md-12">
                    <div>{!! $msg !!}</div>
                </div>
                
            </div>
        </div>                

        @endif
        
        <script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
        <script>
            document.getElementById("submit").onclick = function(e) {
                if (document.getElementById("skype").value == "" || document.getElementById("skype").value.length < 8) {
                    e.preventDefault();
                    alert("Пожалуйста, напишите логин скайпа!");
                    return null;
                }
            }

            $('.days input[type=radio]').change(function(){

            });

            $('.times input[type=radio]').change(function(){
                
                

                var firstDate = new Date(); // Todays date
                var secondDate = new Date(2021,5,21, parseFloat($(this).val()) ,00,00);

                var diffDays = (firstDate.getHours() - secondDate.getHours()); 

                
                //console.log(diffDays)

                $(this).parent().find('button').prop('disabled', false);
            });
        </script>    

        
    </body>
</html>
