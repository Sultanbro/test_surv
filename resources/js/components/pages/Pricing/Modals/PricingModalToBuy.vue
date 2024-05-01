<template>
	<div class="pricing-buy-modal">
		<h1 class="pricing-buy-title">
			Оплата тарифа Pro
		</h1>
		<div class="pricing-buy-description">
			Выберите необходимые параметры
		</div>
		<div class="PricingRates-options-content">
			<div class="PricingRates-options-title">
				Cрок подключения
			</div>
			<div class="PricingRates-options-button-group">
				<button
					:class="{'activeOption' : activeOption === 1}"
					class="PricingRates-options-button"
					@click="handleClickOptions(1)"
				>
					<p>1 месяц</p>
					<p class="pricing-options-price">
						17 636 ₽
					</p>
				</button>
				<button
					:class="{'activeOption' : activeOption === 2}"
					class="PricingRates-options-button"
					@click="handleClickOptions(2)"
				>
					<p>3 месяца</p>
					<p class="pricing-options-price">
						52 908 ₽
					</p>
				</button>
				<button
					:class="{'activeOption' : activeOption === 3}"
					class="PricingRates-options-button"
					@click="handleClickOptions(3)"
				>
					<p>Год</p>
					<p class="pricing-options-price">
						169 549 ₽
					</p>
				</button>
			</div>
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
		<div class="pricing-buy-added-people">
			<div class="pricing-buy-added-content">
				<p class="pricing-buy-added-title">
					Добавление пользователей*
				</p>
				<div class="pricing-buy-added-button-content">
					<button
						class="pricing-buy-added-button"
						@click="removeSumPeople"
					>
						<svg
							width="18"
							height="2"
							viewBox="0 0 18 2"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M0.75 1H17.25"
								stroke="#333333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</svg>
					</button>
					<input
						:value="sumPeople"
						class="pricing-buy-added-input"
						type="number"
						@input="sumPeople = $event.target.value"
					>
					<button
						class="pricing-buy-added-button"
						@click="addedSumPeople"
					>
						<svg
							width="18"
							height="18"
							viewBox="0 0 18 18"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M0.75 9H17.25"
								stroke="#333333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
							<path
								d="M9 0.75V17.25"
								stroke="#333333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</svg>
					</button>
				</div>

				<p class="pricing-buy-added-clue">
					*Подключаются на весь период оплаты тарифа
				</p>
			</div>
			<p class="pricing-buy-added-price">
				+1 800 ₽ к оплате
			</p>
		</div>
		<p class="pricing-buy-link-promo">
			У меня есть промокод
		</p>
		<div class="pricing-buy-promo-content">
			<input
				placeholder="Введите промокод"
				class="pricing-buy-promo-input"
			>
			<button class="pricing-buy-promo-button">
				Применить
			</button>
		</div>
		<div class="pricing-buy-total-content">
			<div class="pricing-buy-total-count">
				<p class="pricing-buy-total-count-title">
					Pro на 3 месяца
				</p>
				<p class="pricing-buy-total-count-price">
					52 908 ₽
				</p>
			</div>
			<div class="pricing-buy-total">
				<p class="pricing-buy-total-title">
					Итого
				</p>
				<p class="pricing-buy-total-price">
					54 708 ₽
				</p>
			</div>
		</div>
		<div class="pricing-button-group">
			<button class="pricing-button-connect">
				Перейти к оплате
			</button>
			<button
				class="pricing-button-later"
				@click="closeModal"
			>
				Закрыть
			</button>
		</div>
	</div>
</template>

<script>
import DropdownPrice from '../DropDown.vue';
import {mapActions} from 'pinia';
import {useModalStore} from '../../../../stores/Modal';

export default {
	name: 'PricingModalToBuy',
	components: {DropdownPrice},
	data(){
		return{
			options: [{logo: '/images/price/logo.png', name: '1suol9rbcn'}, {logo: '/images/price/logo.png', name: 'ИП Самозанятость'}],
			activeOption: 2,
			sumPeople: 0
		}
	},
	methods:{
		...mapActions(useModalStore, ['removeModalActive']),
		handleClickOptions(id){
			this.activeOption = id
		},
		closeModal(){
			this.removeModalActive()
		},
		addedSumPeople(){
			this.sumPeople+=1
		},
		removeSumPeople(){
			if (this.sumPeople >0) this.sumPeople-=1
		},
	}
}
</script>

<style lang="scss" scoped>
.PricingRates{
  &-options-content{
	display: flex;
	gap: 12px;
	flex-direction: column;
	align-items: flex-start;
	margin-bottom: 24px;
  }

  &-options-title{
	font-size: 16px;
	font-weight: 400;
  }

  &-options-button-group{
	display: flex;
	background-color: #F2F2F2;
	padding: 4px;
	border-radius: 8px;
  }

  &-options-button{
	background-color: #F2F2F2;
	padding: 7px 48px ;
	border-radius: 8px;
  }

  &-options-button:focus{
	outline: none;

  }
}

.pricing-choice-tariff{
  display: flex;
  flex-direction: column;
  gap: 8px;
	max-width: 414px;
  margin-bottom: 24px;
}

.pricing-tariff-title{
  color: #737B8A;
  font-size: 16px;
}
.pricing-button-group{
  margin-top: auto;
  padding: 0 0 32px 0;
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

.pricing-buy-modal{
  display: flex;
  flex-direction: column;
	height: 100vh;
  position: relative;
  bottom: 7%;
  padding: 40px 40px 0 40px;
}

.pricing-buy-title{
font-weight: 600;
	font-size: 28px;
	margin-bottom: 12px;
}

.pricing-buy-description{
font-size: 16px;
	color: #333333;
  margin-bottom: 32px;
}

.pricing-buy-added-people{
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 24px;
}
.pricing-buy-added-content{
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pricing-buy-added-title{
  color: #737B8A;
  font-size: 16px;
}

.pricing-buy-added-button-content{
  display: flex;
	gap: 8px;
}

.pricing-buy-added-button{
background-color: #F2F2F2;
	border-radius: 8px;
	padding: 12px;
  display: flex;
  align-items: center;
	justify-content: center;
	width: 48px;
	height: 48px;
}

.pricing-buy-added-input{
padding: 14px 16px;
	border-radius: 8px;
	border: 1px solid #CDD1DB;
	max-width: 88px;
}

.pricing-buy-added-clue{
color: #737B8A;
	font-size: 14px;
}

.pricing-buy-added-price{
font-weight: 600;
	line-height: 24px;
}

.pricing-buy-link-promo{
color: #0C50FF;
	font-weight: 500;
  margin-bottom: 24px;
}

.pricing-buy-promo-content{
  display: flex;
	gap: 8px;
  margin-bottom: 24px;
}

.pricing-buy-promo-input{
border-radius: 8px;
	border: 1px solid #AFB5C0;
	padding: 14px 16px;
}

.pricing-buy-promo-button{
background-color: #DBEAFE;
	border-radius: 8px;
	padding: 13px 24px;
	color: #0C50FF;
}

.pricing-buy-total-content{
background-color: #EDEDED;
	border-radius: 8px;
	padding: 24px;
	margin-bottom: 84px;
}

.pricing-buy-total-count{
  display: flex;
	justify-content: space-between;
}

.pricing-buy-total-count-title{

}

.pricing-buy-total-count-price{

}

.pricing-buy-total{
  display: flex;
  justify-content: space-between;
}

.pricing-buy-total-title{
font-size: 28px;
	font-weight: 600;
	line-height: 36px;
}

.pricing-buy-total-price{
  font-size: 28px;
  font-weight: 600;
  line-height: 36px;
}

.activeOption{
	background-color: white;
	color: #1E40AF;
}

.pricing-options-price{
	font-size: 12px;
}
</style>
