<template>
	<div class="PricingRates mt-4">
		<h3 class="PricingRates-title">
			Сменить тариф:
		</h3>
		<table class="PricingRates-table">
			<tr class="PricingRates-headers">
				<th class="PricingRates-header" />
				<th
					v-for="item in tarifs"
					:key="item.name"
					class="PricingRates-header text-center"
				>
					{{ item.name }}
				</th>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Количество пользователей
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
					Место
				</td>
				<td
					v-for="item in tarifs"
					:key="item.name"
					class="PricingRates-col text-center"
				>
					{{ item.space }}
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
						{{ item[feature.field] }}
					</td>
				</tr>
			</template>
			<tr class="PricingRates-row">
				<td
					class="PricingRates-col PricingRates-action"
					@click="showFeatures = !showFeatures"
					:colspan="items.length + 1"
				>
					Все возможности
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Оплата в месяц
				</td>
				<td
					v-for="item in tarifs"
					:key="'monthly' + item.name"
					class="PricingRates-col PricingRates-action text-center"
					@click="$emit('update', {rate: item.monthly, period: 'monthly'})"
				>
					{{ $separateThousands(Math.round(item.monthly.multiCurrencyPrice[currencyCode])) }} {{ currency }}
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Оплата в год
				</td>
				<td
					v-for="item in tarifs"
					:key="'annual' + item.name"
					class="PricingRates-col PricingRates-action text-center"
					@click="$emit('update', {rate: item.annual, period: 'annual'})"
				>
					{{ $separateThousands(Math.round(item.annual.multiCurrencyPrice[currencyCode])) }} {{ currency }}
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Скидка при оплате за год
				</td>
				<td
					v-for="item in tarifs"
					:key="'discount' + item.name"
					class="PricingRates-col text-center"
				>
					{{ item.discount }}
				</td>
			</tr>
		</table>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { usePricingStore } from '@/stores/Pricing'

export default {
	name: 'PricingRates',
	components: {},
	props: {
		currency: {
			type: String,
			default: '₽'
		}
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
			}
		}
	},
	computed: {
		...mapState(usePricingStore, ['items', 'rates']),
		tarifs(){
			return this.items.reduce((tarifs, item) => {
				if(!tarifs[item.kind]){
					tarifs[item.kind] = {}
					this.features.forEach(el => {
						tarifs[item.kind][el.field] = el.field === 'domain' && item.kind === 'free' ? '-' : '+'
						tarifs[item.kind].name = this.names[item.kind]
						tarifs[item.kind].space = this.space[item.kind]
						tarifs[item.kind].discount = this.discount[item.kind]
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
				'$': 'dollar'
			})[this.currency]
		}
	},
	created(){
		this.fetchPricing()
	},
	methods: {
		...mapActions(usePricingStore, ['fetchPricing'])
	}
}
</script>

<style lang="scss">
.PricingRates{
	&-title{
		font-size: 1.5em;
	}
	&-col{
		min-width: 25rem;
	}
	&-action{
		text-decoration: dotted underline;
		cursor: pointer;
		color: green;
		&:hover{
			color: lightgreen;
		}
	}
}
</style>
