<template>
	<div class="mt-5 call-bases">
		<table class="table b-table table-bordered table-sm table-responsive">
			<tr>
				<th />
				<th>Сумма</th>
				<th colspan="31">
					{{ items.current_month }}
				</th>
			</tr>

			<tr>
				<th class="table-primary b-table-sticky-column text-left px-2 t-name border-blue">
					Кол-во базы
				</th>
				<th>
					<input
						v-model="items.total"
						type="number"
						class="form-control cell-input"
						@change="save()"
					>
				</th>
				<th
					v-for="day in 31"
					:key="day"
					class="bg-bluegrey"
				>
					{{ day }}
				</th>
			</tr>

			<tr>
				<td class="table-primary b-table-sticky-column text-left px-2 t-name border-blue">
					<div class="wd d-flex align-items-center">
						Кол-во кредитов
					</div>
				</td>
				<td>{{ Number(items.current_credits.sum).toFixed(0) }}</td>
				<td
					v-for="day in 31"
					:key="day"
					class="px-0 day-minute text-center"
				>
					<div>
						<input
							v-model="items.current_credits[day]"
							type="number"
							class="form-control cell-input"
							@change="save()"
						>
					</div>
				</td>
			</tr>

			<tr>
				<td class="table-primary b-table-sticky-column text-left px-2 t-name border-blue">
					<div class="wd d-flex align-items-center">
						Сумма выданных кредитов
					</div>
				</td>
				<td>{{ Number(items.current_given.sum).toFixed(0) }}</td>
				<td
					v-for="day in 31"
					:key="day"
					class="px-0 day-minute text-center"
				>
					<div>
						<input
							v-model="items.current_given[day]"
							type="number"
							class="form-control cell-input"
							@change="save()"
						>
					</div>
				</td>
			</tr>

			<tr>
				<th />
				<th />
				<th colspan="31">
					{{ items.next_month }}
				</th>
			</tr>

			<tr>
				<th class="table-primary b-table-sticky-column text-left px-2 t-name border-blue">
					Конверсия в выдачу
				</th>
				<th>{{ Number(items.conversion).toFixed(2) }}</th>
				<th
					v-for="day in 31"
					:key="day"
					class="bg-bluegrey"
				>
					{{ day }}
				</th>
			</tr>

			<tr>
				<td class="table-primary b-table-sticky-column text-left px-2 t-name border-blue">
					<div class="wd d-flex align-items-center">
						Кол-во кредитов
					</div>
				</td>
				<td>{{ Number(items.next_credits.sum).toFixed(0) }}</td>
				<td
					v-for="day in 31"
					:key="day"
					class="px-0 day-minute text-center"
				>
					<div>
						<input
							v-model="items.next_credits[day]"
							type="number"
							class="form-control cell-input"
							@change="save()"
						>
					</div>
				</td>
			</tr>

			<tr>
				<td class="table-primary b-table-sticky-column text-left px-2 t-name border-blue">
					<div class="wd d-flex align-items-center">
						Сумма выданных кредитов
					</div>
				</td>
				<td>{{ Number(items.next_given.sum).toFixed(0) }}</td>
				<td
					v-for="day in 31"
					:key="day"
					class="px-0 day-minute text-center"
				>
					<div>
						<input
							v-model="items.next_given[day]"
							type="number"
							class="form-control cell-input"
							@change="save()"
						>
					</div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script>
export default {
	name: 'CallBase',
	components: {},
	props: ['data', 'monthInfo'],
	data() {
		return {
			items: [],
		}
	},
	created() {
		this.items = this.data;
		this.calc();
	},
	methods: {
		form() {

		},

		calc() {


			let sum = 0;

			for(let i = 1;i < 31; i++) {
				if(this.items.current_credits[i] !== undefined && !isNaN(this.items.current_credits[i])) {
					sum += Number(this.items.current_credits[i]);
				}
			}

			this.items.current_credits['sum'] = sum;


			///


			sum = 0;

			for(let i = 1;i < 31; i++) {
				if(this.items.current_given[i] !== undefined && !isNaN(this.items.current_given[i])) {
					sum += Number(this.items.current_given[i]);
				}
			}

			this.items.current_given['sum'] = sum;

			//
			sum = 0;

			for(let i = 1;i < 31; i++) {
				if(this.items.next_credits[i] !== undefined && !isNaN(this.items.next_credits[i])) {
					sum += Number(this.items.next_credits[i]);
				}
			}

			this.items.next_credits['sum'] = sum;

			//

			sum = 0;

			for(let i = 1;i < 31; i++) {
				if(this.items.next_given[i] !== undefined && !isNaN(this.items.next_given[i])) {
					sum += Number(this.items.next_given[i]);
				}
			}

			this.items.next_given['sum'] = sum;


			//

			if(Number(this.items.total) !== 0) {
				this.items.conversion =(Number(this.items.next_credits['sum']) + Number(this.items.current_credits['sum'])) / Number(this.items.total) * 100;
				this.items.conversion = Number(Number(this.items.conversion).toFixed(2)) - 0.01;
				if(this.items.conversion == -0.01) this.items.conversion = 0;
			} else {
				this.items.conversion = 0;
			}

		},

		save() {

			this.calc();

			this.query()
		},

		query() {
			let loader = this.$loading.show();
			this.axios
				.post('/timetracking/analytics/save-call-base', {
					date: this.$moment(
						`${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
						'MMMM YYYY'
					).format('YYYY-MM-DD'),
					total: this.items.total,
					conversion: this.items.conversion,
					'current_credits' : this.items.current_credits,
					'next_credits': this.items.next_credits,
					'current_given' : this.items.current_given,
					'next_given': this.items.next_given,
				})
				.then(() => {
					loader.hide();
				})
				.catch(() => console.error('Error'))
		}
	}
}
</script>

<style>
.bg-bluegrey {
    background: #e9ecef;
}
</style>
