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
				:selected-option="selectedOption"
				:options="options.data"
				placeholder="Выберите компанию"
				@update="updateSelected"
			/>
		</div>
		<div class="pricing-button-group">
			<button
				class="pricing-button-connect"
				:class="{ 'disabledSelect': selectedOption === null }"
				:disabled="selectedOption === null"
				@click="connectFreePeriod"
			>
				Подключить бесплатно
			</button>
		</div>
	</div>
</template>

<script>
import DropdownPrice from '../DropDown.vue';
import {mapActions, mapState} from 'pinia';
import {useModalStore} from '../../../../stores/Modal';
import {usePricingPeriodStore} from '../../../../stores/PricingPeriod';
import {useCurrentFetchStore} from '../../../../stores/currentFetch';

export default {
	name: 'PricingModalDefault',
	components: {DropdownPrice},
	data(){
		return{
			options: [],
			selectedOption: null
		}
	},

	computed:{
		...mapState(usePricingPeriodStore, ['tariffStore']),
		...mapState(useCurrentFetchStore, ['current']),


	},
	created(){
		this.selectedOption = this.current

	},
	mounted() {
		this.getPriceData()

	},

	methods:{
		...mapActions(useModalStore, ['removeModalActive',  'setPrice']),
		...mapActions(usePricingPeriodStore, ['connectedTariff']),

		updateSelected(option) {
			this.selectedOption = option;
		},
		async getPriceData() {
			try {
				this.axios.get('/tenants').then((response) => {
					this.options = response.data;
				})
			} catch (error) {
				console.error('Failed to fetch price data:', error);
			}
		},
		async postDemo() {
			try {
				await this.axios.post('/tariff/trial', {
					// eslint-disable-next-line camelcase
					selectedOption: this.selectedOption.id
				})
			} catch (error) {
				console.error('Failed to fetch price data:', error);
			}
		},
		closeModal(){
			this.removeModalActive()
		},
		connectFreePeriod(){
			this.postDemo()
			this.removeModalActive()
			window.location.reload();

		},

	}
}
</script>

<style scoped>
@media (min-width: 1600px) {
	.pricing-default-modal{
		display: flex;
		flex-direction: column;
		height: 100vh !important;
	}
	.pricing-img-block{
		position: relative;
		bottom: 4.8% !important;
		height: 326px !important;
		background-color: #1A1A1A;
		border-radius: 40px;
		overflow: hidden;

	}

	.pricing-eclipse{
		background-color: #0C50FF;
		box-shadow: 0 0 130px 10px rgba(12, 80, 255, 0.8);
		width: 351px !important;
		height: 351px !important;
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
		width: 88px !important;
		height: 88px !important;
		top: 5% !important;
		left: 16% !important;
	}

	.pricing-icon-checkBox{
		position: absolute;
		z-index: 7;
		width: 153px !important;
		height: 153px !important;
		top: 35% !important;
		left: 5% !important;
	}

	.pricing-icon-box{
		position: absolute;
		z-index: 7;
		width: 262px !important;
		height: 262px !important;
		left: 35% !important;
		top: 10% !important;

	}

	.pricing-title{
		padding: 0 32px 0 32px !important;
		margin: 0 0 12px 0 !important;
		font-weight: 600;
		font-size: 28px !important;
		line-height: 36px	!important;
		width: 100% !important;
	}

	.pricing-description{
		padding: 0 32px 0 32px !important;
		width: 548px !important;
		font-size: 16px !important;
		margin-bottom: 24px !important;
			line-height: 24px !important;
		font-family: Inter,serif;
	}

	.pricing-choice-tariff{
		display: flex;
		flex-direction: column;
		gap: 8px;
		padding: 32px;
	}

	.pricing-tariff-title{
		color: #737B8A !important;
		font-size: 16px !important;
	}

	.pricing-button-group{
		margin-top: auto !important;
		padding: 0 32px 32px 32px !important;
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

}

.pricing-default-modal{
	display: flex;
	flex-direction: column;
	font-family: Inter,serif !important;
	height: 100vh;
}

.disabledSelect{
		background-color: gray !important;
	cursor: not-allowed !important;
}

.pricing-img-block{
	position: relative;
	bottom: 4.8%;
	height: 206px;
	background-color: #1A1A1A;
	border-radius: 40px;
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
	width: 68px;
	height: 68px;
	top: 10%;
	left: 16%;
}

.pricing-icon-checkBox{
	position: absolute;
	z-index: 7;
	width: 103px;
	height: 103px;
	top: 40%;
	left: 10%;
}

.pricing-icon-box{
	position: absolute;
	z-index: 7;
	width: 202px;
	height: 202px;
	left: 35%;
	top: 10%;
}



.pricing-title{
	padding: 0 16px 0 16px;
	margin: 0 0 12px 0;
	font-weight: 600;
	font-size: 20px;
	line-height: 24px	;
	width: 404px;
	color: #333333;

	font-family: Inter,serif !important;
}

.pricing-description{
	padding: 0 16px 0 16px;
	width: 404px;
	font-size: 14px;
	margin-bottom: 24px;
	font-family: Inter,serif;
		line-height:18px
}

.pricing-choice-tariff{
	display: flex;
	flex-direction: column;
	gap: 8px;
	padding: 16px;
}

.pricing-tariff-title{
	color: #737B8A;
	font-family: Inter,serif;
	font-size: 14px;
}

.pricing-button-group{
	margin-top: auto;
	padding: 0 16px 25px 16px;
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
	font-family: Inter,serif;
}

.pricing-button-connect:hover{
	background-color: #0d3bb2;

}

</style>
