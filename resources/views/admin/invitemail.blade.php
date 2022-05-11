Уважаемый(я) {{$name}},<br>
Приглашаю вас на портал сервиса обзвона.<br>
Здесь мы будем осуществлять свою работу над обзвоном потенциальных и существующих клиентов компании,
общаться в чате и многое другое.<br>
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
	Либо Вы можете воостановить Ваш пароль по ссылке <a target="_blank" href='{{env('CALLIBRO_URL')}}/obzvon/account/reset'>"Восстановление пароля"</a><br>
@endif