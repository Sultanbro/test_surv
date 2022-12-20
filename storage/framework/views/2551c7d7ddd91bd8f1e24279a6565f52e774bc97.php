Уважаемый(я) <?php echo e($name); ?>,<br>
Добро пожаловать в нашу большую семью )<br>

Здесь вы найдете все то ,что так не хватает для комфортной работы.<br> 
У нас никак у Всех...<br>
<br>
Перейдите по ссылке для активации Вашего аккаунта, и введите ниже указанные данные для входа:<br>
<a target="_blank" href='<?php echo e(env('CALLIBRO_URL')); ?>/obzvon/activate/<?php echo e($activate_key); ?>'>
	<?php echo e(env('CALLIBRO_URL')); ?>/obzvon/activate/<?php echo e($activate_key); ?>

</a><br>
<br>
Ваши данные для входа:<br>
<b>Логин:</b>&nbsp;<?php echo e($email); ?><br>
<b>Пароль:</b>&nbsp;
<?php if($original_password): ?>
	<?php echo e($original_password); ?><br>
<?php else: ?>
	Используйте ранее выданный Вам пароль к Вашему кабинету.<br>
	Либо Вы можете восстановить Ваш пароль по ссылке <a target="_blank" href='<?php echo e(env('CALLIBRO_URL')); ?>/obzvon/account/reset'>"Забыли пароль?"</a><br>
<?php endif; ?>
<br>


Спасибочки за внимание!<br>
<?php /**PATH /var/www/job/resources/views/admin/invitemail.blade.php ENDPATH**/ ?>