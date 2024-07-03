<template>
	<div class="mk-know-base">
		<div class="mk-know-base__wrapper">
			<header class="mk-know-base__header">
				<button>{{ date }} в 17:00 по мск</button>
				<p>
					Бесплатный доступ к онлайн мастер-классу для руководителей бизнеса
				</p>
			</header>
			<main class="mk-know-base__main">
				<p class="mk-know-base__main-title">
					<span>Как выйти из операционки</span> и освободить от 3 часов в день с помощью Базы Знаний
				</p>
				<div class="mk-know-base__main-bonus">
					<img
						class="mk-know-base__main-preview"
						:src="require('../assets/img/preview-video.png').default"
						alt=""
					>
					<p class="mk-know-base__main-text">
						Получите бонус после регистрации:
						<span>«7 правил для сотрудников по работе с задачами»</span>
					</p>
				</div>
				<div class="mk-know-base__main-registration">
					<div class="mk-know-base__main-button">
						<button @click="locationGroup">
							<a> Зарегистрироваться </a>
						</button>
						<p>
							Нажмите на кнопку и <span>подпишитесь в WhatsApp-группу</span>
						</p>
					</div>
					<div class="mk-know-base__main-timetracker">
						<p>{{ formattedTime }}</p>
						<!-- <input type="text" v-model="whatsAppUrl" /> -->
						<img
							src="https://optim.tildacdn.one/tild3433-3036-4264-b363-663938333336/-/resize/255x/-/format/webp/___1.png"
							width="170"
							height="70"
							alt=""
						>
					</div>
					<div class="mk-know-base__main-free-reg">
						<p>Успейте зарегистрироваться бесплатно</p>
					</div>
				</div>

				<div class="mk-know-base__main-master-class">
					<span class="mk-know-base__main-subtitle">
						На мастер-классе мы разберем:
					</span>
					<div class="line" />
					<div class="mk-know-base__main-services">
						<div class="mk-know-base__main-service">
							<section>
								<DocumentationIcon />
								<p>
									<span>Что такое База Знаний</span>, и как с ее помощью
									повысить результативность своего бизнеса
								</p>
							</section>
							<section>
								<ProgressIcon />
								<p>
									<span>Как</span> выводить новых менеджеров по продажам
									<span> на рабочие показатели в 5 раз быстрее</span>
								</p>
							</section>
						</div>
						<div class="mk-know-base__main-service">
							<section>
								<EasyOperationIcon />
								<p>
									<span>Как легко управлять удаленной командой</span>, сделав ее
									эффективной и автономной
								</p>
							</section>
							<section>
								<StaffTurnoverIcon />
								<p>
									<span>Как сократить текучку кадров</span> и не терять
									наработанный опыт при увольненнии сотрудников
								</p>
							</section>
						</div>
					</div>
				</div>

				<div class="mk-know-base__main-reg-button">
					<div>
						<button @click="locationGroup">
							<a> Успейте зарегистрироваться бесплатно </a>
						</button>
						<p>
							Нажмите на кнопку и <span>подпишитесь в WhatsApp-группу</span>
						</p>
					</div>
				</div>

				<div class="mk-know-base__main-profile">
					<img
						class="mk-know-base__main-img"
						:src="require('../../pages/assets/img/profile-image.png').default"
						alt=""
					>
					<div class="mk-know-base__main-info">
						<p>Спикер мастер-класса</p>
						<h1><span>Адиль Каримов</span></h1>
						<h2>Founder & CEO</h2>
						<ul>
							<li><span>20 лет</span> предпринимательского опыта</li>
							<li><span>Опыт управления</span> 10.000 сотрудниками</li>
							<li>
								<span>3 действующих бизнеса,</span> достигающих планы по прибыли
								без участия Адиля в операционке
							</li>
							<li>
								Обучает и помогает руководителям бизнеса
								<span>выходу из операционки</span>
							</li>
						</ul>
					</div>
				</div>

				<div class="mk-know-base__main-root">
					<div>
						<section>
							<h3>
								Регистрируйтесь бесплатно и получите «7 правил для сотрудников
								по работе с задачами»
							</h3>
							<p>
								Внедрите эти 7 правил в свой бизнес и вам больше не нужно будет
								контролировать сотрудников, чтобы они выполняли свои задачи и
								планы.
							</p>
						</section>
					</div>
					<img
						:src="require('../assets/img/preview-video2.png').default"
						alt=""
					>
				</div>
				<div class="mk-know-base__main-reg">
					<button @click="locationGroup">
						<a> Зарегистрироваться </a>
					</button>
					<p>Нажмите на кнопку и <span>подпишитесь в WhatsApp-группу</span></p>
				</div>
			</main>
		</div>
	</div>
</template>

<script>
import DocumentationIcon from '../../pages/assets/icons/DocumentationIcon.vue';
import EasyOperationIcon from '../../pages/assets/icons/EasyOperationIcon.vue';
import StaffTurnoverIcon from '../../pages/assets/icons/StaffTurnoverIcon.vue';
import ProgressIcon from '../../pages/assets/icons/ProgressIcon.vue';

export default {
	name: 'MkKnowBase',
	components: {
		DocumentationIcon,
		EasyOperationIcon,
		StaffTurnoverIcon,
		ProgressIcon,
	},
	data() {
		return {
			time: 6 * 60,
			timerRunning: false,
			interval: null,
			whatsAppUrl: 'https://chat.whatsapp.com/BEw8kjWjHSlATo8VIXeKrq',

			date: '4 июля',
		};
	},
	computed: {
		formattedTime() {
			const minutes = Math.floor(this.time / 60);
			const seconds = this.time % 60;
			return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(
				2,
				'0'
			)}`;
		},
	},
	mounted() {
		this.$nextTick(() => {
			this.setMetaViewport();
		});
		this.updateDateUrl();

		this.checkUrlChange();
		this.loadTime();
		if (this.time > 0) {
			this.startTimer();
		}
	},
	beforeDestroy() {
		this.stopTimer();
	},
	updated() {
		this.setMetaViewport();
	},
	methods: {
		setMetaViewport() {
			const meta = document.createElement('meta');
			meta.setAttribute('name', 'viewport');
			meta.setAttribute('content', 'width=device-width, initial-scale=1.0');
			document.head.appendChild(meta);
		},
		startTimer() {
			if (!this.timerRunning) {
				this.timerRunning = true;
				this.interval = setInterval(() => {
					if (this.time > 0) {
						this.time--;
						this.saveTime();
					} else {
						this.stopTimer();
						localStorage.setItem('remainingTime', '0');
					}
				}, 1000);
			}
		},
		stopTimer() {
			clearInterval(this.interval);
			this.timerRunning = false;
		},
		saveTime() {
			localStorage.setItem('remainingTime', this.time.toString());
		},
		loadTime() {
			const savedTime = localStorage.getItem('remainingTime');
			if (savedTime !== null) {
				const storedTime = parseInt(savedTime, 10);
				if (!isNaN(storedTime)) {
					this.time = storedTime;
				}
			}
		},
		resetTimer() {
			this.stopTimer();
			this.time = 7 * 60;
			this.saveTime();
			this.startTimer();
		},
		locationGroup() {
			window.location.href = this.whatsAppUrl;
		},
		checkUrlChange() {
			const savedUrl = localStorage.getItem('savedWhatsAppUrl');
			if (savedUrl !== this.whatsAppUrl) {
				localStorage.setItem('savedWhatsAppUrl', this.whatsAppUrl);
				this.resetTimer();
			}
		},
		updateDateUrl() {

			const months = {
				0: 'Января',
				1: 'Февраля',
				2: 'Марта',
				3: 'Апреля',
				4: 'Мая',
				5: 'Июня',
				6: 'Июля',
				7: 'Августа',
				8: 'Сентября',
				9: 'Октября',
				10: 'Ноября',
				11: 'Декабря',
			};

			const scheduledDates = [
				// {
				// 	date: '6-2',
				// 	url: 'https://chat.whatsapp.com/GZ8FSwFy5XU3gExCXMTQo8',
				// },
				{
					date: '6-4',
					url: 'https://chat.whatsapp.com/BEw8kjWjHSlATo8VIXeKrq',
				},
				{ date: '6-9' },
				{ date: '6-11' },
				{ date: '6-16' },
				{ date: '6-18' },
				{ date: '6-23' },
				{ date: '6-25' },
				{ date: '6-30' },
			];

			const currentDate = new Date();

			const currentDateMounth = `${currentDate.getMonth()}-${currentDate.getDate()}`;
			const currentHours = currentDate.getHours();

			if (currentHours >= 18) {
				for (const scheduledDate of scheduledDates) {
					if (scheduledDate.date >= currentDateMounth) {
						const [mounthKey, day] = scheduledDate.date.split('-');

						this.date = `${day} ${months[mounthKey]}`;
						this.whatsAppUrl = scheduledDate.url;

						break;
					}
				}
				this.checkUrlChange();
			}
		},
	},
};
</script>

<style scoped lang="scss">
$defaultColor: #ffd500;

@mixin mainButton($padding, $fz, $paddingLeft) {
	button {
		border-radius: 30px;
		background-color: $defaultColor;
		padding: $padding;
		a {
			font-size: $fz;
			font-weight: 700;
			text-decoration: none;
			color: black;
		}
	}
	p {
		padding-top: 1%;
		padding-left: $paddingLeft;
		font-size: 17.5px;
		font-weight: 700;
		span {
			color: $defaultColor;
		}
	}
}

%border-bottom {
	border-bottom: 5px solid $defaultColor;
}

.mk-know-base {
	min-height: 100vh;
	background-color: #222222;
	color: white;
	&__wrapper {
		max-width: 1050px;
		margin: auto;
		.mk-know-base__header {
			padding-top: 2%;
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 7%;
			button {
				background-color: #222222;
				padding: 1.5% 2%;
				font-size: 27px;
				width: 525px;
				font-weight: bold;
				color: white;
				border-radius: 30px;
				border: 4px solid $defaultColor;
				outline: none;
				cursor: default;
			}
			p {
				font-size: 25px;
			}
		}
		.mk-know-base__main {
			.mk-know-base__main-title {
				padding-top: 3%;
				font-size: 45px;
				font-weight: 700;
				max-width: 671px;
				line-height: 1.4;
				span {
					@extend %border-bottom;
				}
			}
			.mk-know-base__main-bonus {
				display: flex;
				align-items: center;
				gap: 4%;
				.mk-know-base__main-preview {
					width: 186px;
					height: 186px;
					transform: rotate(-5deg);
				}
				.mk-know-base__main-text {
					max-width: 575px;
					font-size: 25px;
					line-height: 1.6;
					span {
						font-weight: 800;
						background-color: rgb(28, 28, 30);
					}
				}
			}
			.mk-know-base__main-registration {
				padding-top: 1%;
				display: flex;
				gap: 5%;
				.mk-know-base__main-button {
					@include mainButton(6%, 40px, 0%);
				}
				.mk-know-base__main-timetracker {
					p {
						padding-top: 2%;
						font-size: 45px;
						text-align: center;
						z-index: 2;
						position: relative;
					}
					img {
						position: relative;
						bottom: 30px;
						left: 4px;
						z-index: 1;
					}
				}
				.mk-know-base__main-free-reg {
					p {
						font-size: 20px;
						max-width: 150px;
					}
				}
			}

			.mk-know-base__main-master-class {
				padding-top: 6%;
				.mk-know-base__main-subtitle {
					font-size: 45px;
					font-weight: 700;
					line-height: 27px;
					@extend %border-bottom;
				}

				.line {
					display: none;
					height: 3px;
					width: auto;
					background-color: $defaultColor;
				}

				.mk-know-base__main-services {
					padding-top: 5%;
					display: flex;
					gap: 3%;
					.mk-know-base__main-service {
						section {
							display: flex;
							align-items: center;
							gap: 2%;
							p {
								font-size: 25px;
								span {
									font-weight: bold;
								}
							}
						}
					}
				}
			}

			.mk-know-base__main-reg-button {
				margin-top: 2%;
				@include mainButton(3%, 27px, 4.5%);
			}

			.mk-know-base__main-profile {
				margin-top: 9%;
				display: flex;
				align-items: center;
				gap: 10%;
				.mk-know-base__main-img {
					width: 385px;
					height: 385px;
				}
				.mk-know-base__main-info {
					p {
						font-size: 22px;
					}
					h1 {
						span {
							font-size: 45px;
							font-weight: bold;
							@extend %border-bottom;
						}
					}
					h2 {
						margin-top: 2%;
						font-size: 30px;
					}
					ul {
						margin-top: 2%;
						padding-left: 20px;

						li {
							font-size: 25px;
							margin-top: 4%;
							line-height: 1.55;
							position: relative;
							span {
								font-weight: 800;
							}

							&::before {
								content: "";
								background-color: #ffd500;
								width: 15px;
								height: 15px;
								border-radius: 50%;
								position: absolute;
								left: -28px;
								top: 11px;
							}
						}
					}
				}
			}

			.mk-know-base__main-root {
				margin-top: 11%;
				display: flex;
				align-items: center;
				gap: 2%;
				h3 {
					font-weight: bold;
					font-size: 40px;
					line-height: 1.4;
					max-width: 573px;
				}
				p {
					margin-top: 8%;
					font-size: 28px;
					line-height: 1.4;
					max-width: 725px;
				}

				img {
					transform: rotate(5deg);
				}
			}

			.mk-know-base__main-reg {
				margin-top: 9%;
				@include mainButton(3% 6%, 40px, 2%);
			}
		}
	}
}

@media (max-width: 1000px) {
	.mk-know-base {
		&__wrapper {
			max-width: 800px;
			margin: auto;
			margin: 0% 12%;
			.mk-know-base__header {
				flex-wrap: wrap;
				justify-content: center;
				button {
					font-size: 14px;
					width: 100%;
					padding: 3%;
				}
				p {
					margin-top: 3%;
					font-size: 14px;
				}
			}
			.mk-know-base__main {
				.mk-know-base__main-title {
					margin-top: 3%;
					font-size: 20px;
					font-weight: bold;
					span {
						@extend %border-bottom;
					}
				}
				.mk-know-base__main-bonus {
					margin-top: 10%;
					.mk-know-base__main-preview {
            width: 110px;
            height: 110px;
					}
					.mk-know-base__main-text {
						font-size: 11px;
					}
				}
				.mk-know-base__main-registration {
					margin-top: 2%;
					flex-direction: column;
					.mk-know-base__main-button {
						text-align: center;
						button {
							padding: 5%;
							width: 100%;
							border-radius: 30px;
							a {
								font-size: 14px;
							}
						}
						p {
							font-size: 14px;
						}
					}
					.mk-know-base__main-timetracker {
						display: flex;
						flex-direction: column-reverse;
						justify-content: center;
						align-items: center;
						p {
							padding-top: 3%;
						}
						img {
							transform: rotate(90deg);
							width: 55px;
              height: 55px;
              top: 15px;
              left: 6px;
						}
					}
					.mk-know-base__main-free-reg {
						p {
							text-align: center;
							margin-top: 5%;
							font-size: 15px;
							max-width: 100%;
						}
					}
				}

				.mk-know-base__main-master-class {
					padding-top: 15%;
					.mk-know-base__main-subtitle {
						font-size: 24px;
						border: none;
					}

					.line {
						display: block;
					}

					.mk-know-base__main-services {
						flex-direction: column;
						.mk-know-base__main-service {
							section {
								p {
									font-size: 15px;
								}
								svg {
									width: 99px;
									height: 86px;
								}
							}
						}
					}
				}

				.mk-know-base__main-reg-button {
					margin-top: 5%;
					button {
						width: 100%;
						a {
							font-size: 15px;
						}
					}
					p {
						font-size: 15px;
						text-align: center;
						padding-left: 1%;
					}
				}

				.mk-know-base__main-profile {
					margin-top: 15%;
					flex-direction: column-reverse;
					.mk-know-base__main-img {
						margin-top: 7%;
						width: 191px;
						height: 191px;
					}
					.mk-know-base__main-info {
						p {
							font-size: 15px;
						}
						h1 {
							margin-top: 2%;
							span {
								font-size: 24px;
							}
						}
						h2 {
							font-size: 18px;
						}
						ul {
							margin-top: 6%;
							li {
								font-size: 15px;

								&::before {
									content: "";
									background-color: #ffd500;
									width: 10px;
									height: 10px;
									border-radius: 50%;
									position: absolute;
									left: -20px;
									top: 7px;
								}
							}
						}
					}
				}

				.mk-know-base__main-root {
					padding-top: 5%;
					flex-direction: column;
					h3 {
						font-weight: 800;
						font-size: 28px;
						line-height: 1.4;
					}
					p {
						margin-top: 4%;
						font-size: 16px;
						line-height: 1.7;
					}

					img {
						margin-top: 1%;
						transform: rotate(4deg);
						width: 313px;
						height: 313px;
					}
				}

				.mk-know-base__main-reg {
					margin-top: 3%;
					button {
						width: 100%;
						padding: 5%;
						a {
							font-weight: bold;
							font-size: 15px;
						}
					}
					p {
						font-size: 15px;
						text-align: center;
						padding-left: 0%;
					}
				}
			}
		}
	}
}
</style>
