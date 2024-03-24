<template>
	<div class="PricingRates mt-4">
		<template v-if="items && items.length">
			<h3 class="PricingRates-title">
				Сменить тариф:
			</h3>
			<table class="PricingRates-table">
				<tr class="PricingRates-headers">
					<th class="PricingRates-header PricingRates-header_empty" />
					<th
						v-for="item in tarifs"
						:key="item.name"
						class="PricingRates-header text-center"
					>
						{{ item.name }}
						<b-form-checkbox
							v-if="item.name === 'PRO' && !proUsed"
							v-model="proUsed"
							name="check-button"
							switch
							title="Пробный месяц"
							class="PricingRates-usePro"
						/>
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
							<ChatIconMassReaded v-if="item[feature.field] === '+'" />
							<template v-else-if="item[feature.field] !== '-'">
								{{ item[feature.field] }}
							</template>
						</td>
					</tr>
				</template>
				<tr class="PricingRates-row">
					<td
						class="PricingRates-col PricingRates-action"
						:colspan="items.length + 1"
						@click="showFeatures = !showFeatures"
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
import { useSettingsStore } from '@/stores/Settings'
import {
	ChatIconMassReaded,
} from '@icons'

export default {
	name: 'PricingRates',
	components: {
		ChatIconMassReaded,
	},
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
	async created(){
		this.fetchPricing()
		const {settings} = await this.fetchSettings('pricing_pro_used')
		this.proUsed = settings.custom_pricing_pro_used === '1'
	},
	methods: {
		...mapActions(usePricingStore, ['fetchPricing']),
		...mapActions(useSettingsStore, ['fetchSettings', 'updateSettings']),
		useProDemo(){
			this.$emit('use-pro')
			// this.updateSettings({
			// 	type: 'pricing_pro_used',
			// 	custom_pricing_pro_used: 1
			// })
		}
	}
}
</script>

<style lang="scss">
.PricingRates{
	&-header,
	&-col{
		padding: 10px;
		border: 1px solid #fff;
	}
	&-header{
		position: relative;
		color: #fff;
		background-color: #3361FF;
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
	&-row{
		&:nth-child(even){
			.PricingRates-col{
				background-color: #F7FBFF;
			}
		}
	}
	&-col{
		background-color: #EBEBF9;
	}
	&-title{
		font-size: 1.5em;
	}
	&-col{
		min-width: 25rem;
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
}
</style>
