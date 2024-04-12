<template>
	<div class="auth__info">
		<AuthHeader />
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
		<AuthFooter />
	</div>
</template>

<script>
import AuthHeader from './AuthHeader.vue';
import AuthFooter from './AuthFooter.vue';
export default {
	components: {
		AuthHeader,
		AuthFooter,
	},
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

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap");
$md2: 991.98px;
$md3: 767.98px;
$md4: 479.98px;
.auth__info {
	color: #333333;
	font-family: "Inter", sans-serif;
	background: url("../../assets/img/auth/bg.png");
	padding: 20px;

	&__title {
		font-size: 56px;
		font-weight: 600;
		@media (max-width: $md2) {
			font-size: 34px;
			line-height: 40px;
			text-align: center;
		}
		@media (max-width: $md3) {
			font-size: 24px;
			line-height: 30px;
		}
		@media (max-width: $md4) {
			font-size: 18px;
			line-height: 20px;
		}
	}
	&__subtitle {
		font-size: 24px;
		font-weight: 600;
		color: #737b8a;
		@media (max-width: $md2) {
			font-size: 20px;
			line-height: 30px;
			text-align: center;
		}
		@media (max-width: $md3) {
			font-size: 16px;
			line-height: 20px;
		}
	}
	&__row {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
		@media only screen and (max-width: 768px) {
			grid-template-columns: 1fr;
		}
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
				font-size: 18px;
				font-weight: 600;
				@media (max-width: $md2) {
					font-size: 16px;
					line-height: 30px;
				}
				@media (max-width: $md3) {
					font-size: 14px;
					line-height: 20px;
				}
			}
			&__desc {
				font-size: 16px;
				font-weight: 400;
				color: #737b8a;
				@media (max-width: $md2) {
					font-size: 14px;
				}
				@media (max-width: $md3) {
					font-size: 12px;
				}
			}
		}
	}
}
</style>
