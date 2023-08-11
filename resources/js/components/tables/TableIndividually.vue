<template>
	<div class="mb-0">
		<b-table
			v-for="(item, index) in items"
			:key="index"
			responsive
			:sticky-header="true"
			class="text-nowrap text-right my-table"
			:small="true"
			:bordered="true"
			:items="item.items"
			:fields="item.fields"
			show-empty
			empty-text="Нет данных"
		/>
	</div>
</template>

<script>
export default {
	name: 'TableIndividually',
	props: {
		data: {
			type: Object,
			default: null
		},
		month: {
			type: Object,
			default: null
		},
	},
	data() {
		return {
			hide: false,
			items: [],
			fields: [],
		};
	},
	watch: {
		// эта функция запускается при любом изменении данных
		data: function () {
			this.loadItems();
		},
	},

	created() {
		this.loadItems();
	},
	methods: {
		loadItems() {
			/* global collect */
			let accounts = this.data.accounts;
			let rows = [];
			let plan = this.data.plan;

			for (var i = 0; i < accounts.length; i++) {
				let fields = [];
				let days = [];
				let minutes = [];
				let consents = [];
				let min60 = [];
				let sum = 0;
				let sum60 = 0;
				// let variants = {};

				for (let d = 1; d <= this.month.daysInMonth; d++) {
					if (!Array.isArray(accounts[i].calls)) continue;
					days[d] = collect(accounts[i].calls)
						.where('day', d.toString())
						.where('billsec', '>=', 10)
						.count() || null;
					minutes[d] = Number(parseFloat(collect(accounts[i].calls)
						.where('day', d.toString())
						.where('billsec', '>=', 10)
						.sum('billsec') / 60
					).toFixed()) || null;

					min60[d] = collect(accounts[i].calls)
						.where('day', d.toString())
						.where('billsec', '>=', 60)
						.count() || null;

					consents[d] = collect(accounts[i].calls)
						.where('day', d.toString())
						.where('script_status_id', 2519)
						.where('correct_or_not', '!=', 2)
						.unique('call_contact_id')
						.count() || null;
				}

				sum = collect(consents.filter((item) => item != null)).sum();
				sum60 = collect(min60.filter((item) => item != null)).sum();

				fields = [{
					key: 'name',
					stickyColumn: true,
					label: accounts[i]['full_name'],
					variant: 'primary',
					class: 'text-left px-3 t-name',
				},
				{
					key: 'days',
					label: 'Дней',
					class: 'px-3',
				},
				{
					key: 'month',
					label: this.month.workDays.toString(),
					class: 'px-3',
					sortable: true,
				},
				];

				// let now = new Date();
				// let year = now.getFullYear();
				for (let i = 1; i <= this.month.daysInMonth; i++) {
					let dayName = this.$moment(`${i} ${this.month.date}`, 'D MMMM YYYY')
						.locale('en')
						.format('ddd');

					fields.push({
						key: `${i}`,
						label: `${i}`,
						sortable: true,
						class: `px-3 day ${dayName}`,
					});
				}

				let row = {
					items: [{
						name: 'План, звонков в день - мес',
						days: plan.calls_per_day,
						month: plan.calls_per_day * this.month.workDays,
						...days,
					},
					{
						name: 'План, минут в день - мес',
						days: plan.minutes_per_day,
						month: plan.minutes_per_day * this.month.workDays,
						...minutes,
					},
					{
						name: 'Исх. Вызовы более 1 мин',
						days: sum60,
						month: null,
						...min60,
						class: 'highlited',
					},
					{
						name: 'Согласие',
						days: sum,
						month: null,
						...consents,
						class: 'highlited',
					},
					],
					fields: fields,
				};

				rows.push(row);
			}

			this.items = rows;
		},
	},
};
</script>
