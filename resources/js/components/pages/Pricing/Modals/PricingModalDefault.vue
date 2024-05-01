<template>
	<div class="pricing-default-modal">
		<div class="pricing-img-block">
			<div class="pricing-eclipse" />
			<img
				src="/images/price/PriceTrialLike.png"
				class="pricing-icon-like"
			>
			<img
				src="/images/price/PriceTrialCheckBox.png"
				class="pricing-icon-checkBox"
			>
			<img
				src="/images/price/PriceTrialBox.png"
				class="pricing-icon-box"
			>
		</div>
		<h1 class="pricing-title">
			Подключение пробного периода тарифа Pro
		</h1>
		<div class="pricing-description">
			На 30 дней бесплатно. Если захотите продлить тариф или перейти на другой, вы просто можете оплатить любой из них до окончания пробного периода. Оставшиеся дни бесплатного периода не сгорят после оплаты.
		</div>
		<div class="pricing-choice-tariff">
			<div class="pricing-tariff-title">
				Подключить тариф для пространства
			</div>
			<DropdownPrice
				:options="options"
				placeholder="Выберите компанию"
			/>
		</div>
		<div class="pricing-button-group">
			<button
				class="pricing-button-connect"
				@click="connectFreePeriod"
			>
				Подключить бесплатно
			</button>
			<button
				class="pricing-button-later"
				@click="closeModal"
			>
				Попробую позже
			</button>
		</div>
	</div>
</template>

<script>
import DropdownPrice from '../DropDown.vue';
import {mapActions} from 'pinia';
import {useModalStore} from '../../../../stores/Modal';
import {usePricingPeriodStore} from '../../../../stores/PricingPeriod';

export default {
	name: 'PricingModalDefault',
	components: {DropdownPrice},
	data(){
		return{
			options: [{logo: '/images/price/logo.png', name: '1suol9rbcn'}, {logo: '/images/price/logo.png', name: 'ИП Самозанятость'}]
		}
	},
	computed:{
		...mapActions(usePricingPeriodStore, ['tariffStore'])
	},
	methods:{
		...mapActions(useModalStore, ['removeModalActive']),
		...mapActions(usePricingPeriodStore, ['connectedTariff']),

		closeModal(){
			this.removeModalActive()
		},
		connectFreePeriod(){
			this.connectedTariff('pro')
			this.removeModalActive()
		}
	}
}
</script>

<style scoped>
.pricing-default-modal{
	display: flex;
		flex-direction: column;
	height: 100vh;
}
.pricing-img-block{
	position: relative;
	bottom: 5%;
	height: 326px;
	background-color: #1A1A1A;
	border-radius: 8px;
	overflow: hidden;

}

.pricing-eclipse{
	background-color: #0C50FF;
	box-shadow: 0 0 130px 10px rgba(12, 80, 255, 0.8);
	width: 351px;
	height: 351px;
	border-radius: 50%;
	position: absolute;
	top: 50%;
	left: 60%;
	transform: translate(-50%, -50%);
	z-index: 2;
	filter: blur(40px);
}

.pricing-icon-like{
	position: absolute;
	z-index: 7;
	width: 88px;
	height: 88px;
	top: 5%;
	left: 16%;
}

.pricing-icon-checkBox{
	position: absolute;
	z-index: 7;
	width: 153px;
	height: 153px;
	top: 35%;
	left: 5%;
}

.pricing-icon-box{
	position: absolute;
	z-index: 7;
	width: 262px;
	height: 262px;
	left: 35%;
	top: 10%;

}

.pricing-title{
	padding: 0 32px 0 32px;
	margin: 32px 0 12px 0;
	font-weight: 600;
	font-size: 28px;
	line-height: 36px	;
	width: 484px;
}

.pricing-description{
	padding: 0 32px 0 32px;
	width: 484px;
	font-size: 16px;
	margin-bottom: 24px;
}

.pricing-choice-tariff{
	display: flex;
	flex-direction: column;
	gap: 8px;
	padding: 32px;
}

.pricing-tariff-title{
	color: #737B8A;
	font-size: 16px;
}

.pricing-button-group{
	margin-top: auto;
	padding: 0 32px 32px 32px;
	font-weight: 500;
	font-size: 16px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.pricing-button-connect{
	width: 100%;
	padding: 13px 0;
	text-align: center;
	border-radius: 8px;
	background-color: #0C50FF;
	color: white;
}
.pricing-button-later{
	width: 100%;
	padding: 13px 0;
	text-align: center;
	border-radius: 8px;
	background-color: #EDEDED;
}
</style>
