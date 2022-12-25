Уважаемый(я) {{$name}},<br>
Приглашаю вас на портал.<br><br>

<br>
Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;{{$email}}<br>
<b>Пароль:</b>&nbsp;@if($original_password) {{$original_password}}@endif <br>

Используйте выданный Вам пароль к Вашему кабинету по ссылке <a target="_blank" href="{{ $hostname }}">{{ $hostname }}</a>.<br>