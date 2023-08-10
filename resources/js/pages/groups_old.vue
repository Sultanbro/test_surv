<template>
	<div
		v-if="activeuserid"
		class="groups"
	>
		<b-alert
			v-if="message!=null"
			variant="info"
		>
			{{ message }}
		</b-alert>
		<b-row class="align-items-center">
			<b-col
				cols="12"
				lg="4"
				md="6"
			>
				<b-form-group label="Группа">
					<b-form-select
						v-model="activebtn"
						:options="statuses"
						size="md"
						class="group-select col-lg-6 d-flex"
						@change="selectGroup"
					>
						<template #first>
							<b-form-select-option
								:value="null"
								disabled
							>
								Выберите группу из списка
							</b-form-select-option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col
				cols="12"
				lg="4"
				md="6"
				class="col-lg-3 col-md-6"
			>
				<b-form-group
					label="Добавить группу"
					class="add-grade"
				>
					<b-form-input
						v-model="new_status"
						type="text"
						class="form-control"
					/>
					<button
						class="btn btn-success ml-4"
						@click="addStatus"
					>
						<i class="fa fa-plus" />
					</button>
					<button
						class="btn btn-info rounded add-s ml-4"
						title="Восстановить из архива"
						@click="showArchiveModal = true"
					>
						<i class="fa fa-archive" />
					</button>
				</b-form-group>
			</b-col>
			<b-col
				cols="12"
				md="5"
				lg="4"
			>
				<b-form-group
					label="Название"
					class="add-grade"
				>
					<b-form-input
						v-model="gname"
						type="text"
					/>
				</b-form-group>
			</b-col>
		</b-row>

		<hr
			v-if="activebtn != null"
			class="my-4"
		>

		<div
			v-if="activebtn != null"
			class="row"
		>
			<div class="col-lg-6 mb-3">
				<div class="dialerlist">
					<div class="fl">
						Время работы с
					</div>
					<div class="fl">
						<input
							v-model="timeon"
							type="time"
							class="form-control scscsc"
							name="start_time"
						>
						<span class="before">до</span>
						<input
							v-model="timeoff"
							type="time"
							value=""
							class="form-control"
							name="end_time"
						>
					</div>
				</div>

				<div class="dialerlist">
					<div class="fl">
						Подтягивать время
						<i
							class="fa fa-cog ml-2"
							@click="editTimeAddress()"
						/>
					</div>
					<div class="fl">
						<input
							v-model="time_address_text"
							type="text"
							class="form-control scscsc"
							style="background: #fff"
							disabled
						>
					</div>
				</div>





				<div class="dialerlist">
					<div class="fl">
						Кол-во рабочих дней
					</div>
					<div class="fl">
						<input
							v-model="workdays"
							type="number"
							class="form-control scscsc"
							min="1"
							max="7"
						>
					</div>
				</div>

				<div class="dialerlist">
					<b-form-checkbox
						v-model="editable_time"
						:value="1"
						:unchecked-value="0"
						switch
					>
						Табель редактируется
					</b-form-checkbox>
				</div>

				<div class="dialerlist">
					<b-form-checkbox
						v-model="paid_internship"
						:value="1"
						:unchecked-value="0"
						switch
					>
						Оплачиваемая стажировка
					</b-form-checkbox>
				</div>

				<!-- <button
					@click="showKPI = !showKPI"
					class="btn btn-primary rounded mr-2"
				>
					<i class="fa fa-star-half-o"></i> KPI группы
				</button>

				<button
					@click="showBonuses = !showBonuses"
					class="btn btn-primary mr-2 rounded"
				>
					<i class="fa fa-star-half-o"></i> Бонусы
				</button> -->
			</div>

			<div class="col-lg-6 mb-3 sssz">
				<div class="dialerlist blu">
					<multiselect
						v-model="corps"
						:options="corp_books"
						:multiple="true"
						:close-on-select="false"
						:clear-on-select="false"
						:preserve-search="true"
						placeholder="Выберите корп книги"
						label="title"
						track-by="title"
						:taggable="true"
						@tag="addTag"
					/>
				</div>

				<div class="blu">
					<b-form-checkbox
						v-model="show_payment_terms"
						class="mt-3"
						:value="1"
						:unchecked-value="0"
						switch
					>
						Показывать в профиле
					</b-form-checkbox>

					<b-form-group label="Условия оплаты труда">
						<b-textarea
							v-model="payment_terms"
							style="min-height: 150px;"
						/>
					</b-form-group>
				</div>
			</div>

			<div class="col-lg-12 mb-3 mt-3">
				<h6 class="mb-2">
					Сотрудники
				</h6>
				<div class="dialerlist">
					<div
						class="fl"
						style="flex-direction: column"
					>
						<multiselect
							v-model="value"
							:options="options"
							:multiple="true"
							:close-on-select="false"
							:clear-on-select="false"
							:preserve-search="true"
							placeholder="Выберите"
							label="email"
							track-by="email"
							:taggable="true"
							@tag="addTag"
						/>
						<a
							href="#"
							@click="showAlert()"
						>Удалить всех пользователей</a>
					</div>
				</div>
			</div>
			<div class="col-lg-12 mb-3">
				<button
					class="btn btn-success mr-2 rounded"
					@click="saveusers"
				>
					Сохранить
				</button>
				<button
					class="btn btn-danger mr-2 rounded"
					@click.stop="deleted"
				>
					<i class="fa fa-trash" /> Удалить группу
				</button>
			</div>
		</div>

		<Sidebar
			v-if="showBonuses"
			title="Бонусы"
			:open="showBonuses"
			width="72%"
			@close="showBonuses = false"
		>
			<table class="table table-bordered table-sm">
				<tr>
					<th class="left mark">
						Наименование
					</th>
					<th class="mark">
						Активность
					</th>
					<th class="mark">
						Ед.изм
					</th>
					<th class="mark">
						Кол-во
					</th>
					<th class="mark">
						ПД
					</th>
					<th class="mark">
						Сумма , тг
					</th>
					<th class="mark">
						Описание
					</th>
					<th class="mark" />
				</tr>

				<tr
					v-for="(bonus, index) in bonuses"
					:key="index"
				>
					<td>
						<input
							v-model="bonus.title"
							type="text"
							class="form-control form-control-sm"
						>
					</td>
					<td class="left">
						<select
							v-model="bonus.activity_id"
							class="form-control form-control-sm"
						>
							<option :value="0">
								Нет активности
							</option>
							<option
								v-for="activity in activities"
								:key="activity.id"
								:value="activity.id"
							>
								{{ activity.name }}
							</option>
						</select>
					</td>
					<td class="left">
						<select
							v-model="bonus.unit"
							class="form-control form-control-sm"
						>
							<option
								v-for="unit in units"
								:key="unit.value"
								:value="unit.value"
							>
								{{ unit.title }}
							</option>
						</select>
					</td>
					<td class="left">
						<input
							v-model="bonus.quantity"
							type="text"
							class="form-control form-control-sm"
						>
					</td>
					<td class="left">
						<select
							v-model="bonus.daypart"
							class="form-control form-control-sm"
						>
							<option
								v-for="daypart in dayparts"
								:key="daypart.value"
								:value="daypart.value"
							>
								{{ daypart.title }}
							</option>
						</select>
					</td>
					<td class="left">
						<input
							v-model="bonus.sum"
							type="text"
							class="form-control form-control-sm"
						>
					</td>
					<td class="left">
						<textarea
							v-model="bonus.text"
							class="form-control form-control-sm"
						/>
					</td>
					<td class="left">
						<i
							class="fa fa-trash"
							@click="deleteBonusItem(index)"
						/>
					</td>
				</tr>
			</table>

			<p v-if="showAfterEdit">
				Не забудьте нажать на кнопку "Сохранить", чтобы сохранить изменения и
				удаления
			</p>

			<div class="d-flex">
				<button
					class="btn btn-success btn-sm rounded mr-2"
					@click="saveBonus"
				>
					Сохранить
				</button>
				<button
					class="btn btn-primary btn-sm rounded"
					@click="addBonus"
				>
					Добавить
				</button>
				<button
					v-if="showDeleteButton"
					class="btn btn-danger btn-sm rounded"
					@click="before_deleteBonus"
				>
					Удалить
				</button>
			</div>
		</Sidebar>

		<b-modal
			id="bv-modal"
			hide-footer
		>
			<template #modal-title>
				Подтвердите удаление
			</template>
			<div class="d-block">
				<p>
					Вы уверены, что хотите удалить выбранные бонусы? На прошедшие дни
					это не повлияет.
				</p>
			</div>
			<div class="d-flex">
				<b-button
					class="mt-3 mr-1"
					variant="danger"
					block
					@click="deleteBonus"
				>
					Удалить
				</b-button>
				<b-button
					variant="primary"
					class="mt-3 ml-1"
					block
					@click="$bvModal.hide('bv-modal')"
				>
					Отмена
				</b-button>
			</div>
		</b-modal>

		<!-- Modal	-->
		<b-modal
			v-model="showEditTimeAddress"
			title="Подтягивать часы"
			size="lg"
			class="modalle"
			@ok="saveTimeAddress()"
		>
			<div class="row">
				<div class="col-5 mt-1">
					<p class="">
						Источник часов

						<i
							v-b-popover.hover.right.html="'При смене источника, новые данные в табеле будут только со дня смены источника'"
							class="fa fa-info-circle"
						/>
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="time_address"
						class="form-control form-control-sm"
					>
						<option
							v-for="(time, key) in time_variants"
							:key="key"
							:value="key"
						>
							{{ time }}
						</option>
					</select>
				</div>
			</div>

			<div
				v-if="time_address == -1"
				class="row"
			>
				<div class="col-5 mt-1">
					<div class="fl">
						ID диалера
						<i
							v-b-popover.hover.right.html="'Нужен, чтобы <b>подтягивать часы</b> или <b>оценки диалогов</b> для контроля качества.<br>С сервиса cp.callibro.org'"
							class="fa fa-info-circle ml-2"
							title="Диалер в U-Calls"
						/>
					</div>
				</div>
				<div class="col-7 mt-1">
					<div class="fl d-flex">
						<input
							v-model="dialer_id"
							type="text"
							placeholder="ID"
							class="form-control scscsc"
						>
						<input
							v-model="script_id"
							type="number"
							placeholder="ID скрипта"
							class="form-control scscsc"
						>
					</div>
				</div>
			</div>

			<div
				v-if="time_address == -1"
				class="row"
			>
				<div class="col-5 mt-1">
					<div class="fl">
						Сколько минут считать, за полный рабочий день
						<i
							v-b-popover.hover.right.html="'Запишите сколько минут разговора с сервиса cp.callibro.org считать, за полный рабочий день. <br>Пример: 250 минут считается как 8 часов'"
							class="fa fa-info-circle ml-2"
							title="Ставить полный рабочий день"
						/>
					</div>
				</div>
				<div class="col-7 mt-1">
					<div class="fl d-flex">
						<input
							v-model="talk_minutes"
							type="text"
							placeholder="ID"
							class="form-control scscsc"
						>
						<input
							v-model="talk_hours"
							type="number"
							placeholder="ID скрипта"
							class="form-control scscsc"
						>
					</div>
				</div>
			</div>

			<div class="row mt-1">
				<div class="col-12">
					<p class="">
						Исключения

						<i
							v-b-popover.hover.right.html="'Часы выбранных сотрудников, не будут копироваться из аналитики в табель'"
							class="fa fa-info-circle"
						/>
					</p>
				</div>
				<div class="col-12 mt-1">
					<multiselect
						v-model="time_exceptions"
						:options="time_exceptions_options"
						:multiple="true"
						:close-on-select="false"
						:clear-on-select="false"
						:preserve-search="true"
						placeholder="Выберите, кого не связывать"
						label="email"
						track-by="email"
						:taggable="true"
						@tag="addExceptionTag"
					/>
				</div>
			</div>
		</b-modal>

		<!-- Modal restore archived group -->
		<b-modal
			v-model="showArchiveModal"
			size="md"
			title="Восстановить из архива"
			class="modalle"
			@ok="restoreGroup()"
		>
			<div>
				<div class="col-5">
					<p class="">
						Группа
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="restore_group"
						class="form-control form-control-sm"
					>
						<option
							v-for="(archived_group, key) in archived_groups"
							:key="key"
							:value="archived_group.id"
						>
							{{ archived_group.name }}
						</option>
					</select>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
export default {
	name: 'PageGroups',
	components: {
		Sidebar,
	},
	props: [
		'statuseses',
		'corpbooks',
		'activeuserid',
		'archived_groupss',
	],
	data() {
		return {
			message: null,
			activebtn: null,
			statuses: [],
			bonuses: [],
			activities: [],
			restore_group: null,
			new_status: '',
			value: [], // selected users
			options: [], // users options

			corps: [], // selected corp_books

			corp_books: [], // corp_books options
			archived_groups: [],
			payment_terms: '', // Условия оплаты труда в группе
			timeon: '09:00',
			timeoff: '18:00',
			dialer_id: null,
			script_id: null,
			talk_minutes: null,
			talk_hours: null,
			// time edit
			time_address: 0,
			editable_time: 0,
			time_address_text: 'Не выбран',
			time_variants: [],
			workdays: 5,
			quality: 'local',
			time_exceptions: [],
			time_exceptions_options: [],
			showEditTimeAddress: false,
			paid_internship: 0,
			show_payment_terms: 1,
			zarplata: '0',
			showKPI: false,
			showBonuses: false,
			showDeleteButton: false,
			showArchiveModal: false,
			showAfterEdit: false,
			group_id: 0,
			gname: '', // Название группы
			zoom_link: '', // Ссылка zoom для обучения стажеров
			bp_link: '',
			units: [
				{
					title: 'За единицу',
					value: 'one',
				},
				{
					title: 'За все, первому',
					value: 'first',
				},
				{
					title: 'За все, каждому',
					value: 'all',
				},
			],
			dayparts: [
				{
					title: 'Весь день',
					value: 0,
				},
				{
					title: '09:00 - 13:00',
					value: 1,
				},
				{
					title: '14:00 - 19:00',
					value: 2,
				},
				{
					title: '12:00 - 16:00',
					value: 3,
				},
				{
					title: '17:00 - 21:00',
					value: 4,
				},
			],
		};
	},
	watch:{
		activeuserid(){
			this.init()
		}
	},
	created() {
		if(this.activeuserid){
			this.init()
		}
	},
	mounted() {},
	methods: {
		init(){
			this.axios.post('/timetracking/users-new', {}).then((response) => {
				this.options = response.data?.data.users;
			});
			this.statuses = this.statuseses;
			this.archived_groups = this.archived_groupss;
			this.corp_books = this.corpbooks;
		},
		saveBonus() {
			this.axios
				.post('/timetracking/users/bonus/save', {
					bonuses: this.bonuses,
				})
				.then((response) => {
					this.$toast.info('Успешно сохранено');
					this.bonuses = response.data.bonuses;
					this.messageoff();
				})
				.catch((error) => {
					console.error(error.response);
					this.$toast.info(error.response);
				});
		},

		addBonus() {
			this.bonuses.push({
				id: 0,
				title: 'Нет названия',
				sum: 0,
				group_id: this.group_id,
				activity_id: 0,
				unit: 'one',
				quantity: 0,
				daypart: 0,
			});
		},

		addTag(newTag) {
			const tag = {
				email: newTag,
				ID: newTag,
			};
			this.options.push(tag);
			this.value.push(tag);
		},

		addExceptionTag(newTag) {
			const tag = {
				email: newTag,
				ID: newTag,
			};
			this.time_exceptions_options.push(tag);
			this.time_exceptions.push(tag);
		},

		addCorpBookTag(newTag) {
			const tag = {
				title: newTag,
				id: newTag,
			};
			this.corps.push(tag);
		},

		messageoff() {
			setTimeout(() => {
				this.message = null;
			}, 3000);
		},
		selectGroup() {


			let loader = this.$loading.show();

			this.axios
				.post('/timetracking/users-new', {
					id: this.activebtn,
				})
				.then((response) => {
					if (response.data?.data) {
						const data = response.data.data
						console.warn(data)
						this.gname = data.name;
						this.value = data.users;

						this.timeon = data.timeon;
						this.timeoff = data.timeoff;
						this.group_id = data.group_id;
						this.zoom_link = data.zoom_link;
						this.bp_link = data.bp_link;
						this.dialer_id = data.dialer_id;
						this.talk_minutes = data.talk_minutes;
						this.talk_hours = data.talk_hours;
						this.script_id = data.script_id;
						this.quality = data.quality;
						this.corps = data.corp_books;
						this.bonuses = data.bonuses;
						this.activities = data.activities;
						this.payment_terms = data.payment_terms;
						this.time_address = data.time_address;
						this.workdays = data.workdays;
						this.paid_internship = data.paid_internship;
						this.show_payment_terms = data.show_payment_terms;
						this.statuses = data.groups;
						this.archived_groups = data.archived_groups;

						this.editable_time = data.editable_time;
						if (this.time_address != -1 || this.time_address != 0)
							this.time_address_text = 'Из аналитики';
						if (this.time_address == -1) this.time_address_text = 'Из U-calls';
						if (this.time_address == 0) this.time_address_text = 'Не выбран';

						loader.hide();
					} else {
						this.value = [];
					}
				});
		},

		saveusers() {
			// save group data
			let loader = this.$loading.show();

			this.axios
				.post('/timetracking/users/group/save-new', {
					group_id: this.activebtn,
					users: this.value,
					group_info: {
						work_start: this.timeon,
						work_end: this.timeoff,
						name: this.gname,
						zoom_link: this.zoom_link,
						workdays: this.workdays,
						payment_terms: this.payment_terms,
						editable_time: this.editable_time,
						paid_internship: this.paid_internship,
						quality: this.quality,
						show_payment_terms: this.show_payment_terms,
						bp_link: this.bp_link,
					},
					script_id: this.script_id,
					dialer_id: this.dialer_id,
					talk_hours: this.talk_hours,
					talk_minutes: this.talk_minutes,
				})
				.then((response) => {
					this.statuses = response.data.groups;
					// this.activebtn = response.data.group;
					this.$toast.info('Успешно сохранено');
					this.messageoff();

					loader.hide();
				})
				.catch((error) => {
					console.error(error.response);
					this.$toast.info(error.response);
					loader.hide();
				});
		},
		addStatus() {
			if (this.new_status.length > 0) {
				this.axios
					.post('/timetracking/group/save-new', {
						name: this.new_status,
					})
					.then((response) => {
						if (response.data.status == 200) {
							this.$toast.success('Добавлено');
							this.statuses.push(this.new_status);
						} else {
							this.$toast.error(
								'Название "' +
					this.new_status +
					'" не свободно, выберите другое имя для группы!'
							);
						}

						this.selectGroup(this.new_status);
						this.new_status = '';
					});
			}
		},
		deleted() {
			if (confirm('Вы уверены что хотите удалить группу?')) {
				this.axios
					.post('/timetracking/group/delete-new', {
						group: this.activebtn,
					})
					.then(() => {
						this.$toast.info('Удалена');
					});

				let ind = this.statuses.indexOf(status);
				if (ind > -1) this.statuses.splice(ind, 1);
				this.statuses.splice(ind, 1);
				this.activebtn = null;
			}
		},
		showAlert() {
			if (confirm('Вы уверены что хотите удалить всех пользователей?')) {
				this.value = [];
			}
		},

		before_deleteBonus() {
			this.bonuses.forEach(bonus => {
				if (bonus.checked) this.$bvModal.show('bv-modal');
			});
		},
		deleteBonus() {
			this.bonuses.forEach(bonus => {
				this.showAfterEdit = true;
				if (bonus.checked) bonus.deleted = true;
				this.$bvModal.hide('bv-modal');
			});
		},

		editTimeAddress() {
			this.showEditTimeAddress = true;

			this.axios
				.post('/timetracking/settings/get_time_addresses', {
					group_id: this.activebtn,
				})
				.then((response) => {
					this.time_variants = response.data.time_variants;
					this.time_exceptions_options = response.data.time_exceptions_options;
					this.time_exceptions = response.data.time_exceptions;
				})
				.catch((error) => {
					alert(error);
				});
		},

		restoreGroup() {
			if (!confirm('Вы уверены что хотите восстановить группу?')) {
				return '';
			}

			let loader = this.$loading.show();
			this.axios
				.post('/timetracking/groups/restore-new', {
					id: this.restore_group,
				})
				.then(() => {
					this.$toast.success('Восстановлен!');
					this.selectGroup(this.restore_group);
					this.restore_group = null;
					this.showArchiveModal = false;
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					this.$toast.error('Ошибка!');
					alert(error);
				});
		},

		saveTimeAddress() {
			this.axios
				.post('/timetracking/settings/save_time_addresses', {
					group_id: this.activebtn,
					time_address: this.time_address,
					time_exceptions: this.time_exceptions,
				})
				.then(() => {
					if (this.time_address != -1 || this.time_address != 0)
						this.time_address_text = 'Из аналитики';
					if (this.time_address == -1) this.time_address_text = 'Из U-calls';
					if (this.time_address == 0) this.time_address_text = 'Не выбран';

					this.time_address_text =
						this.time_variants !== undefined
							? this.time_variants[this.time_address]
							: 'Не выбран';
				})
				.catch((error) => {
					alert(error);
				});

			this.showEditTimeAddress = false;
		},

		deleteBonusItem(i) {
			if(!confirm('Вы уверены?')) {
				return;
			}

			if(this.bonuses[i].id == 0) {
				this.bonuses.splice(i, 1);
				return;
			}

			this.axios
				.post('/timetracking/settings/delete-group-bonus', {
					id: this.bonuses[i].id,
				})
				.then(() => {
					this.bonuses.splice(i, 1);
					this.$toast.success('Бонус удален');
				})
				.catch((error) => {
					alert(error);
				});


		}
	},
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
.ant-tabs {
	overflow: visible;
}

.listprof {
	display: flex;
	flex-wrap: wrap;
	margin-top: 20px;
}

.profitem {
	margin-right: 10px;
	margin-bottom: 5px;
}

.add-grade {
	display: flex;
	max-width: 500px;
}

.dialerlist {
	display: flex;
	align-items: center;
	margin: 0 0 20px 0;
	.fl {
		flex: 1;
		display: flex;
		align-items: center;
	}
}

.group-select {
	border-radius: 0;
	max-width: 100%;
}

p.choose {
	line-height: 31px;
	margin-right: 15px;
}
span.before {
	padding: 0 10px;
}
.multiselect__tags {
	border-radius: 0 !important;
}
.multiselect__tag {
	display: block !important;
	max-width: max-content !important;
}
.blu .multiselect__tag {
	background: #017cff !important;
}
@media (min-width: 1000px) {
	.groups .multiselect__tags-wrap {
		flex-wrap: wrap;
		display: flex !important;
	}
	.groups .multiselect__tag {
		flex: 0 0 49%;
		/* margin-left: 1% !important; */
		margin-right: 1% !important;
		max-width: 49% !important;
	}
}

@media (min-width: 1300px) {
	.groups .multiselect__tag {
		flex: 0 0 32%;
		/* margin-left: 1% !important; */
		margin-right: 1% !important;
		max-width: 32% !important;
	}
}
@media (min-width: 1700px) {
	.groups .multiselect__tag {
		flex: 0 0 24%;
		/* margin-left: 1% !important; */
		margin-right: 1% !important;
		max-width: 24% !important;
	}
}
.custom-table-permissions{
	.groups .multiselect__tag{
		flex: 0 0 auto!important;
		max-width: 100%!important;
		margin-right: 5px !important
	}
}

.scscsc {
	margin-left: 15px;
}
.sssz button {
	margin-top: 1px;
}
.add-grade input {
	border-radius: 0;
}
</style>
