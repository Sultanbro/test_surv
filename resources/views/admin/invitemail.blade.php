Уважаемый(я) {{$name}},<br>
Добро пожаловать в нашу большую семью )<br>

Здесь вы найдете все то ,что так не хватает для комфортной работы.<br> 
У нас никак у Всех...<br>
<br>
Перейдите по ссылке для активации Вашего аккаунта, и введите ниже указанные данные для входа:<br>
<a target="_blank" href='{{env('CALLIBRO_URL')}}/obzvon/activate/{{$activate_key}}'>
	{{env('CALLIBRO_URL')}}/obzvon/activate/{{$activate_key}}
</a><br>
<br>
Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;{{$email}}<br>
<b>Пароль:</b>&nbsp;
@if($original_password)
	{{$original_password}}<br>
@else
	Используйте ранее выданный Вам пароль к Вашему кабинету.<br>
	Либо Вы можете восстановить Ваш пароль по ссылке <a target="_blank" href='{{env('CALLIBRO_URL')}}/obzvon/account/reset'>"Забыли пароль?"</a><br>
@endif
<br>


Спасибочки за внимание!<br>
