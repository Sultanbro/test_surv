<script>
import DefaultLayout from '@/layouts/DefaultLayout'

export default {
	name: 'FAQView',
	components: {
		DefaultLayout,
	},
	data(){
		return {
			page: '',
		}
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<div class="old__content">
				<div class="">
					<div
						id="card1"
						class="card p-3"
					>
						<h3>Учет времени</h3>
						<p>
							<strong>Кнопка &laquo;Начать день&raquo;</strong>
						</p>
						<p>Данная функция фиксирует время начала рабочего дня у удаленных работников:</p>
						<ol>
							<li>У удаленных операторов он фиксируется по нажатию на кнопку &laquo;Начать рабочий день&raquo; в панели Учета времени, при этом нажать на кнопку можно не ранее 08:30. Время фиксации учитывается в соответствии с временем интернета. </li>
							<li>У штатных операторов данный показатель фиксируется по нажатии на сканер отпечатков Anviz W1 PRO у входа в колл-центр.</li>
						</ol>
						<p>Если у вас нет ни кнопки начать день, ни завершить день, значит вы работаете в системе U-calls. При нажатии на Готов в панели оператора начало дня фиксируется автоматически.</p>
						<p>
							<strong>Кнопка &laquo;Завершить день&raquo;</strong>
						</p>
						<p>Данная функция фиксирует время завершения рабочего дня у штатных и удаленных работников:</p>
						<ol>
							<li>Для того чтобы вручную завершить свой рабочий день по какой-либо из причин, будь то это завершенный полностью рабочий день, или вынужденное прерывание рабочего дня по причине надобности ухода с работы в этот день, или же из-за Вашего рабочего графика, Вам требуется нажать на кнопку &laquo;Завершить день&raquo;.</li>
						</ol>
						<p>После того как Вы нажмете на эту кнопку оплата за работу начисляться не будет.</p>
						<p>Так же эта кнопка может автоматически нажаться/завершить Ваш рабочий день, в этих случаях:</p>
						<ol>
							<li>По установленному рабочему времени группы, в которой Вы работаете. Например, Время работы Вашей группы до 18:00, значит система автоматически завершит Ваш рабочий день в это время. Если же Вы работаете в 2х или более отделах, то система завершит Ваш рабочий день в то время которое указано максимально, например, в одной группе 18:00 а в другой 19:00, значит система завершит в 19:00</li>
							<li>Если Вы находитесь более 10 минут в любом из статусов кроме &laquo;ГОТОВ&raquo; в панели оператора сервиса обзвона U-Calls <a href="http://joxi.ru/1A5wKYktG1J49m">http://joxi.ru/1A5wKYktG1J49m</a>, то система автоматически завершит Ваш рабочий день и не будет начислять Вам оплату. Если же Вы вернетесь в статус &laquo;ГОТОВ&raquo; в течени 10 минут, то система автоматически вернет Вас в рабочее время в панели Учета времени. </li>
						</ol>
						<div class="bp-text">
							<p>&nbsp;</p>
							<p>
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-size: 13pt; color: #0065aa;">
										<strong>Корпоративный&nbsp; портал Jobtron</strong>
									</span> разработан с целью облегчить понимание сотрудника о том сколько он заработал на текущий момент времени и как заработать больше, а также чтобы коммуницировать с коллегами и обучаться новым навыкам. А также портал помогает руководителям анализировать показатели продуктивности своего отдела на основе ежедневных автоматических отчетов.
								</span>
							</p>
							<p>
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Каждый сотрудник может ежедневно видеть, сколько он заработал денег на текущий момент времени.</span>
							</p>
							<p>
								<img
									src="/bpartners/1653044718.png"
									alt="картинка"
									width="947"
									height="382"
								>
							</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">&nbsp;</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-size: 13pt;font-family: arial, helvetica, sans-serif;color: #000000;">
									<strong>Кнопка «Начать день»</strong>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">Данная функция фиксирует время начала рабочего дня у удаленных работников:</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">У</span>
									<span style="font-weight: 500;">&nbsp;удаленных сотрудников начало работы фиксируется по нажатию на кнопку «Начать рабочий день» на странице "Мой профиль", при этом нажать на кнопку можно не ранее 08:30 и не позднее начала Вашего рабочего времени, которое указано на странице "мой профиль" (время фиксации учитывается в соответствии с временем в интернете)</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">У сотрудников которые работают в офисе компании показатель прихода на работу, фиксируется по нажатию на сканер отпечатков пальца у входа в колл-центр.</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">Если у вас нет ни кнопки начать день, ни завершить день, значит вы работаете в системе обзвона U-calls. При нажатии на Готов в панели оператора начало дня фиксируется автоматически.</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">&nbsp;</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-size: 13pt;color: #000000;font-family: arial, helvetica, sans-serif;">
									<strong>Кнопка «Завершить день»</strong>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">Данная функция фиксирует время завершения рабочего дня у штатных и удаленных работников:</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">Для того чтобы вручную завершить свой рабочий день по какой-либо из причин, будь то это завершенный полностью рабочий день, или вынужденное прерывание рабочего дня по причине надобности ухода с работы в этот день, или же из-за Вашего рабочего графика, Вам требуется нажать на кнопку «Завершить день».&nbsp;</span>
									<span style="font-weight: 500;">После того как Вы нажмете на эту кнопку оплата за работу начисляться не будет.</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">&nbsp;</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">Так же эта кнопка может автоматически нажаться/завершить Ваш рабочий день, в этих случаях:</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">По установленному рабочему времени группы, в которой Вы работаете. Например, Время работы Вашей группы до 18:00, значит система автоматически завершит Ваш рабочий день в это время. Если же Вы работаете в 2х или более отделах, то система завершит Ваш рабочий день в то время которое указано максимально, например, в одной группе 18:00 а в другой 19:00, значит система завершит в 19:00</span>
								</span>
							</h3>
							<h3 style="box-sizing: border-box; margin: 0px 0px 0.5em; line-height: 1.2;">
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">
									<span style="font-weight: 500;">Если Вы находитесь более 10 минут в любом из статусов кроме «ГОТОВ» в панели оператора сервиса обзвона U-Calls, то система автоматически завершит Ваш рабочий день и не будет начислять Вам оплату. Если же Вы вернетесь в статус «ГОТОВ» в течении 10 минут, то система автоматически вернет Вас в рабочее время в панели Учета времени.</span>
								</span>
							</h3>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>
								<span style="color: #000000;font-family: arial, helvetica, sans-serif;font-size: 13pt;">
									<strong>Важно!!!</strong>
								</span>
							</p>
							<p>
								<span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">В случае, если при начале рабочего дня Вы нажали статус "Готов" или кнопку "Начать рабочий день" позже 8:45, то Вам будет автоматически начислено депримирование, согласно Системы депремирования.</span>
							</p>
							<p>
								<span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">Если Вы вообще не отметитесь в системе, то этот день Вам не будет засчитан, как отработанный, и соответственно не будет оплачен.</span>
							</p>
							<p>&nbsp;</p>
							<p>
								<span style="color: #000000;font-family: arial, helvetica, sans-serif;font-size: 13pt;">
									<strong>Как войти в личный кабинет:</strong>
								</span>
							</p>
							<p>
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">1. Переходим по ссылке&nbsp; <a
									href="../login"
									target="_blank"
								>http://joytron.org/</a>
								</span>
							</p>
							<p>
								<span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">2. Заходите используя свой логин и пароль</span>
							</p>
							<p>&nbsp;</p>
						</div>
					</div>
					<div
						id="card2"
						class="card p-3"
					>
						<h3>Показатели в таблицах аналитики</h3>
						<p>
							<strong>Оклад &ndash; </strong>эта строка показывает фактические начисления за каждый отработанный Вами день. Так же тут показаны суммы депримирований, если таковые имелись, например, опоздания, выговоры, прогулы. Вам начисляется ежедневно сумма в строку &laquo;Оклад&raquo; по простой формуле: Ваш оклад, поделенный на кол-во рабочих дней в месяце (это кол-во дней минус кол-во воскресений) далее поделенный на количество рабочих часов и умноженных на фактически отработанные часы, которые отражаются в строке &laquo;часы работы&raquo;
						</p>
						<p>
							<strong>Время прихода &ndash; </strong>в данной строке указано время начала рабочего дня, это тогда, когда Вы нажали на кнопки &laquo;Начать день&raquo; если Вы штатный сотрудник или &laquo;Готов&raquo; если Вы работаете удаленно.
						</p>
						<p>
							<strong>Часы работы &ndash; </strong>это показатель количества отработанных часов за день.
						</p>
						<p>
							<strong>Оплата труда</strong>
						</p>
						<p>Она делится на 2 показателя + внутренняя валюта. Первая часть &mdash; это оклад, и вторая это KPI (выполнение определённых показателей в работе)</p>
						<p>
							<strong>Показатель &laquo;Баланс Оклада&raquo;</strong>
						</p>
						<p>Вы можете каждый час наблюдать как увеличивается окладная сумма, которую Вы заработали за каждый час работы. Если нажать на сумму, то Вы попадете на страницу где указаны данные начислений за каждый Ваш отработанный день. </p>
						<p>Стандартный оклад оператора составляет 80 000 тенге в месяц, из этого расчёта считается баланс оклада.</p>
						<p>При этом если оператор не сдал запланированный экзамен по прочитанной книге, и не получил положительную оценку от экзаменатора, то расчёт оклада за месяц будет автоматически перерассчитан из суммы 70&nbsp;000 тенге.</p>
						<p>Если же оператор и на второй месяц не сдал запланированный экзамен по прочитанной книге, то оплата будет рассчитываться автоматически из суммы оклада 60&nbsp;000 тенге в месяц.</p>
						<p>
							План книг указан на странице <a
								href="/timetracking/books"
								target="_blank"
							>плана чтения книг</a>.
						</p>
						<p>
							<strong>Показатель &laquo;</strong>
							<strong>KPI&raquo;</strong>
						</p>
						<p>Это показатель выполнения определённых дополнительных показателей в работе, например, таких как:</p>
						<ul>
							<li>Минуты разговора</li>
							<li>Средний рейтинг оператора</li>
							<li>Переданные клиенты</li>
						</ul>
						<p>Ниже с показателя &laquo;Баланс оклада&raquo; находится состояние баланса &laquo;KPI&raquo;, данный показатель рассчитывается по формуле и делает дополнительные начисления к Вашей оплате труда.</p>
						<p>Кликнув на сумму KPI Вам будет показано подробное состояние начислений KPI на сегодняшний день.</p>
						<p>Показатели KPI могут меняться каждый месяц, и устанавливаются они руководителями групп.</p>
						<!-- <p><strong>Внутренняя Валюта - Партнерики</strong></p> -->
						<!-- <p>Помимо заработной платы в национальной валюте тенге, Вы получаете внутреннюю валюту компании.</p><p>Валюта называется &laquo;Партнерики&raquo;. Их Вы получаете каждый день за лучшие результаты по звонкам.</p><p>Собранные &laquo;Партнерики&raquo; Вы можете использовать для улучшения своего рабочего места, обменяв их на
                        мягкое
                        кресло, мощный ноутбук или другие плюшки.</p> -->
						<div class="bp-text">
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-family: arial, helvetica, sans-serif;">
									<span style="font-size: 12pt; color: #000000;">
										<span style="box-sizing: border-box; font-weight: bolder;">1. Баланс оклада –&nbsp;</span>это показатель фактически начисленных денег за каждый отработанный Вами день. Так же тут показаны суммы депримирований, если они были, например, опоздания, нарушения правил, прогулы. Вам начисляется ежедневно сумма в строку «Оклад» по простой формуле: Ваш оклад, поделенный на кол-во рабочих дней в месяце (это кол-во дней минус кол-во воскресений) далее поделенный на количество рабочих часов и умноженных на фактически отработанные часы, которые отражаются в строке «часы работы».&nbsp; </span>Вы можете каждый час наблюдать как увеличивается окладная сумма, которую Вы заработали за каждый час работы.&nbsp; </span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-size: 12pt; color: #000000; font-family: arial, helvetica, sans-serif;">
									<span style="box-sizing: border-box;">Время прихода –&nbsp;</span>в данной строке указано время начала рабочего дня, это тогда, когда Вы нажали на кнопки «Начать день» если Вы штатный сотрудник или «Готов» если Вы работаете удаленно. </span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-size: 12pt; color: #000000; font-family: arial, helvetica, sans-serif;">
									<span style="box-sizing: border-box;">Часы работы –&nbsp;</span>это показатель количества отработанных часов за день. </span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
&nbsp;
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-family: arial, helvetica, sans-serif;">
									<span style="font-size: 12pt; color: #000000;">
										<span style="box-sizing: border-box; font-weight: bolder;">2. Показатель «</span>
										<span style="box-sizing: border-box; font-weight: bolder;">KPI»,&nbsp;</span>
									</span>
									<span style="font-size: 12pt; color: #000000;">это показатель выполнения определённых дополнительных активностей в работе, например, таких как:</span>
								</span>
							</p>
							<ul style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; padding-left: 30px; list-style: none; color: rgba(0, 0, 0, 0.65); font-size: 14px;">
								<li style="box-sizing: border-box;">
									<span style="font-size: 12pt; color: #000000; font-family: arial, helvetica, sans-serif;">Минуты разговора</span>
								</li>
								<li style="box-sizing: border-box;">
									<span style="font-size: 12pt; color: #000000; font-family: arial, helvetica, sans-serif;">Средний рейтинг оператора</span>
								</li>
								<li style="box-sizing: border-box;">
									<span style="font-size: 12pt; color: #000000; font-family: arial, helvetica, sans-serif;">Переданные клиенты</span>
								</li>
							</ul>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-family: arial, helvetica, sans-serif;">
									<span style="font-size: 12pt; color: #000000;">Кликнув на сумму KPI Вам будет показано подробное состояние выполнения назначенных Вам активностей и заработанная сумма денег на сегодняшний день.&nbsp;</span>
									<span style="font-size: 12pt; color: #000000;">Показатели KPI могут меняться каждый месяц, и устанавливаются они руководителями отделов.</span>
								</span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
&nbsp;
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-family: arial, helvetica, sans-serif;">
									<strong>
										<span style="font-size: 12pt; color: #000000;">3. Бонусы:&nbsp;</span>
									</strong>
									<span style="font-size: 12pt; color: #000000;">это деньги которые Вы можете зарабатывать дополнительно каждый день, и Ваша сумма к выплате за месяц будет выше, чем просто оклад. Например бонусы могут начисляться за то, что Вы первый в отделе сделаете больше какой то активности, например больше согласий до обеда, или за день, или больше минут проговорите и тому подобное. Кликните на иконку "Бонусы" и там будет написано, как Вам заработать больше бонусов.</span>
								</span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
&nbsp;
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-family: arial, helvetica, sans-serif;">
									<span style="font-size: 12pt; color: #000000;">
										<strong>4. Квартальная премия.&nbsp;</strong>Чем Выше Вы по должности, тем больше вероятность, что у Вас есть заработать квартальную премию. Она назначается Директорами компании. Поэтому обучатся курсам которые Вам назначены и расти по карьерной лестнице Вам очень выгодно и с финансовой точки зрения. </span>
								</span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
&nbsp;
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
								<span style="font-family: arial, helvetica, sans-serif;">
									<span style="font-size: 12pt; color: #000000;">
										<strong>5. Награды:&nbsp;</strong>это нематериальная мотивация, хотя в этом разделе есть и материальные поощрения, обязательно изучите этот раздел. Тут есть и сертификаты и грамоты, и возможность заработать внутреннюю валюту, которую потом можно обменять на физические, ценные вещи.&nbsp; </span>
								</span>
							</p>
							<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
&nbsp;
							</p>
						</div>
					</div>
					<div
						id="card3"
						class="card p-3 text-black"
					>
						<h3>Правила выдачи оплаты</h3>
						<p>
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;"> 1. Какой график? </span>
							<br style="box-sizing: border-box;"> Есть 3 разных графика работы на полный день и 3 графика на пол дня: <br style="box-sizing: border-box;"> на полный день <br style="box-sizing: border-box;"> с 08:45 - 19:00 <br style="box-sizing: border-box;"> с 13:00 - 23:00 <br style="box-sizing: border-box;">с 18:00 - 02:00 <br style="box-sizing: border-box;">на пол дня <br style="box-sizing: border-box;">c 08:45 - 13:00 <br style="box-sizing: border-box;">c 14:00 - 19:00 <br style="box-sizing: border-box;">c 19:00 - 23:00
						</p>
						<p>
							<br style="box-sizing: border-box;">
							<br style="box-sizing: border-box;">
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">2. Сколько дней обучения для стажера?</span>
							<br style="box-sizing: border-box;">Обучение длится в среднем 5-7 дней, все зависит от усваиваемости материала и способности обучаться. Чем раньше Вы готовы будете рассказать нам о продукте с которым Вы будете работать, тем раньше начнете официально работать.
						</p>
						<p>
							<br style="box-sizing: border-box;">
							<br style="box-sizing: border-box;">
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">3. Как я могу заработать больше проходя обучающие курсы ?</span>
							<br style="box-sizing: border-box;">Обучающие курсы проходить нужно обязательно, они у Вас указаны на странице Вашего профиля, и Вам за это начисляются баллы, а баллы это деньги которые мы прибавим к Вашей выплате за отработанный месяц.
						</p>
						<p>
							<br style="box-sizing: border-box;">
							<br style="box-sizing: border-box;">
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">4.Можно ли самому выбирать выходной день?</span>
							<br style="box-sizing: border-box;">Выходные дни в разных отделах, в разные дни и о них Вам сообщит Вам руководитель отдела. <br style="box-sizing: border-box;">
							<br style="box-sizing: border-box;">
						</p>
						<p>
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">5. Как можно получить аванс?</span>
							<br style="box-sizing: border-box;">Аванс Вы можете запросить при достижении определенных активностей (показателей) в Вашей группе, установленных Вашим руководителем.
						</p>
						<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
&nbsp;
						</p>
						<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">6. Вопросы по оплате</span>
						</p>
						<p style="box-sizing: border-box; font-family: 'Open Sans', sans-serif; margin-top: 0px; margin-bottom: 1em; font-size: 16px; line-height: 24px; color: #333333;">
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">
								<span style="background-color: #ffffff; font-family: sans-serif;">Вопрос:</span>
								<span style="background-color: #ffffff; font-family: sans-serif;" />Сколько составляет оклад? </span>
							<br style="box-sizing: border-box;">
							<span style="color: #000000; font-family: sans-serif; font-size: medium; background-color: #ffffff;">Ответ: </span>Окладная часть оператора зависит от количества отработанных часов в месяце. Работая полный день окладная часть достигает 80 000 тенге + Kpi в размере до 60 000 тенге в зависимости от отдела (проект) в котором Вы работаете.
						</p>
						<p>
							<span style="box-sizing: border-box; background-color: #ffffff; font-weight: bolder; color: #383838;">Вопрос: Когда будет ЗП?</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="background-color: #ffffff;">Ответ: начисления по договору выплачивается до 25 числа&nbsp;</span>
							<span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								style="box-sizing: border-box; background-color: #ffffff;"
								data-entity="date"
								data-messageid="8136640"
								data-ts="1630422000"
							>следующего&nbsp;месяца</span>
							<span style="background-color: #ffffff;">. Но обычно ЗП начинают выдавать в районе 10 числа&nbsp;</span>
							<span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								style="box-sizing: border-box; background-color: #ffffff;"
								data-entity="date"
								data-messageid="8136640"
								data-ts="1630422000"
							>следующего&nbsp;месяца</span>
							<span style="background-color: #ffffff;">.</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="box-sizing: border-box; background-color: #ffffff; font-weight: bolder; color: #383838;">Вопрос: Почему кто-то получил ЗП а я еще нет?</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="background-color: #ffffff;">Ответ: Это может произойти по нескольким причинам. Во-первых, потому что выплаты начинаются по мере готовности ведомости Вашим руководителем. Во-вторых, потому что на банковских картах есть лимит в сутки на перечисление.</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="box-sizing: border-box; background-color: #ffffff; font-weight: bolder; color: #383838;">Вопрос: Мне начислили неправильную сумму &ndash; что мне делать?</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="background-color: #ffffff;">Ответ: Вам нужно обратиться к Вашему руководителю. Перед этим проверить начисление в системе учета рабочего времени.</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="box-sizing: border-box; background-color: #ffffff; font-weight: bolder; color: #383838;">Вопрос: Кому первому выдают ЗП?</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="background-color: #ffffff;">Ответ: Очередность выдачи ЗП осуществляется в случайном порядке.</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="box-sizing: border-box; background-color: #ffffff; font-weight: bolder; color: #383838;">Вопрос: Когда будет повышение ЗП?</span>
							<br style="box-sizing: border-box; background-color: #ffffff;">
							<span style="background-color: #ffffff;">Ответ: Каждые 3&nbsp;</span>
							<span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								style="box-sizing: border-box; background-color: #ffffff;"
								data-entity="date"
								data-messageid="8136640"
								data-ts="1627743600"
							>месяца</span>
							<span style="background-color: #ffffff;">&nbsp;для операторов происходит индексация ЗП, то есть повышение ЗП оператора колл-центра на 5 000 тенге.</span>
						</p>
						<p>
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">
								<span style="background-color: #ffffff;">Вопрос:</span>
								<span style="background-color: #ffffff;" />От чего зависит бонусная часть? </span>
							<br style="box-sizing: border-box;">
							<span style="background-color: #ffffff;">Ответ: </span>В каждой группе колл центра, есть KPI - это показатели по разным активностям оператора, например звонки и минуты. KPI в разных отделах от 20 000 - 60 000 тысяч тенге, и чем активнее Вы работаете, тем больше по итогу&nbsp; <span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								style="box-sizing: border-box;"
								data-entity="date"
								data-messageid="8433466"
								data-ts="1630422000"
							>месяца</span>&nbsp;получите оплату. Так же есть и другие виды материальной мотивации которые описаны в базе знаний. <br style="box-sizing: border-box;">
							<br style="box-sizing: border-box;">
							<span style="box-sizing: border-box; font-weight: bolder; color: #383838;">
								<span style="background-color: #ffffff;">Вопрос: </span>З/П в договоре указана уже с вычетом налогового обложения 10%? </span>
							<br style="box-sizing: border-box;">
							<span style="background-color: #ffffff;">Ответ: </span>В договоре указана сумма без вычета.
						</p>
						<p>
							<strong>Вопрос: Когда будет ЗП?</strong>
							<br>Ответ: ЗП по договору выплачивается до 25 числа&nbsp; <span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								data-entity="date"
								data-messageid="8136640"
								data-ts="1630422000"
							>следующего&nbsp;месяца</span>. Но обычно ЗП начинают выдавать в районе 10 числа&nbsp; <span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								data-entity="date"
								data-messageid="8136640"
								data-ts="1630422000"
							>следующего&nbsp;месяца</span>. <br>
							<br>
							<strong>Вопрос: Почему кто-то получил ЗП а я еще нет?</strong>
							<br>Ответ: Это может произойти по нескольким причинам. Во-первых, потому что выплаты начинаются по мере готовности ведомости. Во-вторых, потому что на банковских картах есть лимит в сутки на перечисление. <br>
							<br>
							<strong>Вопрос: Мне начислили неправильную сумму &ndash; что мне делать?</strong>
							<br>Ответ: Вам нужно обратиться к Вашему руководителю. Перед этим проверить начисление в системе учета рабочего времени. <br>
							<br>
							<strong>Вопрос: Кому первому выдают ЗП?</strong>
							<br>Ответ: Очередность выдачи ЗП осуществляется в случайном порядке. <br>
							<br>
							<strong>Вопрос: Когда будет повышение ЗП?</strong>
							<br>Ответ: Каждые 3&nbsp; <span
								class="bx-messenger-ajax bx-messenger-ajax-black"
								data-entity="date"
								data-messageid="8136640"
								data-ts="1627743600"
							>месяца</span>&nbsp;происходит индексация ЗП, то есть повышение ЗП оператора колл-центра на 5 000 тенге.
						</p>
					</div>
					<!--<div class="card p-3 text-black" id="card3"><h3>Частые вопросы</h3><p><strong>1. Сколько составляет оклад?</strong><br />Окладная часть оператора зависит от количества отработанных часов в месяце. Работая полный день окладная часть составляет 80 000 тенге + Kpi в размере до 60 000 тенге в зависимости от отдела (проект) в котором Вы работаете.
                    <br /><br /><strong>2. Какой график?</strong><br /> Есть 3 разных графика работы на полный день и 3 графика на пол дня:
                    <br />на полный день
                    <br />с 08:45 - 19:00
                    <br />с 13:00 - 23:00
                    <br />с 18:00 - 02:00
                    <br />на пол дня
                    <br />c 08:45 - 13:00
                    <br />c 14:00 - 19:00
                    <br />c 19:00 - 23:00
                    <br /><br /><strong>3. Сколько дней обучение?</strong><br />Обучение длится в среднем 5-7 дней, все зависит от усваиваемости материала и способности обучаться. Чем раньше Вы готовы будете рассказать нам о продукте с которым Вы будете работать, тем раньше начнете официально работать.
                    <br /><br /><strong>4. От чего зависит бонусная часть?</strong><br />В каждой группе колл центра, есть KPI - это показатели по разным активностям оператора, например звонки и минуты. KPI в разных отделах от 20 000 -60 000 тысяч тенге, и чем активнее Вы работаете, тем больше по итогу&nbsp;<span class="bx-messenger-ajax bx-messenger-ajax-black" data-entity="date" data-messageid="8433466" data-ts="1630422000">месяца</span>&nbsp;получите оплату. Так же есть и другие виды материальной мотивации которые описаны в корпроративной книге.
                    <br /><br /><strong>5. З/П в договоре указана уже с вычетом налогового обложения 10%?</strong><br />В договоре указана сумма без вычета.
                    <br /><br /><strong>6. Если за месяц прочитать 2 книги будет ли оплата 20000тг?</strong><br />Читать обязательно чтоыб у Вас сохранялся месяцчный оклад, если в месяц не читать, то оклад снизится на 10 000 тенге, а вот если прочитать еще одну или 2,3 дополнительных книги и сдать по ним экзамены, то Вам прибавится премия в размере суммы которая за книгу указана на странице чтения книг.
                    <br /><br /><strong>7.Можно ли самому выбирать выходной день?</strong><br />Выходные дни в разных отделах, в разные дни и о них Вам сообщит Вам руководитель группы.
                    <br /><br /><strong>8.Что делать если надо выходной на 15-20 дней?</strong><br />Вы можете написать руководителю, и уйти без содержания на это время, а после вернуться.
                    <br /><br /><strong>9. Как можно получить аванс?</strong><br />Аванс Вы можете запросить при достижении определенных активностей (показателей) в Вашей группе, установленных Вашим руководителем. </p></div>-->
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<style scoped>
    .text-black p strong{color:#383838}
ul,
ol {
    padding-left: 30px;
}
.content h3 {
  font-weight: 700;
}
.content p {
    color: #000;
}

.content a {
    color: #007bff;
    text-decoration: dashed;
}
#right-panel.show-sidebar .content {
  padding-left: 15px;
}
body {
    background: #f1f2f7;
}
.card {
    border: 1px solid #ccc;
    margin-bottom: 15px;
}


.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 7rem !important;
}
}
</style>