<template>
	<div v-if="positions">
		<b-tabs
			type="card"
			:default-active-key="'1'"
			class="specialtab pt-4"
		>
			<!-- user notis -->
			<b-tab
				key="1"
				title="Индивидуальные"
			>
				<div class="row align-items-center py-4">
					<div class="col-lg-3 col-md-6">
						<multiselect
							v-model="activeuser"
							label="name"
							:options="user_with_notifications"
							placeholder="Выберите сотрудника из списка"
						/>
					</div>
					<div class="col-lg-3 col-md-6">
						<button
							v-if="activeuser"
							class="btn-primary btn"
							@click="deleteUser"
						>
							Исключить сотрудника
						</button>
					</div>
					<div class="col-lg-3 col-md-6">
						<multiselect
							v-model="newUser"
							label="name"
							:options="users"
							placeholder="Выберите из списка"
						/>
					</div>
					<div class="col-lg-3 col-md-6">
						<button
							class="btn-primary btn"
							@click="addUser"
						>
							Добавить сотрудника
						</button>
					</div>
				</div>

				<div
					v-if="activeuser"
					class="mt-4"
				>
					<div class="">
						<h5 class="mr-2">
							Уведомления
						</h5>
						<button
							class="btn-primary btn btn-sm rounded"
							@click="addNotiToUser"
						>
							Добавить уведомление
						</button>
						<button
							class="btn-success btn btn-sm rounded"
							@click="saveUser"
						>
							Сохранить
						</button>
					</div>

					<div
						v-for="(noti, key) in activeuser_notifications"
						:key="key"
						class="row mt-2"
					>
						<div class="col-lg-3">
							<multiselect
								v-model="noti[0]"
								label="title"
								:options="templates"
								placeholder="Выберите из списка"
							/>
						</div>
						<div class="col-lg-6">
							<div v-if="noti[0] != null && need_group.hasOwnProperty(Number(noti[0].id)) && need_group[noti[0].id]">
								<multiselect
									v-model="noti[1]"
									label="name"
									track-by="id"
									placeholder="Выберите группы"
									open-direction="bottom"
									:options="groups"
									:multiple="true"
									:searchable="true"
									:loading="isLoading"
									:internal-search="false"
									:clear-on-select="false"
									:close-on-select="false"
									:options-limit="300"
									:limit="100"
									:max-height="600"
									:show-no-results="false"
									:hide-selected="true"
								/>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12" />
					</div>
				</div>
			</b-tab>
			<!-- end of user notis -->


			<b-tab
				key="2"
				title="Шаблоны"
			>
				<b-tabs
					type="card"
					default-active-key="1"
					class="specialtab mt-5"
				>
					<b-tab
						key="1"
						title="Индивидуальные"
						card
					>
						<!-- individual notis -->
						<div
							id="noti-individual"
							class="py-4"
						>
							<div class="table-container">
								<b-table-simple class="table-custom-notice">
									<b-thead>
										<b-tr>
											<b-th>Название</b-th>
											<b-th>Текст</b-th>
											<b-th>Сотрудники</b-th>
											<b-th />
										</b-tr>
									</b-thead>
									<b-tbody>
										<b-tr
											v-for="(item, status_index) in user_templates"
											:key="item.id"
											:class="{ active: item.editable }"
										>
											<b-td>
												<b-form-checkbox
													v-model="item.editable"
													switch
												>
													{{ item.title }}
												</b-form-checkbox>
											</b-td>
											<b-td>
												<!-- eslint-disable vue/no-v-html -->
												<div
													class="p-2"
													v-html="item.message"
												/>
												<div
													class="description"
													v-html="item.note"
												/>
												<!-- eslint-disable vue/no-v-html -->
											</b-td>
											<b-td>
												<multiselect
													id="ajax"
													v-model="item.selectedGroups"
													label="name"
													track-by="id"
													placeholder="Выберите сотрудников"
													open-direction="bottom"
													:options="users"
													:multiple="true"
													:disabled="!item.editable"
													:searchable="true"
													:loading="isLoading"
													:internal-search="false"
													:clear-on-select="false"
													:close-on-select="false"
													:options-limit="300"
													:limit="100"
													:max-height="600"
													:show-no-results="false"
													:hide-selected="true"
												>
													<template #clear="props">
														<div
															v-if="item.selectedGroups.length"
															class="multiselect__clear"
															@mousedown.prevent.stop="clearAll(props.search)"
														/>
													</template>
													<template #noResult>
														<span>Упс! Не найдено ни одного сотрудника :(</span>
													</template>
												</multiselect>
											</b-td>
											<b-td>
												<button
													class="btn btn-primary btn-sm rounded"
													:disabled="!item.editable"
													@click="updateNotification(item, status_index)"
												>
													Сохранить
												</button>
											</b-td>
										</b-tr>
									</b-tbody>
								</b-table-simple>
							</div>
						</div>
						<!-- end of individual notis -->
					</b-tab>
					<b-tab
						key="2"
						title="Должностные"
					>
						<!-- position notis -->
						<div
							id="noti-positions"
							class="py-4"
						>
							<div class="table-container">
								<b-table-simple class="table-custom-notice">
									<b-thead>
										<b-tr>
											<b-th>Название</b-th>
											<b-th>Текст</b-th>
											<b-th>Должности</b-th>
											<b-th />
										</b-tr>
									</b-thead>
									<b-tbody>
										<b-tr
											v-for="(item, status_index) in position_templates"
											:key="item.id"
											:class="{ active: item.editable }"
										>
											<b-td>
												<b-form-checkbox
													v-model="item.editable"
													switch
												>
													{{ item.title }}
												</b-form-checkbox>
											</b-td>
											<b-td>
												<div
													class="p-2"
													v-html="item.message"
												/>
												<div
													class="description"
													v-html="item.note"
												/>
											</b-td>
											<b-td>
												<multiselect
													id="ajax"
													v-model="item.selectedGroups"
													label="name"
													track-by="id"
													placeholder="Выберите позиции"
													open-direction="bottom"
													:options="positions"
													:multiple="true"
													:disabled="!item.editable"
													:searchable="true"
													:loading="isLoading"
													:internal-search="false"
													:clear-on-select="false"
													:close-on-select="false"
													:options-limit="300"
													:limit="100"
													:max-height="600"
													:show-no-results="false"
													:hide-selected="true"
												>
													<template #clear="props">
														<div
															v-if="item.selectedGroups.length"
															class="multiselect__clear"
															@mousedown.prevent.stop="clearAll(props.search)"
														/>
													</template>
													<template #noResult>
														<span>Упс! Не найдено ни одной позиции :(</span>
													</template>
												</multiselect>
											</b-td>
											<b-td>
												<button
													class="btn btn-primary btn-sm rounded"
													:disabled="!item.editable"
													@click="updateNotification(item, status_index)"
												>
													Сохранить
												</button>
											</b-td>
										</b-tr>
									</b-tbody>
								</b-table-simple>
							</div>
						</div>
						<!-- end of position notis -->
					</b-tab>

					<b-tab
						key="3"
						title="Групповые"
						card
					>
						<!-- group notis -->
						<div
							id="noti-groups"
							class="py-4"
						>
							<div class="table-container">
								<b-table-simple class="table-custom-notice">
									<b-thead>
										<b-tr>
											<b-th>Название</b-th>
											<b-th>Роботы</b-th>
											<b-th>Текст</b-th>
											<b-th>Группы</b-th>
											<b-th />
										</b-tr>
									</b-thead>
									<b-tbody>
										<b-tr
											v-for="(item, status_index) in group_templates"
											:key="item.id"
											:class="{ active: item.editable }"
										>
											<b-td>
												<b-form-checkbox
													v-model="item.editable"
													switch
												>
													{{ item.title }}
												</b-form-checkbox>
											</b-td>
											<b-td>
												<b-form-select
													v-model="item.action"
													:disabled="!item.editable"
													style="min-width: 200px;"
												>
													<b-form-select-option
														v-for="(action, index) in actions"
														:key="index"
														:value="action.value"
													>
														{{ action.title }}
													</b-form-select-option>
												</b-form-select>
											</b-td>
											<b-td>
												<div
													class="p-2"
													v-html="item.message"
												/>
												<div
													class="description"
													v-html="item.note"
												/>
											</b-td>
											<b-td>
												<multiselect
													id="ajax"
													v-model="item.selectedGroups"
													label="name"
													track-by="id"
													placeholder="Выберите группы"
													open-direction="bottom"
													:options="groups"
													:multiple="true"
													:disabled="!item.editable"
													:searchable="true"
													:loading="isLoading"
													:internal-search="false"
													:clear-on-select="false"
													:close-on-select="false"
													:options-limit="300"
													:limit="100"
													:max-height="600"
													:show-no-results="false"
													:hide-selected="true"
												>
													<template #clear="props">
														<div
															v-if="item.selectedGroups.length"
															class="multiselect__clear"
															@mousedown.prevent.stop="clearAll(props.search)"
														/>
													</template>
													<template #noResult>
														<span>Упс! Не найдено ни одной группы :(</span>
													</template>
												</multiselect>
											</b-td>
											<b-td>
												<button
													class="btn btn-primary btn-sm rounded"
													:disabled="!item.editable"
													@click="updateNotification(item, status_index)"
												>
													Сохранить
												</button>
											</b-td>
										</b-tr>
									</b-tbody>
								</b-table-simple>
							</div>
						</div>
						<!-- end of group notis -->
					</b-tab>

					<b-tab
						key="4"
						title="Другие"
						card
					>
						<!-- group notis -->
						<div
							id="noti-groups"
							class="py-4"
						>
							<div class="table-container">
								<b-table-simple class="table-custom-notice">
									<b-thead>
										<b-tr>
											<b-th>Название</b-th>
											<b-th>Роботы</b-th>
											<b-th>Текст</b-th>
											<b-th>Заметка</b-th>
										</b-tr>
									</b-thead>
									<b-tbody>
										<b-tr
											v-for="item in other_templates"
											:key="item.id"
											:class="{ active: item.editable }"
										>
											<b-td>
												{{ item.title }}
											</b-td>
											<b-td>
												<b-form-select
													v-model="item.action"
													:disabled="true"
													style="min-width: 200px;"
												>
													<b-form-select-option
														v-for="(action, index) in actions"
														:key="index"
														:value="action.value"
													>
														{{ action.title }}
													</b-form-select-option>
												</b-form-select>
											</b-td>
											<b-td>
												<div
													class="p-2"
													v-html="item.message"
												/>
											</b-td>
											<b-td>
												<div
													class="description"
													v-html="item.note"
												/>
											</b-td>
										</b-tr>
									</b-tbody>
								</b-table-simple>
							</div>
						</div>
						<!-- end of group notis -->
					</b-tab>
				</b-tabs>
			</b-tab>
			<!-- end of group notis -->
		</b-tabs>

		<div class="mmm" />
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'PageNotifications',
	props: {
		groups_with_id: {
			type: Array,
			default: () => []
		},
		users: {
			type: Array,
			default: () => []
		},
		positions: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			isLoading: false,
			activeuser: null,
			actions: [
				{
					value: 'profile',
					title: 'Уведомление в ЛК',
				},
				{
					value: 'sms',
					title: 'СМС сообщение',
				},
				{
					value: 'autocall',
					title: 'Автозвонок',
				},
				{
					value: 'whatsapp',
					title: 'Ватсап сообщение',
				}
			],
			newUser: null,
			types: {
				0: 'Сотрудникам',
				1: 'Отделам',
			},
			visible: {
				user: true,
			},
			groups: [],
			position_templates: [],
			group_templates: [],
			other_templates: [],
			user_templates: [],
			templates: [],
			need_group: [],
			user_with_notifications: [],
			activeuser_notifications: [],
			newNoti: {
				user:null,
				notifications:null,
			}
		}
	},
	watch: {
		activeuser: {
			handler () {
				this.selectUser()
			}
		},
		positions(){
			this.init()
		}
	},
	created() {
		if(this.positions){
			this.init()
		}
	},
	mounted() {},
	methods: {
		init(){
			this.fetchData();
			this.groups = this.groups_with_id;
		},
		clearAll () {
			this.selectedGroups = []
		},
		addTag(newTag) {
			const tag = {
				email: newTag,
				ID: newTag
			}
			//this.options.push(tag)
			this.newNoti.notifications.push(tag)
		},
		updateNotification (status, status_index) {

			this.axios.post('/timetracking/settings/notifications/update', {
				id: status.id,
				action: status.action,
				message: status.message,
				ids: status.selectedGroups
			}).then(() => {

				if(status.type == 1) {
					this.group_templates[status_index].editable = false;
				}

				if(status.type == 0) {
					this.user_templates[status_index].editable = false;
				}
				this.$toast.success('Успешно изменено!');

			}).catch(error => {
				console.error(error.response)
				this.$toast.error('Ошибка!');
			});
		},
		addNotiToUser() {
			this.activeuser_notifications.push([
				{},
				[],
				1
			])
		},

		addUser() {
			let item = this.user_with_notifications.find(x => x.id === this.newUser.id);

			if(item !== undefined) {
				this.$toast.info('Пользователь уже есть в списке');
				return null;
			}

			this.user_with_notifications.push(this.newUser);
			this.activeuser = this.newUser
			this.activeuser_notifications = [
				{},
				[]
			];
			this.newUser = null;
			this.$toast.info('Добавьте сотруднику уведомления и сохраните');


			this.selectUser()
		},

		optionSelected() {},

		fetchData () {
			this.axios.get('/timetracking/settings/notifications/get').then(response => {
				this.user_templates = response.data[0];
				this.group_templates = response.data[1];
				this.position_templates = response.data[2];
				this.other_templates = response.data[3];
				this.user_with_notifications = response.data[4];
				this.templates = response.data[5];
				this.need_group = response.data[6];
			});
		},

		saveUser() {
			this.axios.post('/timetracking/settings/notifications/user/save', {
				user_id: this.activeuser.id,
				noti: this.activeuser_notifications,
			})
				.then(() => {
					this.$toast.success('Сохранено');
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.error('Ошибка!');
				});
		},

		deleteUser() {
			this.axios.post('/timetracking/settings/notifications/user/save', {
				user_id: this.activeuser.id,
				noti: [],
			})
				.then(() => {
					this.$toast.success('Пользователь исключен из индивидуальных уведомлений');
					this.activeuser_notifications = [];

					let id = this.activeuser.id;

					let index = this.user_with_notifications.findIndex(x => x.id == id);
					this.user_with_notifications.splice(index, 1)
					this.activeuser = null

				})
				.catch(error => {
					console.error(error)
					this.$toast.error('Ошибка!');
				});
		},

		selectUser() {

			this.axios.post('/timetracking/settings/notifications/user', {
				user_id: this.activeuser,
			})
				.then(response => {
					this.activeuser_notifications = [];
					if(response.data) {
						this.activeuser_notifications =  response.data.notifications;
					}

				})
		},

	},
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
    .vs__clear {
        display: none !important;
    }
</style>
<style lang="scss" scoped>
    .table-custom-notice{
        th,td{
            text-align: left;
            border-left: 1px solid #ddd !important;
            &:first-child{
                border-left: none;
            }
        }
        tbody{
            tr{
                border-bottom: 1px solid #ddd;
            }
            th,td{
                vertical-align: top;
            }
        }
    }
    .groupstatus.active {
        background: #f3f3f3;
    }


    .notifications_table_2_item2 {
        width: 100%;
        max-width: 100%;
        border-radius: 4px;
        margin: 15px auto;
        cursor: pointer;
        padding: 6px 5px;
        border-color: #e6e6e6;
        font-size: 13px;
    }

    select {
        color: #202226;
        font-family: "Open Sans";
        font-size: 12px;
        font-weight: 600;
        padding: 0 10px;
    }

    .textarea {
        margin-top: 10px;
        margin-bottom: 5px;
        min-height: 70px;
        border: 1px solid rgb(230, 230, 230);
        border-radius: 5px;
        background: rgb(251, 251, 251);
        color: rgb(0, 0, 0);
        padding: 8px;
        width: 100%;
    }

    .custom__tag {
        position: relative;
        display: inline-block;
        padding: 4px 26px 4px 10px;
        border-radius: 5px;
        margin-right: 10px;
        color: #fff;
        line-height: 1;
        background: #41b883;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        max-width: 100%;
        text-overflow: ellipsis;
    }

    .save-block {
        display: flex;
        justify-content: center;
        padding: 15px;
    }

    .save-block .btn {
        border-radius: 3px;
    }

    /* Enter and leave animations can use different */
    /* durations and timing functions.              */
    .slide-fade-enter-active {
        transition: all .3s ease;
    }

    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */
    {
        transform: translateY(10px);
        opacity: 0;
    }

    .specialtab {
        min-height: 100vh;
    }
</style>
<style scoped>
    .multiselect {
        margin-top: 0;
        min-height: 30px;
    }

    .multiselect__select {
        position: absolute;
        width: 33px;
        height: 30px
    }

    .multiselect__tags {
        min-height: 28px;
        padding-top: 5px;
        font-size: 12px;
    }

    .multiselect__placeholder {
        margin-bottom: 0;
    }

    .border {
        border: 1px solid #e9ecef;
    }

    .border-3 {
        border-left: 1px solid #e9ecef;
        border-right: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }

    .border-r {
        border-right: 1px solid #e9ecef;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }

    .border-t {
        border-top: 1px solid #e9ecef;
    }

    .fz-14 {
        font-size: 14px;
    }

    .absolute {
        position: absolute;
    }

    .relative {
        position: relative;
    }

    .non {
        width: 100%;
        height: 100%;
        z-index: 2222;
    }

    .description {
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 10px;
        font-size: 12px;
        background: aliceblue;
    }

    .mmm {
        height: 150px;
    }

    .vs__dropdown-menu {
        position: absolute;
        top: 100000;
    }
</style>
