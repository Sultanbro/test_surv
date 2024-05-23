<template>
	<div class="workshop">
		<h1 class="workshop__title">
			Оплата заказа
		</h1>
		<div
			v-for="button in buttons"
			:key="button.id"
		>
			<button
				class="workshop__button"
				@click="openPayForm(button.link)"
			>
				{{ button.title }}
				<img
					class="icon"
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
					title: 'Картой банка не из Росиии',
					link: 'https://wl.walletone.com/checkout/refill/CreditCard/347770942/NewCard',
					img: 'https://cdn-icons-png.flaticon.com/512/5352/5352016.png',
					dopImg:
						'https://wl.walletone.com/Style/checkout/Areas/Cards/Shared/img/credit-card.svg',
					desc: 'Оплачивая картой вы соглашаетесь с услагами оферты',
				},
				{
					id: 2,
					title: 'Картой Российского банка',
					link: 'https://proeducation.kz/e44NQ/',
					img: 'https://gallery.yopriceville.com/downloadfullsize/send/10186',
				},
				{
					id: 3,
					title: 'Оплата Каспи Рассрочка',
					link: 'https://pay.kaspi.kz/pay/jjtpkyxq',
					img: 'https://cdn-icons-png.flaticon.com/512/262/262042.png',
				},
				{
					id: 3,
					title: 'Тест',
					link: 'https://wl.walletone.com/checkout/refill/CreditCard/347773203/NewCard',
					img: 'https://cdn-icons-png.flaticon.com/512/262/262042.png',
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
