<?php $__env->startSection('title', 'Мой профиль'); ?>
<?php $__env->startSection('content'); ?>



<div class="animated fadeIn container" style="margin-top:6px">

    <div class="row">
        <div class="col-xl-12">
            <div class="fast-links">
                <a href="#how_earn_more">Как можно зарабатывать больше</a>
                <a href="#personal_info">Ваша личная информация</a>
                <?php if(count($head_in_groups) > 0): ?><a href="#your_grades">Вот как стажеры оценили ваше обучение</a><?php endif; ?>
                <?php if($is_recruiter): ?>
                    <a href="#recruiter_info">Ваши показатели</a>
                <?php elseif(count(json_decode($activities)) > 0): ?>
                    <a href="#personal_indicators">Ваши показатели</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Картинки с заработком: сумма начислений, крi, бонусы -->
    <div class="row non-pillar">
        <div class="col-xl-12">
            <div class="ublock" style="border-radius:5px 5px 0 0;">
                <h2 class="big-title">Ваш баланс</h2>
                <user-earnings :data="<?php echo e(json_encode($user_earnings)); ?>"
                    :activeuserid="<?php echo e(json_encode(auth()->user()->ID)); ?>"/> 
            </div>
        </div>
        <!-- Таблица начислений -->
        <div class="col-xl-12">
            <div class="ublock pt-0 relative" style="border-top: 1px solid transparent;border-radius:0 0 5px 5px" id="pulse" >  
                <t-usersalary activeuserid="<?php echo e(json_encode(auth()->user()->ID)); ?>" />
            </div>
        </div>
    </div>
    


    
    <!-- Как можно зарабатывать больше -->
    <div id="how_earn_more" class="row mt-27" style="margin: 0;margin-top:25px">
        
        <div class="col-xl-12 ublock">
            <h2 class="big-title">Как можно зарабатывать больше</h2>
            <div class="boxes">

                <?php if($show_payment_terms): ?>
                <div class="box" id="box-2">
                    <h2 class="small-title">
                        За что вы получаете оплату
                        <i class="fa fa-info-circle toooltip">
                            <div>
                            Здесь подробно описаны все Ваши активности в течение рабочего дня и любые другие действия, за которые Вам начисляется оплата. Обязательно ознакомьтесь с разделом и задайте возникшие вопросы по оплате труда Вашему руководителю.
                            </div>
                        </i>
                    </h2>
                    <?php if(count($groups_pt) > 0): ?>
                    <div>
                      <?php $__currentLoopData = $groups_pt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <pre class="gr_pre"><?php if($gr->payment_terms && $gr->payment_terms != '' && $gr->show_payment_terms == 1): ?><?php echo e($gr->payment_terms); ?><?php endif; ?></pre>
                        <br>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if($position_desc && $position_desc->show == 1): ?>
                <div class="box" id="box-1">
                    <div style="height:100%;overflow-x:auto">
                        <h2 class="small-title">Растите по карьерной лестнице 
                            <i class="fa fa-info-circle toooltip">
                                <div>
                                У Вас обязательно будет карьерный рост в компании, и здесь описаны требования, необходимые знания и навыки для перехода на следующую ступень карьерной лестницы. Обязательно ознакомьтесь с разделом и задайте возникшие вопросы по карьерному росту Вашему руководителю.
                                </div>
                            </i>
                        </h2>
                        <div class="owl-carousel owl-theme" id="owl">
                            <div class="item">
                                <table class="pos-desc">
                                    <tr>
                                        <th>Следующая ступень карьерного роста</th>
                                    </tr>
                                    <tr>
                                        <td><pre><?php echo $position_desc->next_step; ?></pre></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="item">
                                <table class="pos-desc">
                                    <tr>
                                        <th>Требования к кандидату</th>
                                    </tr>
                                    <tr>
                                        <td><pre><?php echo $position_desc->require; ?></pre></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="item">
                                <table class="pos-desc">
                                    <tr>
                                        <th>Что нужно делать</th>
                                    </tr>
                                    <tr>
                                        <td><pre><?php echo $position_desc->actions; ?></pre></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="item">
                                <table class="pos-desc">
                                    <tr>
                                        <th>График работы</th>
                                    </tr>
                                    <tr>
                                        <td><pre><?php echo $position_desc->time; ?></pre></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="item">
                                <table class="t pos-desc">
                                    <tr>
                                        <th>Заработная плата</th>
                                    </tr>
                                    <tr>
                                        <td><pre><?php echo $position_desc->salary; ?></pre></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="item">
                                <table class="pos-desc">
                                    <tr>
                                        <th>Нужные знания для перехода на следующую должность</th>
                                    </tr>
                                    <tr>
                                        <td><pre><?php echo $position_desc->knowledge; ?></pre></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div> 
                </div>
                <?php endif; ?> 
                

                <div class="box" id="box-3">
                    <div class="third-block">
                        <h2 class="small-title">
                            Расскажите о себе
                            <i class="fa fa-info-circle toooltip">
                                <div>
                                Напишите здесь короткий рассказ о себе. Ориентируясь на Ваши навыки и умения, мы предложим Вам позицию с оплатой выше чем сейчас, и Вы быстрее вырастите по карьерной лестнице.
                                </div>
                            </i>
                        </h2>
                        <div class="ublockx">
                            <div class="question">
                                <p class="title">Расскажите, где вы ранее работали и в какой должности?
                                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                                </p>
                                <form class="answer" data-id="1">
                                    <?php echo e(csrf_field()); ?>

                                    <textarea class="form-control"><?php echo e($answers[1]); ?></textarea>
                                    <button class="btn btn-primary rounded btn-sm mt-2">Сохранить</button>
                                </form>
                            </div>
                        </div>

                        <div class="ublockx mt-2">
                            <div class="question">
                                <p class="title">
                                    Расскажите, что вы умеете делать очень хорошо?
                                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                                </p>

                                <form class="answer" data-id="2">
                                    <?php echo e(csrf_field()); ?>

                                    <textarea class="form-control"><?php echo e($answers[2]); ?></textarea>
                                    <button class="btn btn-primary rounded btn-sm mt-2">Сохранить</button>
                                </form>
                            </div>
                        </div>

                        <div class="ublockx mt-2">
                            <div class="question">
                                <p class="title">
                                    Напишите, какие профессиональные тренинги или книги вы проходили?
                                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                                </p>

                                <form class="answer" data-id="3">
                                    <?php echo e(csrf_field()); ?>

                                    <textarea class="form-control"><?php echo e($answers[3]); ?></textarea>
                                    <button class="btn btn-primary rounded btn-sm mt-2">Сохранить</button>
                                </form>
                            </div>
                        </div>

                        <div class="ublockx mt-2">
                            <div class="question">
                                <p class="title">
                                    Управляли ли вы персоналом?
                                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                                </p>

                                <form class="answer" data-id="4">
                                    <?php echo e(csrf_field()); ?>

                                    <input class="form-control" id="answer-4" 
                                        <?php if($answers[4] == 'true'): ?>checked="checked" <?php endif; ?>
                                        type="checkbox" 
                                        style="display:inline-block"/>
                                    <span>Да, управлял</span>
                                    <button class="btn btn-primary rounded btn-sm mt-2 d-block">Сохранить</button>
                                </form>
                            </div>
                        </div>

                        <div class="ublockx mt-2">
                            <div class="question">
                                <p class="title">
                                    Какие у вас есть сертификаты (Фото)?
                                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                                </p>
                    
                                <form class="answer" data-id="5" enctype="multipart/form-data">
                                    <?php echo e(csrf_field()); ?>

                                    <input type="file" class="form-control" id="answer-5"
                                            data-multiple-caption="{count} файлов выбрано"
                                            multiple
                                            name="files[]"
                                            accept="image/*">
                                    <button class="btn btn-primary rounded btn-sm mt-2 d-block">Загрузить</button>
                                    <div class="certificates mt-2">
                                        <?php $__currentLoopData = $answers[5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e($file); ?>" target="_blank" class="link">Файл</a> 
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </form>
                            </div>
                        </div>


                        

                    </div>
                </div>
            </div>
        </div>
        
    </div>

    
    <div id="personal_info" class="row non-pillar mt-27">
        <div class="col-xl-12">
            <div class="ublock">
                <h2 class="big-title">Ваша личная информация</h2>
                <div class="row">

                
                    <!-- Контактная информация -->
                    <div class="col-xl-8">
                        <div class="contact-information ublockx h-autoч">
                    
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <?php echo e(csrf_field()); ?>

                                <input type="hidden" name="id" value="<?php echo e($user->ID); ?>">
                                <div class="data-information">


                                    <div class="flex-1" style=" 
                                                                    border: 1px solid #eff2f3;
                                                                    padding: 15px;">
                                        <div >
                                            <div class="d-flex">
                                                <p class="user-name"><?php echo e($user->LAST_NAME); ?> <?php echo e($user->NAME); ?>  <b> Ваш ID: <?php echo e($user->ID); ?></b></p>
                                            </div>
                                            <div class="user-groups" style="font-size:14px">Название группы:
                                            <div class="mb-2 d-flex" style="font-size:12px">
                                                <div class="mr-2"><?php echo $groups ? $groups : 'Нет группы'; ?></div>
                                            </div>
                                            </div>

                                        </div>


                                        <table class="w-full table-2">

                                            <tr>
                                                <td colspan="2">
                                                    <p class="name pr-1 mt-2">Должность</p>
                                                </td>
                                                <td colspan="2">
                                                    <input  type="text" name="oklad" disabled
                                                        value="<?php echo e($user_position ? $user_position->position : ''); ?>">
                                                </td>
                                            </tr>

                                            

                                            <tr>
                                                <td colspan="2">
                                                    <p class="name pr-1 mt-2">Ваш оклад</p>
                                                </td>
                                                <td colspan="2">
                                                    <input  type="text" name="oklad" disabled
                                                        value="<?php echo e($oklad); ?>">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2">
                                                    <p class="name pr-1 mt-2">Рабочий график</p>
                                                </td>
                                                <td colspan="2">
                                                    <div class="d-flex justify-content-between">
                                                        <input  type="text" name="workday" disabled style="width:48%"
                                                            value="<?php if($user->working_day_id == 1): ?> 5-2 <?php else: ?> 6-1 <?php endif; ?>">
                                                        <input  type="text" name="workday" disabled style="width:48%" 
                                                            value="<?php echo e(substr($user->work_starts_at(), 0 , 5)); ?> <?php if($user->work_end): ?>- <?php echo e(substr($user->work_end, 0 , 5)); ?> <?php endif; ?>">
                                                    </div>
                                                    
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2">
                                                    <p class="name pr-1 mt-2">Рабочие часы</p>
                                                </td>
                                                <td colspan="2">
                                                    <input  type="text" name="worktime" disabled
                                                        value="<?php if($user->working_time_id == 1): ?> 8 <?php else: ?> 9 <?php endif; ?> часов">
                                                </td>
                                            </tr>
                                    
                                            

                                            <tr class="after-edit"> 
                                                <td colspan="2">
                                                    <p class="name pr-1 mt-2">Валюта</p>
                                                </td>
                                                <td colspan="2">
                                                    <select name="currency" id="currency" class="form-control form-control-sm mb-2 mt-2 changers">
                                                        <option value="kzt" <?php if($user->currency == 'kzt'): ?>selected <?php endif; ?>>KZT Казахстанский тенге</option>
                                                        <option value="rub" <?php if($user->currency == 'rub'): ?>selected <?php endif; ?>>RUB Российский рубль</option>
                                                        <option value="kgs" <?php if($user->currency == 'kgs'): ?>selected <?php endif; ?>>KGS Киргизский сом</option>
                                                        <option value="uzs" <?php if($user->currency == 'uzs'): ?>selected <?php endif; ?>>UZS Узбекский сум</option>
                                                        <option value="uah" <?php if($user->currency == 'uah'): ?>selected <?php endif; ?>>UAH Украинская гривна</option>
                                                        <option value="byn" <?php if($user->currency == 'byn'): ?>selected <?php endif; ?>>BYN Белорусский рубль</option>
                                                    </select>
                                                </td>
                                            </tr>    

                                            <tr class="after-edit"> 
                                                <td >
                                                    <p class="name pr-1">Email</p>
                                                </td>
                                                <td>
                                                    <input  type="email" name="email" required placeholder="Email"
                                                        value="<?php echo e($user->EMAIL); ?>" class="changers" />
                                                </td>
                                                <td >
                                                    <p class="name pr-1 ml-2" >Новый пароль</p>
                                                </td>
                                                <td >
                                                    <input type="text" name="password" class="changers" style="margin:0">
                                                </td>
                                            </tr>


                                            <tr id="saveProfile" style="display:none">
                                                <td colspan="2">
                                                    
                                                </td>
                                                <td colspan="2">
                                                    <button class="btn btn-primary btn-sm rounded mt-2" style="float:right;" >Сохранить</button>
                                                </td>
                                            </tr>


                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>



                    <!-- План чтения книг и важная информация -->
                    <div class="col-xl-4 pl-md-0">
                        <div class="wrappy">
                            <div class="contact-information ublockx mb-3">
                                <h5 class="mb-1">План чтения книг</h5>
                                <p style="font-size: 14px;
                                    line-height: 1em;
                                    margin-bottom: 8px;
                                    
                                    color: #000;">Книга на экзамене в этом месяце: </p>
                                <table class="text-left ">
                                    <tr>
                                        <td><a href="<?php echo e($book['link_book']); ?>" target="_blank" style="color: #05adef;"><?php echo e($book['name']); ?></a>
                                            <?php if(isset($book['success']) && $book['success'] == 1): ?> <span class="badge badge-success">Экзамен сдан</span>
                                            <?php else: ?> <span class="badge badge-primary">Экзамен не сдан</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="contact-information ublocx mb-3">
                                <h5 class="mb-1">Важная информация</h5>
                                <p style="font-size: 14px;line-height: 1em;color: #ad0000;">Пожалуйста, ознакомьтесь со следующими пунктами. Возможно среди них уже есть вопрос, который вас интересует.</p>
                                <a data-toggle="modal" data-target="#oplataModal" style="color: #05adef;">Правила выдачи оплаты</a><br>
                                <a href="/timetracking/info#card1" target="_blank" style="color: #05adef;">Частые вопросы</a><br>
                                <a href="/timetracking/fines" target="_blank" style="color: #05adef;">Депримирование</a><br>
                                <a data-toggle="modal" data-target="#cantStartModal" style="color: #05adef;">Нет кнопки "Начать день"</a><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>

<?php if(count($head_in_groups) > 0): ?>
    <!-- Группы руководителя -->
    <div id="your_grades" class="row">
        <div class="col-xl-12">
            <div class="contact-information ublock mt-27">
                <h2>Вот как стажеры оценили ваше обучение</h2>

                <trainee-report :trainee_report="<?php echo e(json_encode($trainee_report)); ?>" :groups="<?php echo e(json_encode($head_in_groups)); ?>"></trainee-report>
            </div>
        </div>
    </div>
<?php endif; ?>

    <?php if($is_recruiter): ?>
    <!-- Информация для рекрутера -->
    <div id="recruiter_info" class="row">
       
        <div class="col-xl-12 mt-3 mb-3">
            <div class="ublock">
                <h2 class="big-title">Информация по показателям вашей группы</h2>
                <div class="ublocx mb-4">
                    <t-recruiter-stats :data="<?php echo e($recruiter_stats); ?>" daysInMonth="<?php echo e(date('d')); ?>" :rates="<?php echo e($recruiter_stats_rates); ?>"></t-recruiter-stats>
                </div>
                <div class="ublocxk mb-4">
                    <t-recruting-user :month="<?php echo e(json_encode($month)); ?>"  :records="<?php echo e(json_encode($recruiter_records)); ?>" name="<?php echo e(auth()->user()->LAST_NAME . ' ' . auth()->user()->NAME); ?>"></t-recruting-user>
                </div> 
                <div class="ublocxk">
                <g-recruting :records="<?php echo e($indicators); ?>" :isAnalyticsPage="false"></g-recruting>
                </div>
            </div>
        </div>
        
    </div>

    <?php elseif(count(json_decode($activities)) > 0): ?>
    <!-- Информация -->
    <div id="personal_indicators" class="row">

        <div class="col-xl-12 mt-27 mb-3">
            <div class="ublock">
                <h2 class="big-title">Сравните Ваши показатели с другими сотрудниками</h2>

                <t-user-analytics :activities="<?php echo e(json_encode($activities)); ?>" :quality="<?php echo e(json_encode($quality)); ?>" />

            </div>
        </div>

    </div>
    
    <?php endif; ?>

</div>

    <!-- Модалка Правила выдачи оплаты -->
    <div class="modal fade" id="oplataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body text-left">
                    <h5>Правила выдачи оплаты</h5>
                </div>
                <div class="text-left mb-3 texter px-3 text-black">
                    <p><strong>Вопрос: Когда будет ЗП?</strong><br />Ответ: ЗП по договору выплачивается до 25 числа&nbsp;<span class="bx-messenger-ajax bx-messenger-ajax-black" data-entity="date" data-messageid="8136640" data-ts="1630422000">следующего&nbsp;месяца</span>. Но обычно ЗП начинают выдавать в районе 10 числа&nbsp;<span class="bx-messenger-ajax bx-messenger-ajax-black" data-entity="date" data-messageid="8136640" data-ts="1630422000">следующего&nbsp;месяца</span>.<br /><br /><strong>Вопрос: Почему кто-то получил ЗП а я еще нет?</strong><br />Ответ: Это может произойти по нескольким причинам. Во-первых, потому что выплаты начинаются по мере готовности ведомости. Во-вторых, потому что на банковских картах есть лимит в сутки на перечисление.<br /><br /><strong>Вопрос: Мне начислили неправильную сумму &ndash; что мне делать?</strong><br />Ответ: Вам нужно обратиться к Вашему руководителю. Перед этим проверить начисление в системе учета рабочего времени.<br /><br /><strong>Вопрос: Кому первому выдают ЗП?</strong><br />Ответ: Очередность выдачи ЗП осуществляется в случайном порядке.<br /><br /><strong>Вопрос: Когда будет повышение ЗП?</strong><br />Ответ: Каждые 3&nbsp;<span class="bx-messenger-ajax bx-messenger-ajax-black" data-entity="date" data-messageid="8136640" data-ts="1627743600">месяца</span>&nbsp;происходит индексация ЗП, то есть повышение ЗП оператора колл-центра на 5 000 тенге.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Модалка  Нет кнопки "Начать день"-->
    <div class="modal fade" id="cantStartModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body text-left">
                    <h5>Нет кнопки "Начать день"</h5>
                </div>
                <div class="text-left mb-3 texter px-3 text-black">
                    <p>Если вы не работаете в системе U-calls и вы работаете на удаленной основе, то у вас должны отображаться кнопки "Начать день" и "Завершить день". </p>
                    <p>Попробуйте нажать комбинацию клавиш на клавиатуре:</p>
                    <p>На настольном компьютере: <b>CTRL + F5</b></p>
                    <p>На ноутбуке: <b>CTRL + FN + F5</b></p>
                </div>
            </div>
        </div>
    </div>



    <!-- Модалка при ошибке -->
    <?php if($errors->any()): ?>
    <u-modal :items="[<?php echo e(json_encode($errors->first())); ?>]" title="Не сохранено" />
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    $('#showPasswordInputs').click(function() {
        $('#pwdInputs').slideToggle();
    });
</script>
<script>
$('#hoverPulse').mouseenter(function() {
    $('#pulse').css('box-shadow', '0 0 16px 12px #7f91bd');
});
$('#hoverPulse').mouseout(function() {
    $('#pulse').css('box-shadow', 'unset');
});
</script>
<script>
    (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_8_dzfbjh.js');
</script>
<script>
    function copyClipboard() {
    var copyText = document.getElementById("copyClipboard");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    
    var tooltip = document.getElementById("myTooltip");
    tooltip.innerHTML = "Скопировано: " + copyText.value;
    }

    function outcopyClipboard() {
    var tooltip = document.getElementById("myTooltip");
    tooltip.innerHTML = "Скопировать";
    }
</script>
<script>
$('.question .title').click(function() {
    $(this).toggleClass('active');
    $(this).parent().find('.answer').slideToggle();
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('.answer').submit(function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    let answer;
    if(id == 4) {
        answer = $('#answer-4').is(':checked');
        send(id, answer);
    } else if(id == 5){
        sendFile();
    } else { 
        answer = $('textarea', this).val();
        if(answer.length > 10) {
            send(id, answer);
        }
    }
    

    
});

function send(question, answer) {
    $.ajax({
        url: '/user/save/answer',
        type: 'POST',
        data: {
            user_id: <?php echo e(auth()->user()->ID); ?>,
            question: question,
            answer: answer,
        },
        success: function(response) {
            $('.answer[data-id="'+ question +'"').removeClass('active');
            $('.answer[data-id="'+ question +'"').slideUp();
        }
    });
}

function sendFile() {
    var fd = new FormData();

   // Read selected files
   for (var index = 0; index < document.getElementById('answer-5').files.length; index++) {
        fd.append("answers[]", document.getElementById('answer-5').files[index]);
   }

    if(document.getElementById('answer-5').files.length > 0){
        fd.append('question',5);
        fd.append('user_id',<?php echo e(auth()->user()->ID); ?>);
    
        $.ajax({
            url: '/user/save/answer',
            type: 'POST',
            dataType: 'json',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response) {
                //response = JSON.parse(response);
                $('.certificates').html('');
                response.files.forEach(file => {
                    
                    $('.certificates').append('<a href="' + file + '" target="_blank" class="link">Файл</a><br>');
                });
                
            }
        });

    }
}




</script>
<script>

<?php if($blocks_number == 3): ?>

let margin = '1.5%';
let big = '50%';
let small = '23.5%';
let normal = '33.333%';

$('#box-2').css('margin-right', margin);
$('#box-3').css('margin-left', margin);

<?php elseif($blocks_number == 2): ?>

let margin = '1.5%';
let big = '50%';
let small = '47.5%';
let normal = '50%';

$('#box-2').css('margin-right', margin);


<?php else: ?> 

let margin = '0%';
let big = '100%';
let small = '100%';
let normal = '100%';


$('#box-3').css('max-width', big);

<?php endif; ?>


$('#box-1').mousemove(function(){
    $('#box-1').css('width', big);
    $('#box-1').css('opacity', '1');
    $('#box-2').css('opacity', '0.2');
    $('#box-3').css('opacity', '0.2');
    $('#box-2').css('width', small);
    $('#box-3').css('width', small);
    $('#owl').trigger('refresh.owl.carousel');
});

$('#box-2').mousemove(function(){
    $('#box-2').css('width', big);
    $('#box-1').css('width', small);
    $('#box-3').css('width', small);
    $('#box-2').css('opacity', '1');
    $('#box-1').css('opacity', '0.2');
    $('#box-3').css('opacity', '0.2');
});

$('#box-3').mousemove(function(){
    $('#box-3').css('width', big); 
    $('#box-1').css('width', small);
    $('#box-2').css('width', small);
    $('#box-3').css('opacity', '1');
    $('#box-2').css('opacity', '0.2');
    $('#box-1').css('opacity', '0.2');
});

$('.non-pillar').mousemove(function(){
    $('#box-1').css('width', normal);
    $('#box-2').css('width', normal);
    $('#box-3').css('width', normal);
    $('#box-1').css('opacity', '1');
    $('#box-2').css('opacity', '1');
    $('#box-3').css('opacity', '1');
    $('#owl').trigger('refresh.owl.carousel');
});

$('#left-panel').mousemove(function(){
    $('#box-1').css('width', normal);
    $('#box-2').css('width', normal);
    $('#box-3').css('width', normal);
    $('#box-1').css('opacity', '1');
    $('#box-2').css('opacity', '1');
    $('#box-3').css('opacity', '1');
    $('#owl').trigger('refresh.owl.carousel');
});

</script>
<script>
$('.changers').change(function(e) {
    $('#saveProfile').show();
});
</script>
<script src="/js/owl.carousel.min.js"></script>
<script>
$('#owl').owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    dots: false,
    items: 1,
    //autoplay: true,
    //autoplayTimeout: 5000,
    //responsiveClass: true,
   // autoHeight:true
});


</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="/js/owl.carousel.min.css">
<link rel="stylesheet" href="/js/owl.theme.default.min.css">
<style>
#header {
    display: block !important;
}   
</style>
<style>
    html {
        scroll-behavior: smooth;
    }
    .fast-links {
        width: 100%;
        overflow-x: auto;
        border: 1px solid rgba(12,83,125,.14);
        background: white;
        padding: 5px 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .fast-links a {
        color: #4e4e4e;
        margin-right: 10px;
        cursor: pointer;
        font-weight: 600;
    }
    .fast-links a:hover {
        color: #1890ff;
    }
</style>
<style>
    .toooltip div {
        display: none;
        position: absolute;
        left: 140%;
        z-index: 700;
        /* bottom: 100%; */
        background: #f8fcff;
        top: -5px;
        line-height: 16px;
        min-width: 250px;
        /* display: block; */
        padding: 10px;
        font-size: 13px;
        font-weight: 400;
        font-family: 'Open Sans', sans-serif;
        height: auto;
        border: 1px solid #dee7eb;
    }
    .toooltip {
        position: relative;
    }
    .toooltip:hover div{
        display: block;
    }
</style>
<style>
/* .owl-carousel {
    border: 1px solid #dee7ec;
} */

.relative {
    position: relative;
}
#pulse {
    transition: 1s ease all;
}
</style>
<style>


.owl-stage {
    flex-wrap: wrap;
}
.pillars,  
.pillars2 {
    transition: 0.5s ease all;
}

@media(min-width: 1024px) {
    .pillars {
        max-width: 66.666%;
        flex: 0 0 66.666%;
    }
    .pillars2 {
        max-width: 33.333%;
        flex: 0 0 33.333%;
    }
    .pillars.small {
        max-width: 50%;
        flex: 0 0 50%;
    }
    .pillars2.big {
        max-width: 50%;
        flex: 0 0 50%;
    }
    .pillars.big {
        max-width: 80%;
        flex: 0 0 80%;
    }
    .pillars2.small {
        max-width: 20%;
        flex: 0 0 20%;
    }
}

.pulse-on-hover {
    -webkit-animation: blinktwo infinite 1.5s;
    animation: blinktwo infinite 1.5s;
}
</style>
<style>
    
    #currency {
        background: #f8fcfe;
        border: 1px solid #daecf5;
        font-size: 13px;
        margin-top: 0 !important;
        height: 35px;
    }
    .user-name {
        line-height: 1em;
        margin-bottom: 0;
        font-size: 1em;
    }
    .user-id {
        font-size: 0.9em;
        color: #000;
    }
    .text-black p strong{color:#383838}
    .w-200 {width: 170px;}

    .ublock {
        padding: 20px;
        background: #fff;
        border: 1px solid rgba(12,83,125,.14);
        border-radius: 5px;
    } 

    .mt-27 {
        margin-top: 25px;
    }
    .data-info-1 {
        margin-right: 15px;
        margin-bottom: 0;
    }

    .data-info-1 img {
        max-width: 300px;
        background: #f0f0f0;
        border: 1px solid #03a9f405
    }

    .contacts-info2 {
        border-bottom: 2px solid #e4e4e4;
        margin-left: 175px;
        padding-bottom: 20px;
        width: 440px;
    }

    .contacts-info3 {
        margin-left: 175px;
        display: block;
        padding: 50px 0;
    }

    .data-information {
        display: flex;
        flex-wrap: wrap;
        padding: 0 0 0 0;
        justify-content: space-between;
    }

    .contact-information h1 {
        color: #202226;
        font-family: "Open Sans";
        font-size: 18px;
        font-weight: 400;
        line-height: 33px;
        padding: 0 0 30px 0;
    }

    .data-info-2 p {
        color: #202226;
        font-family: "Open Sans";
        font-size: 14px;
        font-weight: 700;
        line-height: 35px;
        margin-bottom: 10px;
    }

    p.groups,
    .data-information input {
        margin: 0;
        line-height: 20px;
        padding: 7.5px 10px;
        background: #f8fcfe;
        border: 1px solid #daecf5;
        width: 100%;
        box-sizing: border-box;
        /* border: none; */
        border-radius: 3px;
        font: 14px/30px 'Open Sans', Arial, Helvetica, sans-serif;
        color: #202226;
        margin-bottom: 10px;
        font-size: 13px;
        
    }

    .data-information input {
        height: 35px;
    }

    p.groups span {
        display: block;
        line-height: 20px;
    }
    .pos-desc th,
    .pos-desc td pre {
        font-size: 12px;
        font-family: 'Open sans';
        white-space: -moz-pre-wrap;
        white-space: -o-pre-wrap;
        word-wrap: break-word;
        white-space: pre-wrap;
    }
    pre.gr_pre {
        white-space: -moz-pre-wrap;
        white-space: -o-pre-wrap;
        word-wrap: break-word;
        white-space: pre-wrap;
        font-family: 'Open sans';
        font-size: 12px;
    }
    .data-information input[type="submit"],
    .data-information input[type="button"] {
        background-color: #d632e9;
        transition: all 0.5s linear;
        height: 35px;
        width: 100%;
        border-radius: 50px;
        line-height: 5px;
        color: #fff;
        margin-top: 10px;
    }

    .data-information input[type="submit"]:hover,
    .data-information input[type="button"]:hover {
        background-color: #c00eb5;
        transition: all 0.2s linear;
    }

    .data-info-2 {
        padding-right: 30px;
    }

    .data-info-3 {
        padding-right: 30px;
    }

    .data-info-4 {
        padding-right: 50px;
    }

    .contacts-info {
        display: flex;
        margin-left: 0;
        padding-bottom: 40px;
    }

    .obwie_dannye {
        color: #202226;
        font-family: "Open Sans";
        font-size: 22px;
        font-weight: 400;
        margin-top: 30px;
        margin-bottom: 50px;
    }

    .title span {
        font-size: 12px;
        color: cornflowerblue;
        text-decoration: underline;
    }

    .iconsfile {
        width: 170px !important;
        margin: auto;
    }

    /* .box span {
        width: 170px;
        font-size: 12px;
        overflow: hidden;
        text-align: center;
    }

    .box label {
        vertical-align: top;
    }

    .box {
        display: flex;
        align-items: end;

    }

    .box label {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .box svg {
        display: block;
        height: 50px;
        fill: #1076b0;
        cursor: pointer;
        width: 310px;
        height: 103px;
        background-size: contain;
        cursor: pointer;
        background: rgba(39, 44, 51, 0.1);
        padding: 30px;
        fill: #96da34;
        margin: 0 5px;
    } */

    .active a {
        color: green;
    }

    #right-panel.show-sidebar .content {
        padding: 5px;
    }
    #right-panel.show-sidebar .content {
                padding-left: 10px;
            }
    .content h2 {
        font-size: 20px;
        margin-bottom: 25px;
        min-width: 195px;
    }

    .photo-label {
        display: flex;
        flex-direction: column;
        text-align: center;
        width: fit-content;
        align-items: center;
        margin: 0 auto;
    }

    .flex-1 {
        flex: 1
    }

    .w-full {
        width: 100%;
    }

    @media(min-width: 1024px) {
        .pl-md-0 {
            padding-left: 0;
        }
    }

    .h-auto {
        height: 100%;
    }
    p.groups span {
        font-size: 13px;
    }
    body {
        background: #edf0f1;
    }
    .table-2 p{
        font-size: 13px;
    }
</style>
<style>
    .tooltips {
    position: relative;
    display: inline-block;
    }

    .tooltips .tooltiptext {
    visibility: hidden;
    width: auto;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 150%;
    left: 50%;
    margin-left: -75px;
    opacity: 0;
    transition: opacity 0.3s;
    }

    .tooltips .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
    }

    .tooltips:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
    }
.wrappy {
    padding: 15px;
    border: 1px solid #eff2f3;
    height: 100%;
    background: aliceblue;
    background: #f6fafc;
}
</style>
<style>
.question {
    padding: 7px 0;
}
.question .title {
    cursor: pointer;
    position: relative;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Open Sans', sans-serif;
    line-height: 16px;
    color: #000;
    margin-bottom: 0;
    user-select:none;
    padding-right: 15px;
}
.question .answer {
    display: none;
    margin-top: 10px;
    padding-right: 20px;
}
.question textarea {
    min-height: 120px;
    font-size: 13px;
}
.question .title .fa {
    position: absolute;
    transition: 0.3s ease all;
    right: 5px;
    top: 0;
    font-size: 16px;
}
.question .title.active .fa {
    transform: rotate(180deg);
}
.link {
    display: inline-block;
    margin-right: 10px;
}
.breadcrumbs {
    display: none !important;
}
.big-title {
    text-align: center !important;
    font-weight: 700;
    color: #272c32;
    text-transform: uppercase; 
    font-size: 24px;
}
h2.small-title {
    font-size: 15px !important;
    font-weight: 600;
}
#owl .owl-nav .owl-prev {
    font-size: 24px;
    position: absolute;
    right: 15px;
    top: -17px;
}
#owl .owl-nav .owl-next {
    font-size: 24px;
    position: absolute;
    right: 0px;
    top: -17px;
}
</style>
<style>
    .boxes {
        display: flex;
    }
 /*    .box {
        flex: 1;
        transition: 1s ease all;
       max-width:100%;
    } 
    .box:hover {
        flex: 5;
    } */

    .box {
        transition: 1s ease all;
        max-width: 70%;
        background: #fff;
        max-height: 420px;
        overflow-x: hidden;
        padding: 15px;
        z-index: 22;
        border: 1px solid #eff2f3;
    }

    .box::-webkit-scrollbar {
        width: 0px;
    }
    .box > div::-webkit-scrollbar {
        width: 0;
    }
</style>
<style>
.table-success {
    background-color: #01c601 !important;
}
.table-danger {
    background-color: #ff7669  !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>