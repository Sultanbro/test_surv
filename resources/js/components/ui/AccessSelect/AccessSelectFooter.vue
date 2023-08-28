<template>
	<div class="AccessSelectFooter">
		<span class="AccessSelectFooter-count">
			{{ strCount }}
		</span>
		<JobtronButton
			v-if="submitButton"
			class="AccessSelectFooter-button"
			:disabled="submitDisabled"
			@click="onSubmit"
		>
			{{ submitButton }}
		</JobtronButton>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button'
export default {
	name: 'AccessSelectFooter',
	components: {
		JobtronButton,
	},
	props: {
		count: {
			type: Number,
			default: 0
		},
		submitButton: {
			type: String,
			default: 'Пригласить сотрудника'
		},
		submitDisabled: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		strAdded(){
			return this.enumerate(this.count, ['Добавлен', 'Добавлено', 'Добавлено'])
		},
		strElements(){
			return this.enumerate(this.count, ['элемент', 'элемента', 'элементов'])
		},
		strCount(){
			return `${this.strAdded} ${this.count} ${this.strElements}`
		}
	},
	methods: {
		enumerate(num, dec) {
			if (num > 100) num = num % 100;
			if (num <= 20 && num >= 10) return dec[2];
			if (num > 20) num = num % 10;
			return num === 1 ? dec[0] : num > 1 && num < 5 ? dec[1] : dec[2];
		},
		onSubmit(){
			this.$emit('submit')
		}
	}
}
</script>

<style>
.AccessSelectFooter {
	display: inline-flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	border-top: 1px solid #35354a;
	padding-top: 10px;
}
.AccessSelectFooter-count {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 400;
	font-size: 13px;
	line-height: 20px;
	letter-spacing: -0.02em;
	color: #8DA0C1;
}
.AccessSelectFooter-button {
	white-space: nowrap;
}
</style>
