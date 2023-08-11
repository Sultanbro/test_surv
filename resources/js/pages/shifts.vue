<template>
	<div class="settings-company-shifts">
		<b-button
			variant="success"
			class="mb-2"
			@click="createNewShift"
		>
			Создать новую смену
		</b-button>
		<div
			v-if="shiftsData.length"
			class="table-container"
		>
			<b-table-simple
				id="awards-table"
				bordered
				:hover="false"
				class="table-shifts"
			>
				<b-thead>
					<b-tr>
						<b-th>№</b-th>
						<b-th>Название</b-th>
						<b-th>График</b-th>
						<b-th>Рабочие часы</b-th>
						<b-th>Время отдыха</b-th>
						<b-th>Дата создания</b-th>
						<b-th class="w-100px" />
					</b-tr>
				</b-thead>
				<b-tbody>
					<b-tr
						v-for="(shift, index) in shiftsData"
						:key="index"
					>
						<b-td>{{ index + 1 }}</b-td>
						<b-td>{{ shift.text_name }}</b-td>
						<b-td>{{ getShiftDays(shift) }}</b-td>
						<b-td>с {{ shift.start_time }} по {{ shift.end_time }}</b-td>
						<b-td>{{ shift.rest_time || 0 }} минут</b-td>
						<b-td>{{ $moment(shift.created_at).format('YYYY-MM-DD') }}</b-td>
						<b-td class="td-actions">
							<div class="d-flex mx-2">
								<b-button
									class="btn btn-primary btn-icon"
									@click="editShift(shift)"
								>
									<i class="fa fa-pen" />
								</b-button>
								<b-button
									class="btn btn-danger btn-icon"
									@click="openModal(shift.id)"
								>
									<i class="fa fa-trash" />
								</b-button>
							</div>
						</b-td>
					</b-tr>
				</b-tbody>
			</b-table-simple>
		</div>
		<div
			v-else
			class="mt-4"
		>
			<h4>Нет ни одной смены</h4>
		</div>

		<sidebar
			id="edit-shift-sidebar"
			:title="sidebarName ? sidebarName : 'Сертификат'"
			:open="showSidebar"
			width="600px"
			@close="closeSidebar"
		>
			<b-form @submit.prevent="onSubmit">
				<!-- Название -->
				<div class="form-group row">
					<label
						for="chartName"
						class="col-sm-4 col-form-label"
					>Название</label>
					<div class="col-sm-8 form-inline">
						<input
							id="chartName"
							v-model="form.name"
							class="form-control"
						>
					</div>
				</div>

				<!-- Часы -->
				<div
					id="workShedule"
					class="form-group work-schedule row"
				>
					<label
						for="workStartTime"
						class="col-sm-4 col-form-label"
					>
						Рабочий график
						<img
							v-b-popover.hover.left="'Укажите во сколько начинается и заканчивается рабочий день всей группы по умолчанию (индивидуально устанавливается в профиле сотрудника)'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</label>
					<div class="col-sm-8 form-inline">
						<input
							id="workStartTime"
							v-model="form.workStartTime"
							name="work_start_time"
							type="time"
							class="form-control mr-2 work-start-time"
						>
						<label
							for="workEndTime"
							class="col-form-label mx-3"
						>До </label>
						<input
							id="workEndTime"
							v-model="form.workEndTime"
							name="work_start_end"
							type="time"
							class="form-control mx-2 work-end-time"
						>
					</div>
				</div>

				<!-- Отдых -->
				<div class="form-group row">
					<label
						for="chartRest"
						class="col-sm-4 col-form-label"
					>
						Время отдыха
						<img
							v-b-popover.hover.left="'Укажите сколько времени в минутах положено отдыха (обед и т.п.). Это время будут не оплачиваемым'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</label>
					<div class="col-sm-8 form-inline">
						<input
							id="chartRest"
							v-model="form.restTime"
							type="number"
							class="form-control"
						>
					</div>
				</div>

				<!-- Тип -->
				<div class="form-group row">
					<label
						for="workStartTime"
						class="col-sm-4 col-form-label"
					>Смены или дни</label>
					<div class="col-sm-8">
						<b-select
							v-model="form.type"
							:options="typeOptions"
						/>
					</div>
				</div>

				<!-- Плавающие выходные -->
				<div
					v-if="form.type === 1"
					class="form-group row"
				>
					<label
						for="chartRest"
						class="col-sm-4 col-form-label"
					>
						Плавающие выходные
						<img
							v-b-popover.hover.left="'В любой из рабочих дней человек сможет отдохнуть то количество которое вы указали в поле'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</label>
					<div class="col-sm-8 form-inline">
						<input
							id="chartRest"
							v-model="form.floatingDayoffs"
							type="number"
							class="form-control"
						>
					</div>
				</div>

				<!-- Смена -->
				<b-form-group
					v-if="form.type === 2"
					label="График"
					label-cols="4"
				>
					<b-row>
						<b-col cols="5">
							<b-form-select
								v-model="form.workdays"
								:options="workdays"
							/>
						</b-col>
						<b-col
							cols="2"
							class="col-form-label"
						>
							на
						</b-col>
						<b-col cols="5">
							<b-form-select
								v-model="form.dayoffs"
								:options="dayoffs"
							/>
						</b-col>
					</b-row>
				</b-form-group>

				<!-- Дни -->
				<div
					v-if="form.type === 1"
					class="form-group row"
				>
					<label
						for="workStartTime"
						class="col-sm-4 col-form-label"
					>
						Отметьте выходные дни
						<img
							v-b-popover.hover.left="'Отметив выходные дни сотрудник не сможет начать рабочй день в эти дни'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</label>
					<div class="col-sm-8">
						<BitMaskCheckGroup
							v-model="form.usualSchedule"
							red
						/>
					</div>
				</div>

				<hr class="my-4">
				<b-button
					type="submit"
					variant="success"
				>
					Сохранить
				</b-button>
			</b-form>
		</sidebar>

		<b-modal
			v-model="modal"
			centered
			title="Удалить смену"
		>
			<p class="my-4">
				Вы уверены, что хотите удалить смену?
			</p>
			<template #modal-footer>
				<b-button
					variant="danger"
					@click="deleteShift"
				>
					Удалить
				</b-button>
				<b-button
					variant="secondary"
					@click="modal = false"
				>
					Отмена
				</b-button>
			</template>
		</b-modal>
	</div>
</template>


<script>
import BitMaskCheckGroup from '@ui/BitMaskCheckGroup'
import { getShiftDays } from '@/composables/shifts'

function flipbits(v, digits) {
	return ~v & (Math.pow(2, digits) - 1);
}

export default {
	name: 'CompanyShifts',
	components: {
		BitMaskCheckGroup,
	},
	data() {
		return {
			modal: false,
			shiftsData: [],
			workType: 1,
			sidebarName: 'Создание новой смены',
			showSidebar: false,
			editShiftId: null,
			form: {
				name: null,
				workStartTime: null,
				workEndTime: null,
				type: 1,
				restTime: 0,
				workdays: 0,
				dayoffs: 0,
				usualSchedule: 0,
				floatingDayoffs: 0
			},
			typeOptions: [
				{
					value: 1,
					text: 'Обычная рабочая неделя'
				},
				{
					value: 2,
					text: 'Смены'
				}
			]
		}
	},
	computed: {
		workdays(){
			const filled = +this.form.dayoffs || 0
			return Array(30 - filled).fill(0).map((value, index) => {
				return index + 1
			})
		},
		dayoffs(){
			const filled = +this.form.workdays || 0
			return Array(30 - filled).fill(0).map((value, index) => {
				return index + 1
			})
		},
	},
	mounted() {
		this.fetchData();
	},
	methods: {
		async fetchData() {
			this.shiftsData = [];
			let loader = this.$loading.show();
			const response = await this.axios.get('/work-chart');
			if (response.data) {
				this.shiftsData = response.data.data || [];
				this.shiftsData.forEach(shift => {
					shift.workdays = flipbits(+shift.workdays, 7)
				})
				loader.hide();
			}
		},
		createNewShift() {
			this.resetForm()
			this.showSidebar = true;
			this.sidebarName = 'Создание новой смены';
		},
		openModal(id) {
			this.editShiftId = id;
			this.modal = true;
		},
		editShift(shift) {
			const splitted = shift.name.split('-')
			const type = typeof shift.work_charts_type === 'number' ? shift.work_charts_type : shift.work_charts_type.id
			this.editShiftId = shift.id;
			this.form.name = shift.text_name;
			this.form.workStartTime = shift.start_time;
			this.form.workEndTime = shift.end_time;
			this.form.restTime = shift.rest_time || 0;
			this.form.type = type
			this.form.workdays = splitted[0]
			this.form.dayoffs = splitted[1]
			this.form.usualSchedule = shift.workdays
			this.form.floatingDayoffs = shift.floating_dayoffs
			this.sidebarName = `Редактирование ${shift.name}`;
			this.showSidebar = true;
		},
		async deleteShift() {
			let loader = this.$loading.show();
			await this.axios.delete('/work-chart/' + this.editShiftId);
			this.modal = false;
			loader.hide();
			this.resetForm();
			this.fetchData();
			this.$toast.success('Смена удалена');
		},
		resetForm() {
			this.editShiftId = null
			this.form.name = null
			this.form.workStartTime = null
			this.form.workEndTime = null
			this.form.type = 1
			this.form.restTime = 0
			this.form.workdays = 0
			this.form.dayoffs = 0
			this.form.usualSchedule = 0
			this.form.floatingDayoffs = 0
		},
		async onSubmit() {
			if(!this.form.name){
				this.$toast.error('Заполните название графика');
				return;
			}
			if(!this.form.workStartTime){
				this.$toast.error('Заполните рабочее время');
				return;
			}
			if(!this.form.workEndTime){
				this.$toast.error('Заполните рабочее время');
				return;
			}
			if(this.form.type == 2 && (!this.form.workdays || !this.form.dayoffs)){
				this.$toast.error('Выберите график');
				return
			}

			if(this.form.floatingDayoffs > 6){{
				this.$toast.error('Укажите корректное кол-во плавающих выходных');
				return
			}}

			if(this.form.type === 2){
				this.form.floatingDayoffs = 0
			}

			let loader = this.$loading.show();

			/* eslint-disable camelcase */
			const request = {
				name: this.form.name,
				start_time: this.form.workStartTime,
				end_time: this.form.workEndTime,
				rest_time: this.form.restTime,
				work_charts_type: this.form.type,
				chart_workdays: this.form.workdays,
				chart_dayoffs: this.form.dayoffs,
				floating_dayoffs: this.form.floatingDayoffs,
				usual_schedule: flipbits(this.form.usualSchedule, 7).toString(2).padStart(7, '0')
			}
			/* eslint-enable camelcase */

			const {data} = await this.axios[this.editShiftId ? 'put' : 'post'](`/work-chart/${this.editShiftId || ''}`, request)
			if(!data) {
				this.$toast.error(`Не удалось ${this.editShiftId ? 'обновить' : 'добавить'} смену`)
				loader.hide()
				return
			}

			if(this.editShiftId){
				this.fetchData();
				this.$toast.success('Смена обновлена');
			}
			else{
				this.shiftsData.push({
					...data.data,
					workdays: flipbits(+data.data.workdays, 7),
				});
				this.$toast.success('Смена добавлена');
			}

			loader.hide();
			this.closeSidebar();
		},
		closeSidebar() {
			this.showSidebar = false;
			this.resetForm();
		},
		getShiftDays,
	}
}
</script>


<style lang="scss" scoped>
	.label-style{
		color: #8DA0C1;
		margin-top: 5px;
	}
	.table-shifts {
		tbody {
			td, th {
				padding: 5px 10px !important;
				vertical-align: middle;
			}

			.td-actions {
				padding: 3px 10px !important;
			}
		}

		.weekdays {
			display: flex;
			align-items: center;
			justify-content: flex-start;

			.weekday {
				width: 25px;
				height: 25px;
				font-size: 14px;
				border-radius: 6px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				margin-right: 5px;
				background-color: green;
				color: #fff;

				&:last-child {
					margin-right: 0;
				}
			}
		}
	}

	#edit-shift-sidebar {
		form {
			padding-right: 10px;
		}

		.work-schedule {
			.form-inline {
				margin-left: -7px;
			}
		}

		.col-form-label {
			color: #8DA0C1 !important;
			font-size: 16px;
		}

		.weekdays-container {
			display: flex;
			align-items: center;

			.weekday {
				width: 35px;
				height: 35px;
				border-radius: 6px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				margin-right: 5px;
				cursor: pointer;
				color: #333;
				border: 1px solid #ddd;

				&.active {
					background-color: green;
					color: #fff;
					border: 1px solid green;
				}
			}
		}
	}
</style>
