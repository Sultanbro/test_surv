<template>
	<div class="RefWidget">
		<div class="RefWidget-title">
			Реферальная программа «Business&nbsp;Family»
			<b-badge variant="warning">
				Demo
			</b-badge>
		</div>

		<div class="RefWidget-border">
			Ваш статус: {{ status }}
		</div>

		<div class="RefWidget-border">
			<router-link :to="'/ref-preza'">
				«Как заработать миллион»
			</router-link>
		</div>

		<div
			class="RefWidget-border pointer"
			@click="copyLink"
		>
			{{ reflink }}
		</div>

		<div class="RefWidget-border">
			<router-link :to="'/ref-promo'">
				«Как заработать миллион»
			</router-link>
		</div>

		Заработанно:
		<div class="RefWidget-border">
			за все время: {{ total }} ₸
		</div>
		<div class="RefWidget-border">
			свои за месяц: {{ month }} ₸
		</div>
		<div class="RefWidget-border">
			от рефералов: {{ monthRef }} ₸
		</div>
		<div class="RefWidget-border">
			<a
				v-if="leads"
				href="#RefStat"
			>
				статистика
			</a>
			<span
				v-else
				v-b-popover.hover.righttop="'У Вас пока нет приглашенных кандидатов и статистики. Начните скорее отправлять реферальную ссылку'"
				class="underdot"
			>
				статистика
			</span>
		</div>
	</div>
</template>

<script>
import { copy2clipboard } from '@/composables/copy2clipboard'
const fakeData = {
	status: 'Promouter', // Promoter/Activist/Ambassador
	reflink: location.origin + '/reflink?r=qwerty123456',
	total: 100,
	month: 50,
	monthRef: 10,
	leads: 1,
}
export default {
	name: 'RefWidget',
	components: {},
	data(){
		return {
			reflink: '',
			status: '',
			total: 0,
			month: 0,
			monthRef: 0,
			leads: 0,
		}
	},
	mounted(){
		this.fetchData()
	},
	methods: {
		async fetchData(){
			this.reflink = fakeData.reflink
			this.status = fakeData.status
			this.total = fakeData.total
			this.month = fakeData.month
			this.monthRef = fakeData.monthRef
			this.leads = fakeData.leads
		},
		copyLink () {
			copy2clipboard(this.reflink)
			this.$toast.info('Ссылка скопированна')
		},
	},
}
</script>

<style lang="scss">
.RefWidget{
  padding: 1.7rem 1rem;
  margin-top: 20px;

  position: relative;

	background: #fff;
  border-radius: 1rem;

	opacity: 0;
	visibility: hidden;
	transform: translateY(10px);
	transition: all 1s .6s;

	&-title{
		margin-bottom: 1rem;

		text-align: center;
		font-size: 1.8rem;
		font-weight: 600;
		color: #5b6166;
	}
	&-border{
		display: flex;
		justify-content: space-between;
		align-items: center;

		width: 100%;
		min-height: 4rem;
		padding: 0 1.5rem;
		margin-bottom: 1rem;
		border: 1px solid #e7eaea;

		font-size: 1.2rem;
		color: #62788b;

		border-radius: 1rem;
	}
}
.header__profile._active{
	.RefWidget{
		opacity: 1;
    transform: translateY(0);
    visibility: visible;
	}
}
</style>
