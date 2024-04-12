<template>
	<div class="auth__info">
		<h2 class="auth__info__title">
			Масштабируйте свой бизнес с легкостью вместе с нами
		</h2>
		<h4 class="auth__info__subtitle">
			Инструменты, которые помогут вам расти и развиваться!
		</h4>

		<div class="auth__info__row">
			<div
				v-for="item in items"
				:key="item.id"
				class="auth__info__item"
			>
				<div class="auth__info__item__icon">
					<img
						:src="require(`../../assets/img/auth/${item.path}.svg`).default"
						:alt="item.path"
					>
				</div>
				<div class="auth__info__item__content">
					<div class="auth__info__item__content__title">
						{{ item.title }}
					</div>
					<div class="auth__info__item__content__desc">
						{{ item.desc }}
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	components: {},
	data() {
		return {
			items: [
				{
					id: 1,
					path: 'books',
					title: 'База заний',
					desc: 'Нет системности в отделах? Объедините все регламенты, документы, должностные инструкции и др. в одном месте',
				},
				{
					id: 2,
					path: 'graduation',
					title: 'Автоматизированное обучение сотрудников (LSM)',
					desc: 'Нет системности в отделах? Объедините все регламенты, документы, должностные инструкции и др. в одном месте',
				},
				{
					id: 3,
					path: 'chart',
					title: 'Внедрение KPI',
					desc: 'Определяйте и отслеживайте KPI, чтобы достигать цели.Есть интеграция с CRM Битрикс24 и AMOCRM.',
				},
				{
					id: 4,
					path: 'light',
					title: 'Мотивация',
					desc: 'Назначайте материальную и нематериальную мотивацию за выполнение сотрудниками поставленных задач',
				},
				{
					id: 5,
					path: 'tree',
					title: 'Структура компании',
					desc: 'Визуальная подробная структура компании облегчает взаимодействие в работе сотрудников',
				},
				{
					id: 6,
					path: 'glass',
					title: 'Контроль качества',
					desc: 'Назначайте материальную и нематериальную мотивацию за выполнение сотрудниками поставленных задач',
				},
				{
					id: 7,
					path: 'wallet',
					title: 'Табелирование и начисления',
					desc: 'Определяйте и отслеживайте KPI, чтобы достигать цели. Есть интеграция с CRM Битрикс24 и AMOCRM.',
				},
				{
					id: 8,
					path: 'news',
					title: 'Новости, опросы, корпоративный чат',
					desc: 'Привычная лента новостей, как в соцсетях. Делитесь новостями, узнавайте мнение коллег и общайтесь',
				},
			],
		};
	},
};
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap");
$md2: 991.98px;
$md3: 767.98px;
$md4: 479.98px;

.auth__info {
	color: #333333;
	font-family: "Inter", sans-serif;

	&__title {
		font-size: 56px;
		font-weight: 600;
		margin: 1em 0 0.4em;
	}
	&__subtitle {
		font-size: 24px;
		font-weight: 600;
		color: #737b8a;
	}
	&__row {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
	}
	&__item {
		background: #ffffff;
		padding: 20px;
		border-radius: 24px;
		display: flex;
		&__icon {
			margin-right: 20px;
		}
		&__content {
			&__title {
				margin-bottom: 8px;
				font-size: 18px;
				line-height: 24px;
				font-weight: 600;
			}
			&__desc {
				font-size: 16px;
				font-weight: 400;
				color: #737b8a;
			}
		}
	}
}

@container auth-info (max-width: 991.98px) {
	.auth__info{
		&__title{
			font-size: 34px;
			line-height: 40px;
		}
		&__subtitle{
			font-size: 20px;
			line-height: 30px;
		}
		&__item{
			&__content{
				&__title{
					font-size: 16px;
					line-height: 22px;
				}
				&__desc{
					font-size: 14px;
				}
			}
		}
	}
}
@container auth-info (max-width: 767.98px) {
	.auth__info{
		&__title{
			font-size: 24px;
			line-height: 30px;
			text-align: center;
		}
		&__subtitle{
			font-size: 16px;
			line-height: 20px;
			text-align: center;
		}
		&__row{
			grid-template-columns: 1fr;
		}
		&__item{
			&__content{
				&__title{
					font-size: 14px;
					line-height: 20px;
				}
				&__desc{
					font-size: 12px;
				}
			}
		}
	}
}
@container auth-info (max-width: 479.98px) {
	.auth__info{
		&__title{
			font-size: 18px;
			line-height: 20px;
		}
	}
}
</style>
