<!DOCTYPE html>
<html>

<head>

	<meta charset="utf-8">
	<!-- <base href="/"> -->

	<title>Личный кабинет</title>
	<meta name="description" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<link rel="icon" href="images/favicon.png">
	<meta property="og:image" content="/design/images/dist/preview.jpg">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Open+Sans:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/design/css/app.min.css">

</head>

<body ontouchstart="">
<div class="container header__container">
	<div class="header__profile _anim _anim-no-hide custom-scroll-y">
		<div class="profile__content">
			<img src="/design/images/dist/logo.png" alt="logo image" class="profile__logo">
			<a href="#" class="profile__button"><p>Начать рабочий день</p></a>
			<div class="profile__balance">Текущий баланс <p>3,567.12</p></div>
			<select class="select-css profile-select">
				<option>Май</option>
				<option>Апрель</option>
				<option>Март</option>
				<option>Февраль</option>
				<option>Январь</option>
			</select>
			<div class="profile__about">
				<div class="profile__name">Каримов Адиль</div>
				<div class="profile__job profile-border">Старший специалист группы</div>
				<div class="profile__salary profile-border">ОКЛАД 500 000</div>
				<div class="profile__wrapper">
					<p class="profile-border">5 - 2</p>
					<p class="profile-border">09:00 - 18:00</p>
					<p class="profile-border">8 часов</p>
				</div>
				<select class="select-css">
					<option>KZT Казахстанский тенге</option>
					<option>KZT Казахстанский тенге</option>
					<option>KZT Казахстанский тенге</option>
					<option>KZT Казахстанский тенге</option>
					<option>KZT Казахстанский тенге</option>
				</select>
			</div>
			<div class="profile__point profile-box">
				<div class="profile__title">Цель на сегодня</div>
				<div class="profile__point-wrapper profile-slick">
					<div class="profile__point-start">
						<p>Начало рабочего дня</p>
						<p>10 : 20 am</p>
						<div class="profile__point-time">
							<div class="profile__time-row">
								<p class="blue time spent">1 h 23 m</p>
								<p>пройдено</p>
							</div>
							<div class="profile__time-row">
								<p class="time left">0 h 34 m</p>
								<p>осталось</p>
							</div>
						</div>
					</div>
					<div class="profile__progressbar">
						<svg class="progress-ring" width="80" height="80">
							<circle stroke="#fff" stroke-width="8" cx="40" cy="40" r="30" fill="#8FAF00"/>
							<circle class="progress-ring__circle" stroke="rgba(96,142,233,0.5)" stroke-width="4" cx="40" cy="40" r="36" fill="transparent"/>
						</svg>
						<div class="profile__progressbar-number">
							<span>87</span>%
						</div>
					</div>
				</div>
				<img src="/design/images/dist/close.svg" alt="close icon" class="point-close">
			</div>

			<div class="profile__active profile-box">
				<div class="profile__title">График активности</div>
				<div class="tabs__include profile-slick">
					<div class="tab__content-include">
						<div class="tab__content-item-include is-active"  data-content="1">
							<img src="/design/images/dist/schedule.png" alt="schedule image">
						</div>
						<div class="tab__content-item-include"  data-content="2">
							<img src="/design/images/dist/profile-active.png" alt="schedule image">
						</div>
						<div class="tab__content-item-include"  data-content="3">
							<img src="/design/images/dist/schedule.png" alt="schedule image">
						</div>
					</div>
					<div class="tabs__wrapper">
						<div  class="tab__item-include is-active" onclick="switchTabsInclude(this)"  data-index="1">День</div>
						<div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="2">месяц</div>
						<div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="3">год</div>
					</div>
				</div>
				<img src="/design/images/dist/close.svg" alt="close icon" class="point-close">
			</div>
		</div>

	</div>
	<header class="header">

		<div class="header__left closed">
			<div class="header__avatar">
				<img src="/design/images/dist/header-avatar.png" alt="avatar image" >
				<div class="header__menu">
					<div class="header__menu-title">
						Специалист тутми <a href="#">#14182</a>
						<p>mikle@tutmee.ru</p>
					</div>
					<a href="#" class="menu__item">
						<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
						<span class="menu__item-title">Настройки</span>
					</a>
					<a href="#"  class="menu__item">
						<img src="/design/images/dist/icon-exit.svg" alt="settings icon">
						<span class="menu__item-title">Выход</span>
					</a>
				</div>
			</div>
			<nav class="header__nav">
				<div class="header__nav-link profile">
					<a href="#profile">
						<span class="_icon-nav-1 header__nav-icon"></span>
						<span class="header__nav-name">Профиль</span>
					</a>
				</div>
				<div class="header__nav-link">
					<a href="#news">
						<span class="_icon-nav-2 header__nav-icon"></span>
						<span class="header__nav-name">Новости</span>
					</a>
				</div>
				<div class="header__nav-link">
					<a href="#kaspi">
						<span class="_icon-nav-3 header__nav-icon"></span>
						<span class="header__nav-name">Структура</span>
					</a>
				</div>
				<div class="header__nav-link">
					<a href="#nominations">
						<span class="_icon-nav-4 header__nav-icon"></span>
						<span class="header__nav-name">Обучение</span>
					</a>
				</div>
				<div class="header__nav-link">
					<a href="#base" >
						<span class="_icon-nav-5 header__nav-icon"></span>
						<span class="header__nav-name">База знаний</span>
					</a>
				</div>
				<div class="header__nav-link">
					<a href="#balance">
						<span class="_icon-nav-6 header__nav-icon"></span>
						<span class="header__nav-name">Отчеты</span>
					</a>
					<div class="header__menu">
						<div class="header__menu-title">
							Отчеты
						</div>
							<a href="#" class="menu__item">
								<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
								<span class="menu__item-title">ТОП</span>
							</a>
							<a href="#" class="menu__item">
								<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
								<span class="menu__item-title">Частые вопросы</span>
							</a>
							<a href="#" class="menu__item">
								<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
								<span class="menu__item-title">Депремирование</span>
							</a>
							<a class="menu__item">
								<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
								<span class="menu__item-title">Табель</span>
							</a>
							<a href="#" class="menu__item">
								<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
								<span class="menu__item-title">Время прихода</span>
							</a>
							<a href="#"  class="menu__item">
								<img src="/design/images/dist/icon-settings.svg" alt="settings icon">
								<span class="menu__item-title">Аналитика</span>
							</a>
					</div>
				</div>


				<div class="header__nav-link">
					<a href="#kpi" >
						<span class="_icon-nav-7 header__nav-icon"></span>
						<span class="header__nav-name">KPI</span>
					</a>
				</div>
				<div class="header__nav-link">
					<a href="#award" >
						<span class="_icon-nav-8 header__nav-icon"></span>
						<span class="header__nav-name">Депрем</span>
					</a>
				</div>
			</nav>
			<div class="header__nav-link last">
				<a href="#">
					<span class="_icon-nav-9 header__nav-icon"></span>
					<span class="header__nav-name">Настройка</span>
				</a>
			</div>
		</div>
		<div class="header__right closed">
			<div class="header__right-nav">
				<a href="#" class="header__right-icon">
					<img src="/design/images/dist/header-right-1.svg" alt="nav icon" class="header__icon-img">
				</a>
				<a href="#" class="header__right-icon red">
					<img src="/design/images/dist/header-right-2.svg" alt="nav icon" class="header__icon-img">
				</a>
				<a href="#" class="header__right-icon loop">
					<img src="/design/images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img">
				</a>
				<a href="#" class="header__right-icon">
					<img src="/design/images/dist/header-right-4.svg" alt="nav icon" class="header__icon-img">
				</a>
				<a href="#" class="header__right-icon">
					<img src="/design/images/dist/header-right-5.svg" alt="nav icon" class="header__icon-img">
				</a>
				<a href="#" class="header__right-icon">
					<img src="/design/images/dist/header-right-6.svg" alt="nav icon" class="header__icon-img">
				</a>
			</div>
			<div class="header__right-messages">
				<a href="#" class="header__message-item new">
					<img src="/design/images/dist/header-right-avatar-1.png" alt="header avatar">
				</a>
				<a href="#" class="header__message-item new">
					<img src="/design/images/dist/header-right-avatar-2.png" alt="header avatar">
				</a>
				<a href="#" class="header__message-item new">
					<img src="/design/images/dist/header-right-avatar-3.png" alt="header avatar">
				</a>
				<a href="#" class="header__message-item read">
					<img src="/design/images/dist/header-right-avatar-4.png" alt="header avatar">
				</a>
				<a href="#" class="header__message-item read">
					<img src="/design/images/dist/header-right-avatar-5.png" alt="header avatar">
				</a>
				<a href="#" class="header__message-item read">
					<img src="/design/images/dist/header-right-avatar-6.png" alt="header avatar">
				</a>
			</div>
			<div class="header__right-arrow">
				<a href="#"><img src="/design/images/dist/header-arrow.svg" alt="arrow icon"></a>
			</div>
		</div>

		<div class="header__arrow">
			<a href="#"><img src="/design/images/dist/header-arrow.svg" alt="arrow icon"></a>
		</div>
	</header>


</div>
<div class="wrapper">
	<main class="main">
			<div class="container">
				<div class="header__top">
					<a href="#" class="header__top-button burger-left">Раскрыть меню</a>
					<div class="header__top-wrapper">
						<a href="#" class="header__top-button burger-right">Раскрыть меню</a>
						<a href="#" class="header__right-icon">
							<img src="/design/images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img">
						</a>
						<a href="#" class="header__right-icon red">
							<img src="/design/images/dist/header-right-2.svg" alt="nav icon" class="header__icon-img">
						</a>
					</div>
				</div>
					<div class="intro content">
						<div class="intro__top _anim _anim-no-hide block">
							<a href="#" class="intro__top-burger"><img src="/design/images/dist/intro-burger.svg" alt="burger menu" class=""></a>
							<div class="intro__top-buttons">
								<a href="#profit"	class="intro__button button-blue">Как можно зарабатывать больше</a>
								<a href="#profitInfo" class="intro__button button-blue">Ваша личная информация</a>
								<a href="#trainee" class="intro__button button-blue">Вот как стажеры оценили ваше обучение</a>
								<a href="#index" class="intro__button button-blue">Ваши показатели</a>
							</div>
						</div>
						<div class="intro__stats _anim _anim-no-hide block">
							<div class="stat__item" data-item="balance">
								<div class="stat__image">
									<div class="back">
										<img src="/design/images/dist/stat-1.png" alt="stat image" class="stat__back">
									</div>
									<div class="front">
										<img src="/design/images/dist/stat-1.png" alt="stat image" class="stat__front">
									</div>
								</div>
								<div class="stat__about">
									<div class="stat__name">Баланс оклада</div>
									<div class="stat__value"><span>15,841</span> KZT</div>
								</div>
							</div>
							<div class="stat__item" data-item="kpi">
								<div class="stat__image">
									<div class="back">
										<img src="/design/images/dist/stat-2.png" alt="stat image" class="stat__back">
									</div>
									<div class="front">
										<img src="/design/images/dist/stat-2.png" alt="stat image" class="stat__front">
									</div>
								</div>
								<div class="stat__about">
									<div class="stat__name">KPI</div>
									<div class="stat__value"><span>0</span> KZT</div>
								</div>
							</div>
							<div class="stat__item" data-item="kaspi">
								<div class="stat__image">
									<div class="back">
										<img src="/design/images/dist/stat-3.png" alt="stat image" class="stat__back">
									</div>
									<div class="front">
										<img src="/design/images/dist/stat-3.png" alt="stat image" class="stat__front">
									</div>
								</div>
								<div class="stat__about">
									<div class="stat__name">Бонусы</div>
									<div class="stat__value"><span>30</span> KZT</div>
								</div>
							</div>
							<div class="stat__item" data-item="award">
								<div class="stat__image">
									<div class="back">
										<img src="/design/images/dist/stat-4.png" alt="stat image" class="stat__back">
									</div>
									<div class="front">
										<img src="/design/images/dist/stat-4.png" alt="stat image" class="stat__front">
									</div>
								</div>
								<div class="stat__about">
									<div class="stat__name">Квартальный</div>
									<div class="stat__value"><span>25,932</span> KZT</div>
								</div>
							</div>
							<div class="stat__item" data-item="nominations">
								<div class="stat__image">
									<div class="back">
										<img src="/design/images/dist/stat-5.png" alt="stat image" class="stat__back">
									</div>
									<div class="front">
										<img src="/design/images/dist/stat-5.png" alt="stat image" class="stat__front">
									</div>
								</div>
								<div class="stat__about">
									<div class="stat__name">Номинации</div>
									<div class="stat__value"><span>14</span></div>
								</div>
							</div>
						</div>
						<div class="intro__table block _anim _anim-no-hide">
							<div class="intro__table-title">
								Ваша смарт цель на неделю
							</div>
							<div class="intro__table-inner">
								<table>
									<thead>
									<tr>
										<th>Цель</th>
										<th>Какой должен быть
											результат?</th>
										<th>С чем сравниваем?</th>
										<th>Чего достигли?</th>
										<th>Какие ресурсы есть
											для этого?</th>
										<th>Для чего нам это?</th>
										<th>До какого числа
											нужно сделать?</th>
										<th>Как будете
											достигать цели?</th></tr>
									</thead>

									<tbody>
									<tr>
										<td>
											<p>Опишите коротко цель</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td><p>Цель должна быть КОНКРЕТНОЙ</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td>
											<p>Цель должна быть ИЗМЕРИМОЙ</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td>
											<p>Опишите достигнутый результат</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td>
											<p>Цель должна быть РЕАЛЬНОЙ</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td>
											<p>Цель должна быть ОБОСНОВАННОЙ</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td>
											<p>Цель должна быть ОПРЕДЕЛЕНА ВО ВРЕМЕНИ</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
										<td>
											<p>Опишите подробно как</p>
											<p>Продажа — обмен товара или услуги на деньги, подтвержденный чеком продажи, актом выполненных работ, накладной передачи товара (в последних двух случаях денежное движение фиксируется отдельным документом)</p>
										</td>
									</tr>
									</tbody>
								</table>
							</div>

						</div>
					</div>
					<div class="profit block _anim _anim-no-hide content" id="profit" >
						<div class="profit__title title">
							Как можно зарабатывать больше
						</div>
						<div class="profit__subtitle subtitle">
							Информация, которая может быть полезна для Вашего карьерного роста
						</div>
						<div class="profit__inner">
							<div class="profit__inner-item">
								<div class="profit__inner__left">
									<div class="profit__left-wrapper">
										<div class="profit__inner-title">
											За что вы получаете оплату
										</div>
										<a href="#"><img src="/design/images/dist/profit-info.svg" alt="info icon"></a>
									</div>
									<div class="profit__inner-text">Оплата начисляется за количество часов активной работы.</div>
									<div class="profit__inner-subtitle">
										Оплачиваемое время складывается из:
									</div>
									<ol class="profit__inner-list">
										<li class="profit__inner-text">время в разговоре с клиентами</li>
										<li class="profit__inner-text">времени в ожидании поступления звонка (то есть статусе
											“Готов”)</li>
										<li class="profit__inner-text">времени работы на “ручном режиме” обзвона</li>
										<li class="profit__inner-text">времени заполнения карты клиента (проставление статуса,
											комментарии и т.д.)</li>
										<li class="profit__inner-text">времени простоя не по вине оператора (отсутствие интернета,
											сбой системы обзвона и другое)</li>
									</ol>
									<div class="profit__inner-text">
										<p>Система автоматически фиксирует время Вашей работы и
											ежедневно производит перерасчет фактически отработанных часов.
										<span class="help semibold italic">* перерасчет производится на следующий день</span>
										</p>
										<p>Поэтому ежедневно на следующий день проверяйте в разделе “<span class="semibold">Мой
											профиль</span>” количество отработанных Вами часов за предыдущий
											день и начисления по оплате!
										</p>
										<p class="semibold italic">* например, фактические часы работы за 20 февраля нужно
											проверять 21 февраля после обеда.
										</p>
									</div>




								</div>
								<div class="profit__inner-right">
									<div class="profit__inner-title">
										Заголовок дополнительного блока
									</div>
									<div class="profit__inner-text profit-right">Оплата начисляется за количество часов активной работы.</div>
									<div class="profit__inner-text profit-right">
										Здесь может располагаться любой длинный текст, который будет меняться
										блоками при клике по иконкам слайдера справа сверху. Ширина блока
										определяется от начала заголовка до конца иконки слайдера.
									</div>
									<div class="profit__inner-subtitle">
										Оплачиваемое время складывается из:
									</div>
									<ol class="profit__inner-list">
										<li class="profit__inner-text">время в разговоре с клиентами</li>
										<li class="profit__inner-text">времени в ожидании поступления звонка (то есть статусе
											“Готов”)</li>
										<li class="profit__inner-text">времени работы на “ручном режиме” обзвона</li>
										<li class="profit__inner-text">времени заполнения карты клиента (проставление статуса,
											комментарии и т.д.)</li>
										<li class="profit__inner-text">времени простоя не по вине оператора (отсутствие интернета,
											сбой системы обзвона и другое)</li>
									</ol>
									<div class="profit__inner-text">
										<p>Система автоматически фиксирует время Вашей работы и
											ежедневно производит перерасчет фактически отработанных часов.
											<span class="help semibold italic">* перерасчет производится на следующий день</span>
										</p>
										<p>Поэтому ежедневно на следующий день проверяйте в разделе “<span class="semibold">Мой
											профиль</span>” количество отработанных Вами часов за предыдущий
											день и начисления по оплате!
										</p>
								</div>
							</div>
								<div class="profit__arrows">
									<a href="#" class="profit__prev"></a>
									<a href="#" class="profit__next"></a>
								</div>
						</div>
							<div class="profit__inner-item">
								<div class="profit__inner__left">
									<div class="profit__left-wrapper">
										<div class="profit__inner-title">
											Титульный заголовок 2
										</div>
										<a href="#"><img src="/design/images/dist/profit-info.svg" alt="info icon"></a>
									</div>
									<div class="profit__inner-text">Текст, который изменяется при листании слайдера.</div>
									<div class="profit__inner-subtitle">
										Оплачиваемое время складывается из:
									</div>
									<ol class="profit__inner-list">
										<li class="profit__inner-text">время в разговоре с клиентами</li>
										<li class="profit__inner-text">времени в ожидании поступления звонка (то есть статусе
											“Готов”)</li>
										<li class="profit__inner-text">времени работы на “ручном режиме” обзвона</li>
										<li class="profit__inner-text">времени заполнения карты клиента (проставление статуса,
											комментарии и т.д.)</li>
										<li class="profit__inner-text">времени простоя не по вине оператора (отсутствие интернета,
											сбой системы обзвона и другое)</li>
									</ol>
									<div class="profit__inner-text">
										<p>Система автоматически фиксирует время Вашей работы и
											ежедневно производит перерасчет фактически отработанных часов.
											<span class="help semibold italic">* перерасчет производится на следующий день</span>
										</p>
										<p>Поэтому ежедневно на следующий день проверяйте в разделе “<span class="semibold">Мой
											профиль</span>” количество отработанных Вами часов за предыдущий
											день и начисления по оплате!
										</p>
										<p class="semibold italic">* например, фактические часы работы за 20 февраля нужно
											проверять 21 февраля после обеда.
										</p>
									</div>




								</div>
								<div class="profit__inner-right">
									<div class="profit__inner-title">
										Титульный заголовок 2
									</div>
									<div class="profit__inner-text profit-right">Оплата начисляется за количество часов активной работы.</div>
									<div class="profit__inner-text profit-right">
										Здесь может располагаться любой длинный текст, который будет меняться
										блоками при клике по иконкам слайдера справа сверху. Ширина блока
										определяется от начала заголовка до конца иконки слайдера.
									</div>
									<div class="profit__inner-subtitle">
										Оплачиваемое время складывается из:
									</div>
									<ol class="profit__inner-list">
										<li class="profit__inner-text">время в разговоре с клиентами</li>
										<li class="profit__inner-text">времени в ожидании поступления звонка (то есть статусе
											“Готов”)</li>
										<li class="profit__inner-text">времени работы на “ручном режиме” обзвона</li>
										<li class="profit__inner-text">времени заполнения карты клиента (проставление статуса,
											комментарии и т.д.)</li>
										<li class="profit__inner-text">времени простоя не по вине оператора (отсутствие интернета,
											сбой системы обзвона и другое)</li>
									</ol>
									<div class="profit__inner-text">
										<p>Система автоматически фиксирует время Вашей работы и
											ежедневно производит перерасчет фактически отработанных часов.
											<span class="help semibold italic">* перерасчет производится на следующий день</span>
										</p>
										<p>Поэтому ежедневно на следующий день проверяйте в разделе “<span class="semibold">Мой
											профиль</span>” количество отработанных Вами часов за предыдущий
											день и начисления по оплате!
										</p>
									</div>
								</div>
								<div class="profit__arrows">
									<a href="#" class="profit__prev"></a>
									<a href="#" class="profit__next"></a>
								</div>
							</div>
							<div class="profit__inner-item">
								<div class="profit__inner__left">
									<div class="profit__left-wrapper">
										<div class="profit__inner-title">
											Титульный заголовок 3
										</div>
										<a href="#"><img src="/design/images/dist/profit-info.svg" alt="info icon"></a>
									</div>
									<div class="profit__inner-text">Оплата начисляется за количество часов активной работы.</div>
									<div class="profit__inner-subtitle">
										Оплачиваемое время складывается из:
									</div>
									<ol class="profit__inner-list">
										<li class="profit__inner-text">время в разговоре с клиентами</li>
										<li class="profit__inner-text">времени в ожидании поступления звонка (то есть статусе
											“Готов”)</li>
										<li class="profit__inner-text">времени работы на “ручном режиме” обзвона</li>
										<li class="profit__inner-text">времени заполнения карты клиента (проставление статуса,
											комментарии и т.д.)</li>
										<li class="profit__inner-text">времени простоя не по вине оператора (отсутствие интернета,
											сбой системы обзвона и другое)</li>
									</ol>
									<div class="profit__inner-text">
										<p>Система автоматически фиксирует время Вашей работы и
											ежедневно производит перерасчет фактически отработанных часов.
											<span class="help semibold italic">* перерасчет производится на следующий день</span>
										</p>
										<p>Поэтому ежедневно на следующий день проверяйте в разделе “<span class="semibold">Мой
											профиль</span>” количество отработанных Вами часов за предыдущий
											день и начисления по оплате!
										</p>
										<p class="semibold italic">* например, фактические часы работы за 20 февраля нужно
											проверять 21 февраля после обеда.
										</p>
									</div>




								</div>
								<div class="profit__inner-right">
									<div class="profit__inner-title">
										Титульный заголовок 3
									</div>
									<div class="profit__inner-text profit-right">Оплата начисляется за количество часов активной работы.</div>
									<div class="profit__inner-text profit-right">
										Здесь может располагаться любой длинный текст, который будет меняться
										блоками при клике по иконкам слайдера справа сверху. Ширина блока
										определяется от начала заголовка до конца иконки слайдера.
									</div>
									<div class="profit__inner-subtitle">
										Оплачиваемое время складывается из:
									</div>
									<ol class="profit__inner-list">
										<li class="profit__inner-text">время в разговоре с клиентами</li>
										<li class="profit__inner-text">времени в ожидании поступления звонка (то есть статусе
											“Готов”)</li>
										<li class="profit__inner-text">времени работы на “ручном режиме” обзвона</li>
										<li class="profit__inner-text">времени заполнения карты клиента (проставление статуса,
											комментарии и т.д.)</li>
										<li class="profit__inner-text">времени простоя не по вине оператора (отсутствие интернета,
											сбой системы обзвона и другое)</li>
									</ol>
									<div class="profit__inner-text">
										<p>Система автоматически фиксирует время Вашей работы и
											ежедневно производит перерасчет фактически отработанных часов.
											<span class="help semibold italic">* перерасчет производится на следующий день</span>
										</p>
										<p>Поэтому ежедневно на следующий день проверяйте в разделе “<span class="semibold">Мой
											профиль</span>” количество отработанных Вами часов за предыдущий
											день и начисления по оплате!
										</p>
									</div>
								</div>
								<div class="profit__arrows">
									<a href="#" class="profit__prev"></a>
									<a href="#" class="profit__next"></a>
								</div>
							</div>
					</div>
						<div class="profit__info block _anim _anim-no-hide" id="profitInfo">
							<div class="profit__info-title">
								Информация о курсе
							</div>
							<div class="profit__info__inner">
								<div class="profit__info__item">
									<img src="/design/images/dist/info-1.png" alt="info image" class="profit__info-image">
									<div class="profit__info-about">
										<div class="profit__info-text">
											Описание курса или его содержания, которое может быть оформлено в несколько строчек для удобства чтения и восприятия информации о курсе.
											Здесь так же могут быть данные об авторах курса и другая дополнительная информация. Описание курса или его содержания, которое может быть
											оформлено в несколько строчек для удобства чтения и восприятия информации о курсе. Здесь так же могут быть данные об авторах курса и другая
											дополнительная информация. Описание курса или его содержания, которое может быть оформлено в несколько строчек для удобства чтения и
											восприятия информации о курсе. Здесь так же могут быть данные об авторах курса и другая дополнительная информация
										</div>
										<div class="profit__info-text mobile">
											Описание курса или его содержания, которое может быть оформлено в несколько строчек для удобства чтения и восприятия информации о курсе.
											Здесь так же могут быть данные об авторах курса и другая дополнительная информация.
										</div>
										<div class="profit__info__wrapper">
    										<div class="info__wrapper-item done">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>01 / 10</p>
												</a>
												<div class="info__item-value">100%</div>
											</div>
											<div class="info__wrapper-item ">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>02 / 10</p>
												</a>
												<div class="info__item-value">14%</div>
											</div>
											<div class="info__wrapper-item ">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>03 / 10</p>
												</a>
												<div class="info__item-value">7%</div>
											</div>
											<div class="info__wrapper-item ">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>04 / 10</p>
												</a>
												<div class="info__item-value">4%</div>
											</div>
											<div class="info__wrapper-item ">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>05 / 10</p>
												</a>
												<div class="info__item-value">0%</div>
											</div>
											<div class="info__wrapper-item">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>06 / 10</p>
												</a>
												<div class="info__item-value">0%</div>
											</div>
											<div class="info__wrapper-item">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>07 / 10</p>
												</a>
												<div class="info__item-value">0%</div>
											</div>
											<div class="info__wrapper-item">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>08 / 10</p>
												</a>
												<div class="info__item-value">0%</div>
											</div>
											<div class="info__wrapper-item">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>09 / 10</p>
												</a>
												<div class="info__item-value">0%</div>
											</div>
											<div class="info__wrapper-item">
												<a href='#' class="info__item-box">
													<img src="/design/images/dist/info-circle.png" alt="play image">
													<p>10 / 10</p>
												</a>
												<div class="info__item-value">0%</div>
											</div>

										</div>
									</div>
								</div>
							</div>

							<div class="info__tip">
								Курс доступен до 24.01
							</div>
						</div>
				</div>
					<div class="trainee block _anim _anim-no-hide content" id="trainee" >
						<div class="trainee__title title">
							Оценка стажеров
						</div>
						<div class="trainee__subtitle subtitle">
							Подробная информация об оценке стажерами вашего обучения
						</div>
						<div class="trainee__table ">
							<div class="trainee__table-name">
								Каримов Адиль Kaspi
							</div>
							<div class="tabs custom-scroll">
								<div class="trainee__tabs tabs__wrapper">
									<div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">16.05.2022</div>
									<div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">17.05.2022</div>
									<div class="trainee__tab tab__item"  onclick="switchTabs(this)"  data-index="3">18.05.2022</div>
									<div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="4">19.05.2022</div>
								</div>
								<select class="select-css trainee-select mobile-select">
									<option value="1">16.05.2022</option>
									<option value="2">17.05.2022</option>
									<option value="3">18.05.2022</option>
									<option value="4">19.05.2022</option>
								</select>

								<div class="tab__content">
									<div class="trainee__content tab__content-item is-active"  data-content="1">
										<table>
											<thead>
												<tr>
													<th>Суть работы</th>
													<th>График работы</th>
													<th>Заработная плата</th>
													<th>Оценка тренера</th>
													<th>Рекомендации</th>
												</tr>
											</thead>
											<tbody>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Не понятно (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Еще не знаю (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Не знаю (<span>4</span>) <div class="semibold value">66.6%</div></div></div></td>
												<td class="td-star" rowspan="4"><div class="trainee-star">
													<div class="trainee__star-value">
														9.4 (<span>9</span>)
													</div>
													<div class="trainee__star-wrapper">
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
													</div>
												</div></td>
												<td rowspan="4"><div class="trainee__review">
													<p>Все устраивает</p>
													<p>Пока что все понятно</p>
													<p>Даже не знаю</p>
													<p>Все понятно, обьяснения супер. Айджан</p>
													<p>молодец, отвечает на все вопросы.</p>
													<p>Мне понравилось</p>
												</div>
													</td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Сет маркетинг (<span>0</span>)<div class="semibold value">0%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">6 - 1 (<span>5</span>)<div class="semibold value">55.6%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 100 - 200 (<span>0</span>)<div class="semibold value">55.6%</div></div></div></td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#B7E100"><div class="text">Обраб. вх и исх <div class="semibold value">66.7%</div></div></div></td>
												<td ><div class="colored" data-color="#FF82AF"><div class="text">Свободный (<span>1</span>)<div class="semibold value">11.1%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">оплата за каждый час (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>

											</tr>
											<tr>
												<td class="none"></td>
												<td class="none"></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 80 + 40 бон(<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>

											</tr>



											</tbody>



										</table>
										<table class="invite">
											<thead>
												<tr>
													<th class="first-td">Приглашенные</th>
													<th>1 день</th>
													<th>2 день</th>
													<th>3 день</th>
													<th>4 день</th>
													<th>5 день</th>
													<th>6 день</th>
													<th>7 день</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="first-td">64</td>
													<td>16</td>
													<td>0</td>
													<td>0</td>
													<td>0</td>
													<td>0</td>
													<td>0</td>
													<td>0</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="trainee__content tab__content-item "  data-content="2">
										<table>
											<thead>
											<tr>
												<th>Суть работы</th>
												<th>График работы</th>
												<th>Заработная плата</th>
												<th>Оценка тренера</th>
												<th>Рекомендации</th>
											</tr>
											</thead>
											<tbody>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Не понятно (<span>8</span>)<div class="semibold value">88.8%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Еще не знаю (<span>6</span>)<div class="semibold value">66.6%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Не знаю (<span>4</span>) <div class="semibold value">44.4%</div></div></div></td>
												<td class="td-star" rowspan="4"><div class="trainee-star">
													<div class="trainee__star-value">
														7.2 (<span>7</span>)
													</div>
													<div class="trainee__star-wrapper">
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
													</div>
												</div></td>
												<td rowspan="4"><div class="trainee__review">
													<p>Все устраивает</p>
													<p>Пока что все понятно</p>
													<p>Даже не знаю</p>
													<p>Все понятно, обьяснения супер. Айджан</p>
													<p>молодец, отвечает на все вопросы.</p>
													<p>Мне понравилось</p>
												</div>
												</td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Сет маркетинг (<span>1</span>)<div class="semibold value">11.1%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">6 - 1 (<span>9</span>)<div class="semibold value">99%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 100 - 200 (<span>7</span>)<div class="semibold value">77.6%</div></div></div></td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#B7E100"><div class="text">Обраб. вх и исх <div class="semibold value">44.4%</div></div></div></td>
												<td ><div class="colored" data-color="#FF82AF"><div class="text">Свободный (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">оплата за каждый час (<span>8</span>)<div class="semibold value">88.8%</div></div></div></td>

											</tr>
											<tr>
												<td class="none"></td>
												<td class="none"></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 80 + 40 бон(<span>4</span>)<div class="semibold value">44.4%</div></div></div></td>

											</tr>



											</tbody>



										</table>
										<table class="invite">
											<thead>
											<tr>
												<th class="first-td">Приглашенные</th>
												<th>1 день</th>
												<th>2 день</th>
												<th>3 день</th>
												<th>4 день</th>
												<th>5 день</th>
												<th>6 день</th>
												<th>7 день</th>
											</tr>
											</thead>
											<tbody>
											<tr>
												<td class="first-td">64</td>
												<td>16</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
											</tr>
											</tbody>
										</table>
									</div>
									<div class="trainee__content tab__content-item "  data-content="3">
										<table>
											<thead>
											<tr>
												<th>Суть работы</th>
												<th>График работы</th>
												<th>Заработная плата</th>
												<th>Оценка тренера</th>
												<th>Рекомендации</th>
											</tr>
											</thead>
											<tbody>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Не понятно (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Еще не знаю (<span>1</span>)<div class="semibold value">11.1%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Не знаю (<span>4</span>) <div class="semibold value">66.6%</div></div></div></td>
												<td class="td-star" rowspan="4"><div class="trainee-star">
													<div class="trainee__star-value">
														6.2 (<span>6</span>)
													</div>
													<div class="trainee__star-wrapper">
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
													</div>
												</div></td>
												<td rowspan="4"><div class="trainee__review">
													<p>Все устраивает</p>
													<p>Пока что все понятно</p>
													<p>Даже не знаю</p>
													<p>Все понятно, обьяснения супер. Айджан</p>
													<p>молодец, отвечает на все вопросы.</p>
													<p>Мне понравилось</p>
												</div>
												</td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Сет маркетинг (<span>0</span>)<div class="semibold value">0%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">6 - 1 (<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 100 - 200 (<span>0</span>)<div class="semibold value">55.6%</div></div></div></td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#B7E100"><div class="text">Обраб. вх и исх <div class="semibold value">88.8%</div></div></div></td>
												<td ><div class="colored" data-color="#FF82AF"><div class="text">Свободный (<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">оплата за каждый час (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>

											</tr>
											<tr>
												<td class="none"></td>
												<td class="none"></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 80 + 40 бон(<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>

											</tr>



											</tbody>



										</table>
										<table class="invite">
											<thead>
											<tr>
												<th class="first-td">Приглашенные</th>
												<th>1 день</th>
												<th>2 день</th>
												<th>3 день</th>
												<th>4 день</th>
												<th>5 день</th>
												<th>6 день</th>
												<th>7 день</th>
											</tr>
											</thead>
											<tbody>
											<tr>
												<td class="first-td">64</td>
												<td>16</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
											</tr>
											</tbody>
										</table>
									</div>
									<div class="trainee__content tab__content-item "  data-content="4">
										<table>
											<thead>
											<tr>
												<th>Суть работы</th>
												<th>График работы</th>
												<th>Заработная плата</th>
												<th>Оценка тренера</th>
												<th>Рекомендации</th>
											</tr>
											</thead>
											<tbody>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Не понятно (<span>4</span>)<div class="semibold value">44.4%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Еще не знаю (<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>
												<td><div class="colored" data-color="#DDE9FF"><div class="text">Не знаю (<span>7</span>) <div class="semibold value">77.7%</div></div></div></td>
												<td class="td-star" rowspan="4"><div class="trainee-star">
													<div class="trainee__star-value">
														9.4 (<span>9</span>)
													</div>
													<div class="trainee__star-wrapper">
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
														<div class="star__item"><img src="/design/images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																			 src="/design/images/dist/trainee-star-done.svg" alt="star done icon"></div>
													</div>
												</div></td>
												<td rowspan="4"><div class="trainee__review">
													<p>Все устраивает</p>
													<p>Пока что все понятно</p>
													<p>Даже не знаю</p>
													<p>Все понятно, обьяснения супер. Айджан</p>
													<p>молодец, отвечает на все вопросы.</p>
													<p>Мне понравилось</p>
												</div>
												</td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">Сет маркетинг (<span>8</span>)<div class="semibold value">88.8%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">6 - 1 (<span>5</span>)<div class="semibold value">55.6%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 100 - 200 (<span>0</span>)<div class="semibold value">55.6%</div></div></div></td>

											</tr>
											<tr>
												<td ><div class="colored" data-color="#B7E100"><div class="text">Обраб. вх и исх <div class="semibold value">33.3%</div></div></div></td>
												<td ><div class="colored" data-color="#FF82AF"><div class="text">Свободный (<span>5</span>)<div class="semibold value">55.5%</div></div></div></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">оплата за каждый час (<span>3</span>)<div class="semibold value">22.2%</div></div></div></td>

											</tr>
											<tr>
												<td class="none"></td>
												<td class="none"></td>
												<td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 80 + 40 бон(<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>

											</tr>



											</tbody>



										</table>
										<table class="invite">
											<thead>
											<tr>
												<th class="first-td">Приглашенные</th>
												<th>1 день</th>
												<th>2 день</th>
												<th>3 день</th>
												<th>4 день</th>
												<th>5 день</th>
												<th>6 день</th>
												<th>7 день</th>
											</tr>
											</thead>
											<tbody>
											<tr>
												<td class="first-td">64</td>
												<td>16</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="index block _anim _anim-no-hide content" id="index">
						<div class="title index__title">
							Сравнение показателей
						</div>
						<div class="subtitle index__subtitle">
							Сравните Ваши показатели с другими сотрудниками
						</div>

						<div class="index__table">
							<div class="tabs custom-scroll">
								<div class="index__tabs tabs__wrapper">
									<div  class="index__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Минуты разговора</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="2"> 3-6 НБ</div>
									<div class="index__tab tab__item"  onclick="switchTabs(this)"  data-index="3"> 7-30 НБ</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="4">31-60 НБ</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="5">61-90 НБ</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="6">6-30 РЕД</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="7">Среднее время диалога</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="8">ОКК</div>
									<div  class="index__tab tab__item" onclick="switchTabs(this)"  data-index="9">Контроль Качества</div>
								</div>
								<select class="select-css trainee-select mobile-select">
									<option value="1">Минуты разговора</option>
									<option value="2"> 3-6 НБ</option>
									<option value="3">7-30 НБ</option>
									<option value="4">31-60 НБ</option>
									<option value="5">61-90 НБ</option>
									<option value="6">6-30 РЕД</option>
									<option value="7">Среднее время диалога</option>
									<option value="8">ОКК</option>
									<option value="9">Контроль Качества</option>
								</select>
								<div class="tab__content">
									<div class="tab__content-item index__content is-active" data-content="1">
										<table>

											<colgroup>

											</colgroup>

											<thead>
												<tr>
													<th>Сотрудник</th>
													<th>Ср.</th>
													<th>План</th>
													<th>Вып.</th>
													<th>%</th>
													<th>1</th>
													<th>2</th>
													<th>3</th>
													<th>4</th>
													<th>5</th>
													<th>6</th>
													<th>7</th>
													<th>8</th>
													<th>9</th>
													<th>10</th>
													<th>11</th>
													<th>12</th>
													<th>13</th>
													<th>14</th>
													<th>15</th>
													<th>16</th>
													<th>17</th>
												</tr>
											</thead>
											<tbody>
												<tr class="prize first-place">
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="red"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="green"><div>280</div></td>
													<td class="green"><div>228</div></td>
													<td class="green"><div>146</div></td>
													<td ><div>150</div></td>
													<td ><div>206</div></td>
													<td ><div>120</div></td>
													<td ><div>134</div></td>
												</tr>
												<tr class="prize second-place">
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="red"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="red"><div>280</div></td>
													<td class="red"><div>228</div></td>
													<td class="red"><div>146</div></td>
													<td  class="red"><div>150</div></td>
													<td  class="red"><div>206</div></td>
													<td  class="red"><div>120</div></td>
													<td  class="red"><div>134</div></td>
												</tr>
												<tr class="prize third-place">
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="green"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="green"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="green"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="green"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="green"><div>280</div></td>
													<td class="green"><div>228</div></td>
													<td class="green"><div>146</div></td>
													<td ><div>150</div></td>
													<td ><div>206</div></td>
													<td ><div>120</div></td>
													<td ><div>134</div></td>
												</tr>
												<tr>
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="red"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="green"><div>280</div></td>
													<td class="green"><div>228</div></td>
													<td class="green"><div>146</div></td>
													<td ><div>150</div></td>
													<td ><div>206</div></td>
													<td ><div>120</div></td>
													<td ><div>134</div></td>
												</tr>
												<tr>
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="green"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="green"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="green"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="green"><div>280</div></td>
													<td class="red"><div>228</div></td>
													<td class="green"><div>146</div></td>
													<td class="green"><div>150</div></td>
													<td class="green"><div>206</div></td>
													<td class="red"><div>120</div></td>
													<td class="red"><div>134</div></td>
												</tr>
												<tr>
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="red"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="red"><div>280</div></td>
													<td class="red"><div>228</div></td>
													<td class="red"><div>146</div></td>
													<td ><div>150</div></td>
													<td ><div>206</div></td>
													<td ><div>120</div></td>
													<td ><div>134</div></td>
												</tr>
												<tr>
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="red"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="red"><div>280</div></td>
													<td class="red"><div>228</div></td>
													<td class="red"><div>146</div></td>
													<td ><div>150</div></td>
													<td ><div>206</div></td>
													<td ><div>120</div></td>
													<td ><div>134</div></td>
												</tr>
												<tr>
													<td><div class="large">Аппазова Карлыгаш</div></td>
													<td><div class="medium">233</div></td>
													<td><div class="medium">7020</div></td>
													<td class="blue"><div>3026.00</div></td>
													<td><div class="small">43.11</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>260</div></td>
													<td class="red"><div>3</div></td>
													<td class="red"><div>216</div></td>
													<td class="red"><div>151</div></td>
													<td class="red"><div>239</div></td>
													<td class="red"><div>191</div></td>
													<td class="red"><div>295</div></td>
													<td class="red"><div>275</div></td>
													<td class="red"><div>280</div></td>
													<td class="green"><div>228</div></td>
													<td class="red"><div>146</div></td>
													<td class="red"><div>150</div></td>
													<td class="red"><div>206</div></td>
													<td class="red"><div>120</div></td>
													<td class="red"><div>134</div></td>
												</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="2">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="3">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="4">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="5">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="6">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="7">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="8">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>
									<div class="tab__content-item index__content " data-content="9">
										<table>

											<colgroup>

											</colgroup>

											<thead>
											<tr>
												<th>Сотрудник</th>
												<th>Ср.</th>
												<th>План</th>
												<th>Вып.</th>
												<th>%</th>
												<th>1</th>
												<th>2</th>
												<th>3</th>
												<th>4</th>
												<th>5</th>
												<th>6</th>
												<th>7</th>
												<th>8</th>
												<th>9</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
											</tr>
											</thead>
											<tbody>
											<tr class="prize first-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr class="prize second-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td  class="red"><div>150</div></td>
												<td  class="red"><div>206</div></td>
												<td  class="red"><div>120</div></td>
												<td  class="red"><div>134</div></td>
											</tr>
											<tr class="prize third-place">
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="green"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="green"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="green"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="green"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="green"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="green"><div>146</div></td>
												<td class="green"><div>150</div></td>
												<td class="green"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="red"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td ><div>150</div></td>
												<td ><div>206</div></td>
												<td ><div>120</div></td>
												<td ><div>134</div></td>
											</tr>
											<tr>
												<td><div class="large">Аппазова Карлыгаш</div></td>
												<td><div class="medium">233</div></td>
												<td><div class="medium">7020</div></td>
												<td class="blue"><div>3026.00</div></td>
												<td><div class="small">43.11</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>260</div></td>
												<td class="red"><div>3</div></td>
												<td class="red"><div>216</div></td>
												<td class="red"><div>151</div></td>
												<td class="red"><div>239</div></td>
												<td class="red"><div>191</div></td>
												<td class="red"><div>295</div></td>
												<td class="red"><div>275</div></td>
												<td class="red"><div>280</div></td>
												<td class="green"><div>228</div></td>
												<td class="red"><div>146</div></td>
												<td class="red"><div>150</div></td>
												<td class="red"><div>206</div></td>
												<td class="red"><div>120</div></td>
												<td class="red"><div>134</div></td>
											</tr>
											</tbody>

										</table>


									</div>

								</div>

							</div>
						</div>


					</div>



			</div>
	</main>
	<footer class="footer"></footer>




	<div class="overlay custom-scroll-y js-overlay">
		<div class="popup  award js-popup">
			<div class="popup__content">
			<div class="popup-header">
				<a class="popup-close js-close-popup" href="#" ><img src="/design/images/dist/popup-close.svg" alt="Close icon" ></a>
				<div class="popup__header-content">
					<div class="popup__title">
						Квартальная премия
					</div>
					<div class="popup__subtitle">
						Дополнительное поле с описанием функционала данного окна
					</div>
				</div>
			</div>
				<div class="popup__body">
					<div class="popup__filter">
						<select class="select-css">
							<option>Май</option>
							<option>Апрель</option>
							<option>Март</option>
							<option>Февраль</option>
							<option>Январь</option>
						</select>
					</div>
					<div class="popup__award">
						<div class="award__title popup__content-title">
							За период с 01.03.2020 до 31.06.2022
						</div>
						<table class="award__table">
							<tr>
								<td class="blue">Сумма</td>
								<td>400.000</td>
							</tr>
							<tr>
								<td class="blue">Комментарии</td>
								<td>Нужно выполнить условие: 1.хх 2.хх</td>
							</tr>
						</table>
					</div>

				</div>
			</div>
		</div>
		<div class="popup  kpi js-popup">
			<div class="popup__content">
				<div class="popup-header">
					<a class="popup-close js-close-popup" href="#" ><img src="/design/images/dist/popup-close.svg" alt="Close icon" ></a>
					<div class="popup__header-content">
						<div class="popup__title">
							KPI
						</div>
						<div class="popup__subtitle">
							Дополнительное поле с описанием функционала данного окна
						</div>
					</div>
				</div>
				<div class="popup__body">
					<div class="popup__filter">
						<select class="select-css">
							<option>Май</option>
							<option>Апрель</option>
							<option>Март</option>
							<option>Февраль</option>
							<option>Январь</option>
						</select>
					</div>
					<div class="kpi__content">
						<div class="kpi__kaspi">
							<div class="kpi__title popup__content-title">
								Kaspi
							</div>
							<div class="kpi__kaspi-wrapper">
								<div class="kpi__kaspi-left">
									<table>
										<tr>
											<td class="blue">Выполнение KPI от 80-99%</td>
											<td>20.000</td>
										</tr>
										<tr>
											<td class="blue">Выполнение KPI на 100%</td>
											<td >40.000</td>
										</tr>
									</table>
								</div>
								<div class="kpi__kaspi-right">
									<table>
										<thead>
											<tr>
												<th>Нижний порог отсечения премии, %</th>
												<th>Верхний порог отсечения премии, %</th>
											</tr>
										</thead>
										<tr>
											<td>80</td>
											<td >100</td>
										</tr>
									</table>
								</div>
							</div>

						</div>

						<div class="kpi__activities">
							<div class="kpi__title popup__content-title">
								Активности KPI
							</div>
							
							<table>
								<thead>
									<tr>
										<th>Наименование активности</th>
										<th>Целевое значение
											за мес</th>
										<th>Выполнено</th>
										<th>Удельный
											вес %</th>
										<th>Сумма премии при
											выполнении плана, тг</th>
										<th>% выполнения</th>
										<th>Сумма премии за
											% выполнения</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Минуты разговора</td>
										<td>7020</td>
										<td>0</td>
										<td>70</td>
										<td>28.000</td>
										<td>0.00</td>
										<td>0.0</td>
									</tr>
									<tr>
										<td>Оценка диалога</td>
										<td>100%</td>
										<td>0</td>
										<td>30</td>
										<td>12.000</td>
										<td>0.00</td>
										<td>0.0</td>
									</tr>
									<tr>
										<td class="none"></td>
										<td class="none"></td>
										<td class="none"></td>
										<td class="none"></td>
										<td class="none"></td>
										<td class="none"></td>
										<td><b>0</b></td>
									</tr>
								</tbody>
							</table>

							<div class="kpi__activities-tip">
								* сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
							</div>
						</div>

					</div>

				</div>
			</div>
		</div>
		<div class="popup  balance js-popup">
			<div class="popup__content">
				<div class="popup-header">
					<a class="popup-close js-close-popup" href="#" ><img src="/design/images/dist/popup-close.svg" alt="Close icon" ></a>
					<div class="popup__header-content">
						<div class="popup__title">
							Баланс оклада
						</div>
						<div class="popup__subtitle">
							Дополнительное поле с описанием функционала данного окна
						</div>
					</div>
				</div>
				<div class="popup__body">
					<div class="popup__filter">
						<div class="popup__filter-title">
							Ваши начисления за период работы
						</div>
						<select class="select-css">
							<option>Май</option>
							<option>Апрель</option>
							<option>Март</option>
							<option>Февраль</option>
							<option>Январь</option>
						</select>
					</div>
					<div class="balance__content custom-scroll">
						<table class="balance__table">
							<thead>
								<tr>
									<th>Дни</th>
									<th>К выдаче</th>
									<th></th>
									<th></th>
									<th>1</th>
									<th>2</th>
									<th>3</th>
									<th>4</th>
									<th>5</th>
									<th>6</th>
									<th>7</th>
									<th>8</th>
									<th>9</th>
									<th>10</th>
									<th>11</th>
									<th>12</th>
									<th>13</th>
									<th>14</th>
									<th>15</th>
									<th>16</th>
									<th>17</th>
									<th>18</th>
									<th>19</th>
									<th>20</th>
									<th>21</th>
									<th>22</th>
									<th>23</th>
									<th>24</th>
									<th>25</th>
								</tr>
							</thead>
							<tbody>
							<tr>
								<td>Время прихода</td>
								<td></td>
								<td></td>
								<td></td>
								<td class="yellow"></td>
								<td>09:25</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td class="yellow"></td>
								<td class="pink"></td>
								<td class="pink"></td>
								<td></td>
								<td></td>
								<td>15:45</td>
								<td></td>
								<td class="yellow"></td>
								<td class="pink"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td class="yellow"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Начисления</td>
								<td>15841</td>
								<td>0</td>
								<td>12000</td>
								<td class="pink"></td>
								<td class="pink">21496</td>
								<td></td>
								<td class="green-pink"></td>
								<td></td>
								<td></td>
								<td class="yellow"></td>
								<td class="pink"></td>
								<td class="pink"></td>
								<td></td>
								<td></td>
								<td class="pink">6345</td>
								<td></td>
								<td class="yellow"></td>
								<td class="pink"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td class="yellow"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Отработанные часы</td>
								<td>9.8</td>
								<td>0</td>
								<td>12000</td>
								<td class="yellow"></td>
								<td>7.57</td>
								<td class="blue"></td>
								<td></td>
								<td class="orange"></td>
								<td></td>
								<td class="yellow"></td>
								<td class="pink"></td>
								<td class="pink"></td>
								<td></td>
								<td></td>
								<td>2.23</td>
								<td></td>
								<td class="yellow"></td>
								<td class="pink"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td class="yellow"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							</tbody>
						</table>
						<div class="balance__title">
							ИСТОРИЯ
						</div>
						<div class="balance__inner">
							<div class="balance__item">
								<div class="balance__item-title">Начислено</div>
								<div class="balance__item-value">0</div>
							</div>
							<div class="balance__item">
								<div class="balance__item-title">Депремирование</div>
								<div class="balance__item-value">Нет штрафов</div>
							</div>
							<div class="balance__item">
								<div class="balance__item-title">Бонусы</div>
								<div class="balance__item-value">Нет бонусов </div>
							</div>
							<div class="balance__item">
								<div class="balance__item-title">Авансы</div>
								<div class="balance__item-value">Нет авансов</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="popup  kaspi js-popup">
			<div class="popup__content">
				<div class="popup-header">
					<a class="popup-close js-close-popup" href="#" ><img src="/design/images/dist/popup-close.svg" alt="Close icon" ></a>
					<div class="popup__header-content">
						<div class="popup__title">
							KASPI
						</div>
						<div class="popup__subtitle">
							Дополнительное поле с описанием функционала данного окна
						</div>
					</div>
				</div>
				<div class="popup__body">
					<div class="popup__filter-title">
						Зарабатывайте бонусы, выполняя дополнительные активности
					</div>
						<div class="tabs ">
							<div class="popup__filter">
							<div class="trainee__tabs tabs__wrapper">
								<div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Заработанные бонусы</div>
								<div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">Можно заработать</div>
							</div>
								<select class="select-css trainee-select mobile-select">
									<option value="1">Заработанные бонусы</option>
									<option value="2">Можно заработать</option>
								</select>

						<select class="select-css">
							<option>Май</option>
							<option>Апрель</option>
							<option>Март</option>
							<option>Февраль</option>
							<option>Январь</option>
						</select>

						</div>

						<div class="tab__content">
							<div class="kaspi__content custom-scroll-y tab__content-item is-active"  data-content="1">
								<table>
									<thead>
										<tr>
											<th>Дата</th>
											<th>Бонус</th>
											<th>Комментарии</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>22-06-14</td>
											<td>1700</td>
											<td><p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p></td>
										</tr>
										<tr>
											<td>22-06-14</td>
											<td>1700</td>
											<td><p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p></td>
										</tr>
										<tr class="green">
											<td>22-06-14</td>
											<td>1700</td>
											<td><p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p></td>
										</tr>
										<tr>
											<td>22-06-14</td>
											<td>1700</td>
											<td><p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p>
												<p>Кол-во сборов 3-6 НБ: 17;</p></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="kaspi__content custom-scroll-y tab__content-item"  data-content="2">
								<div class="kaspi__wrapper">
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												100 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												200 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												300 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												400 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												500 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												600 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												1000 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												1500 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												2000 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
									<div class="kaspi__item">
										<img src="/design/images/dist/kaspi-gift.png" alt="" class="kaspi__item-img">
										<div class="kaspi__item-about">
											<div class="kaspi__item-title">
												3000 KZT
											</div>
											<div class="kaspi__item-text">
												За каждую единицу сбора
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>


				</div>
			</div>
		</div>
		<div class="popup  nominations js-popup">
			<div class="popup__content">
				<div class="popup-header">
					<a class="popup-close js-close-popup" href="#" ><img src="/design/images/dist/popup-close.svg" alt="Close icon" ></a>
					<div class="popup__header-content">
						<div class="popup__title">
							Номинации
						</div>
						<div class="popup__subtitle">
							Дополнительное поле с описанием функционала данного окна
						</div>
					</div>
				</div>
				<div class="popup__body">

					<div class="tabs ">
						<div class="popup__filter">
							<div class="trainee__tabs tabs__wrapper">
								<div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Номинации</div>
								<div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">Сертификаты</div>
								<div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="3">Лучшие сотрудники</div>
							</div>
							<select class="select-css trainee-select mobile-select">
								<option value="1">Номинации</option>
								<option value="2">Сертификаты</option>
								<option value="3">Лучшие сотрудники</option>
							</select>


<!--							<div class="nominations__arrows">-->
<!--								<a id="nominationBack" href="#" class="arrow__item"><img src="/design/images/dist/arrow-back.svg" alt="arrow icon"></a>-->
<!--								<a id="nominationNext" href="#" class="arrow__item"><img src="/design/images/dist/arrow-next.svg" alt=" arrow icon"></a>-->
<!--							</div>-->

						</div>

						<div class="tab__content">
							<div class="nominations__content custom-scroll-y tab__content-item is-active"  data-content="1">
								<div class="nominations__wrapper">
									<div class="nominations__left">
										<div class="nominations__half">
											<div class="nominations__half-title">Количество исходящих
												вызовов</div>
											<div class="nominations__value">597</div>
											<div class="nominations__half-title">
												План: 470
											</div>
										</div>
										<div class="nominations__half">
											<div class="nominations__half-title">Количество исходящих
												вызовов</div>
											<div class="nominations__value">597</div>
											<div class="nominations__half-title">
												План: 470
											</div>
										</div>
									</div>
									<div class="nominations__item">
										<div class="nominations__item-title">
											Процент успешных
											исходящих продаж
										</div>
										<div class="nominations__item-avatar gift-1">
											<img src="/design/images/dist/profile-avatar.png" alt="profile avatar">
										</div>
										<div class="nominations__item-name">
											Елена Линовская
										</div>
										<div class="nominations__item-subtext">
											колл-центр
										</div>
										<div class="nominations__item-value">
											15 500 ₸
										</div>
										<div class="nominations__item-wrapper">
											<div class="nominations__item-row">
												<p>KPI</p>
												<p>1300 ₸</p>
											</div>
											<div class="nominations__item-row">
												<p>БОНУСЫ</p>
												<p>200 ₸</p>
											</div>
											<div class="nominations__item-row">
												<p>ОКЛАД</p>
												<p>14000 ₸</p>
											</div>
										</div>
									</div>
									<div class="nominations__item green">
										<div class="nominations__item-title">
											Процент успешных
											исходящих продаж
										</div>
										<div class="nominations__item-avatar gift-2">
											<img src="/design/images/dist/profile-avatar.png" alt="profile avatar">
										</div>
										<div class="nominations__item-name">
											Елена Линовская
										</div>
										<div class="nominations__item-subtext">
											колл-центр
										</div>
										<div class="nominations__item-value">
											15 500 ₸
										</div>
										<div class="nominations__item-wrapper">
											<div class="nominations__item-row">
												<p>KPI</p>
												<p>1300 ₸</p>
											</div>
											<div class="nominations__item-row">
												<p>БОНУСЫ</p>
												<p>200 ₸</p>
											</div>
											<div class="nominations__item-row">
												<p>ОКЛАД</p>
												<p>14000 ₸</p>
											</div>
										</div>
									</div>
									<div class="nominations__item">
										<div class="nominations__item-title">
											Процент успешных
											исходящих продаж
										</div>
										<div class="nominations__item-avatar gift-3">
											<img src="/design/images/dist/profile-avatar.png" alt="profile avatar">
										</div>
										<div class="nominations__item-name">
											Елена Линовская
										</div>
										<div class="nominations__item-subtext">
											колл-центр
										</div>
										<div class="nominations__item-value">
											15 500 ₸
										</div>
										<div class="nominations__item-wrapper">
											<div class="nominations__item-row">
												<p>KPI</p>
												<p>1300 ₸</p>
											</div>
											<div class="nominations__item-row">
												<p>БОНУСЫ</p>
												<p>200 ₸</p>
											</div>
											<div class="nominations__item-row">
												<p>ОКЛАД</p>
												<p>14000 ₸</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="nominations__content custom-scroll-y tab__content-item"  data-content="2">
								<div class="tabs__include">
									<div class="tabs__wrapper wrapper-include">
										<div  class="tab__item-include is-active" onclick="switchTabsInclude(this)"  data-index="1">Мои номинации</div>
										<div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="2">Доступные номинации</div>
										<div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="3">Номинации других участников</div>
									</div>
									<div class="tab__content-include">
										<div class="tab__content-item-include is-active"  data-content="1">
											<div class="certificates__title">
												Сертификатов: <span class="current">10</span> из <span class="all">17</span>
											</div>

											<div class="certificates__wrapper">
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
											</div>

										</div>
										<div class="tab__content-item-include"  data-content="2">
											<div class="certificates__title">
												Сертификатов: <span class="current">0</span> из <span class="all">80</span>
											</div>

											<div class="certificates__wrapper">
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
											</div>
										</div>
										<div class="tab__content-item-include"  data-content="3">
											<div class="certificates__title">
												Сертификатов: <span class="current">3</span> из <span class="all">80</span>
											</div>

											<div class="certificates__wrapper">
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-1.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-2.png" alt="certificate image">
												</div>
												<div class="certificates__item">
													<img src="/design/images/dist/certificate-3.png" alt="certificate image">
												</div>
											</div>
										</div>
								</div>

								</div>
							</div>

						</div>
					</div>


				</div>
			</div>
		</div>
	</div>
</div>


<script>

	let switchTabs = (tab) => {
		// get all tab list items and remove the is-active class
		let tabs = tab.closest('.tabs').querySelectorAll('.tab__item');
		tabs.forEach(t => {t.classList.remove("is-active");});
		// set is-active on the passed tab element
		tab.classList.add("is-active");
		// get all content elements and remove is-active
		let contents = tab.closest('.tabs').querySelectorAll(".tab__content .tab__content-item");
		contents.forEach(t => {t.classList.remove("is-active");});
		// get the data-index data attribute from the selected tab (passed here)
		let activeTabIndex = tab.getAttribute("data-index");
		// get the corresonding tab content via the data-content attribute
		let activeContent = tab.closest('.tabs').querySelector(`[data-content='${activeTabIndex}']`);
		// set is-active on the corresponding tab content
		activeContent.classList.add("is-active");
	}

	let switchTabsInclude = (tab) => {
		// get all tab list items and remove the is-active class
		let tabs = tab.closest('.tabs__include').querySelectorAll('.tab__item-include');
		tabs.forEach(t => {t.classList.remove("is-active");});
		// set is-active on the passed tab element
		tab.classList.add("is-active");
		// get all content elements and remove is-active
		let contents = tab.closest('.tabs__include').querySelectorAll(".tab__content-include .tab__content-item-include");
		contents.forEach(t => {t.classList.remove("is-active");});
		// get the data-index data attribute from the selected tab (passed here)
		let activeTabIndex = tab.getAttribute("data-index");
		// get the corresonding tab content via the data-content attribute
		let activeContent = tab.closest('.tabs__include').querySelector(`[data-content='${activeTabIndex}']`);
		// set is-active on the corresponding tab content
		activeContent.classList.add("is-active");
	}

	// let 	arrowBack = document.getElementById('nominationBack'),
	// 		arrowNext = document.getElementById('nominationNext'),
	// 		nominationTabList = document.querySelectorAll('.nominations .tab__item');
	// document.addEventListener('click', function(event){
	// 	if(event.target.closest(`#${arrowBack.id}`)){
	// 	} else if(event.target.closest(`#${arrowNext.id}`)){
	// 	}
	// })
</script>


	<script src="/design/js/app.min.js"></script>

</body> 
</html>
