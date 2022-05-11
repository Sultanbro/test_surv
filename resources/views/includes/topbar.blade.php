	@if(Auth::user())
	<div id="wrapper">
		<div id="sidebar">
			<a href="{{ url('/') }}" class="logo">{{ config('app.name', 'MediaSend') }}</a>
			<strong>Меню</strong>
			<ul class="custom-accordion">
				<li><a href="#" class="item-1">Контакты</a></li>
				<li>
					<a href="#" class="opener-ac item-2"><span>Рассылки</span></a>
					<div class="slide-ac">
						<ul>
							<li><a href="#">Профиль</a></li>
							<li><a href="#">Общие настройки</a></li>
							<li><a href="#">API ключ</a></li>
							<li><a href="#">Как стать партнером</a></li>
							<li><a href="#">Шаблоны СМС и аудио ролики</a></li>
							<li><a href="#">Стоп лист</a></li>
							<li><a href="#">Пополнить баланс</a></li>
							<li><a href="#">Денежные транзакции</a></li>
						</ul>
					</div>
				</li>
				<li>
					<a href="#" class="opener-ac item-3"><span>Статистика</span></a>
					<div class="slide-ac">
						<ul>
							<li><a href="#">Профиль</a></li>
							<li><a href="#">Общие настройки</a></li>
							<li><a href="#">API ключ</a></li>
							<li><a href="#">Как стать партнером</a></li>
							<li><a href="#">Шаблоны СМС и аудио ролики</a></li>
							<li><a href="#">Стоп лист</a></li>
							<li><a href="#">Пополнить баланс</a></li>
							<li><a href="#">Денежные транзакции</a></li>
						</ul>
					</div>
				</li>
				<li><a href="#" class="item-4">CRM</a></li>
				<li>
					<a href="#" class="opener-ac item-5"><span>Интеграция</span></a>
					<div class="slide-ac">
						<ul>
							<li><a href="#">Голосовые интеграции</a></li>
							<li><a href="#">Смс интеграции</a></li>
						</ul>
					</div>
				</li>
				<li>
					<a href="#" class="opener-ac item-6"><span>Настройки</span></a>
					<div class="slide-ac">
						<ul>
							<li class="active"><a href="#">Профиль</a></li>
							<li><a href="#">Общие настройки</a></li>
							<li><a href="#">API ключ</a></li>
							<li><a href="#">Как стать партнером</a></li>
							<li><a href="#">Шаблоны СМС и аудио ролики</a></li>
							<li><a href="#">Стоп лист</a></li>
							<li><a href="#">Пополнить баланс</a></li>
							<li><a href="#">Денежные транзакции</a></li>
						</ul>
					</div>
				</li>
			</ul>
			<a class="opener" href="#"><span>+ View More</span><em>- View Less</em></a>
		</div>
	@else
@endif