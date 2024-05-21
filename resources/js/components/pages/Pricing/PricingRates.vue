<template>
	<div
		class="PricingRates mt-4"
		:class="[
			(selectedRate ? 'PricingRates_' + selectedRate.kind : ''),
			(selectedRate ? 'PricingRates_' + selectedRate.validity : ''),
		]"
	>
		<template v-if="items && items.length">
			<h3 class="PricingRates-title">
				Тарифы
			</h3>
			<div class="PricingRates-options">
				<div class="PricingRates-options-content">
					<div class="PricingRates-options-title">
						Период
					</div>
					<div class="PricingRates-options-button-group">
						<button
							:class="{'activeOption' : activeOption === 1}"
							class="PricingRates-options-button"
							@click="$emit('updateOption', 1)"
						>
							1 месяц
						</button>
						<button
							:class="{'activeOption' : activeOption === 3}"
							class="PricingRates-options-button"
							@click="$emit('updateOption', 3)"
						>
							3 месяца
						</button>
						<button
							:class="{'activeOption' : activeOption === 12}"
							class="PricingRates-options-button"
							@click="$emit('updateOption', 12)"
						>
							Год
							<div class="pricing-discount">
								До -20%
							</div>
						</button>
					</div>
				</div>
				<div class="PricingRates-options-content">
					<div
						style="width: 100%;"
						class="PricingRates-options-title"
					>
						Стоимость в
					</div>
					<div class=" PricingRates-options-button-currency-group">
						<button
							class=" PricingRates-options-button-currency"
							:class="{'active': currency === '₽'}"
							@click="changeCurrency('₽')"
						>
							₽
						</button>
						<button
							class="PricingRates-options-button-currency"
							:class="{'active': currency === '₸'}"
							@click="changeCurrency('₸')"
						>
							₸
						</button>
						<!--						<button-->
						<!--							class="PricingRates-options-button"-->
						<!--							:class="{'active': currency === '$'}"-->
						<!--							@click="changeCurrency('$')"-->
						<!--						>-->
						<!--							$-->
						<!--						</button>-->
					</div>
				</div>
			</div>
			<table class="PricingRates-table">
				<tr class="PricingRates-headers">
					<th class="PricingRates-header PricingRates-header_empty" />
					<th
						v-for="item in tarifs"
						:key="item.name"
						class="PricingRates-header text-center"
					>
						<div class="PricingRates-name-col">
							<p class="PricingRates-header-name">
								{{ item.name }}
							</p>
							<p
								v-if="activeOption === 1"
								class="PricingRates-header-price"
							>
								{{ $separateThousands(Math.round(item.monthly.multiCurrencyPrice[currencyCode])) }} {{ currency }}
							</p>
							<p
								v-else-if="activeOption === 12"
								class="PricingRates-header-price"
							>
								{{ $separateThousands(Math.round(item.yearly.multiCurrencyPrice[currencyCode])) }} {{ currency }}
							</p>
							<p
								v-else-if="activeOption === 3"
								class="PricingRates-header-price"
							>
								{{ $separateThousands(Math.round(item.threeMonthly.multiCurrencyPrice[currencyCode])) }} {{ currency }}
							</p>
							<p
								v-if="activeOption === 1"
								class="PricingRates-header-connection"
							>
								{{ item.connection }}
							</p>
							<p v-else-if="activeOption === 12">
								В год
							</p>
							<p v-else-if="activeOption === 3">
								В 3 месяца
							</p>
							<button
								class="PricingRates-header-item"
								:class="{
									'connectionBtn': info.tariff === null && item.name==='Бесплатный',
									'selectedBtn': info.tariff && item.name !== names[info.tariff.kind]
								}"
								@click="pricingModal(item)"
							>
								{{ info.tariff ? (names[info.tariff.kind] === item.name ? 'Продлить' : 'Перейти'): '' }}
								{{ info.tariff === null ?(item.name === 'Бесплатный' ? 'Подключен' : 'Подключить'): '' }}
							</button>
							<button
								class="activePro"
								@click="editRate"
							>
								{{ info.tariff && names[info.tariff.kind] ===item.name ? 'Управление тарифом' : '' }}
							</button>
						</div>
					</th>
				</tr>
				<tr class="PricingRates-title-table">
					<div class="PricingRates-title-table-text">
						Сравнение тарифов
					</div>
				</tr>
				<tr class="PricingRates-row">
					<td class="PricingRates-col">
						Количество сотрудников
					</td>
					<td
						v-for="item in tarifs"
						:key="item.name"
						class="PricingRates-col text-center"
					>
						{{ item.monthly.users_limit }}
					</td>
				</tr>
				<tr class="PricingRates-row">
					<td class="PricingRates-col">
						Подключение сотрудников сверх ограничений тарифа
					</td>
					<td
						v-for="item in tarifs"
						:key="item.name"
						class="PricingRates-col text-center"
					>
						<p>{{ Math.round(priceForOnePerson[currencyCode]) }} {{ currency }}</p>
						<p class="PricingRates-item-description">
							{{ item.addWorkerDescription }}
						</p>
					</td>
				</tr>
				<template v-if="showFeatures">
					<tr
						v-for="feature in features"
						:key="feature.field"
						class="PricingRates-row"
					>
						<td class="PricingRates-col">
							{{ feature.title }}
						</td>
						<td
							v-for="item in tarifs"
							:key="feature.field + item.name"
							class="PricingRates-col text-center"
						>
							<ChatIconMassReaded v-if="item[feature.field] === '+'" />
							<template v-else-if="item[feature.field] !== '-'">
								{{ item[feature.field] }}
							</template>
						</td>
					</tr>
				</template>

				<tr class="PricingRates-row">
					<td class="PricingRates-col">
						Место в пространстве
					</td>
					<td
						v-for="item in tarifs"
						:key="'monthly' + item.name"
						class="PricingRates-col  text-center"
					>
						<p>{{ item.space }}</p>
						<p class="PricingRates-item-description">
							{{ item.priceDescription }}
						</p>
					</td>
				</tr>
				<tr class="PricingRates-row">
					<td class="PricingRates-col">
						Добавление курсов для сотрудников
					</td>
					<td
						v-for="item in tarifs"
						:key="'annual' + item.name"
						class="PricingRates-col  text-center"
					>
						{{ item.addCourseWorker }}
					</td>
				</tr>
				<tr class="PricingRates-row">
					<td class="PricingRates-col">
						Индивидуальный домен
					</td>
					<td
						v-for="item in tarifs"
						:key="'discount' + item.name"
						class="PricingRates-col text-center"
					>
						{{ item.domain }}
					</td>
				</tr>
			</table>
			<div class="PricingRates-footer">
				Функционалы разделов База знаний, Новости, Обучение, Аналитика, Контроль качества, чат, структура компании, а также поддержка доступны на всех тарифах
			</div>
		</template>
		<template v-else>
			<b-skeleton-table
				:rows="6"
				:columns="5"
				:table-props="{ bordered: true, striped: true }"
			/>
		</template>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { usePricingStore } from '@/stores/Pricing'
import {
	ChatIconMassReaded,
} from '@icons'
import {useModalStore} from '../../../stores/Modal';
import {usePricingPeriodStore} from '../../../stores/PricingPeriod';

// import {
// 	fetchSettings,
// 	updateSettings,
// } from '@/stores/api.js'

export default {
	name: 'PricingRates',
	components: {
		ChatIconMassReaded,
	},
	props: {
		activeOption:{
			type: Number,
			default: 3
		},
		currency: {
			type: String,
			default: '₽'
		},
		selectedRate: {
			type: Object,
			default: null,
		},
	},
	data(){
		return {
			showFeatures: false,
			kinds: {
				null: 'Бесплатный',
				base: 'База',
				standard: 'Стандарт',
				pro: 'PRO',
			},
			features: [
				// {field: 'users', title: 'Количество пользователей'},
				// {field: 'space', title: 'Место'},
				{field: 'kb', title: 'База знаний'},
				{field: 'news', title: 'Новости'},
				{field: 'education', title: 'Обучение'},
				{field: 'analytics', title: 'Аналитика'},
				{field: 'quality_control', title: 'Контроль качества'},
				{field: 'chat', title: 'Чат'},
				{field: 'structure', title: 'Структура компании'},
				{field: 'support', title: 'Поддержка'},
				{field: 'domain', title: 'Индивидуальный домен'},
			],
			names: {
				free: 'Бесплатный',
				base: 'База',
				standard: 'Стандарт',
				pro: 'PRO',
			},
			space: {
				free: '5 Гб',
				base: '20 Гб',
				standard: '50 Гб',
				pro: '1 Тб',
			},
			discount: {
				free: '0%',
				base: '0%',
				standard: '20%',
				pro: '20%',
			},
			connectedPack: 'free',
			price: {
				free:'0 ₽',
				base:'1 485 ₽',
				standard:'4 765 ₽',
				pro:'17 636 ₽'
			},
			priceDescription: {
				free:'',
				base:'В рамках 20 Гб',
				standard:'В рамках 50 Гб',
				pro:'В рамках 1 024 Гб'
			},
			connection: {
				free:'Навсегда',
				base:'В месяц',
				standard:'В месяц',
				pro:'В месяц'
			},
			connectionPack: {
				free:'free',
				base:'base',
				standard:'standard',
				pro:'pro'
			},

			addWorkerDescription: {
				free:'',
				base:'За 1 сотр. / мес',
				standard:'За 1 сотр. / мес',
				pro:'За 1 сотр. / мес'
			},
			addCourseWorker: {
				free:'Не более 3 курсов',
				base:'Безлимит',
				standard:'Безлимит',
				pro:'Безлимит'
			},
			domain: {
				free:'-',
				base:'+',
				standard:'+',
				pro:'+'
			},

			activeMonth: 'threeMonthly',

			currencyTranslate: {
				'₽': 'rub',
				'₸': 'kzt',
				'$': 'usd',
			},
			proUsed: false,
			info: []
		}
	},
	computed: {
		...mapState(useModalStore, ['currentModalId']),
		...mapState(usePricingStore, ['items', 'priceForOnePerson']),
		...mapState(usePricingPeriodStore, ['priceStore', 'tariffStore']),
		activeTariff() {
			return this.tariffStore;
		},
		tarifs(){

			return this.items.reduce((tarifs, item) => {
				if(!tarifs[item.kind]){
					tarifs[item.kind] = {}
					this.features.forEach(el => {
						tarifs[item.kind][el.field] = el.field === 'domain' && item.kind === 'free' ? '-' : '+'
						tarifs[item.kind].name = this.names[item.kind]
						tarifs[item.kind].space = this.space[item.kind]
						tarifs[item.kind].discount = this.discount[item.kind]
						tarifs[item.kind].price = this.price[item.kind]
						tarifs[item.kind].connection = this.connection[item.kind]
						tarifs[item.kind].connectionPack = this.connectionPack[item.kind]
						tarifs[item.kind].addCourseWorker = this.addCourseWorker[item.kind]
						tarifs[item.kind].domain = this.domain[item.kind]
						tarifs[item.kind].priceDescription = this.priceDescription[item.kind]
						tarifs[item.kind].addWorkerDescription = this.addWorkerDescription[item.kind]
					})
				}
				tarifs[item.kind][item.validity] = { ...item, id: item.id };
				return tarifs
			}, {})
		},

		expiredAt(){
			return this.validity;
		},
		currencyCode(){
			return ({
				'₽': 'rub',
				'₸': 'kzt',
				'$': 'usd'
			})[this.currency]
		},
	},
	watch: {
		proUsed(){
			if(this.proUsed) this.useProDemo()
		},

	},

	created(){
		this.fetchPricing()
		this.fetchDemo()
		this.infoFetch()

	},
	methods: {
		...mapActions(useModalStore, ['setCurrentModal', 'setPrice']),
		...mapActions(usePricingStore, ['fetchPricing']),
		...mapActions(usePricingPeriodStore, ['addedPrice']),
		...mapActions(usePricingPeriodStore, ['connectedTariff', 'setTariffId']),
		infoFetch(){
			this.axios('tariff/subscriptions').then((response) => {
				this.info = response.data.data
				this.managerPlainPhone()
			})
		},

		editRate(){
			if (this.info.tariff) this.connectedTariff(this.names[this.info.tariff.kind])
			else this.connectedTariff('Бесплатный')
			this.setCurrentModal('editRate');
		},
		pricingModal( item){

			if (this.info.tariff) {
				this.connectedTariff(this.names[this.info.tariff.kind])
				this.setPrice(item.name)
				this.setTariffId(this.info.tariff)
				this.setCurrentModal('pricingToBuy')
			}
			if ( item.name === 'Бесплатный') {
				this.connectedTariff('Бесплатный')
				this.setPrice('free')
				this.setCurrentModal('pricingToFree')
			}


			this.addedPrice(item)
		},
		useProDemo(){
			this.$emit('use-pro')
			// updateSettings({
			// 	type: 'pricing_pro_used',
			// 	custom_pricing_pro_used: 1
			// })
		},
		async fetchDemo(){
			// const {settings} = await fetchSettings('pricing_pro_used')
			// this.proUsed = settings.custom_pricing_pro_used === '1'
		},
		changeCurrency(newCurrency){
			this.$emit('updateCurrency', newCurrency);
		}
	}
}
</script>

	<style lang="scss" scoped>
  @media (min-width: 1600px) {
	.pricing-discount{
		background-color: #FF5E5C;
		color: white;
		padding: 4px !important;
		font-size: 10px !important;
		font-weight: 400;
		border-radius: 8px;
		width: 49px !important;
	}
	.PricingRates {
		max-width: 1195px;
		width: 100%;
		&-table {
		margin-top: 56px !important;
		}
		&-header,
		&-col {
		padding: 10px !important;
		border-bottom: 1px solid #CCCCCC !important;
		}
	&-header {
	border-bottom:0 !important;

  }

		&-header {
		position: relative !important;
		padding: 0 0 32px 0 !important;

		&_empty {
			background-color: transparent !important;
			color: #394863 !important;
		}
		&:nth-child(2) {
			border-radius: 8px 0 0 0 !important;
		}
		&:last-of-type {
			border-radius: 0 8px 0 0 !important;
		}
		}
		&-name-col {
		display: flex !important;
		flex-direction: column !important;
		gap: 12px !important;
		}
		&-row {
		&:nth-child(even) {
			.PricingRates-col {
			border-bottom-color: #CCCCCC !important;
			}
		}
		}

		&-title {
		margin: 24px 0 24px 0 !important;
		font-size: 44px !important;
		font-weight: 600 !important;
		line-height: 54px !important;
		}
		&-options {
		max-width: 975px !important;
		width: 100% !important;
		display: flex !important;
		justify-content: space-between !important;
		}
		&-options-content {
		display: flex !important;
		gap: 20px !important;
		align-items: center !important;
		}

		&-options-title {
		font-size: 16px !important;
		font-weight: 400 !important;
		}

		&-options-button-group {
		max-width: 385px !important;
		width: 100%;
		display: flex !important;
		background-color: #f2f2f2 !important;
		padding: 4px !important;
		border-radius: 8px !important;
		}

		&-options-button {
		padding: 10px 15px !important;
		display: flex;
			font-size: 16px !important;
			justify-content: center;
		border-radius: 8px !important;
		width: 128px !important;
		}

	&-options-button-currency-group{
	max-width: 385px !important;
	width: 100%;
	display: flex !important;
	background-color: #f2f2f2 !important;
	padding: 4px !important;
	border-radius: 8px !important;
	}

		&-options-button-currency{
		padding: 10px 15px !important;
		display: flex;
		font-size: 16px !important;
		justify-content: center;
		border-radius: 8px !important;
		width: 60px !important;
		}

	&-options-button-currency:focus {
		outline: none !important;
	}
	&-footer{
		max-width: 661px !important;
		width: 100% !important;
		padding: 16px 0 !important;
		font-size: 14px !important;
	}
		&-options-button:focus {
		outline: none !important;
		}
		&-header-item {
		color: white !important;
		padding: 5px 24px !important;
		font-size: 16px !important;
		font-weight: 500 !important;
		border-radius: 8px !important;
		width: 148px !important;
		}

		.PricingRates-header-name {
		font-size: 16px !important;
		font-weight: 400 !important;
		color: black !important;
		}

		.PricingRates-header-price {
		font-size: 44px !important;
		font-weight: 600 !important;
		line-height: 54px !important;
		color: black !important;
		}

		.PricingRates-header-connection {
		font-size: 16px !important;
		font-weight: 400 !important;
		color: #737b8a !important;
		}
		&-item-description {
		color: #737b8a !important;
			font-size: 16px !important;
		}
		&-col {
		min-width: 25rem !important;
		}
		&-footer {
		max-width: 661px !important;
		width: 100% !important;
		padding: 16px 0 !important;
		font-size: 16px !important;
		}
		&-title-table {

		line-height: 30px !important;
		border-bottom: 1px solid !important;
		}
		&-title-table-text {
		padding: 0 0 12px 0 !important;
		font-size: 20px !important;
		font-weight: 600 !important;
		}
		&-action {
		text-decoration: dotted underline !important;
		cursor: pointer !important;
		color: #3361ff !important;
		&:hover {
			color: lighten(#3361ff, 10) !important;
		}
		}
		&-usePro {
		position: absolute !important;
		top: 50% !important;
		left: 50% !important;
		transform: translate(30px, -50%) !important;

		&.custom-control.custom-switch input[type="checkbox"] + .custom-control-label {
			padding: 10px 0 0 50px !important;
			margin: 0 !important;
		}
		}
		&-selected {
		background-color: darken(#3361ff, 5%) !important;
		font-weight: 700 !important;
		}
		&_free {
		.PricingRates {
			&-header:nth-child(2),
			&-col:nth-child(2) {
			border-bottom: 1px solid #3361ff !important;
			background-color: #3361ff !important;
			color: #fff !important;
			.ChatIcon-shape {
				fill: #fff !important;
			}
			}
		}
		}
		&_base {
		.PricingRates {
			&-header:nth-child(3),
			&-col:nth-child(3) {
			border-bottom: 1px solid #3361ff !important;
			background-color: #3361ff !important;
			color: #fff !important;
			.ChatIcon-shape {
				fill: #fff !important;
			}
			}
		}
		}
		&_standard {
		.PricingRates {
			&-header:nth-child(4),
			&-col:nth-child(4) {
			border-bottom: 1px solid #3361ff !important;
			background-color: #3361ff !important;
			color: #fff !important;
			.ChatIcon-shape {
				fill: #fff !important;
			}
			}
		}
		}
		&_pro {
		.PricingRates {
			&-header:nth-child(5),
			&-col:nth-child(5) {
			border-bottom: 1px solid #3361ff !important;
			background-color: #3361ff !important;
			color: #fff !important;
			.ChatIcon-shape {
				fill: #fff !important;
			}
			}
		}
		}
		&_monthly {}
		&_annual {}
	}

	.activePro {
	font-size: 16px !important;
	position: absolute !important;
		top: 85% !important;
		left: 13% !important;
		color: #0c50ff !important;
		margin-top: 5px !important;
	}


	}

  .PricingRates{
		max-width: 900px;
		width: 100%;

	&-options-button-currency-group{
		max-width: 385px ;
		width: 100%;
		display: flex ;
		background-color: #f2f2f2 ;
		padding: 4px ;
		border-radius: 8px ;
	}


	&-options-button-currency{
		background-color: #f2f2f2 ;
		padding: 10px 15px ;
		display: flex;
		font-size: 16px ;
		justify-content: center;
		border-radius: 8px ;
		width: 60px ;
	}
	&-options-button-currency:hover{
	background-color: #c2bfbf;

	}
	&-options-button-currency:focus {
		outline: none ;
	}
		&-table{
			margin-top: 56px;
		max-width: 900px;
		width: 100%;
		}
		&-header,
		&-col{

			padding: 10px;
		border-bottom: 1px solid #CCCCCC;
		}
	&-header {
		border-bottom: 0!important;

	}

		&-header{
			position: relative;
		padding: 0 0 12px 0;

			&_empty{
				background-color: transparent;
				color: #394863;
			}
			&:nth-child(2){
				border-radius: 8px 0 0 0;
			}
			&:last-of-type{
				border-radius: 0 8px 0 0;
			}
		}
		&-name-col{
			display: flex;
			align-items: center;
			flex-direction: column;
			gap: 12px;
		}
		&-row{
			&:nth-child(even){
				.PricingRates-col{
					border-bottom-color: #CCCCCC;
				}
			}
		}

		&-title{
			color: #333333;
			margin: 12px 0 12px 0;
			font-size: 30px;
			font-weight: 600;
			line-height:44px;
		}
		&-options{
			max-width: 650px;
			width: 100%;
			display: flex;
			justify-content: space-between;
		}
		&-options-content{
			display: flex;
			gap: 10px;
			align-items: center;
		}

		&-options-title{
			font-size: 14px;
			font-weight: 400;
		}

		&-options-button-group{
		max-width: 340px;
		width: 100%;
		justify-content: center;
		align-items: center;
		display: flex;
		background-color: #F2F2F2;
		padding: 4px;
		border-radius: 8px;

		}

		&-options-button{
		background-color: #F2F2F2;
		padding: 10px 20px;
		display: flex;
		border-radius: 8px;
		font-size: 14px;
		gap: 5px;
		}
	&-options-button:hover{
	background-color: #c2bfbf;

  }
		&-options-button:focus{
		outline: none;

		}
		&-header-item{
			background-color: #0C50FF;
			color: white;
			padding: 3px 12px;
			font-size: 14px;
			font-weight: 500;
			border-radius: 8px;
			width: 105px;

	}
	&-header-item:hover{
		background-color: #093fcb;
		color: white !important;
	}
		.PricingRates-header-name{
			font-size: 14px;
			font-weight: 400;
		color: black;

		}

		.PricingRates-header-price{
			font-size: 24px;
			font-weight: 600;
			line-height: 32px;
		color: black;
		}

		.PricingRates-header-connection{
			font-size: 14px;
			font-weight: 400;
			color: #737B8A;
		}
		&-item-description{
			color: #737B8A;
			font-size: 14px;
		}
		&-col{
			min-width: 15rem;
		}
		&-footer{
			max-width: 561px;
			width: 100%;
			padding: 16px 0;
			font-size: 14px;
		}
		&-title-table{

			line-height: 30px;
			border-bottom: 1px solid;
		}
		&-title-table-text{
			padding: 0 0 12px 0;
			font-size: 18px;
			font-weight: 600;
		}
		&-action{
			text-decoration: dotted underline;
			cursor: pointer;
			color: #3361FF;
			&:hover{
				color: lighten(#3361FF, 10);
			}
		}
		&-usePro{
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(30px, -50%);

			&.custom-control.custom-switch input[type="checkbox"] + .custom-control-label{
				padding: 5px 0 0 25px;
				margin: 0;
			}
		}
		&-selected{
			background-color: darken(#3361FF, 5%) !important;
			font-weight: 700;
		}
		&_free{
			.PricingRates{
				&-header:nth-child(2),
				&-col:nth-child(2){
					border-bottom: 1px solid #3361FF;
					background-color: #3361FF;
					color: #fff;
					.ChatIcon-shape{
						fill: #fff;
					}
				}
			}
		}
		&_base{
			.PricingRates{
				&-header:nth-child(3),
				&-col:nth-child(3){
					border-bottom: 1px solid #3361FF;
					background-color: #3361FF;
					color: #fff;
					.ChatIcon-shape{
						fill: #fff;
					}
				}
			}
		}
		&_standard{
			.PricingRates{
				&-header:nth-child(4),
				&-col:nth-child(4){
					border-bottom: 1px solid #3361FF;
					background-color: #3361FF;
					color: #fff;
					.ChatIcon-shape{
						fill: #fff;
					}
				}
			}
		}
		&_pro{
			.PricingRates{
				&-header:nth-child(5),
				&-col:nth-child(5){
					border-bottom: 1px solid #3361FF;
					background-color: #3361FF;
					color: #fff;
					.ChatIcon-shape{
						fill: #fff;
					}
				}
			}
		}
		&_monthly{}
		&_annual{}
	}

	.active{
		background-color: white;
		color: #1E40AF;
	}

	.connectionBtn{
		background-color: #EDEDED;
		color: #8991A1;
	}

	.selectedBtn{
		background-color: #DBEAFE;
		color: #0C50FF !important;
	}

	.activePro {
		font-size: 12px;
		position: absolute;
		top: 99%;
		left: 10%;
		color: #0C50FF;
		margin-top: 10px;
	}

  .activeOption {
	background-color: white;
	color: #1e40af ;
  }
  .activeOption:hover {
	background-color: #c5c4c4;
  }

	.pricing-discount{
		background-color: #FF5E5C;
		color: white;
		padding: 4px;
		font-size: 10px;
		font-weight: 400;
		border-radius: 8px;
		width: 49px;
		display: flex;
	}

	.options-button-discount{
	display: flex;
		gap: 6px;
	}
	</style>
