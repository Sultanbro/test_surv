<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='https://fonts.googleapis.com/css?family=Raleway:400,500,300' rel='stylesheet' type='text/css'>
  <title>Вход</title>
</head>
<body> 
  
 

<div id="mainButton" class="active">
	<div class="modal">

    <form action="/videolearning" method="post" id="form">
    
   
		<div class="form-title">Вход</div>
		<div class="input-group">
			<input type="text" id="name" name="login" onblur="checkInput(this)" />
			<label for="name">Логин</label>
		</div>
		<div class="input-group">
			<input type="password" id="password" name="password" onblur="checkInput(this)" />
			<label for="password">Пароль</label>
		</div>
		<button class="form-button" onclick="sendForm()">Войти</div>
		
    {{ csrf_field() }}
    </form>
    <div class="codepen-by">Business Partner</div>
	</div>
</div>

<style>
*, *:before, *:after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  color: #999;
  padding: 20px;
  display: flex;
  min-height: 100vh;
  align-items: center;
  font-family: "Raleway";
  justify-content: center;
  background-color: #fbfbfb;
}

#mainButton {
  color: white;
  border: none;
  outline: none;
  font-size: 24px;
  font-weight: 200;
  overflow: hidden;
  position: relative;
  border-radius: 2px;
  letter-spacing: 2px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  text-transform: uppercase;
  background-color: #00a7ee;
  -webkit-transition: all 0.2s ease-in;
  -moz-transition: all 0.2s ease-in;
  -ms-transition: all 0.2s ease-in;
  -o-transition: all 0.2s ease-in;
  transition: all 0.2s ease-in;
}
#mainButton .btn-text {
  z-index: 2;
  display: block;
  padding: 10px 20px;
  position: relative;
}
#mainButton .btn-text:hover {
  cursor: pointer;
}
#mainButton:after {
  top: -50%;
  z-index: 1;
  content: "";
  width: 150%;
  height: 200%;
  position: absolute;
  left: calc(-150% - 40px);
  background-color: rgba(255, 255, 255, 0.2);
  -webkit-transform: skewX(-40deg);
  -moz-transform: skewX(-40deg);
  -ms-transform: skewX(-40deg);
  -o-transform: skewX(-40deg);
  transform: skewX(-40deg);
  -webkit-transition: all 0.2s ease-out;
  -moz-transition: all 0.2s ease-out;
  -ms-transition: all 0.2s ease-out;
  -o-transition: all 0.2s ease-out;
  transition: all 0.2s ease-out;
}
#mainButton:hover {
  cursor: default;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
}
#mainButton:hover:after {
  -webkit-transform: translateX(100%) skewX(-30deg);
  -moz-transform: translateX(100%) skewX(-30deg);
  -ms-transform: translateX(100%) skewX(-30deg);
  -o-transform: translateX(100%) skewX(-30deg);
  transform: translateX(100%) skewX(-30deg);
}
#mainButton.active {
  box-shadow: 0 19px 38px rgba(0, 0, 0, 0.3), 0 15px 12px rgba(0, 0, 0, 0.22);
}
#mainButton.active .modal {
  -webkit-transform: scale(1, 1);
  -moz-transform: scale(1, 1);
  -ms-transform: scale(1, 1);
  -o-transform: scale(1, 1);
  transform: scale(1, 1);
}
#mainButton .modal {
  top: 0;
  left: 0;
  z-index: 3;
  width: 100%;
  height: 100%;
  padding: 20px;
  display: flex;
  position: fixed;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  background-color: inherit;
  transform-origin: center center;
  background-image: linear-gradient(to top left, #00a7ee 10%, #55ccff 65%, white 200%);
  -webkit-transform: scale(0.000001, 0.00001);
  -moz-transform: scale(0.000001, 0.00001);
  -ms-transform: scale(0.000001, 0.00001);
  -o-transform: scale(0.000001, 0.00001);
  transform: scale(0.000001, 0.00001);
  -webkit-transition: all 0.2s ease-in;
  -moz-transition: all 0.2s ease-in;
  -ms-transition: all 0.2s ease-in;
  -o-transition: all 0.2s ease-in;
  transition: all 0.2s ease-in;
}

.close-button {
  top: 20px;
  right: 20px;
  position: absolute;
  -webkit-transition: opacity 0.2s ease-in;
  -moz-transition: opacity 0.2s ease-in;
  -ms-transition: opacity 0.2s ease-in;
  -o-transition: opacity 0.2s ease-in;
  transition: opacity 0.2s ease-in;
}
.close-button:hover {
  opacity: 0.5;
  cursor: pointer;
}

.form-title {
  margin-bottom: 15px;
}

.form-button {
  width: 100%;
  padding: 10px;
  color: #00a7ee;
  margin-top: 10px;
  max-width: 400px;
  text-align: center;
  border: solid 1px white;
  background-color: white;
  -webkit-transition: color 0.2s ease-in, background-color 0.2s ease-in;
  -moz-transition: color 0.2s ease-in, background-color 0.2s ease-in;
  -ms-transition: color 0.2s ease-in, background-color 0.2s ease-in;
  -o-transition: color 0.2s ease-in, background-color 0.2s ease-in;
  transition: color 0.2s ease-in, background-color 0.2s ease-in;
}
.form-button:hover {
  color: white;
  cursor: pointer;
  background-color: transparent;
}

.input-group {
  width: 100%;
  font-size: 16px;
  max-width: 400px;
  padding-top: 20px;
  position: relative;
  margin-bottom: 15px;
}
.input-group input {
  width: 100%;
  color: white;
  border: none;
  outline: none;
  padding: 5px 0;
  line-height: 1;
  font-size: 16px;
  font-family: "Raleway";
  border-bottom: solid 1px white;
  background-color: transparent;
  -webkit-transition: box-shadow 0.2s ease-in;
  -moz-transition: box-shadow 0.2s ease-in;
  -ms-transition: box-shadow 0.2s ease-in;
  -o-transition: box-shadow 0.2s ease-in;
  transition: box-shadow 0.2s ease-in;
}
.input-group input + label {
  left: 0;
  top: 20px;
  position: absolute;
  pointer-events: none;
  -webkit-transition: all 0.2s ease-in;
  -moz-transition: all 0.2s ease-in;
  -ms-transition: all 0.2s ease-in;
  -o-transition: all 0.2s ease-in;
  transition: all 0.2s ease-in;
}
.input-group input:focus {
  box-shadow: 0 1px 0 0 white;
}
.input-group input:focus + label, .input-group input.active + label {
  font-size: 12px;
  -webkit-transform: translateY(-20px);
  -moz-transform: translateY(-20px);
  -ms-transform: translateY(-20px);
  -o-transform: translateY(-20px);
  transform: translateY(-20px);
}

.codepen-by {
  left: 0;
  bottom: 0;
  width: 100%;
  padding: 10px;
  font-size: 16px;
  position: absolute;
  text-align: center;
  text-transform: none;
  letter-spacing: initial;
}

#form {
  min-width: 300px;
}
</style>
<script>
var button = document.getElementById('mainButton');

var checkInput = function(input) {
	if (input.value.length > 0) {
		input.className = 'active';
	} else {
		input.className = '';
	}
};
sendForm = function() {
  document.getElementById('form').submit();
}

</script>
</body>
</html>
