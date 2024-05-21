<template>
	<div class="workshop">
		<div
			v-for="button in buttons"
			:key="button.id"
		>
			<button @click="openPayForm(button.link)">
				{{ button.title }}
			</button>
		</div>
	</div>
</template>

<script>
export default {
	name: 'WorkshopPage',
	data() {
		return {
			buttons: [
				{
					id: 1,
					title: 'Оплата картой Казахстана',
					link: 'https://wl.walletone.com/checkout/refill/CreditCard/347428001/NewCard',
				},
				{
					id: 2,
					title: 'Оплата картой РФ',
					link: 'https://proeducation.kz/e44NQ/',
				},
				{
					id: 3,
					title: 'Оплата Каспи Рассрочка',
					link: '',
				}
			],
		};
	},
	methods: {
		openPayForm(link) {
			window.open(link, '_blank');
		},
	},
};
</script>

<style scoped lang="scss">
@import url("./styles.scss");
</style>