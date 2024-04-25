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
import * as LANG from './AuthInfo.lang.js'

export default {
	components: {},
	data() {
		return {};
	},
	computed: {
		lang(){
			return LANG[this.$root.$data.lang || 'ru']
		},
		items(){
			return this.lang.items
		}
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
			margin-top: 0;
			font-size: 30px;
			line-height: 1.1;
		}
		&__subtitle{
			font-size: 20px;
			line-height: 1.1;
			margin: 0.5em 0 0.5em;
		}
		&__row{
			gap: 12px;
		}
		&__item{
			padding: 15px;
			&__icon{
				margin-right: 15px;
			}
			&__content{
				&__title{
					font-size: 14px;
					line-height: 1.1;
				}
				&__desc{
					font-size: 12px;
					line-height: 1.1;
				}
			}
		}
	}
}
@container auth-info (max-width: 767.98px) {
	.auth__info{
		&__title{
			font-size: 24px;
			text-align: center;
		}
		&__subtitle{
			font-size: 16px;
			text-align: center;
		}
		&__row{
			grid-template-columns: 1fr;
		}
		&__item{
			&__content{
				&__title{
					font-size: 14px;
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
		}
	}
}
</style>
