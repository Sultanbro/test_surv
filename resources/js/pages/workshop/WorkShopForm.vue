<template>
	<form
		class="workshopform"
		@submit.prevent="redirect"
	>
		<input
			v-model="phone"
			type="text"
			placeholder="Телефон"
		>
		<input
			v-model="sum"
			type="number"
			min="0"
			placeholder="Сумма выполнения"
		>
		<button type="submit">
			Оплатить
		</button>
	</form>
</template>

<script>
import { useAmountStore } from '../../stores/amount';
import { mapActions, mapState } from 'pinia';

export default {
	components: {
		...mapState(useAmountStore, ['phone', 'sum']),
	},
	methods: {
		...mapActions(useAmountStore, ['setUsername']),
		redirect() {
			sessionStorage.setItem('phoneForm', this.phone);
			sessionStorage.setItem('sumForm', this.sum);
			window.location.href = '/payworkshopknowledgebase'
		},
	},
};
</script>

<style scoped lang="scss">
.workshopform {
	padding: 2%;
	display: flex;
	align-items: center;
	gap: 4px;
	input[type="text"], input[type="number"] {
		border-radius: 10px;
		font-size: 15px;
		padding: 1%;
		border: 1px solid #646464;
	}
	button {
		background-color: rgb(0, 145, 255);
		color: white;
		border-radius: 15px;
		font-size: 15px;
		padding: 2%;
	}
}
</style>
