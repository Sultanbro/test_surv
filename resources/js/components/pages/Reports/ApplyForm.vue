<template>
	<section class="ApplyForm">
		<header class="ApplyForm-header">
			<div class="ApplyForm-title">
				Принятие на работу
			</div>
			<div
				class="ApplyForm-close"
				@click="$emit('close')"
			>
				<i class="fa fa-times" />
			</div>
		</header>
		<div class="ApplyForm-content">
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Имя
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<b-form-input
						v-model="localValue.name"
						placeholder="Имя"
					/>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Фамилия
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<b-form-input
						v-model="localValue.lastName"
						placeholder="Фамилия"
					/>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					График работы
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<b-form-select
						v-model="localValue.workChart"
						placeholder="График работы"
						:options="workChartOptions"
					/>
				</span>
			</label>
			<label
				v-if="neetFirstDay"
				class="ApplyForm-field"
			>
				<span class="ApplyForm-label">
					Первый рабочий день
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<input
						v-model="localValue.firstDay"
						type="date"
						class="form-control"
					>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Ставка
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<b-form-select
						v-model="localValue.fullTime"
						placeholder="Ставка"
						:options="shiftTypeOptions"
					/>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Удаленный/Оффисный
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<b-form-select
						v-model="localValue.userType"
						placeholder="Удаленный/Оффисный"
						:options="userTypeOptions"
					/>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Оклад
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<b-form-input
						v-model="localValue.zarplata"
						placeholder="Сумма в KZT"
						class="mb-2"
					/>
					<b-form-select
						v-model="localValue.currency"
						placeholder="Валюта сотрудника"
						:options="currencyOptions"
					/>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Основной телефон
					<span class="red">*</span>
				</span>
				<span class="ApplyForm-input">
					<input
						id="ApplyForm-phone"
						v-model="localValue.phone"
						class="form-control ApplyForm-phone"
					>
				</span>
			</label>
			<label class="ApplyForm-field">
				<span class="ApplyForm-label">
					Супруг(а)
				</span>
				<span class="ApplyForm-input">
					<input
						id="ApplyForm-phone2"
						v-model="localValue.phone2"
						class="form-control ApplyForm-phone"
					>
				</span>
			</label>
		</div>
		<footer class="ApplyForm-footer">
			<JobtronButton
				small
				@click="$emit('input', localValue)"
			>
				Принять
			</JobtronButton>
		</footer>
	</section>
</template>

<script>
import { getShiftDays, getShiftType } from '@/composables/shifts'

import JobtronButton from '../../ui/Button.vue'

export default {
	name: 'ApplyForm',
	components: {
		JobtronButton,
	},
	props: {
		value: {
			type: Object,
			required: true,
		},
		workCharts: {
			type: Array,
			default: () => []
		}
	},
	data(){
		return {
			localValue: JSON.parse(JSON.stringify(this.value)),
			shiftTypeOptions: [
				{
					text: 'Full-Time',
					value: 1,
				},
				{
					text: 'Part-Time',
					value: 0,
				},
			],
			userTypeOptions: [
				{
					text: 'Удаленный',
					value: 'remote',
				},
				{
					text: 'Оффисный',
					value: 'office',
				},
			],
			currencyOptions: [
				{
					text: 'KZT Казахстанский тенге',
					value: 'kzt',
				},
				{
					text: 'RUB Российский рубль',
					value: 'rub',
				},
				{
					text: 'KGS Киргизский сом',
					value: 'kgs',
				},
				{
					text: 'UZS Узбекский сум',
					value: 'uzs',
				},
				// {
				// 	text: 'uah',
				// 	value: 'UAH Украинская гривна',
				// },
				// {
				// 	text: 'byn',
				// 	value: 'BYN Белорусский рубль',
				// },
			]
		}
	},
	computed: {
		workChartOptions(){
			return this.workCharts.map(chart => ({
				text: `${getShiftDays(chart)} (с ${chart.start_time} по ${chart.end_time}) - ${chart.text_name}`,
				value: chart,
			}))
		},
		neetFirstDay(){
			return this.localValue.workChart && getShiftType(this.localValue.workChart) === 2
		},
	},
	watch: {
		value(){
			this.localValue = JSON.parse(JSON.stringify(this.value))
		}
	},
	created(){
		this.initMask()
	},
	mounted(){
		this.applyMask()
	},
	beforeDestroy(){},
	methods: {
		initMask(){
			if(window.intlTelInput) return
			const el = document.createElement('script')
			el.setAttribute('src', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js')
			document.head.appendChild(el)

			const link = document.createElement('link')
			link.rel = 'stylesheet'
			link.href = 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css'
			document.head.appendChild(link)
		},
		applyMask(){
			if(!window.intlTelInput) return setTimeout(this.applyMask, 100)
			const phones = this.$el.querySelectorAll('.ApplyForm-phone')
			phones.forEach(input => {
				window.intlTelInput(input, {
					utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js',
					autoInsertDialCode: true,
					preferredCountries: ['kz', 'ru', 'kg', 'uz'],
					nationalMode: false,
					autoPlaceholder: 'aggressive',
					numberType: 'MOBILE',
					// separateDialCode: true,
					// hiddenInput: true,
				})
			})
		},
	},
}
</script>

<style lang="scss">
.ApplyForm{
	display: grid;
	grid-template-rows: auto 1fr auto;
  max-width: 600px;
	height: fit-content;
	max-height: calc(100% - 40px);
	padding: 0 20px;
  margin: 20px auto;
	background-color: #fff;
	border-radius: 16px;

	&-header{
		display: flex;
		flex-flow: row nowrap;
		align-items: stretch;
		margin-inline: -20px;
		border-bottom: 1px solid #ddd;
	}
	&-title{
		flex: 1;
		padding: 8px 20px;
	}
	&-close{
		flex: 0 0 fit-content;
		display: flex;
		align-items: center;
		padding: 8px 20px;
		cursor: pointer;
		color: #777;
		&:hover{
			color: #000;
		}
	}
	&-content{
		max-height: 100%;
		padding-top: 20px;
		overflow-y: auto;
	}
	&-field{
		display: flex;
		flex-flow: row nowrap;
		justify-content: stretch;
		gap: 10px;
		margin-bottom: 20px;
	}
	&-label{
		flex: 0 0 33%;
		margin-top: 6px;
	}
	&-input{
		flex: 1;
		.iti{
			display: block;
		}
	}
	&-phone{
		&.ApplyForm-phone{
			padding-left: 60px !important;
		}
	}
	&-footer{
		padding-inline: 20px;
		padding-block: 8px;
		margin-inline: -20px;
		border-top: 1px solid #ddd;
	}
}
</style>
