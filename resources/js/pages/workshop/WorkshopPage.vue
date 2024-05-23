<template>
	<div class="workshop">
		<div
			v-for="button in buttons"
			:key="button.id"
		>
			<button @click="openPayForm(button.link)">
				{{ button.title }} <img
					width="90"
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
					img: 'https://upload.wikimedia.org/wikipedia/commons/b/b6/Flag_of_Kazakhstan_%28The_World_Factbook%29.png',
				},
				{
					id: 2,
					title: 'Оплата картой РФ',
					link: 'https://proeducation.kz/e44NQ/',
					img: 'https://gallery.yopriceville.com/downloadfullsize/send/10186'
				},
				{
					id: 3,
					title: 'Оплата Каспи Рассрочка',
					link: '',
					img: 'https://play-lh.googleusercontent.com/w2UcnrRAfBTW-HbV4NGk2Tte1jR5TTXVH81oUUxxZHNTnk_fp3AZIuMKo5XSsvR5b5c'
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
