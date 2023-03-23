<template>
	<div class="AccessSelectFooter">
		<span class="AccessSelectFooter-count">
			{{ strCount }}
		</span>
		<button
			v-if="submit"
			class="AccessSelectFooter-button hover-pointer"
			@click="$emit('submit')"
		>
			<img src="/icon/news/access-modal/plus-accent.svg">
			<span class="AccessSelectFooter-add">
				{{ submit }}
			</span>
		</button>
	</div>
</template>

<script>
export default {
	name: 'AccessSelectFooter',
	components: {},
	props: {
		count: {
			type: Number,
			default: 0
		},
		submit: {
			type: String,
			default: 'Пригласить сотрудника'
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
	}
}
</script>

<style>
.AccessSelectFooter {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	border-top: 1px solid #EBEBF9;
	padding-top: 20px;
	padding-right: 20px;
}
.AccessSelectFooter-count {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 400;
	font-size: 13px;
	line-height: 20px;
	padding-left: 20px;
	letter-spacing: -0.02em;
	color: #8DA0C1;
}
.AccessSelectFooter-button {
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: 10px;
	background: #F7FBFF;
	border-radius: 6px;
	padding: 15px 35px 15px 30px;
}
.AccessSelectFooter-add {
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 500;
	font-size: 14px;
	line-height: 20px;
	letter-spacing: -0.02em;
	color: #156AE8;
}
</style>
