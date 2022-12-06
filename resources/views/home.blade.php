<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="css/home.css" >
	</head>
	<body>
		<nav class="custom_nav">
			<div class="logo">
				<img class="chair" src="logo.png">
				<img class="logo_title" src="jobtron.png">
				<img class="logo_description" src="description.png">
			</div>
			<div class="menu">
				<ul class="menu_items">
					<li>Цены</li>
					<li>Отзывы</li>
					<li>Особенности платформы</li>
				</ul>
			</div>
			<div class="autorize">
				@guest
				<div><a class="autorization" href="/login">Вход</a></div>
				<div><a class="" href="/register">Регистрация</a></div>
				@else
				<form method="POST" action="/logout">
					@csrf
					<button class="user_link" href="#"><img src="user.png"></button>
				</form>
				
				@endguest
				<div>
					<select class="lang" name="lang">
						<option value="en">EN</option>
						<option value="kz">KZ</option>
						<option value="ru">RU</option>
					</select>
				</div>
			</div>
			<button class="hamburger" onclick="showMenu()">
				<img src="menu.png">
			</button>
		</nav>
		<div id="topnav" class="topnav">
			<div class="mobile_menu">
				<ul class="menu_items">
					<li>Цены</li>
					<li>Отзывы</li>
					<li>Особенности платформы</li>
				</ul>
			</div>
			<div class=autorize">
				<div><a class="autorization" href="#">Авторизоваться</a></div>
				<a class="mobile_user_link" href="#"><img src="user.png"></a>
				<div>
					<select class="lang" name="lang">
						<option value="en">EN</option>
						<option value="kz">KZ</option>
						<option value="ru">RU</option>
					</select>
				</div>
			</div>
		</div>
		<div class="content">
			<img class="ellipse3" src="ellipse3.png">
			<img class="ellipse2" src="ellipse2.png">
			<center><p class="main_title">JOBTRON: удобно для сотрудников, полезно для бизнеса</p></center>	
			<div class="content_menu_main">
				<ul class="content_menu">
					<li><a class="menu_link active" href="#">Личный профиль</a>&nbsp;<img src="question.png" width="15"></li>
					<li><a class="menu_link" href="#">База знаний</a>&nbsp;<img src="question.png" width="15"></li>
					<li><a class="menu_link" href="#">KPI</a>&nbsp;<img src="question.png" width="15"></li>
					<li><a class="menu_link" href="#">Курсы</a>&nbsp;<img src="question.png" width="15"></li>
					<li><a class="menu_link" href="#">Структура компании</a>&nbsp;<img src="question.png" width="15"></li>
					<li><a class="menu_link" href="#">Новости и чат</a>&nbsp;<img src="question.png" width="15"></li>
				</ul>
			</div>
		</div>

		<div class="content_1">
			<div class="for_staff">
				<div class="for_staff_text1">
					<h3>Полезно для сотрудника:</h3>
					<ul>
						<li><img src="dot.png">&nbsp;&nbsp;все заработанные деньги видны на экране</li>
						<li><img src="dot.png">&nbsp;&nbsp;% выполнения KPI в реальном времени</li>
						<li><img src="dot.png">&nbsp;&nbsp;премии, бонусы, электронные награды и сертификаты</li>
						<li><img src="dot.png">&nbsp;&nbsp;оценка эффективности собственной работы</li>
					</ul>
				</div>
				<div class="for_staff_text2">
					<h3>Полезно для руководителей:</h3>
					<ul>
						<li><img src="dot.png">&nbsp;&nbsp;повышение эффективности каждого сотрудника</li>
						<li><img src="dot.png">&nbsp;&nbsp;много инструментов мотивации персонала</li>
						<li><img src="dot.png">&nbsp;&nbsp;увеличение вовлеченности</li>
						<li><img src="dot.png">&nbsp;&nbsp;цифровые SMART цели</li>
					</ul>
				</div>
				<div class="buy_button">
					<button>Получить бесплатно</button>
				</div>
			</div>
			<div class="for_staff_banner">
				<img src="banner1.png">
			</div>
		</div>
		<div class="content_2">
			<center><p class="main_title">Компании, использующие JOBTRON, отмечают:<img class="main_title_img1" src="cloud.svg"></p></center>	
			<div class="content_2_percents">
				<div class="percent_1">
					<h1>на 29 %</h1>
					<center><p class="percent_text">Снизилась текучка сотрудников</p></center>
				</div>
				<div class="percent_2">
					<h1>на 15 %</h1>
					<center><p class="percent_text">Увеличилось число активных работников, предлагающих нестандартные решения</p></center>
				</div>
				<div class="percent_3">
					<h1>на 25 %</h1>
					<center><p class="percent_text">Увеличилось вовлеченность сотрудников в жизнь компании</p></center>
				</div>
			</div>
		</div>

		<center><p class="main_title">Отзывы</p><div class="line"></div></center>
		<div class="content_3">
			<img class="content_3_img" src="cloud_big.svg">
			<div class="content_3_buttons">
				<div class="c_buttons">
					<div class="c_button"><button class="active1">Видеоотзывы</button></div>
					<div class="c_button"><button>Текстовые отзывы</button></div>
					<div class="c_button"><button>Фотоотзывы</button></div>
				</div>
				<div class="c_text">
					<p>Повышайте качество и эффективность работы. Вашего бизнеса вместе с нами</p>
				</div>
				<div  class="buy_button">
					<button>Попробовать бесплатно</button>
				</div>
			</div>
			<div class="content_3_videos">
				<div class="main_video">
					<img src="thumbnail.png">
				</div>
				<div class="videolist">
					<img src="thumbnail.png" width="192px" height="130px">
					<img src="thumbnail.png" width="192px" height="130px">
					<img src="thumbnail.png" width="192px" height="130px">
				</div>
			</div>
		</div>
		<br><br>
		<center><p class="main_title">Тарифы<img class="main_title_img2" src="cloud.svg"></p></center>
		<div class="content_4">
			<table class="data_table" cellspacing="0">
				<thead>
					<tr>
						<th class="rounded-left"></th>
						<th>Бесплатный</th>
						<th>База</th>
						<th>Стандарт</th>
						<th class="rounded-right">PRO</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="rounded-left left_space">Количество пользователей</td>
						<td class="center-space">до 5</td>
						<td class="center-space">до 20</td>
						<td class="center-space">до 50</td>
						<td class="rounded-right center-space">до 100</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Место</td>
						<td class="center-space">5 Гб</td>
						<td class="center-space">20 Гб</td>
						<td class="center-space">50 Гб</td>
						<td class="rounded-right center-space">1 Т</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">База знаний</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Новости</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Обучение</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Аналитика</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Контроль качества</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Чат</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Структура компании</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Поддержка</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">-</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Администрирование\Аудит</td>
						<td class="center-space">-</td>
						<td class="center-space">-</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Перенос на сервер клиента</td>
						<td class="center-space">-</td>
						<td class="center-space">-</td>
						<td class="center-space">-</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Индивидуальный номер клиента</td>
						<td class="center-space">-</td>
						<td class="center-space">-</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<td class="rounded-left left_space">Брендирование кабинета (размещение логотипа, фирменный цвет</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="center-space">+</td>
						<td class="rounded-right center-space">+</td>
					</tr>
					<tr>
						<th class="rounded-left left_space">Оплата в месяц</th>
						<th></th>
						<th>9 000</th>
						<th>27 000</th>
						<th class="rounded-right">100 000</th>
					</tr>
					<tr>
						<th class="rounded-left left_space">Оплата в год</th>
						<th></th>
						<th>108 000</th>
						<th>291 600</th>
						<th class="rounded-right">960 000</th>
					</tr>
					<tr>
						<th class="rounded-left left_space">Скидка</th>
						<th></th>
						<th></th>
						<th>20%</th>
						<th class="rounded-right">20%</th>
					</tr>
				</tbody>
			</table>
		</div>

		<center><p class="main_title">С JOBTRON работать легко и удобно<img class="main_title_img3" src="cloud.svg"></p></center>
		<center><p class="secondary_title">Мы заботимся о наших клиентах и предоставляем индивидуальный подход, который будет полезен каждому</p></center>
		<div class="content_5">
			<div class="c_5_desc1">
				<img class="cloud_background1" src="cloud_blue.svg">
				<div class="desc_img">
					<img src="desc1_img.png">
				</div>
				<div class="desc1">
					<h3>Интеграция с вашей  CRM-системой</h3>
					<p>Корпоративный портал JOBTRON может интегрироваться с разными CRM, сревисами рассылки, системами учета времени и трекинга, в также с другими многофункциональными сервисами</p>
				</div>
			</div>
			<div class="c_5_desc1">
				<img class="cloud_background1" src="cloud_yellow.svg">
				<div class="desc1">
					<h3>Служба поддержки</h3>
					<p>Корпоративный портал JOBTRON может интегрироваться с разными CRM, сревисами рассылки, системами учета времени и трекинга, в также с другими многофункциональными сервисами</p>
				</div>
				<div class="desc_img">
					<img src="desc2_img.png">
				</div>
			</div>
			<div class="c_5_desc1">
				<img class="cloud_background1" src="cloud_blue2.svg">
				<div class="desc_img">
					<img src="desc3_img.png">
				</div>
				<div class="desc1">
					<h3>Гибкие возможности</h3>
					<p>Корпоративный портал JOBTRON может интегрироваться с разными CRM, сревисами рассылки, системами учета времени и трекинга, в также с другими многофункциональными сервисами</p>
				</div>
			</div>
		</div>

		<center><p class="main_title">JOBTRON в цифрах<img class="main_title_img4" src="cloud.svg"></p></center>
		<center><p class="secondary_title">JOBTRON поможет каждому сотруднику компании оценить свои силы и почувствовать поддержку. Благодаря нашей платформе каждый новый работник:</p></center>

		<div class="content_6">	
			<div class="content_2_percents">
				<div class="percent_1">
					<h1>3</h1>
					<center><p class="percent_text">В 3 раза быстрее адаптируется к работе в компании</p></center>
				</div>
				<div class="percent_2">
					<h1>на 21 %</h1>
					<center><p class="percent_text">Повышает свою продуктивность</p></center>
				</div>
				<div class="percent_3">
					<h1>на 22 %</h1>
					<center><p class="percent_text">Повышает свой доход уже через два месяца после начала рбаоты</p></center>
				</div>
			</div>
			<div class="content_6_extra">
				<div class="desc_6">Повышайте качество и эффективность работы Вашего бизнеса вместе с нами</div>
				<div class="buttons_6">
					<input type="text" class="extra_button" placeholder="Ваше имя" />
					<input type="text" class="extra_button" placeholder="Номер телефона" />
				</div>
				<div class="buy_button">
					<button>Попробовать бесплатно</button>
				</div>
			</div>
		</div>
	</body>
	<footer>
		<div class="footer_content">
			<div><img src="footer_logo.png"></div>
			<div>
				<p>Цены</p>
				<p>Отзывы</p>
				<p>Особенности платформы</p>
				<br>
				<p>Подпишитесь на новости:</p>
				<p class="footer_icons1"><img src="telegram.svg"><img src="instagram/instagram.svg"><img src="youtube.svg"></p>
			</div>
			<div>
				<p>Документы</p>
				<p>Информация об оплате</p>
				<p>Лицензионное соглашение</p>
				<br>
				<p>Способы оплаты:</p>
				<div class="footer_icons1"><img src="footer_icons2/mir.svg"><img src="footer_icons2/visa.svg"><img src="footer_icons2/money.svg"></div>
			</div>
			<div>
				<p>Отдел продаж:</p>
				<p>8 777 788 08 00</p>
				<p>8 777 788 08 00</p>
				<p>sales@jobtron.com</p>
				<p>Отдел продаж:</p>
				<p>support@jobtron.com</p>
			</div>
		</div>
		<hr class="footer_line"/>
		<div class="license">
			<div><p>© 2022 «Jobton», Все права защищены</p></div>
			<div>
				<p>Правила сайта</p>
				<p>Политика конфиденциальности</p>
			</div>
		</div>
	</footer>
</html>
<script>
	var showMobileMenu = false;
	var menu = document.getElementById('topnav');

	function showMenu(){
		showMobileMenu = !showMobileMenu;
		if(showMobileMenu){
			menu.classList.add('active_topnav');
		}else{
			menu.classList.remove('active_topnav');
		}
	}
</script>