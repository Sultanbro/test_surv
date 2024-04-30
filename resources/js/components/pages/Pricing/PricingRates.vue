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
						<button class="PricingRates-options-button">
							1 месяц
						</button>
						<button class="PricingRates-options-button">
							3 месяца
						</button>
						<button class="PricingRates-options-button">
							Год
						</button>
					</div>
				</div>
				<div class="PricingRates-options-content">
					<div class="PricingRates-options-title">
						Стоимость в
					</div>
					<div class="PricingRates-options-button-group">
						<button
							class="PricingRates-options-button"
							:class="{'active': currency === '₽'}"
							@click="changeCurrency('₽')"
						>
							₽
						</button>
						<button
							class="PricingRates-options-button"
							:class="{'active': currency === '₸'}"
							@click="changeCurrency('₸')"
						>
							₸
						</button>
						<button
							class="PricingRates-options-button"
							:class="{'active': currency === '$'}"
							@click="changeCurrency('$')"
						>
							$
						</button>
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
							<p class="PricingRates-header-price">
								{{ item.price }}
							</p>
							<p class="PricingRates-header-connection">
								{{ item.connection }}
							</p>
							<button
								class="PricingRates-header-item"
								:class="{'connection': connectedPack === item.connectionPack}"
							>
								{{ connectedPack === item.connectionPack ? 'Подключен' : 'Подключить' }}
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
						<p>{{ item.addWorker }}</p>
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
						@click="$emit('update', {rate: item.monthly, period: 'monthly'})"
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
			addWorker: {
				free:'-',
				base:'200 ₽',
				standard:'200 ₽',
				pro:'200 ₽'
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



			currencyTranslate: {
				'₽': 'rub',
				'₸': 'kzt',
				'$': 'usd',
			},
			proUsed: false,
		}
	},
	computed: {
		...mapState(usePricingStore, ['items']),
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
						tarifs[item.kind].addWorker = this.addWorker[item.kind]
						tarifs[item.kind].addCourseWorker = this.addCourseWorker[item.kind]
						tarifs[item.kind].domain = this.domain[item.kind]
						tarifs[item.kind].priceDescription = this.priceDescription[item.kind]
						tarifs[item.kind].addWorkerDescription = this.addWorkerDescription[item.kind]
					})
				}
				tarifs[item.kind][item.validity] = item
				return tarifs
			}, {})
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
		}
	},
	created(){
		this.fetchPricing()
		this.fetchDemo()
	},
	methods: {
		...mapActions(usePricingStore, ['fetchPricing']),
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

<style lang="scss">
.PricingRates{
	&-table{
		margin-top: 56px;
	}
	&-header,
	&-col{
		padding: 10px;
	border-bottom: 1px solid #CCCCCC;
	}


	&-header{
		position: relative;
	padding: 0 0 24px 0;

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
		font-size: 44px;
		font-weight: 600;
		line-height:54px;
	}
	&-options{
		max-width: 802px;
		width: 100%;
		display: flex;
		justify-content: space-between;
	}
	&-options-content{
		display: flex;
		gap: 20px;
		align-items: center;
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
		padding: 10px 31px ;
		border-radius: 8px;
	}

  &-options-button:focus{
	outline: none;

  }
	&-header-item{
		background-color: #0C50FF;
		color: white;
		padding: 5px 24px;
		font-size: 16px;
		font-weight: 500;
		border-radius: 8px;
  }

	.PricingRates-header-name{
		font-size: 16px;
		font-weight: 400;
	color: black;

  }

	.PricingRates-header-price{
		font-size: 44px;
		font-weight: 600;
		line-height: 54px;
	color: black;
	}

	.PricingRates-header-connection{
		font-size: 16px;
		font-weight: 400;
		color: #737B8A;
	}
 &-item-description{
		color: #737B8A;
 }
	&-col{
		min-width: 25rem;
	}
	&-footer{
		max-width: 661px;
		width: 100%;
		padding: 16px 0;
		font-size: 16px;
	}
	&-title-table{

		line-height: 30px;
		border-bottom: 1px solid;
	}
	&-title-table-text{
		padding: 0 0 12px 0;
		font-size: 20px;
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
			padding: 10px 0 0 50px;
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

.connection{
	background-color: #EDEDED;
	color: #8991A1;
}
</style>
