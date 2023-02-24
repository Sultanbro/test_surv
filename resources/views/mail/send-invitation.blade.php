
Уважаемый(я) {{ $mailData['name'] }},<br>
Добро пожаловать в нашу большую семью &#128512;<br><br>
Здесь вы найдете все то, что так не хватает для комфортной работы.<br>У нас никак у Всех...<br>

<br>

@if(isset($mailData['authData']))

Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;{{ $mailData->authData->email }}<br>
<b>Пароль:</b>&nbsp;{{ $mailData->authData->password }}<br>

Используйте выданный Вам пароль к Вашему кабинету по ссылке <a href="http://jobtron.org/login">jobtron.org</a>.<br>

@else

Вас добавили в новый портал на <a href="http://jobtron.org">jobtron.org</a>.<br>

@endif

Спасибочки за внимание !<br>
