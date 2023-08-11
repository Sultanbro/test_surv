<template>
	<div class="check-page mt-2">
		<div class="row">
			<div class="col-md-2">
				<a
					id="showCheckSideBar"
					class="btn btn-success"
					style="color: white"
					@click="addNewCheckModalShow()"
				>Создать чек лист</a>
				<img
					v-b-popover.hover.right="'Для сотрудников, ежедневно выполняющих повторяющиеся действия.'"
					src="/images/dist/profit-info.svg"
					class="img-info"
				>
			</div>

			<div class="col-md-3">
				<input
					v-model="filter"
					type="text"
					class="form-control"
					placeholder="Поиск"
				>
			</div>
		</div>

		<div class="mt-4">
			<i class="bi bi-reception-4" />
			<div class="table-container">
				<b-table-simple class="table table-bordered">
					<b-thead>
						<b-tr>
							<b-th scope="col">
								Сотрудники/отдел
							</b-th>
							<b-th scope="col">
								Кол-показов
								<i
									v-b-popover.hover.right.html="'Сколько раз будет уведомление Чек лист автоматически будет всплывать в кабинете сотрудника'"
									class="fa fa-info-circle"
									style="cursor: pointer"
									title="Работают"
								/>
							</b-th>
							<b-th scope="col">
								Постановщик
							</b-th>
							<b-th scope="col">
								Статитика
							</b-th>
							<b-th />
						</b-tr>
					</b-thead>
					<b-tbody>
						<b-tr
							v-for="(arrCheckList, index) in filteredRows"
							:key="`employee-${index}`"
							class="p-0"
						>
							<b-td>
								<!-- eslint-disable vue/no-v-html -->
								<a
									@click="editCheck(arrCheckList.id,arrCheckList.item_type)"
									v-html="highlightMatches(arrCheckList.title)"
								/>
								<!-- eslint-disable vue/no-v-html -->
							</b-td>
							<b-td>
								{{ arrCheckList.show_count }}
							</b-td>
							<b-td>
								{{ arrCheckList.creator.name }}  {{ arrCheckList.creator.last_name }}
							</b-td>
							<b-td class="position-relative">
								<a
									:href="'/timetracking/quality-control'"
									target="_blank"
									class="btn btn-primary btn-icon"
								>
									<i
										class="fa fa-signal"
										aria-hidden="true"
									/>
								</a>
							</b-td>
							<b-td>
								<a
									class="btn btn-danger btn-icon"
									@click="arrCheckDelete(arrCheckList.id)"
								>
									<i
										class="fa fa-trash"
										aria-hidden="true"
									/>
								</a>
							</b-td>
						</b-tr>
					</b-tbody>
				</b-table-simple>
			</div>
		</div>


		<Sidebar
			title="Создать чек лист"
			:open="showCheckSideBar"
			width="65%"
			@close="closeSideBar()"
		>
			<div class="col-md-12 p-0">
				<div class="col-12 p-0 mt-5">
					<div class="row pb-3 ml-3">
						<div class="col-md-3 p-0">
							<p>Для группы чек лист</p>
						</div>

						<div class="col-md-4 p-0">
							<div class="person">
								<div
									ref="select"
									v-click-outside="close"
									class="super-select"
									style="width: unset"
									:class="posClass"
								>
									<div
										class="selected-items flex-wrap noscrollbar"
										@click="toggleShow"
									>
										<a v-if="!(values.length > 0)">Отделы/Сотрудники</a>


										<div
											v-for="(value, i) in values"
											:key="i"
											class="selected-item"
											:class="'value' + value.type"
										>
											{{ value.name }}
											<i
												v-if="value.checked"
												class="fa fa-times"
												@click.stop="removeValue(i,'1')"
											/>

											<i
												v-else
												class="fa fa-times"
												@click.stop="removeValue(i,'2')"
											/>
										</div>
									</div>

									<div
										v-if="show"
										class="show"
									>
										<div class="search">
											<input
												ref="search"
												v-model="searchText"
												type="text"
												placeholder="Поиск..."
												@keyup="onSearch()"
											>
										</div>

										<div class="options-window">
											<div class="types">
												<div
													class="type"
													:class="{'active': type == 1}"
													@click="changeType(1)"
												>
													<div class="text">
														Сотрудники
													</div>
													<i class="fa fa-user" />
												</div>
												<div
													class="type"
													:class="{'active': type == 2}"
													@click="changeType(2)"
												>
													<div class="text">
														Отделы
													</div>
													<i class="fa fa-users" />
												</div>
												<div
													class="type"
													:class="{'active': type == 3}"
													@click="changeType(3)"
												>
													<div class="text">
														Должности
													</div>
													<i class="fa fa-briefcase" />
												</div>

												<div
													v-if="select_all_btn && !single"
													class="type mt-5 active all"
													@click="selectAll"
												>
													<div class="text">
														Все
													</div>
													<i class="fa fa-check" />
												</div>
											</div>


											<div class="options">
												<div
													v-for="(option, index) in filtered_options"
													:key="index"
													class="option"
													:class="{'selected': option.selected}"
													@click="addValue(index)"
												>
													<i
														v-if="option.type == 1"
														class="fa fa-user"
													/>
													<i
														v-if="option.type == 2"
														class="fa fa-users"
													/>
													<i
														v-if="option.type == 3"
														class="fa fa-briefcase"
													/>
													{{ option.name }}
													<i
														v-if="option.selected"
														class="fa fa-times"
														@click.stop="removeValueFromList(index)"
													/>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>





				<div
					class="row mt-5 pb-3 ml-3"
					style="border-bottom: 1px solid #dee2e6"
				>
					<div class="col-md-3 p-0">
						<p>Колво показов</p>
					</div>

					<div class="col-md-4 p-0">
						<input
							v-model="countView"
							placeholder="Максимум 10 "
							type="number"
							class="form-control btn-block"
						>
					</div>
				</div>
				<div class="row mt-4 pl-3">
					<div
						v-for="(item, index) in arrCheckInput.tasks"
						:key="index"
						class="col-md-12 pr-0 mt-2"
					>
						<div class="row">
							<div class="col-md-6 pr-0 mr-2">
								<!--                              <div class="position-absolute" style="margin-left: -15px;top: 2px">-->
								<!--                                  <b-form-checkbox v-model="item.checked"  ></b-form-checkbox>-->
								<!--                              </div>-->
								<input
									v-model="item.task"
									style="width: 110%"
									type="text"
									placeholder="Впишите активность чек листа"
									class="form-control btn-block "
								>
							</div>
							<!--                          <div class="col-md-3 p-0 mr-3 ml-1">-->
							<!--                              <input v-model="item.https"  type="text" placeholder="https:" class="form-control btn-block ">-->
							<!--                          </div>-->

							<div
								class="col-1"
								style="position: relative"
							>
								<button
									v-if="index == '0'"
									style="position: absolute;right: 11px"
									type="button"
									title="Удалить чек-лист"
									class="btn btn-secondary"
									@click="deleteCheckList(index, item.id)"
								>
									<i
										class="fa fa-trash"
										aria-hidden="true"
									/>
								</button>

								<button
									v-else
									style="position: absolute;right: 11px"
									type="button"
									title="Удалить чек-лист"
									class="btn btn-primary"
									@click="deleteCheckList(index, item.id)"
								>
									<i
										class="fa fa-trash"
										aria-hidden="true"
									/>
								</button>
							</div>
						</div>
					</div>
					<div class="col-md-12 mt-3">
						<div
							v-if="errors.show"
							class="alert mb-3 alert-danger p-2"
						>
							<span v-if="errors.message">
								{{ errors.message }}
							</span>
							<span v-if="errors.countViewError">
								{{ errors.countViewError }}
							</span>

							<button
								type="button"
								class="close mb-3"
								@click="closeAlert()"
							>
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="col-md-6 p-0">
							<button
								type="button"
								title="Добавить новый пункт чек листа"
								class="btn btn-success"
								@click="addCheckList()"
							>
								Добавить пункт чек листа
							</button>

							<button
								v-if="addButton"
								type="button"
								title="Сохранить"
								class="btn btn-primary"
								@click.prevent="saveCheckList()"
							>
								Cохранить
							</button>

							<button
								v-if="editButton"
								type="button"
								title="Сохранить"
								class="btn btn-primary"
								@click.prevent="saveEditCheckList(arrCheckInput.tasks)"
							>
								Изменить Сохранение
							</button>
						</div>
					</div>
				</div>
			</div>
		</Sidebar>
	</div>
</template>


<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import Sidebar from '@/components/ui/Sidebar' // сайдбар table
export default {
	name: 'TableQuarter',
	components: {
		Sidebar,
	},
	// props:['item','groups', 'users', 'roles'],
	props:{

		single: {
			type: Boolean,
			default: false
		},
		select_all_btn: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return{
			deleted_tasks:[],
			editValuesChanged: false,
			editMode: false,
			valueFindGr:[],
			arrCheckInput:{
				tasks:[],
			},
			arrCheckLists:[],
			filter:'',
			countView:'1',
			errors: {
				message:'',
				msg:'',
				text:[],
				counterror:[],
				show:false,
				save:false,
				class:'btn btn-success',
			},
			showCheckSideBar:false,
			addButton:false,
			editButton:false,
			placeholderSelect:true,
			check_id:null,
			flag_type:true,
			allValueArray:[],



			selectedRole:{
				role_1:true,
				role_2:false,
				role_3:false,
			},
			responsibility:{
				block:false,
				input:false,
				inputText:null,
				results: [],
				result_text : false,
			},
			showModalCheck : false,
			selected_search:null,

			// item:{
			//     targets:{}
			//  },
			values:[],
			options: [],
			filtered_options: [],
			type: 1,
			show: false,
			posClass: 'top',
			searchText: '',
			first_time: true,
			selected_all: false,
		}
	},
	computed: {

		filteredRows () {


			return this.arrCheckLists;

		},
	},
	watch:{
		arrCheckInput:{
			handler(){
				this.editValuesChanged = true;
			},
			deep: true,
		},
		countView(){
			this.editValuesChanged = true;
		},
		'values.length'(){
			this.editValuesChanged = true;

		},

	},
	created(){

		this.checkSelectedAll();
		this.viewCheckList();
		this.addCheckList();

	},

	methods:{
		closeSideBar(){
			this.deleted_tasks = [];
			if(this.editValuesChanged && !this.editMode){
				this.showCheckSideBar = false;

				/*
                    if (confirm('Сохранить изменения?') == true) {
                        console.log('save');
                        this.showCheckSideBar = false;
                    }
                    else{
                        console.log('do not save');
                        this.showCheckSideBar = false;
                        this.clearSideBar();
                    }*/
			}else{
				//this.clearSideBar();
				this.editMode = false;
			}
			this.showCheckSideBar = false;
			this.editValuesChanged = false;

		},
		clearSideBar(){
			this.values = [];
			this.countView = 1;
			this.arrCheckInput.tasks = [];
			this.arrCheckInput.tasks.push({
				checked: false,
				task:'',
				text: '',
				https: '',
			});
		},
		obrabotkaArray(groups,positions,allusers){



			if (Object.keys(groups).length > 0) {
				this.groups_arr = groups;
				const arrayFailedGr = Object.entries(this.groups_arr).map((arr) => ({
					code: arr[0],
					name: arr[1],
					checked:false,
					type:1,
				}));

				this.groups_arr = arrayFailedGr
			}


			this.positions_arr = positions;
			if (Object.keys(this.positions_arr).length > 0) {
				const arrayFailedGr = Object.entries(this.positions_arr).map((arr) => ({
					code: arr[0],
					name: arr[1],
					checked:false,
					type:2,
				}));
				this.positions_arr = arrayFailedGr
			}


			if (allusers.length > 0) {
				for (let i = 0; i <  allusers.length; i++) {
					this.allusers_arr[i] = {
						name: allusers[i]['name'] + '  ' + allusers[i]['last_name'],
						code: allusers[i]['id'],
						checked:false,
						type:3,
					}

				}
			}




		},

		getUsers(){
			this.axios.post('/timetracking/settings/get/modal/', {
				type:'3',
			}).then(response => {
				// console.log(response);
				this.obrabotkaArray(response.data['groups'],response.data['positions'],response.data['users'])
			})
		},
		addResponsibility(email){
			this.responsibility.inputText = null;
			this.responsibility.inputText = email;
			this.responsibility.result_text = false;

		},
		viewCheckList(){
			this.axios.get('/timetracking/settings/list/check', {
			}).then(response => {
				this.arrCheckLists = response.data;
			})
		},
		fetchResponsibility() {
			this.axios.post('/timetracking/settings/auth/check/user/responsibility', {
				search:this.responsibility.inputText,
			}).then(response => {
				this.responsibility.results = response.data
				this.responsibility.result_text = true;
			})
		},
		searchSelected(type) {
			this.axios.post('/timetracking/settings/auth/check/search/selected', {
				type:type,
				query:this.selected_search,
			}).then(response => {
				this.allusers_arr = []
				this.groups_arr = []
				this.positions_arr = []

				this.obrabotkaArray(response.data['groups'],response.data['positions'],response.data['users'])

			})
		},
		highlightMatches(text) {
			const matchExists = text.toLowerCase().includes(this.filter.toLowerCase());
			if (!matchExists) return text;

			const re = new RegExp(this.filter, 'ig');
			return text.replace(re, matchedText => `<strong style="color: #80b7ff">${matchedText}</strong>`);
		},
		addNewCheckModalShow(){

			this.showModalCheck = false,
			this.showCheckSideBar = true
			this.addButton = true
			this.editButton = false
			this.allValueArray = [];
			this.editValuesChanged = false;
			// this.refreshArray()
		},
		saveEditCheckList(arrCheckInput){

			//this.validateInput(arrCheckInput,this.countView)




			if (this.values.length > 0){
				let loader = this.$loading.show();
				this.axios.post('/timetracking/settings/edit/check/save/', {
					check_id:this.check_id,
					allValueArray:this.values,
					countView:this.countView,
					arr_check_input:arrCheckInput,
					valueFindGr:this.valueFindGr,
					deleted_tasks: this.deleted_tasks
				}).then(response => {
					loader.hide();

					if (response.data.success === false){
						this.errors.msg = null;
						this.errors.show = true;
						if (response.data.exists[0]['item_type'] == 1){
							this.errors.message = 'Данный Пользователь '+response.data.exists[0]['title']+  ' Ранне Добавлено';
							this.errors.msg = 'Данный Пользователь ' +response.data.exists[0]['title']+  ' Ранне Добавлено';
							this.$toast.error(this.errors.msg);
						}else if(response.data.exists[0]['item_type'] == 2){
							this.errors.message =  'Данный Отдел ' +response.data.exists[0]['title']+ ' Ранне Добавлено  ';
							this.errors.msg = 'Данный Отдел ' +response.data.exists[0]['title']+  ' Ранне Добавлено  ';
							this.$toast.error(this.errors.msg);
						}else if(response.data.exists[0]['item_type'] == 3){
							this.errors.message = 'Данный ' +response.data.exists[0]['title']+  ' Должность Ранне Добавлено ';
							this.errors.msg = 'Данный ' +response.data.exists[0]['title']+ ' Должность Ранне Добавлено ';
							this.$toast.error(this.errors.msg);
						}
					}else {


						//
						// this.viewCheck()

						this.$toast.success('Успешно изменен');
						this.errors.show = false;
						this.showCheckSideBar = false;

						this.viewCheckList()




					}


				})
			}else{
				this.errors.show = true
				this.errors.message = 'Выберите кому вы ставите задачу!'
			}



		},

		editCheck(check_id,type){

			this.editValuesChanged = false;
			this.addButton = false
			this.editButton = true
			this.showCheckSideBar = true
			this.check_id = check_id
			this.errors.show = false


			this.axios.post('/timetracking/settings/edit/check', {
				check_id:check_id,
				type:type,
			}).then(response => {

				this.values = []
				this.placeholderSelect = false


				// this.addDivBlock(response.data['title'],response.data['item_id'],response.data['item_type'],'edit')

				this.editValueThis = response.data;
				this.valueFindGr = response.data.item_id;
				this.countView = response.data.show_count;

				this.arrCheckInput = response.data;
				this.editValueThis.view = true
				this.editValueThis.arr = response.data


				this.showModalCheck = true


				this.values.push({
					name: response.data.title,
					id: response.data.item_id,
					type: response.data.item_type,
					checked:false,
				});


				// console.log(this.values,'xzxzx')
				// console.log(this.filtered_options,'077777')

				// this.selectedRoles(type)


				// if (response.data.item_type == 1){
				//     this.valueGroups = [{name:response.data.title,code:response.data.item_id}];
				//     this.click_show.gr = true;
				//     this.click_show.ps = false
				//     this.valuePositions = null;
				// }else if (response.data.item_type == 2){
				//     this.valuePositions = [{name:response.data.title,code:response.data.item_id}];
				//     this.click_show.ps = true;
				//     this.click_show.gr = false;
				//     this.valueGroups = null;
				// }else if (response.data.item_type == 3){
				//
				//
				//     this.click_show.us = true
				// };
				this.editValuesChanged = false;

			})
			this.editMode = true;

		},
		arrCheckDelete(id){

			let confirmDelte = confirm('Вы действительно хотите безвозвратно удалить ?');

			if (confirmDelte){
				this.axios.post('/timetracking/settings/delete/check', {
					delete_id:id
				}).then(() => {
					this.viewCheckList()
					this.$toast.success('Успешно Удалено');
				})
			}


		},
		saveCheckList(){


			this.saveButton = true
			this.errors.save_checkbox = false
			//this.validateInput(this.arrCheckInput.tasks,this.countView)
			if (this.values.length > 0 || this.values.length > 1){
				let loader = this.$loading.show();
				this.axios.post('/timetracking/settings/add/check', {
					allValueArray:this.values,
					countView:this.countView,
					arr_check_input:this.arrCheckInput,

				}).then(response => {
					loader.hide();



					if (response.data.success == false){
						this.errors.show = false;
						this.errors.msg = null;
						// this.showCheckSideBar = false;
						for (let i = 0;i < this.values.length;i++){
							if (this.values[i]['type'] == response.data.exists[0]['item_type'] && this.values[i]['id'] == response.data.exists[0]['item_id']){


								if (response.data.exists[0]['item_type'] == 1){
									this.errors.msg = 'Данный Пользователь ' +response.data.exists[0]['title']+ ' Ранне Добавлено';
									this.$toast.error(this.errors.msg);
									this.errors.show = true
									this.errors.message =  'Данный Пользователь ' +response.data.exists[0]['title']+ ' Ранне Добавлено  ';
								}else if(response.data.exists[0]['item_type'] == 2){
									this.errors.msg = 'Данный Отдел ' +response.data.exists[0]['title']+ ' Ранне Добавлено  ';
									this.$toast.error(this.errors.msg);
									this.errors.show = true
									this.errors.message =  'Данный Отдел ' +response.data.exists[0]['title']+ ' Ранне Добавлено  ';
								}else if (response.data.exists[0]['item_type'] == 3){
									this.errors.msg = 'Данный Должность' +response.data.exists[0]['title']+ ' Должность Ранне Добавлено ';
									this.$toast.error(this.errors.msg);
									this.errors.show = true
									this.errors.message =  'Данный Должность ' +response.data.exists[0]['title']+ ' Ранне Добавлено  ';
								}
							}
						}
					}else {
						this.$toast.success('Успешно Добавлено');
						this.errors.show = false;
						this.showCheckSideBar = false;
						this.viewCheckList()
					}
					this.clearSideBar();
				})
			}else{
				this.errors.show = true
				this.errors.message = 'Выбрать Кому будем чик листы добавлять'
			}



		},
		addCheckList() {
			// this.buttonClass = 'primary',
			this.arrCheckInput.tasks.push(
				{
					checked: false,
					text: '',
					task: '',
					https: '',
				}
			);
		},
		deleteCheckList(index, item){
			if (index != 0){

				this.arrCheckInput.tasks.splice(index, 1)
				this.deleted_tasks.push(item);
			}else {
				alert('К сожалению последний позиция не удаляется');
			}

		},
		validateInput(array_check_input,count_view){
			this.countView = count_view,
			this.arrCheckInput = array_check_input,
			this.errors.save = false
			for (let i = 0; i < this.arrCheckInput.length;i++){
				// if (this.arrCheckInput[i]['checked'] === true){
				if (this.arrCheckInput[i]['task'] != null){
					if (this.arrCheckInput[i]['task'].length < 1){
						this.errors.text.push('errorText');
					}
				}else {
					this.errors.text.push('errorText');
				}
			}


			if (this.countView < 11 && this.countView != 0){
				this.errors.save = true
			}else{
				this.errors.message = 'заполните текст'
				this.errors.show = true

				if (this.countView == 0){
					if (this.errors.text.length == 0){
						this.errors.message = 'Колво показов минимум 1 '
					}else {
						this.errors.message =  'заполните текст и Колво показов минимум 1'
					}
				}else if (this.countView > 11) {
					if (this.errors.text.length == 0) {
						this.errors.message = 'Колво показов максимум 10'
					}else {
						this.errors.message = 'заполните текст и Колво показов максимум 10'
					}
				}
				this.errors.text = []
				this.errors.counterror = []
			}

		},
		closeAlert() {
			this.errors.show = false;
		},
		// selectedRoles(type){
		//
		//   this.selected_search = null
		//
		//   if (type == 1){
		//     this.selectedRole.role_1 = true
		//     this.selectedRole.role_2 = false
		//     this.selectedRole.role_3 = false
		//
		//   }else if (type == 2){
		//     this.selectedRole.role_1 = false
		//     this.selectedRole.role_2 = true
		//     this.selectedRole.role_3 = false
		//   }else if (type == 3){
		//     this.selectedRole.role_1 = false
		//     this.selectedRole.role_2 = false
		//     this.selectedRole.role_3 = true
		//   }
		//
		// },
		addDivBlock(item,id,type,edit = null){
			this.flag_type = true;
			this.placeholderSelect = false;
			this.responsibility.block = true;

			if (edit == 'edit'){
				this.allValueArray = [];
				this.selectedRoles(type)
				this.refreshArray()

			}

			if (this.allValueArray.length > 0){
				for (let i = 0; i < this.allValueArray.length;i ++){
					if (this.allValueArray[i]['type'] == type && this.allValueArray[i]['code'] == id){
						this.$toast.error('Отдел ранее добавлено');
						this.flag_type = false;
					}
					// else if (this.allValueArray[i]['type'] == type && this.allValueArray[i]['code'] == id){
					// 	this.$toast.error('Должность ранее добавлено');
					// 	this.flag_type = false;
					// }
					// else if (this.allValueArray[i]['type'] == type && this.allValueArray[i]['code'] == id){
					// 	this.$toast.error('Пользователь ранее добавлено');
					// 	this.flag_type = false;
					// }
				}
			}

			if (this.flag_type == true){
				this.allValueArray.push({
					text: item,
					code:id,
					type:type,
				});





				if (type == 1){
					this.groups_arr.forEach(el => {
						if (el['code'] == id){

							el['checked'] = true
						}
					});
				}else if(type == 2){
					this.positions_arr.forEach(el => {
						if (el['code'] === id){
							el['checked'] = true
						}
					});
				}else if(type == 3){

					this.allusers_arr.forEach(el => {
						if (el['code'] !== undefined){
							if (el['code'] === id){
								el['checked'] = true
							}
						}
					});




					// for (var i = 0; i < this.allusers_arr.length;i++){
					//   if ( this.allusers_arr[i]['code']  !== undefined){
					//     console.log(this.allusers_arr[i]['code'],'xzxzxzx',id)
					//     if (this.allusers_arr[i]['code'] == id){
					//       this.allusers_arr[i]['checked'] = true
					//     }
					//   }
					// }


				}
			}
		},

		deleteDesk(id,code,type){
			this.allValueArray.splice(id,1)

			if (this.allValueArray.length == 0){
				this.placeholderSelect = true;
				this.responsibility.block = false;
				this.responsibility.input = false;
			}

			for (let i = 0; i < this.groups_arr.length;i++){
				if (this.groups_arr[i]['type'] == type && this.groups_arr[i]['code'] == code){
					this.groups_arr[i]['checked'] = false
				}
			}

			for (let i = 0; i < this.positions_arr.length;i++){
				if (this.positions_arr[i]['type'] == type && this.positions_arr[i]['code'] == code){
					this.positions_arr[i]['checked'] = false
				}
			}

			this.allusers_arr.forEach(el => {
				if (el['type'] == type && el['code'] == code){
					el['checked'] = false
				}
			});

			// for (var i = 0; i < this.allusers_arr.length;i++){
			//   if (this.allusers_arr[i]['type'] == type && this.allusers_arr[i]['code'] == code){
			//     this.allusers_arr[i]['checked'] = false
			//   }
			// }





		},

		refreshArray(){

			this.groups_arr.forEach(el => {
				el['checked'] = false
			});

			this.positions_arr.forEach(el => {
				el['checked'] = false
			});

			this.allusers_arr.forEach(el => {
				el['checked'] = false
			});

			// if (this.allusers_arr.length > 0){
			//   for (var i = 0; i < this.allusers_arr.length;i++){
			//     this.allusers_arr[i]['checked'] = false
			//   }
			// }


		},



		//// select new

		checkSelectedAll() {
			if(this.values.length == 1
                && this.values[0]['id']== 0
                && this.values[0]['type'] == 0) {
				this.selected_all = true;
				// console.log('okay');
			} else {
				// console.log('wtf');
			}
		},

		filterType() {
			this.filtered_options = this.options.filter(el => {
				return el.type == this.type
			});
		},

		addSelectedAttr() {
			this.filtered_options.forEach(el => {
				el.selected = this.values.findIndex(v => v.id == el.id && v.type == el.type) != -1
			});
		},

		toggleShow() {
			this.show = !this.show;
			if(this.first_time) {
				this.fetch();
			}

			this.$nextTick(() => {
				if(this.$refs.search !== undefined) this.$refs.search.focus();
			});
			this.setPosClass();
		},

		setPosClass() {
			let pos = this.$refs['select'].getBoundingClientRect();
			let viewport_h = document.documentElement.clientHeight;
			this.posClass = (viewport_h - pos.top > 450) ? 'bottom' : 'top';
		},

		changeType(i) {
			this.type = i;
			this.searchText = '';
			this.filterType();
			this.addSelectedAttr();
		},

		addValue(index) {




			if(this.single) this.show = false;

			if(this.single && this.values.length > 0) {
				return;
			}

			if(this.selected_all) return;

			let item = this.filtered_options[index];

			if(this.values.findIndex(v => v.id == item.id && v.type == item.type) == -1) {

				this.values.push({
					name: item.name,
					id: item.id,
					type: item.type,
					checked:true,
				});

				item.selected = true

				this.placeholderSelect = false

			}




		},

		removeValue(i) {

			let v = this.values[i];
			if(v.id == 0 && v.type == 0 && v.name == 'Все') this.selected_all = false;

			this.values.splice(i, 1);

			let index = this.filtered_options.findIndex(o => v.id == o.id && v.type == o.type);
			if(index != -1) this.filtered_options.splice(index, 1);

			if (this.values.length == 0){
				this.placeholderSelect = true
			}



		},

		removeValueFromList(i) {


			let fo = this.filtered_options[i];



			let index = this.values.findIndex(v => v.id == fo.id && v.type == fo.type);



			if(index != -1) {
				this.values.splice(index, 1);
				fo.selected = false;
			}

			if (this.values.length == 0){
				this.placeholderSelect = true
			}




		},

		onSearch() {

			if(this.searchText == '') {
				this.filtered_options = this.options;
			} else {
				this.filtered_options = this.options.filter(el => {
					return el.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1
				});
			}

			this.addSelectedAttr();
		},

		close() {
			this.show = false;
		},

		fetch() {
			this.axios
				.get('/superselect/get', {})
				.then((response) => {

					this.options = response.data.options;

					this.filterType();
					this.addSelectedAttr();
				})
				.catch((error) => {
					alert(error,'111');
				});
		},

		selectAll() {
			if(this.selected_all) return;
			this.values.splice(0, this.values.length);
			this.values.push({
				name: 'Все',
				id: 0,
				type: 0
			});
			this.show = false;
			this.selected_all = true;

		}
	},

}
</script>

<style lang="scss" scoped>
	.img-info{
		vertical-align: middle;
	}
  .table td {
    padding: 2px;
  }
  .selected_search{
    top: -27px;
    position: absolute;
    border-radius: inherit;
    padding: 2px;
  }
  .isActiveRole{
    background-color: #2fc6f6;
  }

  .resultR{
    border: 1px solid #dee2e6;
    max-height: 100px;
    display: block;
    overflow-x: hidden;
    margin-top: 30px;
  }


   .responsibilityLi > a{
     display: block;
     padding: 0px 15px;
     background-color: #f1f1f1;
     border: 1px solid white;
   }

   .responsibilityLi  a:hover{
     padding: 0px 15px;
     background-color: #67dfef;
   }

   .responsibility{
     height: 40px;
     background-color: #e0f6fe;
     position: relative;
   }

   .responsibility input{
     margin-top: 40px;

   }

   .responsibility span{
     font-size: 20px;
     font-weight: bold;
     background-color: #abb1b8;
     padding-left: 10px;
     padding-right: 10px;
     border-radius: 20px;
     margin-left: 12px;
     position: absolute;
     top: 3px;
     color: white;
   }

   .responsibility a {
     margin-left: 55px;
     /* margin-top: -4px; */
     position: absolute;
     top: 5px;
   }

   .div_role_1{

   }

    .check-page {
        .number_input {
            width: 100px;
            display: inline-block;
            text-align: center;

            &.form-control {
                padding-left: 23px;
            }
        }
        .form-control {
            padding: 2px 7px;
            font-size: 14px;
            border: 0;
        }
        .table-bordered {
            th {
                font-weight: 600;
            }

            td,
            th {
                border: 1px solid #dee2e6;
                vertical-align: middle;
                text-align: center;

                &.left {
                    text-align: left;
                }

                &.bold {
                    font-weight: 600;
                }

                &.mark {
                    background: aliceblue;
                    color: #0077e0;
                }
            }
        }

        .form-control {
            padding: 2px 7px;
            font-size: 14px;
            border: 1px solid #dee2e6;
        }
        .error {color:red}
        .call-norm {
            font-size: 18px;
            font-weight: 700;
            padding: 15px 0;
            color: #333;
            margin-bottom: 0;
        }

        .td-transparent {
            border-bottom-color: transparent !important;
            border-left-color: transparent !important;
        }
        .w-92 {width: 92px;}
        .mw {width: 1px;}
    }


    .icon-checked{
      position: absolute;
      right: 30px;
      top: 5px;
      font-size: 25px;
      color:#67dfef;
    }
    .ui-tag-selector-remove-icon{
      color: #acbfc5;
      position: absolute;
      font-size: 17px;
      right: -35px;
      top: -35px;
    }

    /*.ui-tag-selector-tag-remove a:hover{*/
    /*  color: red;*/
    /*}*/
    .ui-tag-selector-tag-remove{
      cursor: pointer;
      position: relative;

    }
    .addElement a:hover{
      background-color: #dafbff;
    }



    .addElement{
      display: -ms-inline-flexbox;
      display: inline-flex;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      z-index: 2;
      -webkit-transition: 50ms;
      -o-transition: 50ms;
      transition: 50ms;
      max-width: 200px;
      margin: 5px;
    }


    .elementHoverList{
      color: white;
      padding: 7px;
      background-color: #67dfef;

    }
    .elementHoverList span {
      margin-right: 30px;

    }

    .selected-block-array{
      border: 2px solid #dee2e6;
      padding: 10px;
      max-height: 175px;
      overflow-x: hidden;
    }



    .style-icons > span{
      margin-top: 5px;
      margin-left: 15px;
    }

    .style-icons{
      /* background-color: #e4ebed; */
      /* align-items: center; */
      /* text-align: center; */
      /* margin-left: 0px; */
      /* border-radius: 100%; */
      /* padding-right: 8px; */
      /* padding-left: 10px; */
      /* padding-bottom: 5px; */
      padding-top: 5px;
      /* padding-top: 0px; */
      font-size: 22px;
      color: #abb1b8;
    }


    .list-role{
      margin: 0px;
      border: 1px solid white;
      position: relative;
    }

    .list-role a:hover{
      background-color: #e0f6fe;
      border: 1px solid white;
    }

    .active{
      background-color: #e0f6fe;
      border: 1px solid white;
    }

    .popupShowSelected{
      max-height:  250px;
      min-height: 250px;
      overflow-x: hidden;
    }

    .role_1{
      padding: 17px 20px 15px 20px;
      background-color: #e4ebed;
      margin-left: -66px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      position: absolute;
      cursor: pointer;
    }
  .role_1_this{
      padding: 17px 20px 15px 20px;
      background-color: #2fc6f6;;
      color: white;
      margin-left: -66px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      position: absolute;
      cursor: pointer;
    }

  .role_1_this > i{
      color: white;
  }

    .role_2{
      padding: 16px 20px 17px 20px;
      background-color: #e4ebed;
      margin-left: -66px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      top: 77px;
      position: relative;
      cursor: pointer;

    }
  .role_2_this{
      padding: 16px 20px 17px 20px;
      background-color: #2fc6f6;;
      margin-left: -66px;
       color: white;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      top: 77px;
      position: relative;
      cursor: pointer;

    }

  .role_2_this i{
    color: white;
  }

    .role_3{
      padding: 20px 20px 15px 20px;
      background-color: #e4ebed;
      margin-left: -68px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      top: 140px;
      position: relative;
      cursor: pointer;

    }
  .role_3_this{
      padding: 20px 20px 15px 20px;
      background-color: #2fc6f6;;
      margin-left: -68px;
       color: white;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      top: 140px;
      position: relative;
      cursor: pointer;
    }

  .role_3_this i {
  color: white;
  }
    .role_icon_false{
      font-size: 20px;
      color: #abb1b8;
      font-weight: bold;
    }

    .role_icon_true{
      font-size: 20px;
      color: #ffffff;
      font-weight: bold;
    }

    .gen-role-class a:hover{
      /*padding: 10px 110px 12px 20px;*/
      background-color: #2fc6f6;

      /*border-top-left-radius: 30%;*/
      /*border-bottom-left-radius: 30%;*/
      /*position: absolute;*/
      /*cursor: pointer;*/

    }

    .gen-role-class a:hover i{
      font-size: 20px;
      color: #ffffff;
      font-weight: bold;
    }

</style>
