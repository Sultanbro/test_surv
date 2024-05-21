<template>
	<div class="workshop">
		<div
			v-for="button in buttons"
			:key="button.id"
		>
			<button @click="openPayForm(button.link)">
				{{ button.title }} <img
					width="50"
					height="50"
					:src="button.img"
					alt=""
				>
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
					img: 'https://cdn6.aptoide.com/imgs/8/4/3/843528874033d56b6d8087f45cc8c87d_icon.png',
				},
				{
					id: 2,
					title: 'Оплата картой РФ',
					link: 'https://proeducation.kz/e44NQ/',
					img: 'https://yt3.googleusercontent.com/7k5TkI9uN3-tDCs1hyUk2fatuNX1q_GNd9HuyinVHL_cA0OncgZDrwJcAWUUKFk2UnHRMpF5=s900-c-k-c0x00ffffff-no-rj'
				},
				{
					id: 3,
					title: 'Оплата Каспи Рассрочка',
					link: '',
					img: 'https://elkom.kz/upload/iblock/141/Logo_Kaspi_Red.png'
				},
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
