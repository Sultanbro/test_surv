@extends('layouts.app')

@section('content')

<div class="container container-left-padding">
	<div class="header__top">
		<a href="#" class="header__top-button burger-left">Раскрыть меню</a>
		<div class="header__top-wrapper">
			<a href="#" class="header__top-button burger-right">Раскрыть меню</a>
			<a href="#" class="header__right-icon">
				<img src="images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img">
			</a>
			<a href="#" class="header__right-icon red">
				<img src="images/dist/header-right-2.svg" alt="nav icon" class="header__icon-img">
			</a>
		</div>
	</div>
		<div class="intro content">
			<div class="intro__top _anim _anim-no-hide block">
				<a href="#" class="intro__top-burger"><img src="images/dist/intro-burger.svg" alt="burger menu" class=""></a>
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

							<img src="images/dist/image-1.svg" alt="stat image" class="stat__front">
						</div>
						<div class="front">
							<img src="images/dist/image-1.svg" alt="stat image" class="stat__front">
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">Баланс оклада</div>
						<div class="stat__value"><span>35,841</span> KZT</div>
					</div>
				</div>
				<div class="stat__item" data-item="kpi">
					<div class="stat__image">
						<div class="back">
							<img src="images/dist/image-2.svg" alt="stat image" class="stat__back">
						</div>
						<div class="front">
							<img src="images/dist/image-2.svg" alt="stat image" class="stat__front">
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">KPI</div>
						<div class="stat__value"><span>30000</span> KZT</div>
					</div>
				</div>
				<div class="stat__item" data-item="kaspi">
					<div class="stat__image">
						<div class="back">
							<img src="images/dist/image-3.svg" alt="stat image" class="stat__back">
						</div>
						<div class="front">
							<img src="images/dist/image-3.svg" alt="stat image" class="stat__front">
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
							<img src="images/dist/image-4.svg" alt="stat image" class="stat__back">
						</div>
						<div class="front">
							<img src="images/dist/image-4.svg" alt="stat image" class="stat__front">
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
							<img src="images/dist/image-5.svg" alt="stat image" class="stat__back">
						</div>
						<div class="front">
							<img src="images/dist/image-5.svg" alt="stat image" class="stat__front">
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">Номинации</div>
						<div class="stat__value"><span>20</span></div>
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
							<a href="#"><img src="images/dist/profit-info.svg" alt="info icon"></a>
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
							<a href="#"><img src="images/dist/profit-info.svg" alt="info icon"></a>
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
							<a href="#"><img src="images/dist/profit-info.svg" alt="info icon"></a>
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
			<div class="courses__wrapper block _anim _anim-no-hide">
				<div class="courses__content">
					<div class="courses__title">
						Ваши курсы
					</div>
					<div class="courses__content__wrapper">
						<div class="courses__item current">
							<img src="images/dist/courses-image.png" alt="" class="courses__image">

							<div class="courses__progress">
								<div class="courses__line"></div>
							</div>
							<!--										Линия зависит от процентов в span-->
							<div class="courses__percent">
								Пройдено: <span>32%</span>
							</div>
							<a href="#" class="courses__button">
								<span>Продолжить курс</span>
							</a>
						</div>
						<div class="courses__item">
							<img src="images/dist/courses-image.png" alt="" class="courses__image">

							<div class="courses__progress">
								<div class="courses__line"></div>
							</div>
							<div class="courses__percent">
								Пройдено: <span>60%</span>
							</div>
							<a href="#" class="courses__button">
								<span>Продолжить курс</span>
							</a>
						</div>
						<div class="courses__item">
							<img src="images/dist/courses-image.png" alt="" class="courses__image">

							<div class="courses__progress">
								<div class="courses__line"></div>
							</div>
							<div class="courses__percent">
								Пройдено: <span>92%</span>
							</div>
							<a href="#" class="courses__button">
								<span>Продолжить курс</span>
							</a>
						</div>
						<div class="courses__item">
							<img src="images/dist/courses-image.png" alt="" class="courses__image">

							<div class="courses__progress">
								<div class="courses__line"></div>
							</div>
							<div class="courses__percent">
								Пройдено: <span>12%</span>
							</div>
							<a href="#" class="courses__button">
								<span>Продолжить курс</span>
							</a>
						</div>
						<div class="courses__item">
							<img src="images/dist/courses-image.png" alt="" class="courses__image">

							<div class="courses__progress">
								<div class="courses__line"></div>
							</div>
							<div class="courses__percent">
								Пройдено: <span>22%</span>
							</div>
							<a href="#" class="courses__button">
								<span>Продолжить курс</span>
							</a>
						</div>
						<div class="courses__item">
							<img src="images/dist/courses-image.png" alt="" class="courses__image">

							<div class="courses__progress">
								<div class="courses__line"></div>
							</div>
							<div class="courses__percent">
								Пройдено: <span>42%</span>
							</div>
							<a href="#" class="courses__button">
								<span>Продолжить курс</span>
							</a>
						</div>
					</div>

				</div>
				<div class="profit__info" id="profitInfo">
					<div class="profit__info-title">
						Информация о курсе
					</div>
					<div class="profit__info-back">
						Назад
					</div>
					<div class="profit__info-back-mobile">

					</div>
					<div class="profit__info__inner">
						<div class="profit__info__item">
							<img src="images/dist/courses-image.png" alt="info image" class="profit__info-image">
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
											<img src="images/dist/info-circle.png" alt="play image">
											<p>01 / 10</p>
										</a>
										<div class="info__item-value">100%</div>
									</div>
									<div class="info__wrapper-item ">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>02 / 10</p>
										</a>
										<div class="info__item-value">14%</div>
									</div>
									<div class="info__wrapper-item ">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>03 / 10</p>
										</a>
										<div class="info__item-value">7%</div>
									</div>
									<div class="info__wrapper-item ">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>04 / 10</p>
										</a>
										<div class="info__item-value">4%</div>
									</div>
									<div class="info__wrapper-item ">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>05 / 10</p>
										</a>
										<div class="info__item-value">0%</div>
									</div>
									<div class="info__wrapper-item">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>06 / 10</p>
										</a>
										<div class="info__item-value">0%</div>
									</div>
									<div class="info__wrapper-item">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>07 / 10</p>
										</a>
										<div class="info__item-value">0%</div>
									</div>
									<div class="info__wrapper-item">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>08 / 10</p>
										</a>
										<div class="info__item-value">0%</div>
									</div>
									<div class="info__wrapper-item">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
											<p>09 / 10</p>
										</a>
										<div class="info__item-value">0%</div>
									</div>
									<div class="info__wrapper-item">
										<a href='#' class="info__item-box">
											<img src="images/dist/info-circle.png" alt="play image">
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
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
													src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
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
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
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
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
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
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
											<div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
																																	src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
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

@endsection

@section('scripts')
<script>
	
</script>
@endsection