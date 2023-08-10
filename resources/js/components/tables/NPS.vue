<template>
	<div class="mb-0">
		<div
			v-if="show_header"
			class="row mb-3 mt-3"
		>
			<div class="col-2">
				<select
					v-model="currentYear"
					class="form-control"
					@change="fetchData"
				>
					<option
						v-for="year in years"
						:key="year"
						:value="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-1">
				<div
					class="btn btn-primary rounded"
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div class="col-6" />
			<div class="col-3" />
		</div>


		<div class="table-responsive table-container mt-4">
			<table
				:key="ukey"
				class="table custom-table-nps table-bordered"
			>
				<thead>
					<tr>
						<template v-for="(field, key) in fields">
							<th
								:key="key"
								class="NPS-th"
								:class="field.klass"
								@click="doSort(field.key)"
							>
								{{ field.name }}
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in sorted"
						:key="index"
					>
						<template v-for="(field, key) in fields">
							<td
								:key="key"
								:class="field.klass"
							>
								<div
									class="inner"
									:class="{'inner-text-top': index > fields.length - 4}"
								>
									<div>{{ item[field.key] }}</div>
									<div class="inner-text">
										<b v-if="item.texts[field.key] !== undefined">Оценки ({{ item.grades[field.key] }})</b>
										<div class="d-flex">
											<div class="w-50">
												<b>Плюсы ({{ item.texts[field.key] !== undefined ? item.texts[field.key].length : 0 }})</b>
												<div
													v-for="(text, plusIndex) in item.texts[field.key]"
													:key="plusIndex"
												>
													<b>{{ plusIndex + 1 }}:</b> {{ text }}
												</div>
											</div>

											<div class="w-50">
												<b>Минусы ({{ item.minuses[field.key] !== undefined ? item.minuses[field.key].length : 0 }})</b>
												<div
													v-for="(text, minusIndex) in item.minuses[field.key]"
													:key="minusIndex"
												>
													<b>{{ minusIndex + 1 }}:</b> {{ text }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="empty-space" />
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '@/composables/yearOptions'

import { fetchTopNPS } from '@/stores/api/top.js'

export default {
	name: 'NPS',
	props: {
		activeuserid: {
			type: Number,
			default: 0,
		},
		show_header: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			users: [],
			fields: [],
			currentYear: new Date().getFullYear(),
			monthInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0
			},
			ukey: 1,

			sortCol: '',
			sortOrder: 'asc',
			sortFn: {
				asc: {
					str: (a, b) => (a[this.sortCol] || '').localeCompare(b[this.sortCol] || ''),
					data: (a, b) => (Number(a[this.sortCol]) || 0) - (Number(b[this.sortCol]) || 0),
				},
				desc: {
					str: (b, a) => (a[this.sortCol] || '').localeCompare(b[this.sortCol] || ''),
					data: (b, a) => (Number(a[this.sortCol]) || 0) - (Number(b[this.sortCol]) || 0),
				}
			}
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		sorted(){
			if(!this.sortCol) return this.users
			const toSort = this.users.slice()
			if(['group_id', 'position', 'name'].includes(this.sortCol)){
				toSort.sort(this.sortFn[this.sortOrder].str)
			}
			else{
				toSort.sort(this.sortFn[this.sortOrder].data)
			}
			return toSort
		}
	},
	created() {
		this.setMonth();
		this.setMonthsTableFields();
		this.fetchData();
	},
	methods: {
		setMonth() {
			this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
			this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
			this.monthInfo.daysInMonth = new Date(2021, this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
		},

		async fetchData() {
			const loader = this.$loading.show();

			try {
				const {users} = await fetchTopNPS({
					month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
					year: this.currentYear,
				})
				this.setMonth()
				this.users = this.addAvg(users)
				this.ukey++;
			}
			catch (error) {
				alert(error)
			}

			loader.hide()
		},

		addAvg(users){
			users.forEach(user => {
				let count = 0
				let sum = 0
				for(let i = 1, l = 12; i <= l; ++i){
					if(user[i]){
						++count
						sum += (Number(user[i]) || 0)
					}
				}
				user.avg = (sum / count) || 0
				user.avg = user.avg.toFixed(user.avg === parseInt(user.avg) ? 0 : 1)
			})
			return users
		},

		setMonthsTableFields() {
			let fieldsArray = []
			let order = 1;

			fieldsArray.push({
				key: 'group_id',
				name: 'Отдел',
				order: order++,
				klass: ' text-left bg-blue w-200'
			})

			fieldsArray.push({
				key: 'position',
				name: 'Должность',
				order: order++,
				klass: ' text-left bg-blue'
			})

			fieldsArray.push({
				key: 'name',
				name: 'ФИО',
				order: order++,
				klass: ' text-left bg-blue w-200'
			})

			fieldsArray.push({
				key: 'avg',
				name: 'Среднее',
				order: order++,
				klass: 'text-center px-1'
			})

			for(let i = 1; i <= 12; i++) {
				if(i.length == 1) i = '0' + i

				fieldsArray.push({
					key: i,
					name: this.$moment(this.currentYear + '-' + i + '-01').format('MMMM'),
					order: order++,
					klass: 'text-center px-1 month'
				})
			}

			this.fields = fieldsArray
		},

		doSort(col) {
			if(this.sortCol === col){
				this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
				return
			}

			this.sortOrder = 'asc'
			this.sortCol = col
		}
	}
};
</script>

<style lang="scss" scoped>
.custom-table-nps{
	tbody{
		th,td{
			padding: 0!important;
			.inner{
				height: 40px;
				padding: 0 10px;
				display: flex;
				align-items: center;
				justify-content: center;
			}
		}
	}
}


div.inner-text {
	display: none;
}

.inner{
	&:not(.inner-text-top){
		.inner-text{
			top: 40px;
		}
	}
	&.inner-text-top{
		.inner-text{
			bottom: 40px;
		}
	}
}

.month:hover {
	div.inner {
		position: relative;
		background: #eee;
		cursor: pointer;
	}

	div.inner-text {
		position: absolute;
		right: 0;
		padding: 15px;
		width: 400px;
		max-width: 400px;
		max-height: 200px;
		text-align: left;
		font-size: 13px;
		background: #fff7c8;
		border-radius: 5px;
		cursor: pointer;
		display: block;
		border: 1px solid #ddd;
		overflow-y:auto;
	}
}

.bg-blue {
	background: aliceblue;
}
td.month {
	vertical-align: middle;
}
.w-200 {
	min-width: 200px;
}
.w-50 {
	width: 50%;
}
</style>

<!-- 360 no scope MLG -->
<style lang="scss">
.NPS{
	&-th{
		cursor: pointer;
		user-select: none;
	}
}
</style>
