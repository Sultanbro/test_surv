<template>
	<div class="pricing-buy-modal">
		<h1 class="pricing-buy-title">
			Переход на тариф бесплатный
		</h1>
		<div class="pricing-buy-description">
			Переход осуществится самостоятельно, после окончания у вас оплаченного периода
		</div>

		<div class="pricing-button-group">
			<button
				class="pricing-button-connect"
				@click="closeModal()"
			>
				Понятно
			</button>
		</div>
	</div>
</template>

<script>
import {mapActions} from 'pinia';
import {useModalStore} from '../../../../stores/Modal';

export default {
	name: 'PricingToFree',

	data() {
		return {
			count: 0
		}
	},
	methods: {
		...mapActions(useModalStore, ['removeModalActive']),

		closeModal(){
			this.removeModalActive()
		},
		increment() {
			this.count++
		},
		decrement() {
			if (this.count > 0) this.count--
		}
	},
};
</script>

<style lang="scss" scoped>
 @media (min-width: 1600px) {
   .pricing-button-group {
		font-size: 16px !important;
}
		.pricing-buy-modal {
		max-width: 564px !important;
		padding: 40px !important;
		}



		.pricing-buy-title {
		font-size: 28px !important;
		}

		.pricing-buy-description {
		font-size: 16px !important;
		color: #000000;
		margin-bottom: 32px;
		}
	}

.pricing-button-group {
  margin-top: auto;
  font-weight: 500;
  font-size: 14px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.pricing-button-connect {
  width: 100%;
  padding: 13px 0;
  text-align: center;
  border-radius: 8px;
  background-color: #0c50ff;
  color: white;
}

.pricing-buy-modal {
  max-width: 464px;
  width: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  bottom: 7%;
  padding: 32px;
}



.pricing-buy-title {
  font-weight: 600;
  font-size: 22px;
  margin-bottom: 12px;
}

.pricing-buy-description {
  font-size: 14px;
  color: #000000;
  margin-bottom: 32px;
}






</style>
