
Уважаемый(я) <?php echo e($mailData['name']); ?>,<br>
Добро пожаловать в нашу большую семью &#128512;<br><br>
Здесь вы найдете все то, что так не хватает для комфортной работы.<br>У нас никак у Всех...<br>


<br>
Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;<?php echo e($mailData['email']); ?><br>
<b>Пароль:</b>&nbsp;<?php echo e($mailData['password']); ?><br>

Используйте выданный Вам пароль к Вашему кабинету по ссылке <a href="http://<?php echo e($mailData['subdomain']); ?>.jobtron.org"><?php echo e($mailData['subdomain']); ?>.jobtron.org</a>.<br>
Спасибочки за внимание !<br>
<?php /**PATH /var/www/job/resources/views/mail/send-invitation.blade.php ENDPATH**/ ?>