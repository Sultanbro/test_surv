<template>
	<div
		v-if="groups"
		class="mt-2 px-3"
	>
		<div class="mb-0">
			<div class="row mb-3">
				<div class="col-3">
					<select
						v-model="currentGroup"
						class="form-control"
						@change="fetchData()"
					>
						<option
							v-for="group in groups"
							:key="group.id"
							:value="group.id"
						>
							{{ group.name }}
						</option>
					</select>
				</div>
				<div class="col-3">
					<select
						v-model="dateInfo.currentMonth"
						class="form-control"
						@change="fetchData()"
					>
						<option
							v-for="month in $moment.months()"
							:key="month"
							:value="month"
						>
							{{ month }}
						</option>
					</select>
				</div>
				<div class="col-2">
					<select
						v-model="dateInfo.currentYear"
						class="form-control"
						@change="fetchData()"
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
						class="btn btn-primary"
						@click="fetchData()"
					>
						<i class="fa fa-redo-alt" />
					</div>
				</div>
				<div class="col-2" />
			</div>

			<div v-if="hasPremission">
				<b-modal
					v-model="modalVisible"
					ok-text="Да"
					cancel-text="Нет"
					title="Вы уверены?"
					size="md"
					@ok="setTimeManually"
				>
					<template v-for="error in errors">
						<b-alert
							:key="error"
							show
							variant="danger"
						>
							{{ error }}
						</b-alert>
					</template>
					<b-form-input
						v-model="comment"
						placeholder="Комментарий"
						:required="true"
					/>
				</b-modal>
				<div class="table-container table-coming">
					<b-table
						id="comingTable"
						responsive
						:sticky-header="true"
						class="text-nowrap text-right table-custom-table-coming"
						:small="true"
						:bordered="true"
						:items="items"
						:fields="fields"
						show-empty
						empty-text="Нет данных"
					>
						<template #cell(name)="data">
							<div>
								{{ data.value }}
								<b-badge
									v-if="data.field.key == 'name' && data.value"
									pill
									variant="success"
								>
									{{ data.item.user_type }}
								</b-badge>
							</div>
						</template>

						<template #cell()="data">
							<div
								:class="{ fine: data.item.fines[data.field.key.toString()].length > 0}"
								@click="setCurrentEditingCell(data)"
							>
								<input
									class="cell-input"
									type="time"
									:value="data.value"
									:readonly="true"
									ondblclick="this.readOnly='';"
									@mouseover="$event.preventDefault()"
									@change="changeTimeInCell"
									@keyup.enter="openModal"
								>
							</div>
						</template>
					</b-table>
				</div>
			</div>
			<div v-else>
				<p>У вас нет доступа к этой группе</p>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '../composables/yearOptions'

export default {
	name: 'TableComing',
	props: {
		groups: Array,
		activeuserid: String,
	},
	data() {
		return {
			data: {},
			openSidebar: false,
			sidebarTitle: '',
			sidebarContent: {},
			items: [],
			fields: [],
			errors: [],
			comment: '',
			dayInfoText: '',
			hasPremission: false,
			dateInfo: {
				currentMonth: null,
				currentYear: new Date().getFullYear(),
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0,
			},
			dataLoaded: false,
			currentGroup: null,
			currentTime: null,
			maxScrollWidth: 0,
			currentEditingCell: null,
			scrollLeft: 0,
			modalVisible: false,
		};
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
	},
	watch: {
		// эта функция запускается при любом изменении данных
		data(newData, oldData) {
			if (oldData) {
				this.loadItems();
			}
		},
		scrollLeft(value) {
			var container = document.querySelector('.table-responsive');
			container.scrollLeft = value;
		},
		groups(){
			this.init()
		}
	},
	created() {
		if(this.groups){
			this.init()
		}
	},
	methods: {
		init(){
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ?
				this.dateInfo.currentMonth :
				this.$moment().format('MMMM');
			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM');

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [
				6,
			]); //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth(); //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays; //Колличество рабочих дней

			//Текущая группа
			this.currentGroup = this.currentGroup ?
				this.currentGroup :
				this.groups[0]['id'];

			this.fetchData();
		},
		//Установка выбранного года
		setYear() {
			this.dateInfo.currentYear = this.dateInfo.currentYear ?
				this.dateInfo.currentYear :
				this.$moment().format('YYYY');
		},
		//Установка выбранного месяца
		setMonth() {
			let year = this.dateInfo.currentYear;
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ?
				this.dateInfo.currentMonth :
				this.$moment().format('MMMM');

			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`;

			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM');
			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(
				this.dateInfo.monthEnd,
				[6]
			); //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth(); //Колличество дней в месяце
			this.dateInfo.workDays =
                this.dateInfo.daysInMonth - this.dateInfo.weekDays; //Колличество рабочих дней
		},
		//Установка заголовока таблицы
		setFields() {
			let fields = [];
			fields = [{
				key: 'name',
				stickyColumn: true,
				label: 'Имя',
				sortable: true,
				class: 'text-left px-3 t-name',
			}, ];

			let days = this.dateInfo.daysInMonth;
			for (let i = 1; i <= days; i++) {
				let dayName = this.$moment(`${i} ${this.dateInfo.date}`, 'D MMMM YYYY')
					.locale('en')
					.format('ddd');
				fields.push({
					key: `${i}`,
					label: `${i}`,
					sortable: true,
					class: `day ${dayName}`,
				});
			}
			this.fields = fields;
		},
		//Загрузка данных для таблицы
		fetchData() {
			let loader = this.$loading.show();

			this.axios
				.post('/timetracking/reports/enter-report', {
					month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
					year: this.dateInfo.currentYear,
					group_id: this.currentGroup,
				})
				.then((response) => {
					if (response.data.error && response.data.error == 'access') {
						console.error(response.data.error);
						this.hasPremission = false;
						loader.hide();
						return;
					}
					this.hasPremission = true;

					this.data = response.data;
					this.setYear();
					this.setMonth();
					this.setFields();
					this.loadItems();
					this.dataLoaded = true;
					setTimeout(() => {
						var container = document.querySelector('.table-responsive');
						this.maxScrollWidth = container.scrollWidth - container.offsetWidth;
					}, 1000);
					loader.hide();
				});
		},
		changeTimeInCell({target}) {
			this.currentTime = target.value;
		},
		setCurrentEditingCell(data) {
			this.currentTime = null;
			this.currentEditingCell = data;
		},
		openModal() {
			this.modalVisible = true;
		},
		setTimeManually() {
			let loader = this.$loading.show();

			if (this.comment.length > 0) {
				this.axios
					.post('/timetracking/reports/enter-report/setmanual', {
						month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
						year: this.dateInfo.currentYear,
						day: this.currentEditingCell.field.key,
						group_id: this.currentGroup,
						time: this.currentTime,
						comment: this.comment,
						user_id: this.currentEditingCell.item.user_id,
					})
					.then(() => {
						this.currentEditingCell = null;
						this.currentTime = null;
						this.modalVisible = false;
						this.comment = '';
						loader.hide();
					});
			} else {
				this.errors = ['Комментарий обязателен'];
			}
		},
		//Добавление загруженных данных в таблицу
		loadItems() {
			this.items = this.data;

			// if (item.selectedFines[key]) {
			//     fine = item.selectedFines[key]
			// }
		},
	},
};
</script>

<style lang="scss">
	.table-custom-table-coming {
		th, td {
			padding: 0 15px !important;
			height: 40px;
			vertical-align: middle !important;
		}

		thead {
			th, td {
				&:not(.b-table-sticky-column) {
					text-align: center;
				}
			}
		}
	}

	.table-coming {
		.fine {
			background-color: #f58c94;
			width: calc(100% + 30px);
			margin: 0 -15px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
		}
	}

	input[type="time"]::-webkit-calendar-picker-indicator {
		background: none;
		display: none;
	}
</style>
