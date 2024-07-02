<template>
	<div class="time-start-panel">
		<div class="listbtn">
			<div
				v-if="program == 2"
				class="lstbtn"
			>
				<div v-if="user_type == 'office'">
					<template v-for="(btn, index) in btnlist_office">
						<button
							:key="index"
							class="btn btn-secondary"
							:disabled="activebtn == btn.name"
							:class="{ activiti: activebtn == btn.name }"
							@click="btnclickk(btn.name)"
						>
							<i
								v-if="activebtn == btn.name"
								class="fa fa-check-square-o"
								aria-hidden="true"
							/>
							{{ btn.name }}
						</button>
					</template>
				</div>
				<div v-else>
					<template v-for="(btn, index) in btnlist">
						<button
							:key="index"
							class="btn btn-secondary"
							:disabled="activebtn == btn.name"
							:class="{ activiti: activebtn == btn.name }"
							@click="btnclickk(btn.name)"
						>
							<i
								v-if="activebtn == btn.name"
								class="fa fa-check-square-o"
								aria-hidden="true"
							/>
							{{ btn.name }}
						</button>
					</template>
				</div>
			</div>
		</div>





		<transition name="fade">
			<sidebar
				v-show="ordersShow"
				title="Заказы руководителей"
				:open="ordersShow"
				width="40%"
				@close="ordersShow = false"
			>
				<div class="mt-2 p-2">
					<div class="form-group my-table">
						<table class="table table-striped table-tt border">
							<tr>
								<th>Название группы</th>
								<th>Требуется</th>
								<th>Наняты</th>
							</tr>
							<tr
								v-for="(order, index) in orders"
								:key="index"
							>
								<td class="text-left t-name bgz table-title">
									{{ order.group }}
								</td>
								<td class="text-left table-title">
									{{ order.required }}
								</td>
								<td class="text-left table-title">
									{{ order.fact }}
								</td>
							</tr>
						</table>
					</div>
					<div
						v-if="position_id == 45"
						class=" mt-2 form-group row"
					>
						<label
							for="firstName"
							class="col-sm-4 col-form-label font-weight-bold"
						>Выберите отдел</label>
						<div class="col-sm-12 relative mb-2">
							<select
								v-model="group_id"
								type="number"
								required="required"
								class="form-control"
							>
								<option
									v-for="group in groups"
									:key="group.id"
									:value="group.id"
								>
									{{
										group.name
									}}
								</option>
							</select>
						</div>
						<label
							for="firstName"
							class="col-sm-4 col-form-label font-weight-bold"
						>Количество</label>
						<div class="col-sm-12 relative mb-2">
							<input
								v-model="quantity"
								type="number"
								required="required"
								class="form-control"
							>
						</div>
						<div class="col-sm-12">
							<p style="font-size: 12px;font-weight: 600">
								Пожалуйста, пишите сколько сотрудников нужно с учетом
								предоставленных, так как факт не меняется от введенного
								значения.
							</p>
							<button
								class="btn btn-primary"
								@click="orderGroup"
							>
								Сохранить
							</button>
						</div>
					</div>
				</div>
			</sidebar>
		</transition>

		<b-modal
			v-model="showCorpBookPage"
			title="Н"
			size="xl"
			class="modalle"
			hide-footer
			hide-header
			no-close-on-backdrop
		>
			<div class="corpbook">
				<div class="inner">
					<h5 class="text-center aet mb-3">
						Ознакомьтесь с одной из страниц Вашей корпоративной книги
					</h5>
					<h3 class="text-center">
						{{ corp_book_page.title }}
					</h3>

					<!-- eslint-disable-next-line -->
					<div v-html="corp_book_page.text" />

					<button
						id="readCorpBook"
						class="btn btn-primary rounded m-auto"
						disabled
						@click="hideBook"
					>
						<span class="text">Я прочитал</span>
						<span class="timer" />
					</button>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import moment from 'moment';

export default {
	name: 'TimeTracking',

	filters: {
		splitNumber(value) {
			return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
		},
	},
	props: {
		activeuserid: {
			type: Number,
			default: 0
		},
		user_type: {
			type: String,
			default: ''
		},
		program: {
			type: Number,
			default: 0
		},
		position_id: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			openSidebar: false,
			ordersShow: false,
			showCorpBookPage: false,
			corp_book_page: {
				title: '',
				text: '',
			},
			total_earned: '',
			groups: [],
			orders: [],
			group_id: 0,
			quantity: 0,
			btnlist: [
				{
					name: 'Начать день',
					type: 'success',
				},
				{
					name: 'Завершить день',
					type: 'warning',
				},
			],
			btnlist_office: [
				{
					name: 'Завершить день',
					type: 'warning',
				},
			],
			activebtn: 'Завершить день',
			zarplata: 0,
			kpi: 0,
			kpiText: 'KPI: 0 ',
			loader: null,
			bonus: 0,
			monthInfo: {
				currentMonth: null,
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0,
			},
		};
	},
	created() {
		this.axios
			.post('/timetracking/status', {})
			.then((response) => {
				this.activebtn = 'Завершить день';
				this.setButton(response.data.status);

				if(response.data.status == 'started' && response.data.corp_book.has) {

					this.corp_book_page = response.data.corp_book.page

					this.showCorpBookPage = this.corp_book_page != null;
					this.bookCounter();
				}

				this.zarplata = response.data.zarplata;

				this.groups = response.data.groupsall;
				this.total_earned = response.data.total_earned;
				this.orders = response.data.orders;
				this.bonus = response.data.bonus;
			})
			.catch((error) => {
				console.error(error);
			});

		this.setMonth();
	},

	mounted() {},

	methods: {

		bookCounter() {
			/* global $ */
			let seconds = 60;
			let interv = setInterval(() => {
				seconds--;
				$('#readCorpBook .timer').text(seconds);
				if(seconds == 0) {
					$('#readCorpBook .timer').text('');
					clearInterval(interv);
				}
			}, 1000);

			setTimeout(() => {
				$('#readCorpBook').prop('disabled', false);
			}, seconds * 1000);
		},
		hideBook() {

			this.axios
				.post('/corp_book/set-read/', {})
				.then(() => {
					this.showCorpBookPage = false;
				})
				.catch((error) => {
					console.error(error);
				});
		},

		setMonth() {
			this.monthInfo.currentMonth = this.$moment().format('MMMM');
			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM');
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(
				currentMonth.startOf('month').toString(),
				currentMonth.endOf('month').toString(),
				[6]
			); //Колличество выходных
			this.monthInfo.daysInMonth = new Date(
				2020,
				this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				0
			).getDate(); //Колличество дней в месяце
			this.monthInfo.workDays =
        this.monthInfo.daysInMonth - this.monthInfo.weekDays; //Колличество рабочих дней
		},

		orderGroup() {
			let loader = this.$loading.show();
			this.axios
				.post('/order-persons-to-group', {
					group_id: this.group_id,
					required: this.quantity,
				})
				.then((response) => {
					this.orders = response.data;
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					console.error(error);
				});
		},

		btnclickk(names) {
			let now = moment();

			if (names == 'Завершить день') {
				if (
					confirm(
						'Уверены что хотите завершить день?\nПосле нажатия данной кнопки, оплата не будет начисляться'
					)
				) {
					this.fetchstop(now.format('HH:mm:ss'));
				}
			} else {
				this.fetch(now.format('HH:mm:ss'));
			}
		},
		fetch(times) {
			this.axios
				.post('/timetracking/starttracking', {
					start: times,
				})
				.then((response) => {
					if (response.data.error) {
						this.showMessage(response.data.error.message);
						return;
					}

					if(response.data.status == 'started' && response.data.corp_book.has) {

						this.corp_book_page = response.data.corp_book.page
						this.showCorpBookPage = this.corp_book_page != null;
						this.bookCounter();
					}
					this.setButton(response.data.status);
				})
				.catch((error) => {
					console.error(error);
				});
		},
		fetchstop(times) {
			this.axios
				.post('/timetracking/starttracking', {
					stop: times,
				})
				.then((response) => {
					if (response.data.error) {
						this.showMessage(response.data.error.message);
					}

					this.setButton(response.data.status);
				})
				.catch((error) => {
					console.error(error);
				});
		},
		setButton(status) {
			if (status == 'started') {
				this.activebtn = 'Начать день';
			} else if (status == 'stopped') {
				this.activebtn = 'Завершить день';
			}
		},
		showMessage(message) {
			this.$notify({
				group: 'foo',
				title: 'Сообщение',
				text: message,
				position: 'top left'
			});
		},
	},
};
</script>

<style lang="scss" scoped>
@media screen and (max-width: 1026px) {
   .listbtn button {
      font-size: 10px;
    }
}


.listbtn {
  display: flex;
  align-items: center;
  justify-content: flex-end;

  .lstbtn {
    flex: 1;
    display: flex;
  }

  button {
    border-radius: 20px;
    margin-right: 10px;
    padding: 6px 30px;


  }

  button:nth-child(1) {
    background-color: green;
    border-color: green;
    color: white;
  }

  button:nth-child(2) {
    background-color: red;
    border-color: red;
    color: white;
  }

  button:disabled {
    cursor: not-allowed;
  }
}

.zarplata {
  align-items: center;

  p {
    margin-bottom: 0;
    font-size: 0.8rem;
    line-height: 8px;
    text-align: left;
  }
  img {
    width: 35px;
    margin-right: 10px;
  }
}

.activiti {
  background-color: #6c757d !important;
  border-color: #6c757d !important;
}
</style>
<style lang="scss">
.zarplata {
  .btn {
    margin-bottom: 0;
    font-size: 0.8rem;
    line-height: 16px;
    text-align: left;
    background: transparent;
    border: 0;
    padding: 0;
    position: relative;
    top: -1px;
    color: #1890ff;
    &:after {
      display: none;
    }
    &:focus,
    &:active,
    &:hover {
      background: transparent !important;
      color: #1890ff !important;
    }
  }
  .dropdown-menu {
    top: 100% !important;
    margin-top: 0 !important;
    padding: 0 !important;

    .dropdown-item {
      font-size: 0.8rem;
    }
  }
}
.linksa {
  color: #1890ff;
      display: block;
    line-height: 1.3em;
}
.btn-orders {
  border-radius: 50%;
  margin-left: 15px;
  background: #202226;
  color: #fff !important;
  padding: 4px 8px;
  &:hover {
    background: #ffc400;
  }
}
.table-tt td,
.table-tt th {
  padding: 0.25rem;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

//// corp book
.m-auto {
    margin: 0 auto;
    display: block;
    cursor: pointer;
}
.aet {
    color: #fefefe;
    font-size: 18px;
    background: #1d6593;
    margin: 0 -30px;
    padding: 20px 0;
    border-bottom: 1px dashed #e1e1e1;
}
.corpbook .inner {
    margin: 0 auto;
    border-radius: 5px;
    padding: 0 30px 30px;
    background: #fff;
    width: 100%;
    overflow: auto;
    max-height: 100%;
}
.modal-dialog.modal-xl {
    width: 1040px;
    max-width: 1040px;
  }
  .modal-xl .modal-body {
    padding: 0;
  }
</style>
