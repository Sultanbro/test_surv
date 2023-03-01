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
						<b-th>График</b-th>
						<b-th>Рабочие часы</b-th>
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
						<b-td>{{ shift.name }}</b-td>
						<b-td>с {{ shift.start_time }} по {{ shift.end_time }}</b-td>
						<b-td>{{ $moment(shift.created_at).format('YYYY-MM-DD') }}</b-td>
						<b-td class="td-actions">
							<div class="d-flex mx-2">
								<b-button
									class="btn btn-primary btn-icon"
									@click="editShift(shift)"
								>
									<i class="fa fa-edit" />
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
					label="График"
					label-cols="4"
				>
					<b-form-select v-model="form.name">
						<b-form-select-option
							disabled
							value="null"
						>
							Выберите график работы
						</b-form-select-option>
						<template v-for="chart in workChartsList">
							<b-form-select-option
								:key="chart"
								:value="chart"
							>
								График {{ chart }}
							</b-form-select-option>
						</template>
					</b-form-select>
				</b-form-group>
				<div
					id="workShedule"
					class="form-group work-schedule row"
				>
					<label
						for="workStartTime"
						class="col-sm-4 col-form-label"
					>Рабочие часы</label>
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
			workType: 1,
			sidebarName: 'Создание новой смены',
			showSidebar: false,
			editShiftId: null,
			form: {
				name: null,
				workStartTime: null,
				workEndTime: null,
			},
			workChartsList: ['1-1', '2-2', '3-3', '5-2', '6-1']
		}
	},
	mounted() {
		this.fetchData();
	},
	methods: {
		toggleWeekDay(idx, day) {
			this.$set(this.weekdays, idx, {day: day, active: this.weekdays[idx].active === 1 ? 0 : 1});
		},
		async fetchData() {
			this.shiftsData = [];
			let loader = this.$loading.show();
			const response = await this.axios.get('/work-chart');
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
			this.form.workStartTime = shift.start_time;
			this.form.workEndTime = shift.end_time;
			this.sidebarName = `Редактирование ${shift.name}`;
		},
		async deleteShift() {
			let loader = this.$loading.show();
			const response = await this.axios.delete('/work-chart/' + this.editShiftId);
			console.log(response.data);
			this.modal = false;
			loader.hide();
			this.resetForm();
			this.fetchData();
			this.$toast.success('Смена удалена');
		},
		resetForm() {
			this.editShiftId = null;
			this.form.name = null;
			this.form.workStartTime = null;
			this.form.workEndTime = null;
		},
		async onSubmit() {
			if(!this.form.name){
				this.$toast.error('Выберите график');
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
			let loader = this.$loading.show();
			const formData = new FormData();
			formData.append('name', this.form.name);
			formData.append('start_time', this.form.workStartTime);
			formData.append('end_time', this.form.workEndTime);
			if (this.editShiftId) {
				formData.append('_method', 'put');
				const response = await this.axios.post('/work-chart/' + this.editShiftId, formData);
				if (response.data) {
					this.fetchData();
					this.$toast.success('Смена обновлена');
				}
			} else {
				const response = await this.axios.post('/work-chart', formData);
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
