<template>
	<div class="TableRecruiterStats">
		<div class="d-flex pt-5 gap-5">
			<div class="TableRecruiterStats-table">
				<JobtronTable
					responsive
					striped
					class="text-nowrap mb-3"
					:small="true"
					:bordered="true"
					:items="items"
					:fields="fields"
					primary-key="a"
					:key="componentKey"
				>
					<template #cell()="data">
						<div>{{ data.value }}</div>
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
								<option value="0">
									кз
								</option>
								<option value="1">
									все удаленные
								</option>
								<option value="2">
									вацап
								</option>
								<option value="3">
									уведомления
								</option>
								<option value="4">
									inhouse
								</option>
								<option value="5">
									иностранные
								</option>
								<option value="6">
									hh
								</option>
								<option value="7">
									чаты
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
					<span class="aaa fz-12 text-red mb-2">20 звонков от 10 секунд (чтобы их сделать, нужно просто делать больше наборов в час)</span>
					<span class="aaa fz-12 text-red">30 минут диалога</span>
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

export default {
	name: 'TableRecruiterStats',
	components: {
		JobtronButton,
		JobtronTable,
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
			],
			showModal: false
		};
	},
	watch: {
		data: {
			immediate: true,
			handler () {
				this.fields[0].label = 'Сотрудники: ' + this.rates[this.currentDay];
				this.items  = this.data;
				this.leads  = this.leads_data;
				this.componentKey++;
			}
		},
		currentDay: {
			handler (val) {
				this.$emit('changeDay', val)
				this.fields[0].label = 'Сотрудники: ' + this.rates[val];
				this.items  = this.data;
				this.leads  = this.leads_data;
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
			let times = {
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
			};

			Object.keys(times).forEach(key => {
				this.fields.push({
					key: `${key}`,
					label: times[key],
					tdClass: 'day'
				});
			})
		},

		changeProfile(index) {
			if(!this.editable) return '';
			this.axios.post('/timetracking/analytics/recruting/change-profile', {
				user_id: this.items[this.currentDay][index]['user_id'],
				profile: this.items[this.currentDay][index]['profile'],
				day: this.currentDay,
				month: this.month,
				year: this.year,
			}).then(() => {
				this.$toast.success('Успешно!');
			}).catch(() => {
				this.$toast.error('Ошибка!');
				//alert(error)
			});
		}

	}
};
</script>

<style lang="scss">
.TableRecruiterStats{
	&-table{
		overflow-y: auto;
	}
}
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
.TableRecruiterStats{
	&-colTitle{
		height: 100%;
	}
	.b-table-sticky-column{
		left: 0;
	}
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
	width: 45px;
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
</style>
