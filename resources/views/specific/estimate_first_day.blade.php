<html>
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Mediasend.kz Управление">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/admin/css/normalize.css">
  <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
  <title>Оцените первый день обучения</title>
  <link rel="stylesheet" href="/admin/css/font-awesome.min.css">
<style>
input[type="radio"] {
  width: 30px !important;
}
.aicx {
  align-items:center;
}
.question {
  font-weight: 600;
  padding: 0 8px;
}
#rating span.checked {
  color: orange;
}
#rating {
  position: relative;
  padding: 0 8px;
}
#rating span {
  display: inline-block;
    position: relative;
    color: #c5c5c5;
    cursor: pointer;
    transition: 0.3s all ease;
    margin-right: 4px;
    font-size: 20px; 
}
#rating span:hover {
  transform: scale(1.15);
  transform-origin:center;
}
#rating span:after {
  position: absolute;
    bottom: 105%;
    left: 6px;
    font-size: 11px;
    font-family: 'Open Sans';
}
#rating span:last-child:after {
  left: 4px;
}
#rating span:nth-child(1):after {content: "1";}
#rating span:nth-child(2):after {content: "2";}
#rating span:nth-child(3):after {content: "3";}
#rating span:nth-child(4):after {content: "4";}
#rating span:nth-child(5):after {content: "5";}
#rating span:nth-child(6):after {content: "6";}
#rating span:nth-child(7):after {content: "7";}
#rating span:nth-child(8):after {content: "8";}
#rating span:nth-child(9):after {content: "9";}
#rating span:nth-child(10):after {content: "10";}
.centered {
  margin: 0 auto;
}

.ramka {
  background: #fff !important;
  padding: 25px 15px !important;
  border-radius: 12px;
  box-shadow: 0 0 14px 11px #f0f0f0;
}
#submit {
  margin-top: 20px;
  background: #2474ca;
  border-color: #2474ca;
}
#submit:hover {
  background: #1c66b6;
  border-color: #1c66b6;
}
label {
  margin-bottom: 0;
  cursor: pointer;
}
.aic div {
  position: relative;
    top: -5px;
}
</style>
</head> 
<body>

<div id="particle-container">
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>
	<div class="particle"></div>




  <div class="container mt-3 mb-3">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 ramka">

      <h3><center>Анкета</center></h3>
      <p class="text-center">Оцените первый день обучения</p>
      <form  method="post">
 
      <div class="block">
        <div>
          <p class="question">1. Скажите в чем будет заключаться Ваша работа?</p>
        </div>
        <div class="mb-2">
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q1" type="radio" value="1">
            <div>это сетевой маркетинг</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q1" type="radio" value="2">
            <div>обрабатывать входящие или исходящие звонки по готовой клиенткой базе</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q1" type="radio" value="0" checked>
            <div>не до конца понятно</div>
          </label>
        </div>
      </div>
       
      <div class="block">
        <div>
          <p class="question">2. Какой график работы у операторов колл центра?</p>
        </div>
        <div class="mb-2">
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q2" type="radio" value="1">
            <div>график 6 на 1</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q2" type="radio" value="2">
            <div>свободный, могу работать в любое удобное время</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q2" type="radio" value="0" checked>
            <div>еще не знаю</div>
          </label>
        </div>
      </div> 

      <div class="block">
        <div>
          <p class="question">3. Какая будет ЗП?</p>
        </div>
        <div class="mb-2">
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q3" type="radio" value="1">
            <div>фиксированная оплата от 100 000 до 120 000 тенге</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q3" type="radio" value="2">
            <div>оплата за каждый час активной работы в разговоре и ожидании разговора</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q3" type="radio" value="3">
            <div>фиксированная ЗП 80 000 и бонусы 40 000 тенге</div>
          </label>
          <label class="d-flex justify-content-start aic">
            <input class="form-control" name="q3" type="radio" value="0" checked>
            <div>все запутанно, не могу точно ответить</div>
          </label>
        </div>
      </div>
 
      <div class="block">
        <div>
          <p class="question">4. Оцените тренера который проводил обучение? (оцените от 1 до 10)</p>
        </div>
        <div class="mb-2">
          <div class="d-flex justify-content-start aic">
            <input class="form-control" name="q4" type="hidden" id="q4" value="0">
            <input class="form-control" name="phone" type="hidden" value="{{ $phone }}">
            <div id="rating" class="mb-2">
                    <span class="fa fa-star" data-id="1" title="1"></span>
                    <span class="fa fa-star" data-id="2" title="2"></span>
                    <span class="fa fa-star" data-id="3" title="3"></span>
                    <span class="fa fa-star" data-id="4" title="4"></span>
                    <span class="fa fa-star" data-id="5" title="5"></span>
                    <span class="fa fa-star" data-id="6" title="6"></span>
                    <span class="fa fa-star" data-id="7" title="7"></span>
                    <span class="fa fa-star" data-id="8" title="8"></span>
                    <span class="fa fa-star" data-id="9" title="9"></span>
                    <span class="fa fa-star" data-id="10" title="10"></span>
                </div>
          </div>
          
        </div>
      </div>

      <div class="block">
        <div>
          <p class="question">5. Напишите Ваши рекомендации по улучшению процесса обучения «_____»:</p>
        </div>
        <div class="mb-2">
          <div class="d-flex justify-content-start aic">
            <textarea class="form-control" name="q5" value="1"></textarea>
          </div>
        </div>
      </div>
        
        <button type="submit" id="submit" class="btn btn-primary rounded centered d-block">Отправить</button>
        {{ csrf_field() }}
      </form>



    </div>
  </div>
</div>
</div>






<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
<script>
/**
  Выбор количества звезд
 */
$('#rating span').click(function() {
  var id = $(this).data('id');
  $('#q4').val(id);

  $('#rating span').removeClass('checked');

  for(let i=Number(id);i>0;i--) {
    $('#rating span[data-id="' + i + '"]').addClass('checked');
  }

});
</script>



<style>
body {
  background: #f5f5f5;
}

.particle {
  position: absolute;
  border-radius: 50%;
}

@-webkit-keyframes particle-animation-1 {
  100% {
    transform: translate3d(62vw, 33vh, 82px);
  }
}

@keyframes particle-animation-1 {
  100% {
    transform: translate3d(62vw, 33vh, 82px);
  }
}
.particle:nth-child(1) {
  -webkit-animation: particle-animation-1 60s infinite;
          animation: particle-animation-1 60s infinite;
  opacity: 0.41;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -0.2s;
          animation-delay: -0.2s;
  transform: translate3d(45vw, 43vh, 30px);
  background: #d926b8;
}

@-webkit-keyframes particle-animation-2 {
  100% {
    transform: translate3d(52vw, 51vh, 21px);
  }
}

@keyframes particle-animation-2 {
  100% {
    transform: translate3d(52vw, 51vh, 21px);
  }
}
.particle:nth-child(2) {
  -webkit-animation: particle-animation-2 60s infinite;
          animation: particle-animation-2 60s infinite;
  opacity: 0.6;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -0.4s;
          animation-delay: -0.4s;
  transform: translate3d(61vw, 79vh, 82px);
  background: #d94726;
}

@-webkit-keyframes particle-animation-3 {
  100% {
    transform: translate3d(10vw, 29vh, 55px);
  }
}

@keyframes particle-animation-3 {
  100% {
    transform: translate3d(10vw, 29vh, 55px);
  }
}
.particle:nth-child(3) {
  -webkit-animation: particle-animation-3 60s infinite;
          animation: particle-animation-3 60s infinite;
  opacity: 0.09;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -0.6s;
          animation-delay: -0.6s;
  transform: translate3d(58vw, 25vh, 31px);
  background: #26d991;
}

@-webkit-keyframes particle-animation-4 {
  100% {
    transform: translate3d(13vw, 29vh, 67px);
  }
}

@keyframes particle-animation-4 {
  100% {
    transform: translate3d(13vw, 29vh, 67px);
  }
}
.particle:nth-child(4) {
  -webkit-animation: particle-animation-4 60s infinite;
          animation: particle-animation-4 60s infinite;
  opacity: 0.98;
  height: 6px;
  width: 6px;
  -webkit-animation-delay: -0.8s;
          animation-delay: -0.8s;
  transform: translate3d(6vw, 27vh, 83px);
  background: #d96826;
}

@-webkit-keyframes particle-animation-5 {
  100% {
    transform: translate3d(81vw, 26vh, 30px);
  }
}

@keyframes particle-animation-5 {
  100% {
    transform: translate3d(81vw, 26vh, 30px);
  }
}
.particle:nth-child(5) {
  -webkit-animation: particle-animation-5 60s infinite;
          animation: particle-animation-5 60s infinite;
  opacity: 0.81;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -1s;
          animation-delay: -1s;
  transform: translate3d(70vw, 34vh, 97px);
  background: #26d9bb;
}

@-webkit-keyframes particle-animation-6 {
  100% {
    transform: translate3d(7vw, 70vh, 30px);
  }
}

@keyframes particle-animation-6 {
  100% {
    transform: translate3d(7vw, 70vh, 30px);
  }
}
.particle:nth-child(6) {
  -webkit-animation: particle-animation-6 60s infinite;
          animation: particle-animation-6 60s infinite;
  opacity: 0.04;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -1.2s;
          animation-delay: -1.2s;
  transform: translate3d(14vw, 51vh, 38px);
  background: #267dd9;
}

@-webkit-keyframes particle-animation-7 {
  100% {
    transform: translate3d(90vw, 83vh, 33px);
  }
}

@keyframes particle-animation-7 {
  100% {
    transform: translate3d(90vw, 83vh, 33px);
  }
}
.particle:nth-child(7) {
  -webkit-animation: particle-animation-7 60s infinite;
          animation: particle-animation-7 60s infinite;
  opacity: 0.39;
  height: 9px;
  width: 9px;
  -webkit-animation-delay: -1.4s;
          animation-delay: -1.4s;
  transform: translate3d(52vw, 45vh, 18px);
  background: #d9cd26;
}

@-webkit-keyframes particle-animation-8 {
  100% {
    transform: translate3d(31vw, 6vh, 97px);
  }
}

@keyframes particle-animation-8 {
  100% {
    transform: translate3d(31vw, 6vh, 97px);
  }
}
.particle:nth-child(8) {
  -webkit-animation: particle-animation-8 60s infinite;
          animation: particle-animation-8 60s infinite;
  opacity: 0.59;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -1.6s;
          animation-delay: -1.6s;
  transform: translate3d(3vw, 2vh, 37px);
  background: #d9ca26;
}

@-webkit-keyframes particle-animation-9 {
  100% {
    transform: translate3d(77vw, 28vh, 73px);
  }
}

@keyframes particle-animation-9 {
  100% {
    transform: translate3d(77vw, 28vh, 73px);
  }
}
.particle:nth-child(9) {
  -webkit-animation: particle-animation-9 60s infinite;
          animation: particle-animation-9 60s infinite;
  opacity: 0.86;
  height: 9px;
  width: 9px;
  -webkit-animation-delay: -1.8s;
          animation-delay: -1.8s;
  transform: translate3d(49vw, 64vh, 22px);
  background: #26d947;
}

@-webkit-keyframes particle-animation-10 {
  100% {
    transform: translate3d(62vw, 71vh, 83px);
  }
}

@keyframes particle-animation-10 {
  100% {
    transform: translate3d(62vw, 71vh, 83px);
  }
}
.particle:nth-child(10) {
  -webkit-animation: particle-animation-10 60s infinite;
          animation: particle-animation-10 60s infinite;
  opacity: 0.39;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -2s;
          animation-delay: -2s;
  transform: translate3d(32vw, 14vh, 40px);
  background: #264dd9;
}

@-webkit-keyframes particle-animation-11 {
  100% {
    transform: translate3d(78vw, 52vh, 28px);
  }
}

@keyframes particle-animation-11 {
  100% {
    transform: translate3d(78vw, 52vh, 28px);
  }
}
.particle:nth-child(11) {
  -webkit-animation: particle-animation-11 60s infinite;
          animation: particle-animation-11 60s infinite;
  opacity: 0.19;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -2.2s;
          animation-delay: -2.2s;
  transform: translate3d(14vw, 10vh, 8px);
  background: #c726d9;
}

@-webkit-keyframes particle-animation-12 {
  100% {
    transform: translate3d(73vw, 73vh, 81px);
  }
}

@keyframes particle-animation-12 {
  100% {
    transform: translate3d(73vw, 73vh, 81px);
  }
}
.particle:nth-child(12) {
  -webkit-animation: particle-animation-12 60s infinite;
          animation: particle-animation-12 60s infinite;
  opacity: 0.13;
  height: 8px;
  width: 8px;
  -webkit-animation-delay: -2.4s;
          animation-delay: -2.4s;
  transform: translate3d(21vw, 21vh, 68px);
  background: #d93526;
}

@-webkit-keyframes particle-animation-13 {
  100% {
    transform: translate3d(63vw, 2vh, 35px);
  }
}

@keyframes particle-animation-13 {
  100% {
    transform: translate3d(63vw, 2vh, 35px);
  }
}
.particle:nth-child(13) {
  -webkit-animation: particle-animation-13 60s infinite;
          animation: particle-animation-13 60s infinite;
  opacity: 0.62;
  height: 8px;
  width: 8px;
  -webkit-animation-delay: -2.6s;
          animation-delay: -2.6s;
  transform: translate3d(17vw, 37vh, 58px);
  background: #2629d9;
}

@-webkit-keyframes particle-animation-14 {
  100% {
    transform: translate3d(50vw, 30vh, 8px);
  }
}

@keyframes particle-animation-14 {
  100% {
    transform: translate3d(50vw, 30vh, 8px);
  }
}
.particle:nth-child(14) {
  -webkit-animation: particle-animation-14 60s infinite;
          animation: particle-animation-14 60s infinite;
  opacity: 0.22;
  height: 8px;
  width: 8px;
  -webkit-animation-delay: -2.8s;
          animation-delay: -2.8s;
  transform: translate3d(30vw, 7vh, 39px);
  background: #7726d9;
}

@-webkit-keyframes particle-animation-15 {
  100% {
    transform: translate3d(25vw, 82vh, 83px);
  }
}

@keyframes particle-animation-15 {
  100% {
    transform: translate3d(25vw, 82vh, 83px);
  }
}
.particle:nth-child(15) {
  -webkit-animation: particle-animation-15 60s infinite;
          animation: particle-animation-15 60s infinite;
  opacity: 0.43;
  height: 6px;
  width: 6px;
  -webkit-animation-delay: -3s;
          animation-delay: -3s;
  transform: translate3d(67vw, 70vh, 90px);
  background: #266ed9;
}

@-webkit-keyframes particle-animation-16 {
  100% {
    transform: translate3d(36vw, 15vh, 6px);
  }
}

@keyframes particle-animation-16 {
  100% {
    transform: translate3d(36vw, 15vh, 6px);
  }
}
.particle:nth-child(16) {
  -webkit-animation: particle-animation-16 60s infinite;
          animation: particle-animation-16 60s infinite;
  opacity: 0.58;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -3.2s;
          animation-delay: -3.2s;
  transform: translate3d(17vw, 59vh, 54px);
  background: #d98b26;
}

@-webkit-keyframes particle-animation-17 {
  100% {
    transform: translate3d(73vw, 79vh, 16px);
  }
}

@keyframes particle-animation-17 {
  100% {
    transform: translate3d(73vw, 79vh, 16px);
  }
}
.particle:nth-child(17) {
  -webkit-animation: particle-animation-17 60s infinite;
          animation: particle-animation-17 60s infinite;
  opacity: 0.36;
  height: 9px;
  width: 9px;
  -webkit-animation-delay: -3.4s;
          animation-delay: -3.4s;
  transform: translate3d(19vw, 11vh, 88px);
  background: #6826d9;
}

@-webkit-keyframes particle-animation-18 {
  100% {
    transform: translate3d(58vw, 38vh, 78px);
  }
}

@keyframes particle-animation-18 {
  100% {
    transform: translate3d(58vw, 38vh, 78px);
  }
}
.particle:nth-child(18) {
  -webkit-animation: particle-animation-18 60s infinite;
          animation: particle-animation-18 60s infinite;
  opacity: 0.9;
  height: 9px;
  width: 9px;
  -webkit-animation-delay: -3.6s;
          animation-delay: -3.6s;
  transform: translate3d(52vw, 34vh, 11px);
  background: #26d9a6;
}

@-webkit-keyframes particle-animation-19 {
  100% {
    transform: translate3d(53vw, 24vh, 85px);
  }
}

@keyframes particle-animation-19 {
  100% {
    transform: translate3d(53vw, 24vh, 85px);
  }
}
.particle:nth-child(19) {
  -webkit-animation: particle-animation-19 60s infinite;
          animation: particle-animation-19 60s infinite;
  opacity: 0.77;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -3.8s;
          animation-delay: -3.8s;
  transform: translate3d(58vw, 60vh, 73px);
  background: #26d9b8;
}

@-webkit-keyframes particle-animation-20 {
  100% {
    transform: translate3d(32vw, 1vh, 56px);
  }
}

@keyframes particle-animation-20 {
  100% {
    transform: translate3d(32vw, 1vh, 56px);
  }
}
.particle:nth-child(20) {
  -webkit-animation: particle-animation-20 60s infinite;
          animation: particle-animation-20 60s infinite;
  opacity: 0.68;
  height: 6px;
  width: 6px;
  -webkit-animation-delay: -4s;
          animation-delay: -4s;
  transform: translate3d(66vw, 16vh, 4px);
  background: #59d926;
}

@-webkit-keyframes particle-animation-21 {
  100% {
    transform: translate3d(14vw, 5vh, 48px);
  }
}

@keyframes particle-animation-21 {
  100% {
    transform: translate3d(14vw, 5vh, 48px);
  }
}
.particle:nth-child(21) {
  -webkit-animation: particle-animation-21 60s infinite;
          animation: particle-animation-21 60s infinite;
  opacity: 0.77;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -4.2s;
          animation-delay: -4.2s;
  transform: translate3d(54vw, 49vh, 21px);
  background: #d9263e;
}

@-webkit-keyframes particle-animation-22 {
  100% {
    transform: translate3d(76vw, 35vh, 20px);
  }
}

@keyframes particle-animation-22 {
  100% {
    transform: translate3d(76vw, 35vh, 20px);
  }
}
.particle:nth-child(22) {
  -webkit-animation: particle-animation-22 60s infinite;
          animation: particle-animation-22 60s infinite;
  opacity: 0.09;
  height: 8px;
  width: 8px;
  -webkit-animation-delay: -4.4s;
          animation-delay: -4.4s;
  transform: translate3d(49vw, 8vh, 15px);
  background: #b526d9;
}

@-webkit-keyframes particle-animation-23 {
  100% {
    transform: translate3d(73vw, 70vh, 61px);
  }
}

@keyframes particle-animation-23 {
  100% {
    transform: translate3d(73vw, 70vh, 61px);
  }
}
.particle:nth-child(23) {
  -webkit-animation: particle-animation-23 60s infinite;
          animation: particle-animation-23 60s infinite;
  opacity: 0.47;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -4.6s;
          animation-delay: -4.6s;
  transform: translate3d(43vw, 42vh, 33px);
  background: #4126d9;
}

@-webkit-keyframes particle-animation-24 {
  100% {
    transform: translate3d(21vw, 7vh, 74px);
  }
}

@keyframes particle-animation-24 {
  100% {
    transform: translate3d(21vw, 7vh, 74px);
  }
}
.particle:nth-child(24) {
  -webkit-animation: particle-animation-24 60s infinite;
          animation: particle-animation-24 60s infinite;
  opacity: 0.44;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -4.8s;
          animation-delay: -4.8s;
  transform: translate3d(15vw, 84vh, 34px);
  background: #d9269d;
}

@-webkit-keyframes particle-animation-25 {
  100% {
    transform: translate3d(51vw, 90vh, 94px);
  }
}

@keyframes particle-animation-25 {
  100% {
    transform: translate3d(51vw, 90vh, 94px);
  }
}
.particle:nth-child(25) {
  -webkit-animation: particle-animation-25 60s infinite;
          animation: particle-animation-25 60s infinite;
  opacity: 0.05;
  height: 10px;
  width: 10px;
  -webkit-animation-delay: -5s;
          animation-delay: -5s;
  transform: translate3d(21vw, 29vh, 82px);
  background: #6bd926;
}

@-webkit-keyframes particle-animation-26 {
  100% {
    transform: translate3d(60vw, 74vh, 94px);
  }
}

@keyframes particle-animation-26 {
  100% {
    transform: translate3d(60vw, 74vh, 94px);
  }
}
.particle:nth-child(26) {
  -webkit-animation: particle-animation-26 60s infinite;
          animation: particle-animation-26 60s infinite;
  opacity: 0.57;
  height: 9px;
  width: 9px;
  -webkit-animation-delay: -5.2s;
          animation-delay: -5.2s;
  transform: translate3d(2vw, 19vh, 9px);
  background: #a626d9;
}

@-webkit-keyframes particle-animation-27 {
  100% {
    transform: translate3d(29vw, 30vh, 50px);
  }
}

@keyframes particle-animation-27 {
  100% {
    transform: translate3d(29vw, 30vh, 50px);
  }
}
.particle:nth-child(27) {
  -webkit-animation: particle-animation-27 60s infinite;
          animation: particle-animation-27 60s infinite;
  opacity: 0.7;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -5.4s;
          animation-delay: -5.4s;
  transform: translate3d(84vw, 49vh, 83px);
  background: #41d926;
}

@-webkit-keyframes particle-animation-28 {
  100% {
    transform: translate3d(9vw, 24vh, 99px);
  }
}

@keyframes particle-animation-28 {
  100% {
    transform: translate3d(9vw, 24vh, 99px);
  }
}
.particle:nth-child(28) {
  -webkit-animation: particle-animation-28 60s infinite;
          animation: particle-animation-28 60s infinite;
  opacity: 0.94;
  height: 8px;
  width: 8px;
  -webkit-animation-delay: -5.6s;
          animation-delay: -5.6s;
  transform: translate3d(12vw, 90vh, 74px);
  background: #afd926;
}

@-webkit-keyframes particle-animation-29 {
  100% {
    transform: translate3d(15vw, 17vh, 4px);
  }
}

@keyframes particle-animation-29 {
  100% {
    transform: translate3d(15vw, 17vh, 4px);
  }
}
.particle:nth-child(29) {
  -webkit-animation: particle-animation-29 60s infinite;
          animation: particle-animation-29 60s infinite;
  opacity: 0.04;
  height: 7px;
  width: 7px;
  -webkit-animation-delay: -5.8s;
          animation-delay: -5.8s;
  transform: translate3d(68vw, 68vh, 67px);
  background: #c1d926;
}

@-webkit-keyframes particle-animation-30 {
  100% {
    transform: translate3d(11vw, 8vh, 1px);
  }
}

@keyframes particle-animation-30 {
  100% {
    transform: translate3d(11vw, 8vh, 1px);
  }
}
.particle:nth-child(30) {
  -webkit-animation: particle-animation-30 60s infinite;
          animation: particle-animation-30 60s infinite;
  opacity: 0.51;
  height: 9px;
  width: 9px;
  -webkit-animation-delay: -6s;
          animation-delay: -6s;
  transform: translate3d(29vw, 23vh, 24px);
  background: #d626d9;
}
</style>

</body>
</html>