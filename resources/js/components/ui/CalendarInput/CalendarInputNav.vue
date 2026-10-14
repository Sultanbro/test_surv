<template>
	<div class="CalendarInputNav">
		<div
			v-for="tab, i in tabs"
			:key="i"
			class="CalendarInputNav-tab"
			:class="{'CalendarInputNav-tab_selected': selectedTab === tab}"
			@click="onTab(tab)"
		>
			{{ tab }}
		</div>
		<b-btn
			v-if="submit"
			class="CalendarInputNav-submit"
			@click="onSubmit"
		>
			Применить
		</b-btn>
	</div>
</template>

<script>
export default {
	name: 'CalendarInputNav',
	components: {},
	inject: [
		'getSubmit',
		'onTabToday',
		'onTabTomorrow',
		'onTabCurrentMonth',
		'onTabPrevMonth',
		'onTabCurrentYear',
		'onTabPrevYear',
		'onTabAllTime',
		'onTabCustom',
		'onSubmit',
	],
	props: {
		tabs: {
			type: Array,
			default: () => [
				'Сегодня',
				'Завтра',
				'Текущий месяц',
				'Прошлый месяц',
				'Текущий год',
				'Прошлый год',
				'Все время',
			]
		}
	},
	data(){
		return {
			selectedTab: 'Сегодня'
		}
	},
	computed: {
		submit(){
			return this.getSubmit()
		}
	},
	methods:{
		onTab(tab){
			this.selectedTab = tab
			switch(tab){
			case 'Сегодня':
				this.onTabToday()
				break;
			case 'Завтра':
				this.onTabTomorrow()
				break;
			case 'Текущий месяц':
				this.onTabCurrentMonth()
				break;
			case 'Прошлый месяц':
				this.onTabPrevMonth()
				break;
			case 'Текущий год':
				this.onTabCurrentYear()
				break;
			case 'Прошлый год':
				this.onTabPrevYear()
				break;
			case 'Все время':
				this.onTabAllTime()
				break;
			default:
				this.onTabCustom(tab)
			}
		}
	}
}
</script>

<style lang="scss">
.CalendarInputNav{
	display: flex;
	flex-flow: column nowrap;
	align-items: stretch;
	padding: 2rem;
	border-left: 0.1rem solid #BFD2F3;
	border-radius: 0 1.6rem 1.6rem 0;
	background: #F1F5FB;
	&-tab{
		padding: 1.1rem 1.5rem;
		margin-left: -2rem;
		font-weight: 600;
		font-size: 11px;
		letter-spacing: -0.04em;
		white-space: nowrap;
		color: #6181B8;
		cursor: pointer;
		user-select: none;
		&_selected{
			background-color: #fff;
			margin-left: -2.1rem;
			border-width: 0.5px 0.5px 0.5px 0;
			border-style: solid;
			border-color: #BFD2F3;
			border-radius: 0 0.5rem 0.5rem 0;
			color: #3361FF;
		}
	}
	&-submit{
		padding: 1.3rem 2.5rem;
		margin-top: auto;
		border: none;
		border-radius: 5px;

		font-weight: 600;
		font-size: 12px;
		line-height: 14px;
		letter-spacing: -0.03em;

		background: #3361FF;
		&:hover{
			background: lighten(#3361FF, 10);
		}
	}
}
</style>
