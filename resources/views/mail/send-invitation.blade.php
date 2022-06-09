
Уважаемый(я) {{ $mailData['name'] }},<br>
Приглашаю вас на портал.<br><br>

<br>
Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;{{ $mailData['email'] }}<br>
<b>Пароль:</b>&nbsp;{{ $mailData['password'] }}<br>

Используйте выданный Вам пароль к Вашему кабинету по ссылке <a href="http://{{ $mailData['subdomain'] }}.jobtron.org">{{ $mailData['subdomain'] }}.jobtron.org</a>.<br>