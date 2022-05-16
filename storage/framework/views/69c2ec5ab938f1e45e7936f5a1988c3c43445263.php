Уважаемый(я) <?php echo e($name); ?>,<br>
Приглашаю вас на портал.<br><br>

<br>
Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;<?php echo e($email); ?><br>
<b>Пароль:</b>&nbsp;<?php if($original_password): ?> <?php echo e($original_password); ?><?php endif; ?> <br>

Используйте выданный Вам пароль к Вашему кабинету по ссылке <a target="_blank" href="https://admin.u-marketing.org/">admin.u-marketing.org</a>.<br>