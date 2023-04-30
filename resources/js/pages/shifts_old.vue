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
						<b-th>Рабочий график</b-th>
						<b-th>Выходные</b-th>
						<b-th class="w-100px" />
					</b-tr>
				</b-thead>
				<b-tbody>
					<b-tr
						v-for="(shift, index) in shiftsData"
						:key="index"
					>
						<b-td>{{ index + 1 }}</b-td>
						<b-td>{{ shift.name }}</b-td>
						<b-td>с {{ shift.time_beg }} по {{ shift.time_end }}</b-td>
						<b-td>
							<div
								class="weekdays"
								v-if="shift.day_off.length"
							>
								<div
									class="weekday"
									v-for="(day, idx) in shift.day_off"
									:key="idx"
								>
									{{ day }}
								</div>
							</div>
							<div v-else>
								Без выходных
							</div>
						</b-td>
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
			@close="closeSidebar"
			width="600px"
		>
			<b-form @submit.prevent="onSubmit">
				<b-form-group
					label="Название графика"
					label-cols="4"
				>
					<b-form-input v-model="form.name" />
				</b-form-group>
				<div
					id="workShedule"
					class="form-group work-schedule row"
				>
					<label
						for="workStartTime"
						class="col-sm-4 col-form-label"
					>Рабочий график</label>
					<div class="col-sm-8 form-inline">
						<input
							name="work_start_time"
							v-model="form.workStartTime"
							type="time"
							id="workStartTime"
							class="form-control mr-2 work-start-time"
						>
						<label
							for="workEndTime"
							class="col-form-label mx-3"
						>До </label>
						<input
							name="work_start_end"
							v-model="form.workEndTime"
							type="time"
							id="workEndTime"
							class="form-control mx-2 work-end-time"
						>
					</div>
				</div>
				<div
					id="weekdays"
					class="form-group row"
				>
					<label
						for="weekdays-input"
						class="col-sm-4 col-form-label"
					>Рабочие дни
						<img
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
							id="info1"
						>
					</label>
					<b-popover
						target="info1"
						triggers="hover"
						placement="bottom"
					>
						<p style="font-size: 15px">
							По умолчанию все дни - рабочие. Выбранные будут считаться выходными.
						</p>
					</b-popover>
					<div class="col-sm-8 form-inline weekdays-container">
						<input
							name="weekdays"
							type="hidden"
							v-model="form.weekdaysString"
							id="weekdays-input"
						>

						<div
							class="weekday"
							:class="{'active': weekdays[0].active === 1}"
							data-id="1"
							@click="toggleWeekDay(0, 'Пн')"
						>
							Пн
						</div>
						<div
							class="weekday"
							:class="{'active': weekdays[1].active === 1}"
							data-id="2"
							@click="toggleWeekDay(1, 'Вт')"
						>
							Вт
						</div>
						<div
							class="weekday"
							:class="{'active': weekdays[2].active === 1}"
							data-id="3"
							@click="toggleWeekDay(2, 'Ср')"
						>
							Ср
						</div>
						<div
							class="weekday"
							:class="{'active': weekdays[3].active === 1}"
							data-id="4"
							@click="toggleWeekDay(3, 'Чт')"
						>
							Чт
						</div>
						<div
							class="weekday"
							:class="{'active': weekdays[4].active === 1}"
							data-id="5"
							@click="toggleWeekDay(4, 'Пт')"
						>
							Пт
						</div>
						<div
							class="weekday"
							:class="{'active': weekdays[5].active === 1}"
							data-id="6"
							@click="toggleWeekDay(5, 'Сб')"
						>
							Сб
						</div>
						<div
							class="weekday"
							:class="{'active': weekdays[6].active === 1}"
							data-id="0"
							@click="toggleWeekDay(6, 'Вс')"
						>
							Вс
						</div>
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
export default {
	name: 'CompanyShifts',
	data() {
		return {
			modal: false,
			shiftsData: [],
			sidebarName: 'Создание новой смены',
			showSidebar: false,
			editShiftId: null,
			form: {
				name: '',
				workStartTime: null,
				workEndTime: null,
				weekdaysString: null
			},
			weekdays: [
				{day: 'Пн', active: 0},
				{day: 'Вт', active: 0},
				{day: 'Ср', active: 0},
				{day: 'Чт', active: 0},
				{day: 'Пт', active: 0},
				{day: 'Сб', active: 0},
				{day: 'Вс', active: 0}
			]
		}
	},
	mounted() {
		this.fetchData();
	},
	methods: {
		async fetchData() {
			this.shiftsData = [];
			let loader = this.$loading.show();
			const response = await this.axios.get('/timetracking/work-chart');
			if (response.data) {
				this.shiftsData = response.data.data;
				loader.hide();
			}
		},
		createNewShift() {
			this.showSidebar = true;
			this.sidebarName = 'Создание новой смены';
		},
		openModal(id) {
			this.editShiftId = id;
			this.modal = true;
		},
		editShift(shift) {
			this.showSidebar = true;
			this.editShiftId = shift.id;
			this.form.name = shift.name;
			this.form.workStartTime = shift.time_beg;
			this.form.workEndTime = shift.time_end;
			shift.day_off.forEach(day => {
				const index = this.weekdays.findIndex(d => d.day === day);
				this.weekdays[index].active = 1;
			});
			this.sidebarName = `Редактирование ${shift.name}`;
		},
		async deleteShift() {
			let loader = this.$loading.show();
			const response = await this.axios.delete('/timetracking/work-chart/' + this.editShiftId);
			console.log(response.data);
			this.modal = false;
			loader.hide();
			this.resetForm();
			this.fetchData();
			this.$toast.success('Смена удалена');
		},
		toggleWeekDay(idx, day) {
			this.$set(this.weekdays, idx, {day: day, active: this.weekdays[idx].active === 1 ? 0 : 1});
		},
		resetForm() {
			this.editShiftId = null;
			this.form.name = '';
			this.form.workStartTime = null;
			this.form.workEndTime = null;
			// delete
			this.weekdays = [
				{day: 'Пн', active: 0},
				{day: 'Вт', active: 0},
				{day: 'Ср', active: 0},
				{day: 'Чт', active: 0},
				{day: 'Пт', active: 0},
				{day: 'Сб', active: 0},
				{day: 'Вс', active: 0}
			]
		},
		async onSubmit() {
			let loader = this.$loading.show();
			const formData = new FormData();
			formData.append('name', this.form.name);
			formData.append('time_beg', this.form.workStartTime);
			formData.append('time_end', this.form.workEndTime);
			const activeWeekdays = this.weekdays.filter(d => d.active === 1);
			activeWeekdays.forEach((day, idx) => {
				formData.append(`day_off[${idx}]`, day.day);
			});
			if (this.editShiftId) {
				formData.append('_method', 'put');
				const response = await this.axios.post('/timetracking/work-chart/' + this.editShiftId, formData);
				if (response.data) {
					this.fetchData();
					this.$toast.success('Смена обновлена');
				}
			} else {
				const response = await this.axios.post('/timetracking/work-chart', formData);
				if (response.data) {
					this.shiftsData.push(response.data.data);
					this.$toast.success('Смена добавлена');
				}
			}
			loader.hide();
			this.closeSidebar();
		},
		closeSidebar() {
			this.showSidebar = false;
			this.resetForm();
		}
	}
}
</script>


<style lang="scss" scoped>
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
