<template>
	<div class="PricingRates mt-4">
		<h3 class="PricingRates-title">
			Сменить тариф:
		</h3>
		<table class="PricingRates-table">
			<tr class="PricingRates-headers">
				<th class="PricingRates-header" />
				<th
					v-for="item in items"
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
					v-for="item in items"
					:key="item.name"
					class="PricingRates-col text-center"
				>
					{{ item.users }}
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Место
				</td>
				<td
					v-for="item in items"
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
						v-for="item in items"
						:key="feature.field + item.name"
						class="PricingRates-col text-center"
					>
						{{ item[feature.field] ? '+' : '-' }}
					</td>
				</tr>
			</template>
			<tr class="PricingRates-row">
				<td
					class="PricingRates-col PricingRates-action"
					@click="showFeatures = !showFeatures"
					:colspan="items.length + 1"
				>
					...
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Оплата в месяц
				</td>
				<td
					v-for="item in items"
					:key="'monthly' + item.name"
					class="PricingRates-col PricingRates-action text-center"
					@click="$emit('update', {rate: item, period: 'monthly'})"
				>
					{{ $separateThousands(Math.round(item.monthly / rates[currency])) }} {{ currency }}
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Оплата в год
				</td>
				<td
					v-for="item in items"
					:key="'annual' + item.name"
					class="PricingRates-col PricingRates-action text-center"
					@click="$emit('update', {rate: item, period: 'annual'})"
				>
					{{ $separateThousands(Math.round(item.annual / rates[currency])) }} {{ currency }}
				</td>
			</tr>
			<tr class="PricingRates-row">
				<td class="PricingRates-col">
					Скидка при оплате за год
				</td>
				<td
					v-for="item in items"
					:key="'discount' + item.name"
					class="PricingRates-col text-center"
				>
					{{ item.discount }}%
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
			]
		}
	},
	computed: {
		...mapState(usePricingStore, ['items', 'rates'])
	},
	created(){
		// this.fetchPricing()
		setTimeout(() => { this.fetchPricing() }, 2500) // timeout for imitate net lag
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
