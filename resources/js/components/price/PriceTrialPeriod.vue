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
		background-color: #1A1A1A;
		width: 1195px !important;
		border-radius: 24px;
		display: flex;
		position: relative;
		overflow: hidden;
	}
	.price-trial-icon-like{
		position: absolute;
		z-index: 7;
		width: 88px;
		height: 88px;
		top: 10%;
		left: 70%;
	}

	.price-trial-checkbox{
		position: absolute;
		z-index: 7;
		width: 153px !important;
		height: 153px !important;
		top: 40% !important;
		left: 65%;
	}
	.price-trial-icon-box{
		position: absolute;
		z-index: 7;
		width: 262px !important;
		height: 262px !important;
		top: 0% !important;
		left: 77%;
	}
}
.price-trial{
		margin-top: 36px;
	background-color: #1A1A1A;
	width: 1000px;
	border-radius: 24px;
	display: flex;
	position: relative;
	overflow: hidden;
}
.price-trial-icon-like{
	position: absolute;
	z-index: 7;
	width: 88px;
	height: 88px;
	top: 10%;
	left: 70%;
}

.price-trial-checkbox{
	position: absolute;
	z-index: 7;
	width: 103px;
	height: 103px;
	top: 55%;
	left: 65%;
}
.price-trial-icon-box{
	position: absolute;
	z-index: 7;
	width: 212px;
	height: 212px;
	top: 20%;
	left: 77%;
}

	.price-trial-content{
			max-width: 610px;
			width: 100%;
			color: white;
			display: flex;
			flex-direction: column;
			gap: 12px;
			padding: 40px;
	}
	.price-trial-title{
			font-weight: 600;
			font-size: 28px;
			line-height:24px;
	}
	.price-trial-description{
			font-size: 18px;
			line-height: 27px;
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
			padding: 16px 32px;
			font-size: 16px;
  }
	.price-trial-text{
			font-size: 14px;
			line-height: 21px;
	}

	.price-trial-icons-content{
	}

	.price-trial-eclipse{
		background-color: #0C50FF;
		box-shadow: 0 0 230px 20px rgba(12, 80, 255, 0.8);
		width: 401px;
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
