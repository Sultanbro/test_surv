<template>
	<div class="price-trial">
		<div class="price-trial-content">
			<h1 class="price-trial-title">
				Максимум возможностей — бесплатно!
			</h1>
			<div class="price-trial-description">
				Получите все преимущества* Pro тарифа на 30 дней в пару кликов. Привязка банковской карты не потребуется
			</div>
			<div class="price-trial-button-group">
				<button
					class="price-trial-button"
					@click="tryFreePeriod('priceModal')"
				>
					Попробовать бесплатно
				</button>
				<div class="price-trial-text">
					*кроме смены доменного имени
				</div>
			</div>
		</div>
		<div class="price-trial-icons-content">
			<div class="price-trial-eclipse" />
			<img
				src="/images/price/PriceTrialLike.png"
				class="price-trial-icon-like"
			>
			<img
				src="/images/price/PriceTrialCheckBox.png"
				class="price-trial-checkbox"
			>
			<img
				src="/images/price/PriceTrialBox.png"
				class="price-trial-icon-box"
			>
		</div>
	</div>
</template>

<script>
import {useModalStore} from '../../stores/Modal';
import { mapState, mapActions } from 'pinia'
import {usePricingPeriodStore} from '../../stores/PricingPeriod';

export default {
	name: 'PriceTrialPeriod',
	props: {

	},
	data(){
		return {

		}
	},

	computed:{
		...mapState(useModalStore, ['currentModalId']),
		...mapState(usePricingPeriodStore, ['priceStore'])
	},

	methods: {
		...mapActions(useModalStore, ['setCurrentModal']),

		tryFreePeriod(value) {
			this.setCurrentModal(value);
		}
	}

}
</script>

<style scoped>
@media (min-width: 1600px) {
	.price-trial{
		width: 1495px !important;

	}
	.price-trial-title{
		font-size: 24px !important;
		line-height:24px;
	}
	.price-trial-description{
		font-size: 16px !important;
		line-height: 24px;
	}
	.price-trial-icon-like{
		position: absolute;
		z-index: 7;
		width: 88px !important;
		height: 88px !important;
		top: 10% !important;
		left: 73% !important;
	}

	.price-trial-checkbox{
		position: absolute;
		z-index: 7;
		width: 143px !important;
		height: 143px !important;
		top: 33% !important;
		left: 68% !important;
	}
	.price-trial-icon-box{
		position: absolute;
		z-index: 7;
		width: 252px !important;
		height: 252px !important;
		top: -9% !important;
		left: 82% !important;
	}
	.price-trial-content{
		padding: 40px !important;
			gap: 0 !important;
	}
	.price-trial-button{
		padding: 16px 32px !important;
		font-size: 16px !important;
	}
	.price-trial-eclipse{
		box-shadow: 0 0 230px 20px rgba(12, 80, 255, 0.8) !important;
		width: 401px !important;
	}
}
.price-trial{
		margin-top: 24px;
	background-color: #1A1A1A;
	width: 1100px;
	border-radius: 24px;
	display: flex;
	position: relative;
	overflow: hidden;
}
.price-trial-icon-like{
	position: absolute;
	z-index: 7;
	width: 78px;
	height: 78px;
	top: 10%;
	left: 74%;
}

.price-trial-checkbox{
	position: absolute;
	z-index: 7;
	width: 93px;
	height: 93px;
	top: 55%;
	left: 65%;
}
.price-trial-icon-box{
	position: absolute;
	z-index: 7;
	width: 192px;
	height: 192px;
	top: 10%;
	left: 81%;
}

	.price-trial-content{
			max-width: 630px;
			width: 100%;
			color: white;
			display: flex;
			flex-direction: column;
			gap: 12px;
			padding: 40px;
	}
	.price-trial-title{
			font-weight: 600;
			font-size: 20px;
			line-height:24px;
	}
	.price-trial-description{
			font-size: 14px;
			line-height: 24px;
	}

	.price-trial-button-group{
		display: flex;
		gap: 24px;
			align-items: center;
		margin-top: 12px;
	}

	.price-trial-button{
		background: linear-gradient(92deg, #15368C 0%, #215DF8 100%);
		border-radius: 8px;
			padding: 13px 28px;
			font-size: 14px;
			color: white;
  }
.price-trial-button:focus{
	outline: none !important;
}
	.price-trial-text{
			font-size: 14px;
			line-height: 21px;
	}

	.price-trial-icons-content{

	}

	.price-trial-eclipse{
		background-color: #0C50FF;
		box-shadow: 0 0 180px 20px rgba(12, 80, 255, 0.8);
		width: 251px;
		height: 401px;
		border-radius: 50%;
		position: absolute;
		top: 60%;
		left: 85%;
		transform: translate(-50%, -50%);
		z-index: 2;
		filter: blur(40px);
	}


</style>
