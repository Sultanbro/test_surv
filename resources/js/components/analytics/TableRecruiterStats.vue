<template>
	<div class="TableRecruiterStats">
		<div class="d-flex pt-4 gap-5">
			<div class="TableRecruiterStats-table">
				<JobtronTable
					responsive
					striped
					class="text-nowrap mb-3"
					:small="true"
					:bordered="true"
					:items="sorted"
					:fields="fields"
					primary-key="a"
					:key="componentKey"
					ref="table"
				>
					<template #header(name)="{field}">
						<div
							class="pointer"
							@click="setSort('name')"
						>
							{{ field.label }}
						</div>
					</template>
					<template #header(agrees)="{field}">
						<div
							class="pointer"
							@click="setSort('agrees')"
						>
							{{ field.label }}
						</div>
					</template>
					<template #header="{field}">
						<div
							class="pointer relative"
							@click.stop="openContext"
							:data-key="field.key"
						>
							{{ field.label }}
							<PopupMenu
								v-show="popupMenu[field.key]"
								v-click-outside="closeContext"
								:data-key="field.key"
							>
								<div
									class="PopupMenu-item"
									@click="setSort(field.key, 'sets')"
								>
									Наборы
								</div>
								<div
									class="PopupMenu-item"
									@click="setSort(field.key, 'calls')"
								>
									Звонки
								</div>
								<div
									class="PopupMenu-item"
									@click="setSort(field.key, 'minutes')"
								>
									Минуты
								</div>
								<div
									class="PopupMenu-item"
									@click="setSort(field.key, 'accepts')"
								>
									Согласия
								</div>
							</PopupMenu>
						</div>
					</template>
					<template #cell="data">
						<div v-html="getCellHtml(data.value)" />
					</template>
					<template #cell(totals)="data">
						<div>{{ totals[data.item.user_id] ? totals[data.item.user_id].join('/') : '' }}</div>
					</template>
					<template #cell(name)="data">
						<div class="d-flex justify-between aic pl-2 bg-white TableRecruiterStats-colTitle">
							<div>{{ data.value }}</div>
							<select
								v-if="data.value != 'ИТОГО' && ![9974,9975,5263,7372].includes(data.item.user_id)"
								class="form-control form-control-sm special-select"
								v-model="data.item.profile"
								@change="changeProfile(data.index)"
							>
								<option
									v-for="prof, index in profiles"
									:key="index"
									:value="index"
								>
									{{ prof }}
								</option>
							</select>
						</div>
					</template>
					<template #cell(agrees)="{value}">
						<div v-html="value" />
					</template>
				</JobtronTable>
			</div>


			<div class="f-200">
				<JobtronButton
					v-if="editable"
					@click="showModal = !showModal"
					class="mb-5"
				>
					Кол-во лидов
				</JobtronButton>
				<select
					class="form-control form-control-sm mb-5"
					v-model="currentDay"
				>
					<option
						v-for="day in days"
						:key="day"
						:value="day"
					>
						{{ day }}
					</option>
				</select>
				<p class="my-5 text-black fz-14">
					<b>Стандарт звонков:</b><br>
					<span class="aaa fz-12 text-red mb-2">кол-во наборов: 30 наборов</span>
					<span class="aaa fz-12 text-red mb-2">20 звонков от 10 секунд (чтобы их сделать, нужно просто делать больше наборов в час)</span>
					<span class="aaa fz-12 text-red">25 минут диалога</span>
					<span class="aaa fz-12 text-red">2 согласия</span>
				</p>
			</div>
		</div>



		<b-modal
			v-model="showModal"
			hide-footer
			title="Количество лидов"
		>
			<div
				v-for="(lead, index) in leads"
				:key="index"
				class="leads"
			>
				<div class="d-flex justify-content-between">
					<p><b> {{ lead.name }}</b></p>
					<p class="ml-2">
						{{ lead.count }}
					</p>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button'
import JobtronTable from '@ui/Table'
import PopupMenu from '@ui/PopupMenu'

export default {
	name: 'TableRecruiterStats',
	components: {
		JobtronButton,
		JobtronTable,
		PopupMenu,
	},
	props: {
		data: {
			type: Array,
			default: () => []
		},
		rates: {
			type: Object,
			default: () => ({})
		},
		leads_data: {
			default: () => [],
			type: Array,
		},
		daysInMonth: {
			default: new Date().getDate(),
			type: Number,
		},
		year: {
			default: new Date().getFullYear(),
			type: Number,
		},
		month: {
			default: Number(new Date().getMonth()) + 1,
			type: Number,
		},
		editable: {
			default: false,
			type: Boolean,
		}
	},
	data: function () {
		return {
			items: [],
			leads: [],
			days: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
			currentDay: 1,
			componentKey: 0,
			fields: [
				{
					key: 'name',
					label: 'Сотрудники',
					variant: 'title',
					tdClass: 'text-left rownumber JobtronTable-sticky',
					thClass: 'text-left JobtronTable-sticky',
				},
				{
					key: 'agrees',
					label: 'Согласия',
					variant: 'title',
					tdClass: 'text-center t-name',
					thClass: 'text-center',
				},
				{
					key: 'totals',
					label: 'Итого',
					variant: 'title',
					tdClass: 'text-center t-name',
					thClass: 'text-center',
				},
			],
			showModal: false,
			profiles: [
				'kz',
				'все удаленные',
				'вацап',
				'уведомления',
				'inhouse',
				'иностранные',
				'hh',
				'чаты',
			],
			times: {
				9: '09-10',
				10: '10-11',
				11: '11-12',
				12: '12-13',
				13: '13-14',
				14: '14-15',
				15: '15-16',
				16: '16-17',
				17: '17-18',
				18: '18-19',
			},
			expectations: [
				30,
				20,
				25,
				2
			],

			popupMenu: {
				totals: false,
			},
			sortCol: 'none',
			sortType: 'none', // sets | calls | minutes | accepts
			sortTypes: {
				sets: 0,
				calls: 1,
				minutes: 2,
				accepts: 3
			},
			compare: {
				name: (a, b) => a.name.localeCompare(b.name),
				agrees: (a, b) => (parseInt(b.agrees) || 0) - (parseInt(a.agrees) || 0),
				data: (a, b) => {
					const aData = a[this.sortCol] ? a[this.sortCol].split('/') : [0, 0, 0, 0]
					const bData = b[this.sortCol] ? b[this.sortCol].split('/') : [0, 0, 0, 0]
					const type = this.sortTypes[this.sortType]
					return (Number(bData[type]) || 0) - (Number(aData[type]) || 0)
				},
				totals: (a, b) => {
					const aData = this.totals[a.user_id] ? this.totals[a.user_id] : [0, 0, 0, 0]
					const bData = this.totals[b.user_id] ? this.totals[b.user_id] : [0, 0, 0, 0]
					const type = this.sortTypes[this.sortType]
					return bData[type] - aData[type]
				},
			}
		};
	},
	computed: {
		totals(){
			return this.data.reduce((result, row) => {
				if(row.user_id) result[row.user_id] = this.getUserTotals(row)
				else result[row.user_id] = this.getTotalTotals(row)
				return result
			}, {})
		},
		sorted(){
			if(this.sortCol === 'none') return this.items

			const toSort = this.data.slice(0, -1)
			const totals = this.data.slice(-1)
			if(['name', 'agrees', 'totals'].includes(this.sortCol)){
				toSort.sort(this.compare[this.sortCol])
			}
			else {
				toSort.sort(this.compare.data)
			}
			return [...toSort, ...totals]
		},
	},
	watch: {
		data: {
			immediate: true,
			handler () {
				this.fields[0].label = 'Сотрудники: ' + this.rates[this.currentDay];
				this.items = this.data;
				this.leads = this.leads_data;
				this.componentKey++;
			}
		},
		currentDay: {
			handler (val) {
				this.$emit('changeDay', val)
				this.fields[0].label = 'Сотрудники: ' + this.rates[val];
				this.items = this.data;
				this.leads = this.leads_data;
				this.componentKey++;
			}
		},
	},
	mounted() {
		this.setFields()
		this.currentDay = this.daysInMonth
	},
	methods: {
		setFields() {
			Object.keys(this.times).forEach(key => {
				this.popupMenu[key] = false
				this.fields.push({
					key: `${key}`,
					label: this.times[key],
					tdClass: 'day',
				});
			})
		},

		changeProfile(index) {
			if(!this.editable) return '';
			this.axios.post('/timetracking/analytics/recruting/change-profile', {
				user_id: this.items[index]['user_id'],
				profile: this.items[index]['profile'],
				day: this.currentDay,
				month: this.month,
				year: this.year,
			}).then(() => {
				this.$toast.success('Успешно!');
			}).catch(() => {
				this.$toast.error('Ошибка!');
			});
		},
		getUserTotals(row){
			return Object.keys(this.times).reduce((result, key) => {
				if(!row[key]) return result
				const items = row[key].split('/')
				return result.map((res, index) => {
					return res + (Number(items[index]) || 0)
				})
			}, [0, 0, 0, 0])
		},
		getTotalTotals(row){
			Object.keys(this.times).reduce((result, key) => {
				if(!row[key]) return result
				return result + (Number(row[key]) || 0)
			}, 0)
		},
		getCellHtml(cell){
			if(!cell) return ''
			if(~('' + cell).indexOf('/')) return cell.split('/').map((value, index) => {
				if((Number(value) || 0) >= this.expectations[index])
					return `<span class="TableRecruiterStats-complete">${value}</span>`
				return value
			}).join('/')
			return cell
		},
		openContext(event){
			// в слотах не работает реактивность, меняем состояние ручками
			const pop = event.target.querySelector('.PopupMenu')
			const thead = event.target.parentNode.parentNode
			thead.querySelectorAll('.PopupMenu').forEach(el => {
				el.style.display = 'none'
			})
			if(pop){
				pop.style.display = ''
			}
		},
		closeContext(event, el){
			// в слотах не работает реактивность, меняем состояние ручками
			el.style.display = 'none'
		},
		setSort(col, type = 'none'){
			this.sortCol = col
			this.sortType = type

			this.$refs.table.$el.querySelectorAll('.PopupMenu').forEach(el => {
				el.style.display = 'none'
			})
		},
	}
};
</script>

<style lang="scss">
.recruiter_stats {
	&.my-table .day {
		min-width: 63px;
	}
	&::-webkit-scrollbar-track {
		background: #ffffff;
	}
	// table{
	// 	max-width: 100px;
	// }
	// .day{
	// 	> div{
	// 		width: 100%;
	// 		height: 100%;
	// 		padding: 0 0.5rem;
	// 	}
	// }

	// th {
	// 	background: #1176b0!important;
	// 	color: #fff !important;
	// 	font-weight: 400;
	// 	&:first-of-type{
	// 		vertical-align: middle;
	// 		div{
	// 			padding: 0 0.5rem;
	// 		}
	// 	}
	// 	&.day{
	// 		div{
	// 			&:hover{
	// 				background-color: none;
	// 			}
	// 		}
	// 	}
	// }
}



.f-200 {
	flex: 0 0 200px;
}
.bg-white {
	background: #fff !important;
}
p.heading {
	color: black;
	font-weight: 600;
	font-family: 'Open sans', sans-serif;
}
.fz14 {
	font-size: 14px;
}
.fz-12 {
	font-size: 12px;
}
.text-red {
	color:red
}
.special-select {
	width: 90px;
	height: 20px !important;
	padding: 0;
	margin-left: 9px;
	border: none;

	// color: #fff;
	font-size: 11px;
	cursor: pointer;
	// background: #1076b0;
}

.special-select:focus,
.special-select:active {
	// background: #1076b0;
	// color: #fff;
}
.justify-between {
	justify-content: space-between;
}
.leads * {
	font-size: 12px;
	margin-bottom: 0;
}
span.aaa {
	font-size: 12px;
	margin-bottom: 12px !important;
	line-height: 15px;
	display: block;
}



.TableRecruiterStats{
	&-table{
		overflow-y: auto;
	}
	&-colTitle{
		height: 100%;
	}
	&-complete{
		color: #8bab00;
	}
	.b-table-sticky-column{
		left: 0;
	}
	.special-select{
		padding: 0 0 0 5px !important;
		margin-top: -5px;
		margin-bottom: -5px;
	}
	.JobtronTable-th,
	.JobtronTable-td{
		padding: 5px;
	}
}
</style>
