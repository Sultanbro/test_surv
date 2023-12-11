<script>
/* eslint-disable camelcase */

import DefaultLayout from '@/layouts/DefaultLayout'
import UserEditMain from '@/components/pages/UserEdit/UserEditMain'
import UserEditAdditional from '@/components/pages/UserEdit/UserEditAdditional'
import UserEditDocuments from '@/components/pages/UserEdit/UserEditDocuments'
import UserEditAdaptation from '@/components/pages/UserEdit/UserEditAdaptation'
import UserEditPhones from '@/components/pages/UserEdit/UserEditPhones'
import UserEditSalary from '@/components/pages/UserEdit/UserEditSalary'
import UserEditMisc from '@/components/pages/UserEdit/UserEditMisc'
// import UserEditBitrix from '@/components/pages/UserEdit/UserEditBitrix'
import { useAsyncPageData, useDataFromResponse } from '@/composables/asyncPageData'
import { loadMapsApi } from '@/composables/ymapsLoader'
import UModal from '@/components/ui/UModal' // модалка НАДО УБРАТЬ
import AwardUserSidebar from '@/components/sidebars/AwardUserSidebar' // сайдбар для награждения пользователя
import 'vue-croppie'
import {
	triggerApplyEmployee,
	triggerFiredEmployee,
} from '@/stores/api.js'
import {
	fire_trainee_causes,
	fire_employee_causes,
} from '@/composables/fire_causes'
import parsePhoneNumber from 'libphonenumber-js'

import axios from 'axios'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'

const DATE_YMD = 'YYYY-MM-DD';
const DATE_DMY = 'DD.MM.YYYY';
const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

export default {
	name: 'UserEditView',
	components: {
		DefaultLayout,
		UserEditMain,
		UserEditAdditional,
		UserEditDocuments,
		UserEditAdaptation,
		UserEditPhones,
		UserEditSalary,
		UserEditMisc,
		// UserEditBitrix,
		UModal,
		AwardUserSidebar,
	},
	data(){
		return {
			activeUserId: this.$route.query.id || '',
			csrf: '',
			workChartId: null,
			user: null,
			groups: [],
			positions: [],
			programs: [],
			workingDays: [],
			workingTimes: [],
			errors: [],
			fire_causes: [],
			auth_identifier: '',
			old_name: '',
			old_last_name: '',
			old_email: '',
			old_birthday: '',
			old_phone: '',
			old_phone_1: '',
			old_phone_2: '',
			old_phone_3: '',
			old_phone_4: '',
			old_zarplata: '',
			old_kaspi_cardholder: '',
			old_kaspi: '',
			old_card_kaspi: '',
			old_jysan_cardholder: '',
			old_jysan: '',
			old_card_jysan: '',
			in_groups: [],
			head_in_groups: [],
			profile_contacts: [],
			taxes: [],
			showBlocks: {
				main: true,
				additional: true,
				groups: true,
				documents: false,
				adaptation: false,
				phones: false,
				salary: false,
				misc: false,
				bitrix: false,
			},
			isUploadImageModal: false,
			filename: 'empty',
			fileurl: this.user?.img_url || 'noavatar.png',
			frontValid:{
				formSubmitted: false,
				phone: true,
				name: true,
				lastName: true,
				position: true,
				birthday: true,
				group: true,
				email: true,
				selectedCityInput: true,
				zarplata: true
			},
			counter: 0,
			profile_errors: 0,
			phone_errors: 0,
			zarplata_errors: 0,
			isBeforeSubmit: false,
			trainee: false,
			increment_provided: false,
			delay: 1,
			deleteError: '',
			fireCause: '',
			isDeleteConfirm: false,
			isRestoreConfirm: false,
			taxesFillData: null,

			test: null,
			fieldErrors: {},
			cityText: this.user?.working_country || '',
			cityLat: 0,
			cityLon: 0,
			file8: null,
		}
	},
	computed: {
		...mapState(usePortalStore, ['isMain']),
		isTrainee(){
			return this.user?.user_description?.is_trainee === 1
		},
		formAction(){
			if(this.user) return '/timetracking/person/update'
			return '/timetracking/person/store'
		},
		userName(){
			if(this.user) return `${this.user.last_name} ${this.user.name}`
			return 'Новый сотрудник'
		},
		lead(){
			if(!this.user) return null
			return this.user.lead
		},
		userPosition(){
			if(!this.user) return 'Новый пользователь'
			if(!this.user.position_id) return 'Пользователь CP.U_MARKETING.ORG'
			const position = this.positions.find(pos => pos.id === this.user.position_id)
			if(position) return position.position
			return ''
		},
		formUserName(){
			if(this.user) return this.user.name
			return this.old_name
		},
		formUserLastName(){
			if(this.user) return this.user.last_name
			return this.old_last_name
		},
		formUserEmail(){
			if(this.user) return this.user.email
			return this.old_email
		},
		formUserBirthday(){
			if(this.user && this.user.birthday) return this.$moment(this.user.birthday).format(DATE_YMD)
			return this.old_birthday
		},
		userCreated(){
			if(!this.user) ''
			if(this.lead && this.lead.created_at) return this.$moment(this.lead.created_at).format(DATE_DMY)
			return this.$moment(this.user.created_at).format(DATE_DMY)
		},
		userApplied(){
			if(this.user?.user_description?.applied) return this.$moment(this.user.user_description.applied).format(DATE_DMY)
			return ''
		},
		userAppliedDays(){
			if(!this.userApplied) return 0
			return this.$moment(Date.now()).diff(this.$moment(this.user.user_description.applied), 'days')
		},
		userDeleted(){
			if(this.user?.delete_time) return this.$moment(this.user?.delete_time).format(DATE_DMY)
			return ''
		},
		userDeletedAt(){
			if(this.user?.deleted_at && this.user.deleted_at !== '0000-00-00 00:00:00')
				return this.$moment(this.user.deleted_at).format(DATE_DMY)
			return ''
		},
		history(){
			if(!this.user) return null
			if(!this.user.restored_data.length) return null
			const hist = []
			this.user.restored_data.forEach(item => {
				if(item.destroyed_at) hist.push({
					label: 'Дата увольнения',
					date: this.$moment(item.destroyed_at).format('DD.MM.YYYY'),
					cause: item.cause,
				})

				if(item.restored_at) hist.push({
					label: 'Дата восстановления',
					date: this.$moment(item.restored_at).format('DD.MM.YYYY'),
				})
			})
			hist.sort((a, b) => this.$moment(a, 'DD.MM.YYYY').diff(this.$moment(b, 'DD.MM.YYYY')))
			return hist
		}
	},
	watch: {
		activeUserId(){
			this.updatePageData()
		},
		user(user){
			if(user){
				this.fileurl = user.img_url || 'noavatar.png'
				this.cityText = user.working_country
				if(user.coordinate){
					this.cityLat = user.coordinate.geo_lat
					this.cityLon = user.coordinate.geo_lon
				}
			}
		},
	},
	created(){
		loadMapsApi()
	},
	mounted(){
		this.updatePageData();
	},
	methods: {
		taxesFill(data){
			this.taxesFillData = data;
		},
		async setData(data){
			// fix phone
			if(data?.user?.phone){
				try {
					const flatPhone = data.user.phone.replace(/[^\d]+/g, '')
					const plusPhone = '+' + flatPhone

					const phone = parsePhoneNumber(plusPhone, 'ZZ')
					data.user.phone = phone.formatInternational() || plusPhone
				}
				catch (error) {
					console.error(error)
					// window.onerror && window.onerror(error)
				}
			}

			this.csrf = data.csrf
			this.user = data.user
			this.groups = data.groups
			this.positions = data.positions
			this.programs = data.programs
			this.workingDays = data.workingDays
			this.workingTimes = data.workingTimes
			this.errors = data.errors
			this.auth_identifier = data.auth_identifier
			this.old_name = data.old_name
			this.old_last_name = data.old_last_name
			this.old_email = data.old_email
			this.old_birthday = data.old_birthday
			this.old_phone = data.old_phone
			this.old_phone_1 = data.old_phone_1
			this.old_phone_2 = data.old_phone_2
			this.old_phone_3 = data.old_phone_3
			this.old_phone_4 = data.old_phone_4
			this.old_zarplata = data.old_zarplata
			this.old_kaspi_cardholder = data.old_kaspi_cardholder
			this.old_kaspi = data.old_kaspi
			this.old_card_kaspi = data.old_card_kaspi
			this.old_jysan_cardholder = data.old_jysan_cardholder
			this.old_jysan = data.old_jysan
			this.old_card_jysan = data.old_card_jysan
			this.in_groups = data.in_groups
			this.head_in_groups = data.head_in_groups
			this.profile_contacts = data.profile_contacts ? data.profile_contacts : []
			this.updateTaxes();
			if(data?.user?.inviter_id){
				try {
					const {data: inviter} = await this.axios.get('/timetracking/get-person', {params: {id: data.user.inviter_id}})
					/* eslint-disable require-atomic-updates */
					this.user.inviter = inviter.user
					/* eslint-enable require-atomic-updates */
				}
				catch (error) {
					console.error(error)
				}
			}
			this.user = this.user ? {...this.user} : null
		},
		async updateTaxes(){
			try {
				const url = this.user ? `/tax?user_id=${this.user.id}` : '/tax/all';
				const { data } = await axios.get(url);
				this.taxes = this.user ? data.items : data.data;
			} catch (error) {
				console.error(error);
			}
		},
		updatePageData(){
			useAsyncPageData(`/timetracking/edit-person?id=${this.activeUserId}`).then(this.setData).catch(error => {
				console.error('useAsyncPageData', error)
			})
		},
		onClickAward(){
			if(!this.user) return
			document.dispatchEvent(new CustomEvent('award-user-sidebar', {
				detail: `${this.user.id}`
			}))
		},
		hideBlocks(){
			for(const type in this.showBlocks)
				this.showBlocks[type] = false
		},
		showBlock(id){
			this.hideBlocks()

			switch (id) {
			case 1:
				this.showBlocks.main = true
				this.showBlocks.additional = true
				this.showBlocks.groups = true
				break;
			case 7:
				this.showBlocks.adaptation = true
				break;
			case 2:
				this.showBlocks.groups = true
				break;
			case 5:
				this.showBlocks.salary = true
				break;
			case 9:
				this.showBlocks.documents = true
				break;
			case 4:
				this.showBlocks.phones = true
				break;
			case 6:
				this.showBlocks.misc = true
				this.showBlocks.bitrix = true
				break;
			}
		},
		addContacts(val){
			this.profile_contacts.push(val);
		},
		changeContact(obj){
			this.profile_contacts[obj.key][obj.input] = obj.value;
		},
		validateEmail(email) {
			return re.test(String(email).toLowerCase());
		},
		crop_image(){
			const options = {
				type: 'canvas',
				size: 'viewport'
			}
			this.$refs.croppieRef.result(options, image => {
				axios.post('/profile/upload/edit/', {
					image,
					user_id: this.activeUserId,
					file_name: this.filename,
				}).then(({data}) => {
					this.isUploadImageModal = false
					this.fileurl = data.filename
					this.filename = data.filename
				}).catch(err => {
					console.error(err)
					this.$toast.error('Не удалось загрузить изображение')
				})
			})
		},
		uploadImage({target, dataTransfer}){
			const files = target.files || dataTransfer.files
			if(!files.length) return

			var reader = new FileReader()
			reader.onload = ({target}) => {
				this.$refs.croppieRef.bind({
					url: target.result
				})
			}

			reader.readAsDataURL(files[0])
			this.isUploadImageModal = true
		},
		validChange(obj){
			this.frontValid[obj.name] = obj.bool;
		},
		selectWorkChart(val) {
			this.workChartId = val;
		},
		async submit(isTrainee, increment_provided, isNew){
			const loader = this.$loading.show();
			this.frontValid.formSubmitted = true;
			this.trainee = isTrainee
			this.increment_provided = increment_provided

			// нужно подождать пока рендер заполнит форму
			await this.$nextTick()
			const formData = new FormData(this.$refs.form)

			this.counter = 0;
			this.profile_errors = 0;
			this.phone_errors = 0;
			this.zarplata_errors = 0;

			const name = formData.get('name');
			const email = formData.get('email');
			const lastName = formData.get('last_name');
			const position = formData.get('position');
			const group = formData.get('group');
			const zarplata = formData.get('zarplata');

			const phone = formData.get('phone').replace(/[^\d]+/g, '')
			formData.set('phone', phone)


			for(let i = 1; i <= 5; i++){
				if(formData.get(`file${i}`).size === 0) formData.delete(`file${i}`);
			}
			if(formData.get('file7').size === 0) formData.delete('file7');

			if (name.length < 3) {
				this.frontValid.name = false;
				this.showBlock(1);
			}

			if (lastName.length < 3) {
				this.frontValid.lastName = false;
				this.showBlock(1);
			}

			if (!this.validateEmail(email)) {
				this.frontValid.email = false;
				this.showBlock(1);
			}

			if(!position){
				this.frontValid.position = false;
				this.showBlock(1);
			}

			if(!group && isNew){
				this.frontValid.group = false;
				this.showBlock(1);
			}

			formData.set('zarplata', zarplata.replace(/\D/g, ''));

			if(this.cityLat || this.cityLon){
				formData.set('selectedCityInput', this.cityText)
				formData.set('coordinates[geo_lat]', this.cityLat)
				formData.set('coordinates[geo_lon]', this.cityLon)
			}

			if(this.frontValid.email && this.frontValid.name && this.frontValid.lastName && this.frontValid.position && this.frontValid.group){
				this.sendForm(formData, isNew);
			}
			else {
				this.$toast.error('Заполните обязательные поля');
			}
			loader.hide();
		},

		async sendForm(formData, isNew){
			this.fieldErrors = {}
			if(this.errors && this.errors.length) return this.$toast.error('Не удалось сохранить информацию о сотруднике');
			const loader = this.$loading.show();

			try{
				const response = await this.axios.post(this.formAction, formData, {
					headers: { 'Content-Type': 'multipart/form-data' }
				});
				const userId = this.user ? this.user.id : response.data.data.id;
				if (this.taxesFillData) {
					// новый налог
					for (let i = 0; i < this.taxesFillData.newTaxes.length; i++) {
						if(this.taxesFillData.newTaxes[i].name && this.taxesFillData.newTaxes[i].value){
							const formDataNewTaxes = new FormData();
							const formDataNewTaxesAssignee = new FormData();
							formDataNewTaxes.append('user_id', userId);
							formDataNewTaxes.append('name', this.taxesFillData.newTaxes[i].name);
							formDataNewTaxes.append('value', this.taxesFillData.newTaxes[i].value);
							formDataNewTaxes.append('is_percent', this.taxesFillData.newTaxes[i].isPercent ? 1 : 0);
							const resNewTax = await this.axios.post('/tax', formDataNewTaxes);
							formDataNewTaxesAssignee.append('user_id', userId);
							formDataNewTaxesAssignee.append('tax_id', resNewTax.data.data.id);
							formDataNewTaxesAssignee.append('is_assigned', 1);
							await this.axios.post('/tax/set-assignee', formDataNewTaxesAssignee);
						}
					}

					// добавление сущесвующих
					for (let i = 0; i < this.taxesFillData.assignTaxes.length; i++) {
						const formDataAssignTaxes = new FormData();
						formDataAssignTaxes.append('user_id', userId);
						formDataAssignTaxes.append('tax_id', this.taxesFillData.assignTaxes[i].id);
						formDataAssignTaxes.append('is_assigned', 1);
						await this.axios.post('/tax/set-assignee', formDataAssignTaxes);
					}

					// редактирование сущуствующих
					for (let i = 0; i < this.taxesFillData.editTaxes.length; i++) {
						if(this.taxesFillData.editTaxes[i].name && this.taxesFillData.editTaxes[i].value){
							const formDataEditTaxes = new FormData();
							formDataEditTaxes.append('_method', 'put');
							formDataEditTaxes.append('user_id', userId);
							formDataEditTaxes.append('id', this.taxesFillData.editTaxes[i].id);
							formDataEditTaxes.append('name', this.taxesFillData.editTaxes[i].name);
							formDataEditTaxes.append('value', this.taxesFillData.editTaxes[i].value);
							formDataEditTaxes.append('is_percent', this.taxesFillData.editTaxes[i].isPercent ? 1 : 0);
							await this.axios.post('/tax', formDataEditTaxes);
						}
					}
				}

				if (this.workChartId) {
					const userId = this.user ? this.user.id : response.data.data.id;
					const formDataWorkChart = new FormData();
					formDataWorkChart.append('user_id', userId);
					formDataWorkChart.append('work_chart_id', this.workChartId);
					await axios.post('/work-chart/user/add', formDataWorkChart);
				}

				const isApplyTrainee = this.user?.user_description?.is_trainee && formData.get('is_trainee') === 'false'
				const isNewEmployee = !this.user && formData.get('is_trainee') === 'false'

				if(isApplyTrainee || isNewEmployee){
					triggerApplyEmployee(userId)
				}

				if (isNew) {
					this.$toast.success('Информация о сотруднике сохранена');
				}
				else {
					this.$toast.success('Информация о сотруднике обновлена');
				}
			}
			catch (error){
				console.error(error);
				const msg = 'Не удалось сохранить информацию о сотруднике'
				const pwdMsg = 'Значения поля "Новый пароль" некорректно.\nПароль должен содержать минимум 8 символов и хотя бы одну строчную и одну заглавную букву'
				if (error.response) {
					if (error.response.data?.message) {
						const respMsg = error.response.data.message.replace('Количество символов в поле new pwd должно быть не меньше 8.', pwdMsg)
						this.$toast.error(`${msg}\n${respMsg.replace('Значение поля new pwd некорректно.', pwdMsg)}`, {timeout: 10000})
					}
					else{
						this.$toast.error(msg)
					}
					if (error.response.data?.errors) {
						this.fieldErrors = error.response.data?.errors
					}
				}
				else if (error.request) {
					this.$toast.error(`${msg}\nСервер не отвечает, попробуйте позже`);
				}
				else{
					this.$toast.error(msg);
				}
			}

			loader.hide();
		},

		async deleteUser(){
			this.deleteError = ''
			if(!this.file8 && this.fireCause !== 'Дубликат, 2 учетки' && !this.isTrainee) {
				this.deleteError = 'Прикрепите Заявление об увольнении!'
				return
			}

			const formData = new FormData(this.$refs.deleteForm)
			try{
				const {data} = await axios({
					method: 'post',
					url: '/timetracking/delete-person',
					data: formData,
					headers: { 'Content-Type': 'multipart/form-data' },
				})
				if(this.user){
					await triggerFiredEmployee(this.user.id)
				}
				this.parseResponse(data)
				this.$toast.success('Сотрудник уволен')
			}
			catch(error){
				console.error(error)
				this.$toast.error('Не удалось уволить сотрудника')
			}
			this.toggleDeleteConfirm(false, 0)
		},
		async recoverUser(){
			const formData = new FormData(this.$refs.recoverForm)
			try{
				const {data} = await axios({
					method: 'post',
					url: '/timetracking/recover-person',
					data: formData,
					headers: { 'Content-Type': 'multipart/form-data' },
				})
				this.parseResponse(data)
				this.$toast.success('Сотрудник восстановлен')
			}
			catch(error){
				console.error(error)
				this.$toast.error('Не удалось восстановить сотрудника')
			}
			this.toggleRestoreConfirm(false)
		},
		parseResponse(html){
			const data = useDataFromResponse(html)
			this.setData(data)
		},
		toggleDeleteConfirm(state, delay){
			this.fire_causes = this.isTrainee ? fire_trainee_causes : fire_employee_causes
			this.delay = delay
			this.isDeleteConfirm = state
		},
		toggleRestoreConfirm(state){
			this.isRestoreConfirm = state
		},
		onChangeCity(city){
			this.cityText = `Страна: ${city.country} Город: ${city.name}`
			this.cityLat = city.coords[0]
			this.cityLon = city.coords[1]
		},
	}
}
</script>

<template>
	<DefaultLayout
		:has-bg="true"
		class="profile-edit"
	>
		<div class="old__content">
			<div class="user-page py-4">
				<div class="d-flex justify-content-between align-items-center">
					<a
						href="/timetracking/settings?tab=1"
						class="btn btn-rounded"
						style="background: #a0a6ab; color: white; font-size: 14px;"
					>
						<i class="fa fa-chevron-left" /> Назад
					</a>

					<div class="data-information d-flex">
						<template v-if="user">
							<template v-if="isTrainee">
								<button
									id="submit_job"
									class="btn btn-warning mr-2 rounded"
									@click.prevent="submit(false, true)"
								>
									Принять на работу
								</button>
								<button
									id="submit_trainee"
									class="btn btn-primary mr-2 rounded"
									@click.prevent="submit(true, false)"
								>
									Сохранить
								</button>
							</template>
							<button
								v-else
								id="submitx"
								class="btn btn-primary mr-2 rounded"
								@click.prevent="submit(false, false)"
							>
								Сохранить
							</button>
						</template>
						<template v-else>
							<button
								id="submitx2"
								class="btn btn-primary mr-2 rounded"
								@click.prevent="submit(false, true, true)"
							>
								Пригласить без стажировки
							</button>
							<button
								id="submit_trainee"
								class="btn btn-warning mr-2 rounded"
								@click.prevent="submit(true, false, true)"
							>
								Пригласить со стажировкой
							</button>
						</template>

						<template v-if="user">
							<template v-if="!user.deleted_at">
								<button
									v-if="isTrainee"
									id="deleteModalBtn"
									type="button"
									class="btn btn-danger rounded"
									@click.prevent="toggleDeleteConfirm(true, 0)"
								>
									Уволить стажера
								</button>
								<template v-else>
									<button
										id="deleteModalBtn"
										type="button"
										class="btn btn-danger rounded mr-2"
										@click.prevent="toggleDeleteConfirm(true, 0)"
									>
										Уволить без отработки
									</button>
									<button
										id="deleteModalBtn2"
										type="button"
										class="btn btn-danger rounded"
										@click.prevent="toggleDeleteConfirm(true, 1)"
									>
										Уволить с отработкой
									</button>
								</template>
							</template>
							<button
								v-else
								type="button"
								class="btn btn-success rounded"
								@click.prevent="toggleRestoreConfirm(true)"
							>
								Восстановить
							</button>
						</template>
					</div>
				</div>
				<form
					id="form"
					ref="form"
					:action="formAction"
					method="post"
					enctype="multipart/form-data"
					class="form-horizontal"
					name="user_form"
					@submit.prevent=""
				>
					<input
						v-if="user"
						name="id"
						:value="user.id"
						type="hidden"
						class="form-control"
					>
					<input
						id="trainee"
						name="is_trainee"
						:value="trainee"
						type="hidden"
						class="form-control"
					>
					<input
						id="increment_provided"
						name="increment_provided"
						:value="increment_provided"
						type="hidden"
						class="form-control"
					>
					<!-- eslint-disable vue/no-lone-template -->
					<!-- eslint-disable vue/no-v-html -->
					<template v-html="csrf" />
					<!-- eslint-enable vue/no-lone-template -->
					<!-- eslint-enable vue/no-v-html -->
					<div id="list-example">
						<!-- PROFILE IMAGE -->
						<input
							id="upload_image"
							name="image"
							type="file"
							accept="image/*"
							hidden
							@change="uploadImage"
						>
						<input
							id="photo"
							name="photo"
							type="file"
							hidden
						>

						<input
							v-if="user"
							id="user_id_img"
							:data-auth-id="auth_identifier"
							:value="user.id"
							hidden
						>
						<input
							v-else
							id="user_id_img"
							data-auth-id="new_user"
							hidden
							value="new_user"
							name="user_img"
						>
						<input
							id="file_name_img"
							name="file_name_img"
							:value="filename"
							hidden
						>

						<template v-if="user">
							<div class="row mt-4">
								<div class="col-12 col-md-6 py-4">
									<div class="card-profile-edit">
										<div class="d-flex">
											<label
												for="upload_image"
												style="cursor:pointer;border: 1px solid #f8f8f8;background-color: unset"
											>
												<img
													:id="user.img_url"
													:src="`/users_img/${fileurl}`"
													style="width: 150px;height: 150px; border-radius: 10px"
												>
											</label>
											<div class="d-flex flex-column justify-content-start align-items-start ml-4">
												<h4 class="font-weight-bold">
													{{ userName }}
												</h4>
												<p class="mt-3 mb-5">
													{{ userPosition }}
												</p>
												<button
													class="btn btn-success"
													@click.prevent="onClickAward"
												>
													Наградить
												</button>
											</div>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6 py-4">
									<div class="UserEditView-scrollCard">
										<div class="card-profile-edit p-0">
											<UserEditAdditional
												:user="user"
												:user-created="userCreated"
												:user-applied="userApplied"
												:user-applied-days="userAppliedDays"
												:is-trainee="isTrainee"
												:user-deleted="userDeleted"
												:user-deleted-at="userDeletedAt"
												:history="history"
											/>
										</div>
									</div>
								</div>
							</div>
						</template>
					</div>

					<hr v-if="!user">

					<ul class="profile-edit-list-tabs">
						<li
							id="bg-this-1"
							:class="{'active': showBlocks.main}"
							@click="showBlock(1)"
						>
							<span>Основные данные <span class="red">*</span></span>
						</li>

						<li
							id="bg-this-9"
							:class="{'active': showBlocks.documents}"
							@click="showBlock(9)"
						>
							<span>Документы</span>
						</li>
						<li
							id="bg-this-4"
							:class="{'active': showBlocks.phones}"
							@click="showBlock(4)"
						>
							<span>Контакты</span>
						</li>
						<li
							id="bg-this-5"
							:class="{'active': showBlocks.salary}"
							@click="showBlock(5)"
						>
							<span>Оплата</span>
						</li>
						<li
							v-if="user && isMain"
							id="bg-this-7"
							:class="{'active': showBlocks.adaptation}"
							@click="showBlock(7)"
						>
							<span>Адаптационные данные</span>
						</li>
					</ul>

					<div
						id="xmyTabContent"
						class="xtab-content"
						data-spy="scroll"
						data-target="#list-example"
					>
						<!-- first tab -->
						<div
							id="contact"
							class="xtab-pane xfade show active"
							role="tabpanel"
							aria-labelledby="contact-tab"
						>
							<!-- PROFILE INFO -->
							<div>
								<UserEditMain
									v-show="showBlocks.main"
									:form-user-name="formUserName"
									:form-user-last-name="formUserLastName"
									:form-user-email="formUserEmail"
									:form-user-birthday="formUserBirthday"
									:positions="positions"
									:groups="groups"
									:in-progress="head_in_groups"
									:programs="programs"
									:working-days="workingDays"
									:working-times="workingTimes"
									:user="user"
									:in_groups="in_groups"
									:front_valid="frontValid"
									:errors="fieldErrors"
									@valid_change="validChange"
									@selectWorkChart="selectWorkChart"
									@changeCity="onChangeCity"
								/>

								<div class="col-9 add_info">
									<!-- documents tab -->
									<UserEditDocuments
										v-show="showBlocks.documents"
										:user="user"
									/>
									<!-- end of documents -->
								</div>
								<div class="col-md-12 add_info">
									<UserEditAdaptation
										v-show="showBlocks.adaptation"
										:user="user"
										:errors="fieldErrors"
									/>
								</div>
							</div>
						</div>
						<!-- second tab -->
						<div
							id="phones"
							class="xtab-pane xfade"
							role="tabpanel"
							aria-labelledby="phones-tab"
						>
							<UserEditPhones
								v-show="showBlocks.phones"
								:user="user"
								:profile-contacts="profile_contacts"
								:old_phone="old_phone"
								:old_phone_1="old_phone_1"
								:old_phone_2="old_phone_2"
								:old_phone_3="old_phone_3"
								:old_phone_4="old_phone_4"
								@add_contacts="addContacts"
								@change_contact="changeContact"
							/>
							<!-- end of phones -->

							<!-- zarplata tab -->
							<UserEditSalary
								v-show="showBlocks.salary"
								:user="user"
								:old_zarplata="old_zarplata"
								:old_kaspi_cardholder="old_kaspi_cardholder"
								:old_kaspi="old_kaspi"
								:old_card_kaspi="old_card_kaspi"
								:old_jysan_cardholder="old_jysan_cardholder"
								:old_jysan="old_jysan"
								:old_card_jysan="old_card_jysan"
								:taxes="taxes"
								@taxes_fill="taxesFill"
								@taxes_update="updateTaxes"
							/>

							<!-- additional tab -->
							<UserEditMisc
								v-show="showBlocks.misc"
								:user="user"
							/>

							<!-- <UserEditBitrix
								v-show="showBlocks.bitrix"
								:user="user"
							/> -->
						</div>
					</div>
					<UModal
						v-if="errors && errors.length"
						:items="errors"
						title="Не сохранено"
					/>
				</form>
			</div>
		</div>

		<template v-if="user">
			<b-modal
				id="modal-deactivate"
				v-model="isDeleteConfirm"
				hide-footer
				hide-header
			>
				<div
					class="modal-dialog"
					role="document"
				>
					<div class="modal-content">
						<div class="modal-body text-center">
							<h6>Вы уверены что хотите уволить пользователя?</h6>
						</div>
						<div class="text-center mb-3">
							<form
								id="deleteForm"
								ref="deleteForm"
								action="/timetracking/delete-person"
								enctype="multipart/form-data"
								method="post"
							>
								<input
									name="id"
									:value="user.id"
									type="hidden"
									class="form-control"
								>
								<input
									id="delay"
									name="delay"
									:value="delay"
									type="hidden"
									class="form-control"
								>
								<!-- eslint-disable vue/no-lone-template -->
								<!-- eslint-disable vue/no-v-html -->
								<template v-html="csrf" />
								<!-- eslint-enable vue/no-lone-template -->
								<!-- eslint-enable vue/no-v-html -->
								<div
									class="row align-items-center justify-content-center p-3"
									style="padding-bottom:0!important"
								>
									<div class="col-md-12 mb-2">
										<select
											id="cause"
											v-model="fireCause"
											name="cause"
											class="form-control form-control-sm"
										>
											<option
												selected
												disabled
											>
												Выберите причину
											</option>
											<option
												v-for="cause in fire_causes"
												:key="cause"
												:value="cause"
											>
												{{ cause }}
											</option>
										</select>
									</div>
									<template v-if="!user.is_trainee">
										<div class="col-md-6">
											<div class="box">
												<div
													class="d-inline-block text-center"
													style="width:100%"
												>
													<input
														id="file-8"
														ref="file8"
														name="file8"
														type="file"
														class="inputfile inputfile-1"
														style="display:none"
														@change="file8 = $event.target.files"
													>
													<label
														v-if="user.downloads && user.downloads.resignation"
														for="file-8"
													>
														<svg
															width="20"
															height="30"
															class=""
														>
															<use :xlink:href="`#${user.downloads.resignation.split('.').at(-1)}-icon`" />
														</svg>
														<span>Заявление об <br>увольнении</span>
													</label>
													<label
														v-else
														for="file-8"
													>
														<svg
															width="20"
															height="30"
															class=""
														>
															<use xlink:href="#download-icon" />
														</svg>
														<span>Заявление об <br>увольнении</span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-md-6 d-flex justify-content-between flex-column">
											<button
												id="deleteUser"
												type="submit"
												class="btn btn-success rounded mb-2"
												@click.prevent="deleteUser"
											>
												Да
											</button>
											<button
												type="reset"
												class="btn btn-primary rounded"
												data-dismiss="modal"
											>
												Нет
											</button>
										</div>
									</template>
									<template v-else>
										<div class="col-md-12 mb-2">
											<input
												id="cause2"
												name="cause2"
												value=""
												type="text"
												class="form-control form-control-sm"
												placeholder="Или напишите свой вариант"
											>
										</div>
										<div class="col-md-6 d-flex justify-content-between flex-column">
											<button
												type="submit"
												class="btn btn-success rounded"
											>
												Да
											</button>
										</div>
										<div class="col-md-6 d-flex justify-content-between flex-column">
											<button
												type="reset"
												class="btn btn-primary rounded"
												data-dismiss="modal"
											>
												Нет
											</button>
										</div>
									</template>
								</div>
								<div class="row mt-2 px-5">
									<span
										id="deleteError"
										style="color:red;text-align:center;width:100%"
									>{{ deleteError }}</span>
								</div>
							</form>
						</div>
					</div>
				</div>
			</b-modal>

			<b-modal
				id="modal-activate"
				v-model="isRestoreConfirm"
				hide-footer
				hide-header
			>
				<div
					class="modal-dialog"
					role="document"
				>
					<div class="modal-content">
						<div class="modal-body text-center">
							<h6>Вы уверены что хотите востановить пользователя?</h6>
						</div>
						<div class="text-center mb-3 ">
							<form
								ref="recoverForm"
								action="/timetracking/recover-person"
								@submit.prevent="recoverUser"
							>
								<input
									name="id"
									:value="user.id"
									type="hidden"
									class="form-control"
								>
								<button
									id="deleteUserButton"
									type="submit"
									class="btn btn-success"
								>
									Да
								</button>
								<button
									type="reset"
									class="btn btn-primary"
									data-dismiss="modal"
								>
									Нет
								</button>
							</form>
						</div>
					</div>
				</div>
			</b-modal>
		</template>

		<div
			v-if="isBeforeSubmit"
			id="beforeSubmit"
			class="modal modal-active"
			tabindex="-1"
			role="dialog"
			@click="isBeforeSubmit = false"
		/>
		<AwardUserSidebar />

		<div
			class="svg-icons"
			style="display: none !important;"
		>
			<svg
				id="zip-icon"
				viewBox="0 0 512 512"
				x="0px"
				y="0px"
				style="enable-background:new 0 0 512 512;padding: 20px;"
				xml:space="preserve"
			><path
				style="fill:#E2E5E7;"
				d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"
			/><path
				style="fill:#B0B7BD;"
				d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"
			/><polygon
				style="fill:#CAD1D8;"
				points="480,224 384,128 480,128 "
			/><path
				style="fill:rgb(220 54 70);"
				d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16  V416z"
			/><g><path
				style="fill:#FFFFFF;"
				d="M132.64,384c-8.064,0-11.264-7.792-6.656-13.296l45.552-60.512h-37.76   c-11.12,0-10.224-15.712,0-15.712h51.568c9.712,0,12.528,9.184,5.632,16.624l-43.632,56.656h41.584   c10.24,0,11.52,16.256-1.008,16.256h-55.28V384z"
			/><path
				style="fill:#FFFFFF;"
				d="M212.048,303.152c0-10.496,16.896-10.88,16.896,0v73.04c0,10.608-16.896,10.88-16.896,0V303.152z"
			/><path
				style="fill:#FFFFFF;"
				d="M251.616,303.152c0-4.224,3.328-8.832,8.704-8.832h29.552c16.64,0,31.616,11.136,31.616,32.48   c0,20.224-14.976,31.488-31.616,31.488h-21.36v16.896c0,5.632-3.584,8.816-8.192,8.816c-4.224,0-8.704-3.184-8.704-8.816   L251.616,303.152L251.616,303.152z M268.496,310.432v31.872h21.36c8.576,0,15.36-7.568,15.36-15.504   c0-8.944-6.784-16.368-15.36-16.368H268.496z"
			/></g><path
				style="fill:#CAD1D8;"
				d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"
			/></svg>

			<svg
				id="xls-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#E2E5E7;"
					d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"
				/>
				<path
					style="fill:#B0B7BD;"
					d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"
				/>
				<polygon
					style="fill:#CAD1D8;"
					points="480,224 384,128 480,128 "
				/>
				<path
					style="fill:#84BD5A;"
					d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16  V416z"
				/>
				<g>
					<path
						style="fill:#FFFFFF;"
						d="M144.336,326.192l22.256-27.888c6.656-8.704,19.584,2.416,12.288,10.736   c-7.664,9.088-15.728,18.944-23.408,29.04l26.096,32.496c7.04,9.6-7.024,18.8-13.936,9.328l-23.552-30.192l-23.152,30.848   c-6.528,9.328-20.992-1.152-13.696-9.856l25.712-32.624c-8.064-10.112-15.872-19.952-23.664-29.04   c-8.048-9.6,6.912-19.44,12.8-10.464L144.336,326.192z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M197.36,303.152c0-4.224,3.584-7.808,8.064-7.808c4.096,0,7.552,3.6,7.552,7.808v64.096h34.8   c12.528,0,12.8,16.752,0,16.752H205.44c-4.48,0-8.064-3.184-8.064-7.792v-73.056H197.36z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M272.032,314.672c2.944-24.832,40.416-29.296,58.08-15.728c8.704,7.024-0.512,18.16-8.192,12.528   c-9.472-6-30.96-8.816-33.648,4.464c-3.456,20.992,52.192,8.976,51.296,43.008c-0.896,32.496-47.968,33.248-65.632,18.672   c-4.24-3.456-4.096-9.072-1.792-12.544c3.328-3.312,7.024-4.464,11.392-0.88c10.48,7.152,37.488,12.528,39.392-5.648   C321.28,339.632,268.064,351.008,272.032,314.672z"
					/>
				</g>
				<path
					style="fill:#CAD1D8;"
					d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"
				/>
			</svg>

			<svg
				id="xlsx-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#E2E5E7;"
					d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"
				/>
				<path
					style="fill:#B0B7BD;"
					d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"
				/>
				<polygon
					style="fill:#CAD1D8;"
					points="480,224 384,128 480,128 "
				/>
				<path
					style="fill:#84BD5A;"
					d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16  V416z"
				/>
				<g>
					<path
						style="fill:#FFFFFF;"
						d="M144.336,326.192l22.256-27.888c6.656-8.704,19.584,2.416,12.288,10.736   c-7.664,9.088-15.728,18.944-23.408,29.04l26.096,32.496c7.04,9.6-7.024,18.8-13.936,9.328l-23.552-30.192l-23.152,30.848   c-6.528,9.328-20.992-1.152-13.696-9.856l25.712-32.624c-8.064-10.112-15.872-19.952-23.664-29.04   c-8.048-9.6,6.912-19.44,12.8-10.464L144.336,326.192z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M197.36,303.152c0-4.224,3.584-7.808,8.064-7.808c4.096,0,7.552,3.6,7.552,7.808v64.096h34.8   c12.528,0,12.8,16.752,0,16.752H205.44c-4.48,0-8.064-3.184-8.064-7.792v-73.056H197.36z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M272.032,314.672c2.944-24.832,40.416-29.296,58.08-15.728c8.704,7.024-0.512,18.16-8.192,12.528   c-9.472-6-30.96-8.816-33.648,4.464c-3.456,20.992,52.192,8.976,51.296,43.008c-0.896,32.496-47.968,33.248-65.632,18.672   c-4.24-3.456-4.096-9.072-1.792-12.544c3.328-3.312,7.024-4.464,11.392-0.88c10.48,7.152,37.488,12.528,39.392-5.648   C321.28,339.632,268.064,351.008,272.032,314.672z"
					/>
				</g>
				<path
					style="fill:#CAD1D8;"
					d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"
				/>
			</svg>

			<svg
				id="docx-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#E1E6E9;"
					d="M337.335,0H95.219C84.874,0,76.488,8.386,76.488,18.732v430.829c0,10.345,8.386,18.732,18.732,18.732  H401.17c10.345,0,18.732-8.386,18.732-18.732V82.567L337.335,0z"
				/>
				<rect
					x="48.39"
					y="258.067"
					style="fill:#27A2DB;"
					width="371.512"
					height="128.3"
				/>
				<g>
					<path
						style="fill:#EBF0F3;"
						d="M182.722,293.744c7.567,6.85,11.342,16.377,11.342,28.583c0,12.201-3.665,21.861-11.004,28.971   c-7.339,7.115-18.571,10.67-33.687,10.67h-26.056v-78.501h26.952C164.343,283.467,175.164,286.894,182.722,293.744z    M180.702,322.66c0-17.968-10.291-26.952-30.881-26.952h-13.252v53.793h14.714c9.505,0,16.789-2.262,21.843-6.795   C178.179,338.179,180.702,331.498,180.702,322.66z"
					/>
					<path
						style="fill:#EBF0F3;"
						d="M276.828,351.129c-7.933,7.75-17.739,11.625-29.419,11.625s-21.486-3.875-29.419-11.625   c-7.942-7.745-11.908-17.406-11.908-28.971c0-11.57,3.966-21.226,11.908-28.976c7.933-7.75,17.739-11.62,29.419-11.62   s21.486,3.87,29.419,11.62c7.942,7.75,11.908,17.406,11.908,28.976C288.736,333.723,284.77,343.383,276.828,351.129z    M267.122,301.997c-5.356-5.538-11.927-8.307-19.713-8.307c-7.787,0-14.358,2.769-19.713,8.307   c-5.346,5.543-8.024,12.26-8.024,20.161s2.678,14.618,8.024,20.156c5.356,5.543,11.927,8.312,19.713,8.312   c7.787,0,14.358-2.769,19.713-8.312c5.346-5.538,8.024-12.256,8.024-20.156S272.469,307.539,267.122,301.997z"
					/>
					<path
						style="fill:#EBF0F3;"
						d="M341.296,349.95c4.56,0,8.49-0.763,11.79-2.298c3.29-1.535,6.736-3.989,10.336-7.357l8.527,8.76   c-8.308,9.208-18.397,13.814-30.26,13.814c-11.872,0-21.715-3.82-29.538-11.456c-7.823-7.636-11.735-17.296-11.735-28.976   s3.985-21.409,11.963-29.2c7.969-7.782,18.041-11.675,30.205-11.675s22.327,4.492,30.488,13.476l-8.417,9.208   c-3.747-3.592-7.284-6.1-10.62-7.526c-3.327-1.421-7.238-2.134-11.735-2.134c-7.933,0-14.595,2.568-19.987,7.695   c-5.392,5.127-8.088,11.68-8.088,19.654c0,7.974,2.678,14.636,8.033,19.987C327.615,347.277,333.957,349.95,341.296,349.95z"
					/>
				</g>
				<polygon
					style="fill:#2D93BA;"
					points="48.39,386.364 76.488,412.491 76.488,386.364 "
				/>
				<polygon
					style="fill:#EBF0F3;"
					points="337.336,82.567 419.902,82.567 337.335,0 "
				/>
				<polygon
					style="fill:#D5D6DB;"
					points="353.221,82.567 419.902,121.255 419.902,82.567 "
				/>
			</svg>

			<svg
				id="doc-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#E1E6E9;"
					d="M337.335,0H95.219C84.874,0,76.488,8.386,76.488,18.732v430.829c0,10.345,8.386,18.732,18.732,18.732  H401.17c10.345,0,18.732-8.386,18.732-18.732V82.567L337.335,0z"
				/>
				<rect
					x="48.39"
					y="258.067"
					style="fill:#27A2DB;"
					width="371.512"
					height="128.3"
				/>
				<g>
					<path
						style="fill:#EBF0F3;"
						d="M182.722,293.744c7.567,6.85,11.342,16.377,11.342,28.583c0,12.201-3.665,21.861-11.004,28.971   c-7.339,7.115-18.571,10.67-33.687,10.67h-26.056v-78.501h26.952C164.343,283.467,175.164,286.894,182.722,293.744z    M180.702,322.66c0-17.968-10.291-26.952-30.881-26.952h-13.252v53.793h14.714c9.505,0,16.789-2.262,21.843-6.795   C178.179,338.179,180.702,331.498,180.702,322.66z"
					/>
					<path
						style="fill:#EBF0F3;"
						d="M276.828,351.129c-7.933,7.75-17.739,11.625-29.419,11.625s-21.486-3.875-29.419-11.625   c-7.942-7.745-11.908-17.406-11.908-28.971c0-11.57,3.966-21.226,11.908-28.976c7.933-7.75,17.739-11.62,29.419-11.62   s21.486,3.87,29.419,11.62c7.942,7.75,11.908,17.406,11.908,28.976C288.736,333.723,284.77,343.383,276.828,351.129z    M267.122,301.997c-5.356-5.538-11.927-8.307-19.713-8.307c-7.787,0-14.358,2.769-19.713,8.307   c-5.346,5.543-8.024,12.26-8.024,20.161s2.678,14.618,8.024,20.156c5.356,5.543,11.927,8.312,19.713,8.312   c7.787,0,14.358-2.769,19.713-8.312c5.346-5.538,8.024-12.256,8.024-20.156S272.469,307.539,267.122,301.997z"
					/>
					<path
						style="fill:#EBF0F3;"
						d="M341.296,349.95c4.56,0,8.49-0.763,11.79-2.298c3.29-1.535,6.736-3.989,10.336-7.357l8.527,8.76   c-8.308,9.208-18.397,13.814-30.26,13.814c-11.872,0-21.715-3.82-29.538-11.456c-7.823-7.636-11.735-17.296-11.735-28.976   s3.985-21.409,11.963-29.2c7.969-7.782,18.041-11.675,30.205-11.675s22.327,4.492,30.488,13.476l-8.417,9.208   c-3.747-3.592-7.284-6.1-10.62-7.526c-3.327-1.421-7.238-2.134-11.735-2.134c-7.933,0-14.595,2.568-19.987,7.695   c-5.392,5.127-8.088,11.68-8.088,19.654c0,7.974,2.678,14.636,8.033,19.987C327.615,347.277,333.957,349.95,341.296,349.95z"
					/>
				</g>
				<polygon
					style="fill:#2D93BA;"
					points="48.39,386.364 76.488,412.491 76.488,386.364 "
				/>
				<polygon
					style="fill:#EBF0F3;"
					points="337.336,82.567 419.902,82.567 337.335,0 "
				/>
				<polygon
					style="fill:#D5D6DB;"
					points="353.221,82.567 419.902,121.255 419.902,82.567 "
				/>
			</svg>

			<svg
				id="ppt-icon"
				viewBox="0 0 512 512"
			>
				<g>
					<path
						style="fill:#E9E9E0;"
						d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074   c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"
					/>
					<polygon
						style="fill:#D9D7CA;"
						points="37.5,0.151 37.5,12 49.349,12  "
					/>
					<path
						style="fill:#F6712E;"
						d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"
					/>
					<g>
						<path
							style="fill:#FFFFFF;"
							d="M17.581,53H15.94V42.924h2.898c0.428,0,0.852,0.068,1.271,0.205    c0.419,0.137,0.795,0.342,1.128,0.615c0.333,0.273,0.602,0.604,0.807,0.991s0.308,0.822,0.308,1.306    c0,0.511-0.087,0.973-0.26,1.388c-0.173,0.415-0.415,0.764-0.725,1.046c-0.31,0.282-0.684,0.501-1.121,0.656    s-0.921,0.232-1.449,0.232h-1.217V53z M17.581,44.168v3.992h1.504c0.2,0,0.398-0.034,0.595-0.103    c0.196-0.068,0.376-0.18,0.54-0.335s0.296-0.371,0.396-0.649c0.1-0.278,0.15-0.622,0.15-1.032c0-0.164-0.023-0.354-0.068-0.567    c-0.046-0.214-0.139-0.419-0.28-0.615c-0.142-0.196-0.34-0.36-0.595-0.492c-0.255-0.132-0.593-0.198-1.012-0.198H17.581z"
						/>
						<path
							style="fill:#FFFFFF;"
							d="M25.853,53h-1.641V42.924h2.898c0.428,0,0.852,0.068,1.271,0.205    c0.419,0.137,0.795,0.342,1.128,0.615c0.333,0.273,0.602,0.604,0.807,0.991s0.308,0.822,0.308,1.306    c0,0.511-0.087,0.973-0.26,1.388c-0.173,0.415-0.415,0.764-0.725,1.046c-0.31,0.282-0.684,0.501-1.121,0.656    s-0.921,0.232-1.449,0.232h-1.217V53z M25.853,44.168v3.992h1.504c0.2,0,0.398-0.034,0.595-0.103    c0.196-0.068,0.376-0.18,0.54-0.335s0.296-0.371,0.396-0.649c0.1-0.278,0.15-0.622,0.15-1.032c0-0.164-0.023-0.354-0.068-0.567    c-0.046-0.214-0.139-0.419-0.28-0.615c-0.142-0.196-0.34-0.36-0.595-0.492c-0.255-0.132-0.593-0.198-1.012-0.198H25.853z"
						/>
						<path
							style="fill:#FFFFFF;"
							d="M39.606,42.924v1.121h-3.008V53h-1.654v-8.955h-3.008v-1.121H39.606z"
						/>
					</g>
					<path
						style="fill:#C8BDB8;"
						d="M39.5,30h-24V14h24V30z M17.5,28h20V16h-20V28z"
					/>
					<path
						style="fill:#C8BDB8;"
						d="M20.499,35c-0.175,0-0.353-0.046-0.514-0.143c-0.474-0.284-0.627-0.898-0.343-1.372l3-5   c0.284-0.474,0.898-0.627,1.372-0.343c0.474,0.284,0.627,0.898,0.343,1.372l-3,5C21.17,34.827,20.839,35,20.499,35z"
					/>
					<path
						style="fill:#C8BDB8;"
						d="M34.501,35c-0.34,0-0.671-0.173-0.858-0.485l-3-5c-0.284-0.474-0.131-1.088,0.343-1.372   c0.474-0.283,1.088-0.131,1.372,0.343l3,5c0.284,0.474,0.131,1.088-0.343,1.372C34.854,34.954,34.676,35,34.501,35z"
					/>
					<path
						style="fill:#C8BDB8;"
						d="M27.5,16c-0.552,0-1-0.447-1-1v-3c0-0.553,0.448-1,1-1s1,0.447,1,1v3C28.5,15.553,28.052,16,27.5,16   z"
					/>
					<rect
						x="17.5"
						y="16"
						style="fill:#D3CCC9;"
						width="20"
						height="12"
					/>
				</g>
			</svg>

			<svg
				id="pptx-icon"
				viewBox="0 0 512 512"
			>
				<g>
					<path
						style="fill:#E9E9E0;"
						d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074   c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"
					/>
					<polygon
						style="fill:#D9D7CA;"
						points="37.5,0.151 37.5,12 49.349,12  "
					/>
					<path
						style="fill:#F6712E;"
						d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"
					/>
					<g>
						<path
							style="fill:#FFFFFF;"
							d="M17.581,53H15.94V42.924h2.898c0.428,0,0.852,0.068,1.271,0.205    c0.419,0.137,0.795,0.342,1.128,0.615c0.333,0.273,0.602,0.604,0.807,0.991s0.308,0.822,0.308,1.306    c0,0.511-0.087,0.973-0.26,1.388c-0.173,0.415-0.415,0.764-0.725,1.046c-0.31,0.282-0.684,0.501-1.121,0.656    s-0.921,0.232-1.449,0.232h-1.217V53z M17.581,44.168v3.992h1.504c0.2,0,0.398-0.034,0.595-0.103    c0.196-0.068,0.376-0.18,0.54-0.335s0.296-0.371,0.396-0.649c0.1-0.278,0.15-0.622,0.15-1.032c0-0.164-0.023-0.354-0.068-0.567    c-0.046-0.214-0.139-0.419-0.28-0.615c-0.142-0.196-0.34-0.36-0.595-0.492c-0.255-0.132-0.593-0.198-1.012-0.198H17.581z"
						/>
						<path
							style="fill:#FFFFFF;"
							d="M25.853,53h-1.641V42.924h2.898c0.428,0,0.852,0.068,1.271,0.205    c0.419,0.137,0.795,0.342,1.128,0.615c0.333,0.273,0.602,0.604,0.807,0.991s0.308,0.822,0.308,1.306    c0,0.511-0.087,0.973-0.26,1.388c-0.173,0.415-0.415,0.764-0.725,1.046c-0.31,0.282-0.684,0.501-1.121,0.656    s-0.921,0.232-1.449,0.232h-1.217V53z M25.853,44.168v3.992h1.504c0.2,0,0.398-0.034,0.595-0.103    c0.196-0.068,0.376-0.18,0.54-0.335s0.296-0.371,0.396-0.649c0.1-0.278,0.15-0.622,0.15-1.032c0-0.164-0.023-0.354-0.068-0.567    c-0.046-0.214-0.139-0.419-0.28-0.615c-0.142-0.196-0.34-0.36-0.595-0.492c-0.255-0.132-0.593-0.198-1.012-0.198H25.853z"
						/>
						<path
							style="fill:#FFFFFF;"
							d="M39.606,42.924v1.121h-3.008V53h-1.654v-8.955h-3.008v-1.121H39.606z"
						/>
					</g>
					<path
						style="fill:#C8BDB8;"
						d="M39.5,30h-24V14h24V30z M17.5,28h20V16h-20V28z"
					/>
					<path
						style="fill:#C8BDB8;"
						d="M20.499,35c-0.175,0-0.353-0.046-0.514-0.143c-0.474-0.284-0.627-0.898-0.343-1.372l3-5   c0.284-0.474,0.898-0.627,1.372-0.343c0.474,0.284,0.627,0.898,0.343,1.372l-3,5C21.17,34.827,20.839,35,20.499,35z"
					/>
					<path
						style="fill:#C8BDB8;"
						d="M34.501,35c-0.34,0-0.671-0.173-0.858-0.485l-3-5c-0.284-0.474-0.131-1.088,0.343-1.372   c0.474-0.283,1.088-0.131,1.372,0.343l3,5c0.284,0.474,0.131,1.088-0.343,1.372C34.854,34.954,34.676,35,34.501,35z"
					/>
					<path
						style="fill:#C8BDB8;"
						d="M27.5,16c-0.552,0-1-0.447-1-1v-3c0-0.553,0.448-1,1-1s1,0.447,1,1v3C28.5,15.553,28.052,16,27.5,16   z"
					/>
					<rect
						x="17.5"
						y="16"
						style="fill:#D3CCC9;"
						width="20"
						height="12"
					/>
				</g>
			</svg>

			<svg
				id="rar-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#ECEDEF;"
					d="M100.641,0c-14.139,0-25.6,11.461-25.6,25.6v460.8c0,14.139,11.461,25.6,25.6,25.6h375.467  c14.139,0,25.6-11.461,25.6-25.6V85.333L416.375,0H100.641z"
				/>
				<path
					style="fill:#D9DCDF;"
					d="M441.975,85.333h59.733L416.375,0v59.733C416.375,73.872,427.836,85.333,441.975,85.333z"
				/>
				<path
					style="fill:#C6CACF;"
					d="M399.308,42.667H75.041v153.6h324.267c4.713,0,8.533-3.821,8.533-8.533V51.2  C407.841,46.487,404.02,42.667,399.308,42.667z"
				/>
				<path
					style="fill:#C4DF64;"
					d="M382.241,179.2H18.843c-7.602,0-11.41-9.191-6.034-14.567L75.041,102.4L12.809,40.167  C7.433,34.791,11.241,25.6,18.843,25.6h363.398c4.713,0,8.533,3.821,8.533,8.533v136.533  C390.775,175.379,386.954,179.2,382.241,179.2z"
				/>
				<g>
					<path
						style="fill:#FFFFFF;"
						d="M185.975,76.8h-34.133c-4.713,0-8.533,3.821-8.533,8.533v51.2c0,4.713,3.821,8.533,8.533,8.533   c4.713,0,8.533-3.821,8.533-8.533v-17.067h3.967l14.533,21.801c1.645,2.467,4.35,3.801,7.108,3.801   c6.711,0,10.793-7.718,7.092-13.267l-8.223-12.334h1.122c4.713,0,8.533-3.821,8.533-8.533v-25.6   C194.508,80.621,190.688,76.8,185.975,76.8z M160.375,93.867h17.067v8.533h-17.067V93.867z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M313.975,110.933v-25.6c0-4.713-3.821-8.533-8.533-8.533h-34.133c-4.713,0-8.533,3.821-8.533,8.533   v51.2c0,4.713,3.821,8.533,8.533,8.533c4.713,0,8.533-3.821,8.533-8.533v-17.067h3.967l14.533,21.801   c1.644,2.467,4.35,3.801,7.108,3.801c6.711,0,10.791-7.719,7.091-13.267l-8.222-12.334h1.122   C310.154,119.467,313.975,115.646,313.975,110.933z M279.841,93.867h17.067v8.533h-17.067V93.867z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M236.737,82.635c-1.162-3.485-4.422-5.835-8.095-5.835s-6.933,2.351-8.095,5.835l-17.067,51.2   c-1.49,4.47,0.926,9.303,5.397,10.794c4.471,1.489,9.303-0.927,10.794-5.397l8.972-26.913l8.972,26.913   c1.191,3.577,4.522,5.838,8.094,5.837c5.742,0,9.907-5.801,8.097-11.234L236.737,82.635z"
					/>
				</g>
				<path
					style="fill:#FFC44F;"
					d="M228.641,349.867h153.6V256h-153.6c-18.851,0-34.133,15.282-34.133,34.133v25.6  C194.508,334.585,209.79,349.867,228.641,349.867z"
				/>
				<path
					style="fill:#FFD791;"
					d="M224.375,332.8c-7.069,0-12.8-5.731-12.8-12.8v-34.133c0-7.069,5.731-12.8,12.8-12.8h157.867V332.8  H224.375z"
				/>
				<path
					style="fill:#DB6B5E;"
					d="M399.308,358.4h-179.2c-18.821,0-34.133-15.312-34.133-34.133V281.6  c0-18.821,15.312-34.133,34.133-34.133h179.2c4.713,0,8.533,3.821,8.533,8.533c0,4.713-3.821,8.533-8.533,8.533h-179.2  c-9.41,0-17.067,7.657-17.067,17.067v42.667c0,9.41,7.657,17.067,17.067,17.067h179.2c4.713,0,8.533,3.821,8.533,8.533  C407.841,354.579,404.02,358.4,399.308,358.4z"
				/>
				<g>
					<path
						style="fill:#F79F4D;"
						d="M228.641,290.133c0,4.713,3.821,8.533,8.533,8.533h145.067V281.6H237.175   C232.462,281.6,228.641,285.421,228.641,290.133z"
					/>
					<path
						style="fill:#F79F4D;"
						d="M237.175,307.2c-4.713,0-8.533,3.821-8.533,8.533c0,4.713,3.821,8.533,8.533,8.533h145.067V307.2   H237.175z"
					/>
				</g>
				<path
					style="fill:#FFC44F;"
					d="M228.641,460.8h153.6v-93.867h-153.6c-18.851,0-34.133,15.282-34.133,34.133v25.6  C194.508,445.518,209.79,460.8,228.641,460.8z"
				/>
				<path
					style="fill:#FFD791;"
					d="M224.375,443.733c-7.069,0-12.8-5.731-12.8-12.8V396.8c0-7.069,5.731-12.8,12.8-12.8h157.867v59.733  H224.375z"
				/>
				<path
					style="fill:#FF8C78;"
					d="M399.308,469.333h-179.2c-18.821,0-34.133-15.312-34.133-34.133v-42.667  c0-18.821,15.312-34.133,34.133-34.133h179.2c4.713,0,8.533,3.821,8.533,8.533s-3.821,8.533-8.533,8.533h-179.2  c-9.41,0-17.067,7.657-17.067,17.067V435.2c0,9.41,7.657,17.067,17.067,17.067h179.2c4.713,0,8.533,3.821,8.533,8.533  S404.02,469.333,399.308,469.333z"
				/>
				<g>
					<path
						style="fill:#F79F4D;"
						d="M228.641,401.067c0,4.713,3.821,8.533,8.533,8.533h145.067v-17.067H237.175   C232.462,392.533,228.641,396.354,228.641,401.067z"
					/>
					<path
						style="fill:#F79F4D;"
						d="M237.175,418.133c-4.713,0-8.533,3.821-8.533,8.533s3.821,8.533,8.533,8.533h145.067v-17.067   H237.175z"
					/>
				</g>
				<path
					style="fill:#55606E;"
					d="M357.821,217.2c-3.526-5.859-9.123-9.994-15.759-11.643c-6.637-1.648-13.518-0.615-19.376,2.911  c-6.329,3.809-16.24,17.498-23.354,28.164c-7.113-10.667-17.025-24.355-23.353-28.164c-12.098-7.281-27.855-3.362-35.136,8.731  c-7.279,12.094-3.362,27.856,8.732,35.135c6.834,4.114,25.812,6.529,38.8,7.745v209.43h17.067V260.506  c12.538-1.026,35.899-3.505,43.649-8.171c5.858-3.526,9.993-9.123,11.642-15.759C362.381,229.939,361.346,223.058,357.821,217.2z   M258.377,237.713c-4.031-2.427-5.338-7.681-2.911-11.711c2.393-3.973,7.726-5.311,11.707-2.914  c2.58,1.618,8.833,9.723,15.355,19.203C271.099,240.967,261.013,239.235,258.377,237.713z M344.169,232.461  c-0.549,2.212-1.927,4.078-3.874,5.249c-2.638,1.522-12.725,3.255-24.156,4.579c6.521-9.481,12.772-17.582,15.348-19.198  c3.987-2.401,9.314-1.071,11.711,2.911C344.373,227.955,344.718,230.249,344.169,232.461z"
				/>
			</svg>

			<svg
				id="pdf-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#E2E5E7;"
					d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"
				/>
				<path
					style="fill:#B0B7BD;"
					d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"
				/>
				<polygon
					style="fill:#CAD1D8;"
					points="480,224 384,128 480,128 "
				/>
				<path
					style="fill:#F15642;"
					d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16  V416z"
				/>
				<g>
					<path
						style="fill:#FFFFFF;"
						d="M101.744,303.152c0-4.224,3.328-8.832,8.688-8.832h29.552c16.64,0,31.616,11.136,31.616,32.48   c0,20.224-14.976,31.488-31.616,31.488h-21.36v16.896c0,5.632-3.584,8.816-8.192,8.816c-4.224,0-8.688-3.184-8.688-8.816V303.152z    M118.624,310.432v31.872h21.36c8.576,0,15.36-7.568,15.36-15.504c0-8.944-6.784-16.368-15.36-16.368H118.624z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M196.656,384c-4.224,0-8.832-2.304-8.832-7.92v-72.672c0-4.592,4.608-7.936,8.832-7.936h29.296   c58.464,0,57.184,88.528,1.152,88.528H196.656z M204.72,311.088V368.4h21.232c34.544,0,36.08-57.312,0-57.312H204.72z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M303.872,312.112v20.336h32.624c4.608,0,9.216,4.608,9.216,9.072c0,4.224-4.608,7.68-9.216,7.68   h-32.624v26.864c0,4.48-3.184,7.92-7.664,7.92c-5.632,0-9.072-3.44-9.072-7.92v-72.672c0-4.592,3.456-7.936,9.072-7.936h44.912   c5.632,0,8.96,3.344,8.96,7.936c0,4.096-3.328,8.704-8.96,8.704h-37.248V312.112z"
					/>
				</g>
				<path
					style="fill:#CAD1D8;"
					d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"
				/>
			</svg>

			<svg
				id="png-icon"
				viewBox="0 0 512 512"
			>
				<path
					style="fill:#E2E5E7;"
					d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"
				/>
				<path
					style="fill:#B0B7BD;"
					d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"
				/>
				<polygon
					style="fill:#CAD1D8;"
					points="480,224 384,128 480,128 "
				/>
				<path
					style="fill:#A066AA;"
					d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16  V416z"
				/>
				<g>
					<path
						style="fill:#FFFFFF;"
						d="M92.816,303.152c0-4.224,3.312-8.848,8.688-8.848h29.568c16.624,0,31.6,11.136,31.6,32.496   c0,20.224-14.976,31.472-31.6,31.472H109.68v16.896c0,5.648-3.552,8.832-8.176,8.832c-4.224,0-8.688-3.184-8.688-8.832   C92.816,375.168,92.816,303.152,92.816,303.152z M109.68,310.432v31.856h21.376c8.56,0,15.344-7.552,15.344-15.488   c0-8.96-6.784-16.368-15.344-16.368L109.68,310.432L109.68,310.432z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M178.976,304.432c0-4.624,1.024-9.088,7.68-9.088c4.592,0,5.632,1.152,9.072,4.464l42.336,52.976   v-49.632c0-4.224,3.696-8.848,8.064-8.848c4.608,0,9.072,4.624,9.072,8.848v72.016c0,5.648-3.456,7.792-6.784,8.832   c-4.464,0-6.656-1.024-10.352-4.464l-42.336-53.744v49.392c0,5.648-3.456,8.832-8.064,8.832s-8.704-3.184-8.704-8.832v-70.752   H178.976z"
					/>
					<path
						style="fill:#FFFFFF;"
						d="M351.44,374.16c-9.088,7.536-20.224,10.752-31.472,10.752c-26.88,0-45.936-15.36-45.936-45.808   c0-25.84,20.096-45.92,47.072-45.92c10.112,0,21.232,3.456,29.168,11.264c7.808,7.664-3.456,19.056-11.12,12.288   c-4.736-4.624-11.392-8.064-18.048-8.064c-15.472,0-30.432,12.4-30.432,30.432c0,18.944,12.528,30.448,29.296,30.448   c7.792,0,14.448-2.304,19.184-5.76V348.08h-19.184c-11.392,0-10.24-15.632,0-15.632h25.584c4.736,0,9.072,3.6,9.072,7.568v27.248   C354.624,369.552,353.616,371.712,351.44,374.16z"
					/>
				</g>
				<path
					style="fill:#CAD1D8;"
					d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"
				/>
			</svg>

			<svg
				id="download-icon"
				viewBox="0 0 20 17"
			>
				<path
					d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"
					style=""
				/>
			</svg>

			<svg
				id="jpg-icon"
				viewBox="0 0 58 58"
			>
				<g
					id="Page-1"
					fill="none"
					fill-rule="evenodd"
				><g
					id="040---JPG"
					fill-rule="nonzero"
				><path
					id="Shape"
					d="m46 14v43c0 .5522847-.4477153 1-1 1h-44c-.55228475 0-1-.4477153-1-1v-56c0-.55228475.44771525-1 1-1h31z"
					fill="#2fa8cc"
				/><path
					id="Shape"
					d="m33 14h13l-14-14v13c0 .5522847.4477153 1 1 1z"
					fill="#67b9cc"
				/><path
					id="Shape"
					d="m28.86 29.72-17.86 28.28h-10c-.55228475 0-1-.4477153-1-1v-13.81l11.16-16.74 5.71-8.57c.3616676-.539975.9645441-.8689524 1.614339-.8809118.649795-.0119594 1.2643687.2946109 1.645661.8209118l6.05 8.25z"
					fill="#3f5c6c"
				/><path
					id="Shape"
					d="m46 37.6v19.4c0 .5522847-.4477153 1-1 1h-34l17.86-28.28.04-.06 4.4-6.97c.3691068-.5803816 1.0099482-.9310557 1.6977552-.9290267.6878071.0020289 1.3265684.3564776 1.6922448.9390267l4.37 7z"
					fill="#35495e"
				/><rect
					id="Rectangle-path"
					fill="#f0c419"
					height="18"
					rx="1"
					width="42"
					x="16"
					y="34"
				/><circle
					id="Oval"
					cx="11"
					cy="11"
					fill="#f0c419"
					r="3"
				/><g fill="#f3d55b"><path
					id="Shape"
					d="m11 6c-.5522847 0-1-.44771525-1-1v-1c0-.55228475.4477153-1 1-1s1 .44771525 1 1v1c0 .55228475-.4477153 1-1 1z"
				/><path
					id="Shape"
					d="m5 12h-1c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1h1c.55228475 0 1 .4477153 1 1s-.44771525 1-1 1z"
				/><path
					id="Shape"
					d="m11 19c-.5522847 0-1-.4477153-1-1v-1c0-.5522847.4477153-1 1-1s1 .4477153 1 1v1c0 .5522847-.4477153 1-1 1z"
				/><path
					id="Shape"
					d="m18 12h-1c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1h1c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1z"
				/></g><path
					id="Shape"
					d="m30 37h-6c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h2v6.5c0 .8284271-.6715729 1.5-1.5 1.5s-1.5-.6715729-1.5-1.5v-.5c0-.5522847-.4477153-1-1-1s-1 .4477153-1 1v.5c.0000001 1.9329966 1.5670034 3.4999999 3.5 3.4999999s3.4999999-1.5670033 3.5-3.4999999v-6.5h2c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
					fill="#3d324c"
				/><path
					id="Shape"
					d="m37.5 37h-3.5c-.5522847 0-1 .4477153-1 1v10c0 .5522847.4477153 1 1 1s1-.4477153 1-1v-4h2.5c1.9329966 0 3.5-1.5670034 3.5-3.5s-1.5670034-3.5-3.5-3.5zm0 5h-2.5v-3h2.5c.8284271 0 1.5.6715729 1.5 1.5s-.6715729 1.5-1.5 1.5z"
					fill="#3d324c"
				/><path
					id="Shape"
					d="m52 43h-4c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h1v1c0 .5522847-.4477153 1-1 1h-2c-.5522847 0-1-.4477153-1-1v-6c0-.5522847.4477153-1 1-1h2c.5522847 0 1 .4477153 1 1s.4477153 1 1 1 1-.4477153 1-1c0-1.6568542-1.3431458-3-3-3h-2c-1.6568542 0-3 1.3431458-3 3v6c0 1.6568542 1.3431458 3 3 3h2c1.6568542 0 3-1.3431458 3-3v-1h1c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
					fill="#3d324c"
				/><path
					id="Shape"
					d="m26.18 26.07-2.77 3.15c-.1787951.2050126-.4333938.3283393-.7050984.3415471-.2717046.0132079-.5370647-.084843-.7349016-.2715471l-2.78-2.64c-.3896281-.3598394-.9903719-.3598394-1.38 0l-2.78 2.64c-.1978369.1867041-.463197.284755-.7349016.2715471-.2717046-.0132078-.5263033-.1365345-.7050984-.3415471l-2.43-2.77 5.71-8.57c.3616676-.539975.9645441-.8689524 1.614339-.8809118.649795-.0119594 1.2643687.2946109 1.645661.8209118z"
					fill="#d1d4d1"
				/><path
					id="Shape"
					d="m41.06 29.7-.89.96c-.4048186.4295623-1.0774137.4605371-1.52.07l-2.93-2.64c-.41331-.3578334-1.02669-.3578334-1.44 0l-2.93 2.64c-.4425863.3905371-1.1151814.3595623-1.52-.07l-.93-1 4.4-6.97c.3691068-.5803816 1.0099482-.9310557 1.6977552-.9290267.6878071.0020289 1.3265684.3564776 1.6922448.9390267z"
					fill="#ecf0f1"
				/></g></g>
			</svg>
			<svg
				id="jpeg-icon"
				viewBox="0 0 58 58"
			>
				<g
					id="Page-1"
					fill="none"
					fill-rule="evenodd"
				><g
					id="040---JPG"
					fill-rule="nonzero"
				><path
					id="Shape"
					d="m46 14v43c0 .5522847-.4477153 1-1 1h-44c-.55228475 0-1-.4477153-1-1v-56c0-.55228475.44771525-1 1-1h31z"
					fill="#2fa8cc"
				/><path
					id="Shape"
					d="m33 14h13l-14-14v13c0 .5522847.4477153 1 1 1z"
					fill="#67b9cc"
				/><path
					id="Shape"
					d="m28.86 29.72-17.86 28.28h-10c-.55228475 0-1-.4477153-1-1v-13.81l11.16-16.74 5.71-8.57c.3616676-.539975.9645441-.8689524 1.614339-.8809118.649795-.0119594 1.2643687.2946109 1.645661.8209118l6.05 8.25z"
					fill="#3f5c6c"
				/><path
					id="Shape"
					d="m46 37.6v19.4c0 .5522847-.4477153 1-1 1h-34l17.86-28.28.04-.06 4.4-6.97c.3691068-.5803816 1.0099482-.9310557 1.6977552-.9290267.6878071.0020289 1.3265684.3564776 1.6922448.9390267l4.37 7z"
					fill="#35495e"
				/><rect
					id="Rectangle-path"
					fill="#f0c419"
					height="18"
					rx="1"
					width="42"
					x="16"
					y="34"
				/><circle
					id="Oval"
					cx="11"
					cy="11"
					fill="#f0c419"
					r="3"
				/><g fill="#f3d55b"><path
					id="Shape"
					d="m11 6c-.5522847 0-1-.44771525-1-1v-1c0-.55228475.4477153-1 1-1s1 .44771525 1 1v1c0 .55228475-.4477153 1-1 1z"
				/><path
					id="Shape"
					d="m5 12h-1c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1h1c.55228475 0 1 .4477153 1 1s-.44771525 1-1 1z"
				/><path
					id="Shape"
					d="m11 19c-.5522847 0-1-.4477153-1-1v-1c0-.5522847.4477153-1 1-1s1 .4477153 1 1v1c0 .5522847-.4477153 1-1 1z"
				/><path
					id="Shape"
					d="m18 12h-1c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1h1c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1z"
				/></g><path
					id="Shape"
					d="m30 37h-6c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h2v6.5c0 .8284271-.6715729 1.5-1.5 1.5s-1.5-.6715729-1.5-1.5v-.5c0-.5522847-.4477153-1-1-1s-1 .4477153-1 1v.5c.0000001 1.9329966 1.5670034 3.4999999 3.5 3.4999999s3.4999999-1.5670033 3.5-3.4999999v-6.5h2c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
					fill="#3d324c"
				/><path
					id="Shape"
					d="m37.5 37h-3.5c-.5522847 0-1 .4477153-1 1v10c0 .5522847.4477153 1 1 1s1-.4477153 1-1v-4h2.5c1.9329966 0 3.5-1.5670034 3.5-3.5s-1.5670034-3.5-3.5-3.5zm0 5h-2.5v-3h2.5c.8284271 0 1.5.6715729 1.5 1.5s-.6715729 1.5-1.5 1.5z"
					fill="#3d324c"
				/><path
					id="Shape"
					d="m52 43h-4c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h1v1c0 .5522847-.4477153 1-1 1h-2c-.5522847 0-1-.4477153-1-1v-6c0-.5522847.4477153-1 1-1h2c.5522847 0 1 .4477153 1 1s.4477153 1 1 1 1-.4477153 1-1c0-1.6568542-1.3431458-3-3-3h-2c-1.6568542 0-3 1.3431458-3 3v6c0 1.6568542 1.3431458 3 3 3h2c1.6568542 0 3-1.3431458 3-3v-1h1c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
					fill="#3d324c"
				/><path
					id="Shape"
					d="m26.18 26.07-2.77 3.15c-.1787951.2050126-.4333938.3283393-.7050984.3415471-.2717046.0132079-.5370647-.084843-.7349016-.2715471l-2.78-2.64c-.3896281-.3598394-.9903719-.3598394-1.38 0l-2.78 2.64c-.1978369.1867041-.463197.284755-.7349016.2715471-.2717046-.0132078-.5263033-.1365345-.7050984-.3415471l-2.43-2.77 5.71-8.57c.3616676-.539975.9645441-.8689524 1.614339-.8809118.649795-.0119594 1.2643687.2946109 1.645661.8209118z"
					fill="#d1d4d1"
				/><path
					id="Shape"
					d="m41.06 29.7-.89.96c-.4048186.4295623-1.0774137.4605371-1.52.07l-2.93-2.64c-.41331-.3578334-1.02669-.3578334-1.44 0l-2.93 2.64c-.4425863.3905371-1.1151814.3595623-1.52-.07l-.93-1 4.4-6.97c.3691068-.5803816 1.0099482-.9310557 1.6977552-.9290267.6878071.0020289 1.3265684.3564776 1.6922448.9390267z"
					fill="#ecf0f1"
				/></g></g>
			</svg>
		</div>

		<div
			v-if="isUploadImageModal"
			id="uploadimageModal"
			class="modal modal-active"
			role="dialog"
		>
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<a
							class="close"
							@click="isUploadImageModal = false"
						>&times;</a>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-8 text-center">
								<div
									id="image_demo"
									style="width:455px; margin-top:15px"
								>
									<vue-croppie
										ref="croppieRef"
										:enable-exif="true"
										:boundary="{ width: 300, height: 300 }"
										:viewport="{ width: 200, height: 200, 'type': 'square' }"
									/>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button
							class="btn btn-default crop_image"
							@click="crop_image"
						>
							Сохранить
						</button>
					</div>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<style lang="scss" scoped>
.sticky {
	position: sticky;top: 20px;height: 300px;
}
.right-panel .breadcrumbs {display: none !important;}
.file-uploads label {
	position: relative;
	display: block;
}
.xtab-content  .form-group {
	margin-bottom: 8px;
}
 .box a {
	font-weight: 600;
	font-size: 12px;
	text-align: left;
}
 .file-uploads label:before {
	content: "\f0ee";
	position: absolute;
	font-family: FontAwesome;
	top: 0;
	left: 0;
	font-size: 36px;
	color: #017cff;
	opacity: 0;
	transition: 0.3s ease all;
	background: rgba(1,124,255, .18);
	width: 100%;
	height: 87px;
	z-index: 2;
	display: flex;
	justify-content: center;
	align-items: center;
	cursor: pointer;}

 .file-uploads label:hover:before {
	opacity: 1;}

 .file-uploads .d-inline-block {
	margin-right: 10px;}

 .contact-delete {
	height: 35px;}

 .data-info-1 img {
	max-width: 100px;
	background: #f0f0f0;
	border-radius: 3px;}

 .contacts-info2 {
	border-bottom: 2px solid #e4e4e4;
	margin-left: 175px;
	padding-bottom: 20px;
	width: 440px;}

 .contacts-info3 {
	margin-left: 175px;
	display: block;
	padding: 50px 0;}

 .data-information {
	padding: 0 0 0 20px;
}

 .contact-information h1 {
	color: #202226;
	font-family: "Open Sans";
	font-size: 18px;
	font-weight: 400;
	line-height: 33px;
	padding: 0 0 30px 175px;}



 .data-information .xtab-content .name {
	float: left;
	color: #202226;
	font-family: "Open Sans";
	font-size: 14px;
	font-weight: 700;
	padding-top: 10px;}

 .data-information .xtab-content .last-name {
	float: left;
	color: #202226;
	font-family: "Open Sans";
	font-size: 14px;
	font-weight: 700;
	padding-top: 0;}

 .data-information .xtab-content .email {
	color: #202226;
	font-family: "Open Sans";
	font-size: 14px;
	font-weight: 700;
	padding-top: 15px;}

 .label-6,
 .data-information .xtab-content select,
 .data-information .xtab-content textarea {
	border: 1px solid #d9d9d9 !important;
}
 .data-information .xtab-content input {
	margin: 0;
	padding: 7px 10px;
	background: #fff;
	height: 35px;
	width: 100%;
	box-sizing: border-box;
	border: 1px solid #d9d9d9;
	border-radius: 3px;
	font: 14px/30px 'Open Sans', Arial, Helvetica, sans-serif;
	color: #202226;
	margin-bottom: 0;
	font-size: 13px;}

 .data-information .xtab-content input[type="submit"],
 .data-information .xtab-content input[type="button"] {
	background-color: #d632e9;
	transition: all 0.5s linear;
	height: 35px;
	border-radius: 50px;
	line-height: 5px;
	color: #fff;
	margin-top: 10px;}

 .data-information .xtab-content input[type="submit"]:hover,
 .data-information .xtab-content input[type="button"]:hover {
	background-color: #c00eb5;
	transition: all 0.2s linear;}

 .data-info-2 {
	padding-right: 30px;}

 .data-info-3 {
	padding-right: 100px;}

 .contacts-info {
	margin-left: 20px;
	padding-bottom: 20px;
	width: 450px;}

 .obwie_dannye {
	color: #202226;
	font-family: "Open Sans";
	font-size: 22px;
	font-weight: 400;
	margin-top: 30px;
	margin-bottom: 50px;}

.xtab-content .title span {
	font-size: 12px;
	color: cornflowerblue;
	text-decoration: underline;}
#right-panel.show-sidebar .content {
	padding-left: 25px;
}
.xtab-content .contacts-info {
	margin-left: 0;
}
 .iconsfile {
	width: 93px !important;
	margin: auto;}

	.box span {
	width: 93px;
	font-size: 10px;
	overflow: hidden;
	text-align: center;
	position: absolute;
	top: 0;
	left: 0;
	font-weight: 500;
	text-align: left;
	padding: 7px;
}
 .box label {
	vertical-align: top;
	line-height: 15px;
	position: relative;
	margin-bottom: 0;
}
 .phone-row .btn-danger {
	display: none;
}
 .phone-row:hover .btn-danger {
	display: block;
}
.box {
	display: flex;
	align-items: flex-end;
}

 .box label {
	vertical-align: top;
	font-size: 10px;
	overflow: hidden;
	font-weight: 700;
	text-align: center;
	width: 93px;
}
 .iconsfile {
	padding: 40px 5px 0 50px !important;
}
 .box svg {
	display: block;
	height: 50px;
	fill: #1076b0;
	cursor: pointer;
	width: 310px;
	height: 87px;
	background-size: contain;
	cursor: pointer;
	background: rgba(16,118,176,0.08);
	padding: 30px;
	fill: #96da34;
	margin: 0;
	margin-bottom: 3px;
	border-radius: 3px;}
.modal-active{
	display: block;
}
 .modal .box label {
	background: #f0f0f0;
	width: 100%;
}
 .modal .box svg {
	width: 360px;
}
 .modal .box span {
	top: 5px;
	left: 5px;
	font-size: 13px;
	width: 106px;
}
 .modal .box svg {
	margin-top: 15px
}
#workStartTime,
#workEndTime {
	width: 90px;
	margin-right: 15px;
}
#workEndTime {
	margin-left: 15px;
}
.xtab-content select.form-control:not([size]):not([multiple]) {
	height: 35px;
	background: #fff;
	font-size: 14px;
	padding-left: 7px;}

	.xtab-content .btn-rounded {
	border-radius: 3px;}

.xtab-content .phone-row {
	position: relative;
	margin: 0 -15px;
}

 .phone-row button {
	position: absolute;
	left: 100%;}

textarea.form-control {
	background: #fff;
	min-height: 60px;
	font-size: 14px;}
 .label-6 {
	display: flex;
	flex-direction: column;
	text-align: center;
	width: fit-content;
	align-items: center;
	background: #e9ecef;
	margin: 0 auto;
	width: 100%;
	border-radius: 3px;
}
.xtab-content .form-control:disabled, .xtab-content .form-control[readonly] {
	/* color: #fff; */
	background-color: #5160745c;
	opacity: 1;
}
 .multiselect__tags {
	border: 0;
	background: #ecf4f9;}
 .data-information{
	padding: 0;
}
 .bread i {
	font-size: 10px;
	padding-left: 7px;
	padding-right: 7px;
}

.xtab-content h5 {
	position: relative;
	color: rgba(4,94,145, 1);
	padding-bottom: 3px;
	font-weight: 700;
	display: inline-block;
	font-size: 18px;
	text-transform: uppercase;
}
/* h5:before {
	content: "";
	display: block;
	position: absolute;
	bottom: -5px;
	width: 100%;
	height: 2px;
	background: #045e92;
} */
 .xxx {
	width: 500px;
}
 .multiselect__tag {
position: relative;
	font-size: 1em;
	font-weight: 400;
	display: inline-block;
	padding: 4px 26px 4px 10px;
	border-radius: 5px;
	margin-right: 10px;
	min-width: 200px;
	line-height: 1.5;
	background: #41b883;
	margin-bottom: 5px;
	white-space: nowrap;
	overflow: hidden;
	max-width: 100%;
	text-overflow: ellipsis;
	background: #d5dde2 !important;
	color: #454545;
}

 .data-information input.multiselect__input {
	border: none;
}

 .user-flex {
	display: flex;
}
 .user-flex .tab-content {
	flex: 0 0 auto;
}
 .user-nav {
	width: 220px;
	flex-direction: column;
	margin-right: 30px;
}
@media(min-width: 1440px) {
	.user-nav {
		width: 250px;
	}
}
.xtab-content .user-nav li {
	border-radius: 0;
}

.xtab-content .xtab-pane {
	margin-bottom: 50px;
}
.xtab-content .list-group-item.active {
	z-index: 2;
	color: #676767;
	background-color: #f2f8ff;
	border-color: #d1e7ff;
}
span.red {
	color: red;
}
 .listo {
	color: red;
	font-weight: 700;
}
 .m0 {
	margin: 0 !important;
	margin-bottom: 8px !important;
	margin-right: -12px !important;
}
 .modal-p .table td,  .modal-p .table th {
	padding: .25rem;
}
 .bg-grey {
	background: #fafafa;
}

.p-30 {
	padding: 30px;
}

.font-sm {
	font-size: 12px
}
body {
	background-color: #ffffff;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='360' height='360' viewBox='0 0 360 360'%3E%3Cpath fill='%235682d9' fill-opacity='0.05' d='M0 85.02l4.62-4.27a49.09 49.09 0 0 0 7.33 3.74l-1.2 10.24 2.66.87 5.05-9c2.62.65 5.34 1.08 8.12 1.28L28.6 98h2.8l2.02-10.12c2.74-.2 5.46-.62 8.12-1.28l5.05 8.99 2.66-.86-1.2-10.24c2.55-1.03 5-2.29 7.33-3.74l7.58 7 2.26-1.65-4.3-9.38a48.3 48.3 0 0 0 5.8-5.8l9.38 4.3 1.65-2.26-7-7.58a49.09 49.09 0 0 0 3.74-7.33l10.24 1.2.87-2.66-9-5.05a48.07 48.07 0 0 0 1.28-8.12L88 41.4v-2.8l-10.12-2.02c-.2-2.74-.62-5.46-1.28-8.12l8.99-5.05-.86-2.66-10.24 1.2c-1.03-2.55-2.29-5-3.74-7.33l7-7.58-1.65-2.26-9.38 4.3a48.3 48.3 0 0 0-5.8-5.8L62.42 0h2.16l-1.25 2.72a50.31 50.31 0 0 1 3.95 3.95l9.5-4.36 3.52 4.85-7.08 7.68c.94 1.6 1.79 3.27 2.54 4.98l10.38-1.21 1.85 5.7-9.11 5.12c.39 1.8.68 3.65.87 5.52L90 37v6l-10.25 2.05a49.9 49.9 0 0 1-.87 5.52l9.11 5.12-1.85 5.7-10.38-1.21c-.75 1.7-1.6 3.37-2.54 4.98l7.08 7.68-3.52 4.85-9.5-4.36a50.31 50.31 0 0 1-3.95 3.95l4.36 9.5-4.85 3.52-7.68-7.08c-1.6.94-3.27 1.79-4.98 2.54l1.21 10.38-5.7 1.85-5.12-9.11c-1.8.39-3.65.68-5.52.87L33 100h-6l-2.05-10.25a49.9 49.9 0 0 1-5.52-.87l-5.12 9.11-5.7-1.85 1.21-10.38c-1.7-.75-3.37-1.6-4.98-2.54L0 87.68v-2.66zM0 52.7V27.3l8.38 4.84a22.96 22.96 0 0 0 0 15.72L0 52.7zm0-39.16A39.91 39.91 0 0 1 26 .2v17.15a22.98 22.98 0 0 0-13.62 7.86L0 18.06v-4.52zm0 52.92v-4.52l12.38-7.15A22.98 22.98 0 0 0 26 62.65V79.8A39.91 39.91 0 0 1 0 66.46zM34 79.8V62.65a22.98 22.98 0 0 0 13.62-7.86l14.85 8.58A39.97 39.97 0 0 1 34 79.8zm32.48-23.36l-14.86-8.58a22.96 22.96 0 0 0 0-15.72l14.86-8.58A39.86 39.86 0 0 1 70 40a39.9 39.9 0 0 1-3.52 16.44zm-4.01-39.8L47.62 25.2A22.98 22.98 0 0 0 34 17.35V.2a39.97 39.97 0 0 1 28.47 16.43v.01zM0 50.38l5.98-3.45a25.01 25.01 0 0 1 0-13.88L0 29.6v20.78zm.5-34.35l11.48 6.63c3.27-3.4 7.44-5.8 12.02-6.94V2.47A37.96 37.96 0 0 0 .5 16.04v-.01zm0 47.92A37.96 37.96 0 0 0 24 77.53V64.28a24.97 24.97 0 0 1-12.02-6.95L.5 63.96v-.01zM36 77.53a37.96 37.96 0 0 0 23.5-13.57l-11.48-6.63A24.97 24.97 0 0 1 36 64.28v13.25zm29.5-23.96a37.91 37.91 0 0 0 0-27.14l-11.48 6.63a25.01 25.01 0 0 1 0 13.88l11.49 6.63h-.01zm-6-37.53A37.96 37.96 0 0 0 36 2.47v13.25c4.66 1.15 8.8 3.6 12.02 6.95l11.48-6.63zM30 54a14 14 0 1 1 0-28 14 14 0 0 1 0 28zm0-2a12 12 0 1 0 0-24 12 12 0 0 0 0 24zm0-2a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm77.47 45.17l-1.62-5.97 5.67-2.06 2.61 5.64c1.09-.25 2.2-.44 3.33-.58l.52-6.2h6.04l.52 6.2c1.13.14 2.24.33 3.33.58l2.6-5.64 5.68 2.06-1.62 5.97c1.02.51 2 1.07 2.95 1.69l4.35-4.38 4.62 3.88-3.53 5c.8.84 1.53 1.71 2.23 2.62l5.52-2.6 3.02 5.23-4.98 3.46c.46 1.06.86 2.14 1.2 3.25l6.02-.54 1.05 5.94-5.84 1.54c.07 1.16.07 2.32 0 3.48l5.84 1.54-1.05 5.94-6.02-.54c-.34 1.1-.74 2.2-1.2 3.25l4.98 3.46-3.02 5.22-5.52-2.6c-.7.92-1.44 1.8-2.23 2.62l3.53 5-4.62 3.89-4.35-4.38a30.2 30.2 0 0 1-2.95 1.69l1.62 5.97-5.67 2.06-2.61-5.64c-1.09.25-2.2.44-3.33.58l-.52 6.2h-6.04l-.52-6.2a30.27 30.27 0 0 1-3.33-.58l-2.6 5.64-5.68-2.06 1.62-5.97c-1.01-.5-2-1.07-2.95-1.69l-4.35 4.38-4.62-3.88 3.53-5a32.5 32.5 0 0 1-2.23-2.62l-5.52 2.6-3.02-5.23 4.98-3.46a29.66 29.66 0 0 1-1.2-3.25l-6.02.54-1.05-5.94 5.84-1.54a30.28 30.28 0 0 1 0-3.48l-5.84-1.54 1.05-5.94 6.02.54c.34-1.1.74-2.2 1.2-3.25l-4.98-3.46 3.02-5.22 5.52 2.6c.7-.92 1.44-1.8 2.23-2.62l-3.53-5 4.62-3.89 4.35 4.38a30.2 30.2 0 0 1 2.95-1.69zm15.2-1.12l-.5-6.05h-2.34l-.5 6.05c-2.18.13-4.3.5-6.32 1.1l-2.54-5.5-2.2.8 1.6 5.85a27.97 27.97 0 0 0-5.56 3.21l-4.27-4.3-1.79 1.5 3.5 4.95a28.14 28.14 0 0 0-4.12 4.92l-5.5-2.59-1.16 2.02 4.98 3.46a27.8 27.8 0 0 0-2.2 6.03l-6.03-.55-.4 2.3 5.86 1.54a28.3 28.3 0 0 0 0 6.42l-5.87 1.55.4 2.3 6.05-.56a27.8 27.8 0 0 0 2.2 6.03l-5 3.47 1.17 2.02 5.49-2.59a28.14 28.14 0 0 0 4.12 4.92l-3.5 4.96 1.79 1.5 4.27-4.31a27.97 27.97 0 0 0 5.56 3.21l-1.6 5.85 2.2.8 2.54-5.5c2.02.6 4.14.97 6.32 1.1l.5 6.05h2.34l.5-6.05c2.18-.13 4.3-.5 6.32-1.1l2.54 5.5 2.2-.8-1.6-5.85a27.97 27.97 0 0 0 5.56-3.21l4.27 4.3 1.79-1.5-3.5-4.95a28.14 28.14 0 0 0 4.12-4.92l5.5 2.59 1.16-2.02-4.98-3.46a27.8 27.8 0 0 0 2.2-6.03l6.03.55.4-2.3-5.86-1.54a28.3 28.3 0 0 0 0-6.42l5.87-1.55-.4-2.3-6.05.56a27.8 27.8 0 0 0-2.2-6.03l4.99-3.46-1.17-2.02-5.49 2.59a28.14 28.14 0 0 0-4.12-4.92l3.5-4.96-1.79-1.5-4.27 4.31a27.97 27.97 0 0 0-5.56-3.21l1.6-5.85-2.2-.8-2.54 5.5c-2.02-.6-4.14-.97-6.32-1.1l.01-.01zM121 128a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm0-2a6 6 0 1 0 0-12 6 6 0 0 0 0 12zm0-18a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm8.49 3.51a5 5 0 1 1 6.95-7.2 5 5 0 0 1-6.95 7.2zM133 120a5 5 0 1 1 10 0 5 5 0 0 1-10 0zm-3.51 8.49a5 5 0 1 1 7.2 6.95 5 5 0 0 1-7.2-6.95zM121 132a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm-8.49-3.51a5 5 0 1 1-6.95 7.2 5 5 0 0 1 6.95-7.2zM109 120a5 5 0 1 1-10 0 5 5 0 0 1 10 0zm3.51-8.49a5 5 0 1 1-7.2-6.95 5 5 0 0 1 7.2 6.95zM121 106a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm9.9 4.1a3 3 0 1 0 4.39-4.09 3 3 0 0 0-4.39 4.09zm4.1 9.9a3 3 0 1 0 6 0 3 3 0 0 0-6 0zm-4.1 9.9a3 3 0 1 0 4.09 4.39 3 3 0 0 0-4.09-4.39zM121 134a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm-9.9-4.1a3 3 0 1 0-4.39 4.09 3 3 0 0 0 4.39-4.09zM107 120a3 3 0 1 0-6 0 3 3 0 0 0 6 0zm4.1-9.9a3 3 0 1 0-4.09-4.39 3 3 0 0 0 4.09 4.39zm129.42-6.95v.01c.87.07 1.74.17 2.6.3l1.5-3.91 1.94-3.64 3.89.97v4.13l-.5 4.13c.83.28 1.64.59 2.44.93l2.42-3.43 2.76-3.07 3.54 1.88-1 4-1.49 3.89c.73.47 1.45.97 2.15 1.49l3.19-2.76 3.42-2.3 2.97 2.67-1.93 3.65-2.38 3.4c.6.64 1.2 1.3 1.76 1.99l3.68-1.94 3.85-1.48 2.29 3.28-2.7 3.11-3.12 2.82c.43.76.84 1.53 1.22 2.32l4.04-1 4.1-.5 1.43 3.73-3.37 2.37-3.7 1.98c.23.84.44 1.68.62 2.54l4.17.01 4.1.5.48 3.97-3.85 1.48-4.06 1.02c.03.87.03 1.75 0 2.62l4.06 1.02 3.85 1.48-.48 3.97-4.1.51h-4.17c-.18.86-.39 1.71-.63 2.54l3.7 1.98 3.38 2.37-1.43 3.73-4.1-.5-4.04-1c-.38.79-.79 1.56-1.22 2.32l3.13 2.82 2.7 3.11-2.3 3.28-3.85-1.48-3.68-1.95a37 37 0 0 1-1.76 2l2.38 3.41 1.93 3.64-2.97 2.67-3.42-2.3-3.19-2.76a40.1 40.1 0 0 1-2.15 1.48l1.48 3.9 1 4-3.53 1.88-2.76-3.07-2.42-3.43c-.8.33-1.61.65-2.45.93l.5 4.13v4.13l-3.88.97-1.94-3.65-1.5-3.9c-.86.13-1.73.23-2.6.31L240 187l-1 4h-4l-1-4-.52-4.16a37.6 37.6 0 0 1-2.6-.3l-1.5 3.91-1.94 3.64-3.89-.97v-4.13l.5-4.13c-.83-.28-1.64-.59-2.44-.93l-2.42 3.43-2.76 3.07-3.54-1.88 1-4 1.49-3.89c-.74-.47-1.45-.97-2.15-1.49l-3.19 2.76-3.42 2.3-2.97-2.67 1.93-3.65 2.38-3.4c-.61-.65-1.2-1.31-1.76-1.99l-3.68 1.94-3.85 1.48-2.29-3.28 2.7-3.11 3.12-2.82c-.43-.76-.84-1.53-1.22-2.32l-4.04 1-4.1.5-1.43-3.73 3.37-2.37 3.7-1.98c-.23-.84-.44-1.68-.62-2.54l-4.17-.01-4.1-.5-.48-3.97 3.85-1.48 4.06-1.02c-.03-.87-.03-1.75 0-2.62l-4.06-1.02-3.85-1.48.48-3.97 4.1-.51h4.17c.18-.86.39-1.71.63-2.54l-3.7-1.98-3.38-2.37 1.43-3.73 4.1.5 4.04 1c.38-.79.79-1.56 1.22-2.32l-3.13-2.82-2.7-3.11 2.3-3.28 3.85 1.48 3.68 1.95a37 37 0 0 1 1.76-2l-2.38-3.41-1.93-3.64 2.97-2.67 3.42 2.3 3.19 2.76c.7-.52 1.41-1.02 2.15-1.48l-1.48-3.9-1-4 3.53-1.88 2.76 3.07 2.42 3.43c.8-.33 1.61-.65 2.45-.93l-.5-4.13v-4.13l3.88-.97 1.94 3.65 1.5 3.9c.86-.13 1.73-.23 2.6-.31L234 99l1-4h4l1 4 .52 4.15zm-14.3 3.4c-1.83.54-3.6 1.21-5.3 2l-3.5-4.97-1.38-1.53-.88.47.5 2 2.16 5.67a38.09 38.09 0 0 0-4.66 3.22l-4.61-4-1.71-1.15-.75.67.97 1.82 3.47 4.98a38.22 38.22 0 0 0-3.79 4.28l-5.37-2.84-1.92-.74-.57.82 1.35 1.56 4.52 4.09a37.9 37.9 0 0 0-2.64 5l-5.89-1.45-2.04-.25-.36.94 1.69 1.18 5.36 2.87a37.74 37.74 0 0 0-1.35 5.5l-6.08.01-2.04.25-.12 1 1.92.73 5.9 1.5a38.54 38.54 0 0 0 0 5.65l-5.9 1.49-1.92.74.12.99 2.04.25 6.08.01c.31 1.86.77 3.7 1.35 5.5l-5.36 2.87-1.7 1.18.37.94 2.04-.25 5.9-1.46a37.9 37.9 0 0 0 2.63 5.01l-4.52 4.1-1.35 1.55.57.82 1.92-.74 5.37-2.84a38.22 38.22 0 0 0 3.8 4.28l-3.48 4.98-.97 1.82.75.67 1.7-1.15 4.62-4a38.09 38.09 0 0 0 4.66 3.22l-2.17 5.67-.5 2 .89.47 1.38-1.53 3.5-4.98c1.7.8 3.47 1.47 5.3 2l-.73 6.04v2.06l.97.24.97-1.82 2.2-5.68c1.83.36 3.7.6 5.62.68L236 187l.5 2h1l.5-2 .75-6.04a38.2 38.2 0 0 0 5.62-.68l2.2 5.68.97 1.82.97-.24v-2.06l-.73-6.03c1.83-.54 3.6-1.21 5.3-2l3.5 4.97 1.38 1.53.88-.47-.5-2-2.16-5.67a38.09 38.09 0 0 0 4.66-3.22l4.61 4 1.71 1.15.75-.67-.97-1.82-3.47-4.98a38.22 38.22 0 0 0 3.79-4.28l5.37 2.84 1.92.74.57-.82-1.35-1.56-4.52-4.09c1-1.6 1.88-3.27 2.64-5l5.89 1.45 2.04.25.36-.94-1.69-1.18-5.36-2.87a37.4 37.4 0 0 0 1.35-5.5l6.08-.01 2.04-.25.12-1-1.92-.73-5.9-1.5c.14-1.88.14-3.77 0-5.65l5.9-1.49 1.92-.74-.12-.99-2.04-.25-6.08-.01a37.4 37.4 0 0 0-1.35-5.5l5.36-2.87 1.7-1.18-.37-.94-2.04.25-5.9 1.46a37.9 37.9 0 0 0-2.63-5.01l4.52-4.1 1.35-1.55-.57-.82-1.92.74-5.37 2.84a38.22 38.22 0 0 0-3.8-4.28l3.48-4.98.97-1.82-.75-.67-1.7 1.15-4.62 4a38.09 38.09 0 0 0-4.66-3.22l2.17-5.67.5-2-.89-.47-1.38 1.53-3.5 4.98c-1.7-.8-3.47-1.47-5.3-2l.73-6.04v-2.06l-.97-.24-.97 1.82-2.2 5.68c-1.83-.36-3.7-.6-5.62-.68L238 99l-.5-2h-1l-.5 2-.75 6.04c-1.92.09-3.8.32-5.62.68l-2.2-5.68-.97-1.82-.97.24v2.06l.73 6.03zm-5.85 5.65A34.82 34.82 0 0 1 236 108v6a28.8 28.8 0 0 0-12.63 3.39l-3-5.2v.01zm2.8.83l1 1.74a30.8 30.8 0 0 1 9.83-2.63v-2.01a32.8 32.8 0 0 0-10.83 2.9zm-4.53.17l3 5.2a29.12 29.12 0 0 0-9.24 9.24l-5.2-3a35.18 35.18 0 0 1 11.44-11.44zm-.67 2.84a33.19 33.19 0 0 0-7.93 7.93l1.74 1a31.18 31.18 0 0 1 7.2-7.2l-1.01-1.73zm-11.77 10.33h-.01l5.2 3A28.8 28.8 0 0 0 208 142h-6a34.82 34.82 0 0 1 4.2-15.63zm.83 2.8a32.8 32.8 0 0 0-2.9 10.83h2.01a30.8 30.8 0 0 1 2.63-9.83l-1.74-1zM202.01 144h6.01c.15 4.41 1.3 8.73 3.38 12.63l-5.2 3a34.82 34.82 0 0 1-4.19-15.63zm2.12 2a32.8 32.8 0 0 0 2.9 10.84l1.74-1a30.8 30.8 0 0 1-2.63-9.84h-2.01zm3.07 15.36l5.2-3c2.34 3.74 5.5 6.9 9.24 9.24l-3 5.2a35.18 35.18 0 0 1-11.44-11.44zm2.84.67a33.19 33.19 0 0 0 7.93 7.93l1-1.74a31.18 31.18 0 0 1-7.2-7.2l-1.73 1.01zm10.33 11.77v.01l3-5.2A28.85 28.85 0 0 0 236 172v6a34.82 34.82 0 0 1-15.63-4.2zm2.8-.83a32.8 32.8 0 0 0 10.83 2.9v-2.01a30.8 30.8 0 0 1-9.83-2.63l-1 1.74zm14.83 5.02v-6.01c4.41-.15 8.73-1.3 12.63-3.38l3 5.2a34.82 34.82 0 0 1-15.63 4.19zm2-2.12a32.8 32.8 0 0 0 10.84-2.9l-1-1.74a30.8 30.8 0 0 1-9.84 2.63v2.01zm15.36-3.07l-3-5.2c3.74-2.34 6.9-5.5 9.24-9.24l5.2 3a35.18 35.18 0 0 1-11.44 11.44zm.67-2.84a33.19 33.19 0 0 0 7.93-7.93l-1.74-1a31.18 31.18 0 0 1-7.2 7.2l1.01 1.73zm11.77-10.33h.01l-5.2-3A28.85 28.85 0 0 0 266 144h6a34.82 34.82 0 0 1-4.2 15.63zm-.83-2.8a32.8 32.8 0 0 0 2.9-10.83h-2.01a30.8 30.8 0 0 1-2.63 9.83l1.74 1zm5.02-14.83h-6.01a28.85 28.85 0 0 0-3.38-12.63l5.2-3a34.82 34.82 0 0 1 4.19 15.63zm-2.12-2a32.8 32.8 0 0 0-2.9-10.84l-1.74 1a30.8 30.8 0 0 1 2.63 9.84h2.01zm-3.07-15.36l-5.2 3a29.12 29.12 0 0 0-9.24-9.24l3-5.2a35.18 35.18 0 0 1 11.44 11.44zm-2.84-.67a33.19 33.19 0 0 0-7.93-7.93l-1 1.74a31.18 31.18 0 0 1 7.2 7.2l1.73-1.01zM238 108a34.82 34.82 0 0 1 15.63 4.19l-3 5.2a28.85 28.85 0 0 0-12.63-3.38V108zm12.84 5.02a32.8 32.8 0 0 0-10.84-2.9v2.01a30.8 30.8 0 0 1 9.83 2.63l1-1.74h.01zM237 156a13 13 0 1 1 0-26 13 13 0 0 1 0 26zm0-2a11 11 0 1 0 0-22 11 11 0 0 0 0 22zM137.54 0h56.92l-.74 1.03c.57.7 1.12 1.4 1.64 2.14l7.75-2.9 2 3.46-6.38 5.25c.37.82.72 1.65 1.03 2.5l8.22-.8 1.04 3.86-7.52 3.43c.15.88.26 1.77.35 2.67L210 22v4l-8.15 1.36c-.09.9-.2 1.8-.35 2.67l7.52 3.43-1.04 3.86-8.22-.8c-.31.85-.66 1.68-1.03 2.5l6.38 5.25-2 3.46-7.75-2.9c-.52.74-1.07 1.45-1.64 2.14l4.8 6.73-2.82 2.83-6.73-4.8c-.7.56-1.4 1.11-2.14 1.63l2.9 7.75-3.46 2-5.25-6.38c-.82.37-1.65.72-2.5 1.03l.8 8.22-3.86 1.04-3.43-7.52c-.88.15-1.77.26-2.67.35L168 68h-4l-1.36-8.15c-.9-.09-1.8-.2-2.67-.35l-3.43 7.52-3.86-1.04.8-8.22c-.85-.31-1.68-.66-2.5-1.03l-5.25 6.38-3.46-2 2.9-7.75a36.15 36.15 0 0 1-2.14-1.64l-6.73 4.8-2.83-2.82 4.8-6.73c-.56-.7-1.11-1.4-1.63-2.14l-7.75 2.9-2-3.46 6.38-5.25c-.37-.82-.72-1.65-1.03-2.5l-8.22.8-1.04-3.86 7.52-3.43c-.15-.88-.26-1.77-.35-2.67L122 26v-4l8.15-1.36c.09-.9.2-1.8.35-2.67l-7.52-3.43 1.04-3.86 8.22.8c.31-.85.66-1.68 1.03-2.5l-6.38-5.25 2-3.46 7.75 2.9c.52-.74 1.07-1.45 1.64-2.14L137.54 0zm2.43 0l.83 1.17a34.14 34.14 0 0 0-3.38 4.4l-7.63-2.86-.33.58 6.29 5.18a33.79 33.79 0 0 0-2.13 5.12l-8.1-.78-.18.64 7.42 3.37a34.02 34.02 0 0 0-.72 5.5L124 23.68v.66l8.04 1.34c.1 1.88.33 3.72.72 5.5l-7.42 3.38.18.64 8.1-.78a33.88 33.88 0 0 0 2.13 5.12l-6.29 5.18.33.58 7.63-2.86c1 1.56 2.14 3.03 3.38 4.4l-4.73 6.63.47.47 6.63-4.73a34.14 34.14 0 0 0 4.4 3.38l-2.86 7.63.58.33 5.18-6.29c1.63.84 3.35 1.56 5.12 2.13l-.78 8.1.64.18 3.37-7.42c1.79.39 3.63.63 5.5.72l1.35 8.04h.66l1.34-8.04c1.88-.1 3.72-.33 5.5-.72l3.38 7.42.64-.18-.78-8.1a33.88 33.88 0 0 0 5.12-2.13l5.18 6.29.58-.33-2.86-7.63c1.56-1 3.03-2.14 4.4-3.38l6.63 4.73.47-.47-4.73-6.63a34.14 34.14 0 0 0 3.38-4.4l7.63 2.86.33-.58-6.29-5.18a33.79 33.79 0 0 0 2.13-5.12l8.1.78.18-.64-7.42-3.37c.39-1.79.63-3.63.72-5.5l8.04-1.35v-.66l-8.04-1.34c-.1-1.88-.33-3.72-.72-5.5l7.42-3.38-.18-.64-8.1.78a33.79 33.79 0 0 0-2.13-5.12l6.29-5.18-.33-.58-7.63 2.86c-1-1.56-2.14-3.03-3.38-4.4l.83-1.17h-52.06V0zm-2.82 27h14.15A15.02 15.02 0 0 0 163 38.7v14.15A29.01 29.01 0 0 1 137.15 27zm12.57-27H163v9.3A15.02 15.02 0 0 0 151.3 21h-14.15a28.99 28.99 0 0 1 12.57-21zM169 52.85V38.7A15.02 15.02 0 0 0 180.7 27h14.15A29.01 29.01 0 0 1 169 52.85zM182.28 0a28.99 28.99 0 0 1 12.57 21H180.7A15.02 15.02 0 0 0 169 9.3V0h13.28zm-42.82 29A27.03 27.03 0 0 0 161 50.54V40.25A17.04 17.04 0 0 1 149.75 29h-10.29zm14.16-29a27.04 27.04 0 0 0-14.16 19h10.29A17.04 17.04 0 0 1 161 7.75V0h-7.38zM171 50.54A27.03 27.03 0 0 0 192.54 29h-10.29A17.04 17.04 0 0 1 171 40.25v10.29zM178.38 0H171v7.75A17.04 17.04 0 0 1 182.25 19h10.29a27.04 27.04 0 0 0-14.16-19zM166 34a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-39.51 176.15l-10.67-7.95 6-10.4 12.23 5.27a23.97 23.97 0 0 1 8.4-4.86L144 177h12l1.55 13.21a23.97 23.97 0 0 1 8.4 4.86l12.23-5.27 6 10.4-10.67 7.95a24 24 0 0 1 0 9.7l10.67 7.95-6 10.4-12.23-5.27a23.97 23.97 0 0 1-8.4 4.86L156 249h-12l-1.55-13.21a23.97 23.97 0 0 1-8.4-4.86l-12.23 5.27-6-10.4 10.67-7.95a24.1 24.1 0 0 1 0-9.7zm29.25-16.4l-1.5-12.75h-8.48l-1.5 12.76c-3.75 1-7.1 2.99-9.79 5.65l-11.8-5.08-4.23 7.34 10.3 7.68c-.98 3.7-.98 7.6 0 11.3l-10.3 7.68 4.23 7.34 11.8-5.08a22.1 22.1 0 0 0 9.8 5.65l1.5 12.76h8.47l1.5-12.76c3.75-1 7.1-2.99 9.79-5.65l11.8 5.08 4.23-7.34-10.3-7.68c.98-3.7.98-7.6 0-11.3l10.3-7.68-4.23-7.34-11.8 5.08a21.98 21.98 0 0 0-9.8-5.65l.01-.01zM150 225a12 12 0 1 1 0-24 12 12 0 0 1 0 24zm0-2a10 10 0 1 0 0-20 10 10 0 0 0 0 20zm3.53 67.72l4.26.07.51 1.93-3.65 2.19c.11.63.2 1.27.25 1.92L159 298v2l-4.1 1.17c-.05.65-.14 1.29-.25 1.92l3.65 2.2-.51 1.92-4.26.07c-.22.61-.47 1.21-.74 1.8l2.96 3.05-1 1.74-4.13-1.04a24.1 24.1 0 0 1-1.18 1.54l2.07 3.72-1.42 1.42-3.72-2.07c-.5.41-1.01.8-1.54 1.18l1.04 4.13-1.74 1-3.05-2.96c-.59.27-1.19.52-1.8.74l-.07 4.26-1.93.51-2.19-3.65c-.63.11-1.27.2-1.92.25L132 327h-2l-1.17-4.1c-.65-.05-1.29-.14-1.92-.25l-2.2 3.65-1.92-.51-.07-4.26c-.61-.22-1.21-.47-1.8-.74l-3.05 2.96-1.74-1 1.04-4.13a24.1 24.1 0 0 1-1.54-1.18l-3.72 2.07-1.42-1.42 2.07-3.72c-.41-.5-.8-1.01-1.18-1.54l-4.13 1.04-1-1.74 2.96-3.05c-.27-.59-.52-1.19-.74-1.8l-4.26-.07-.51-1.93 3.65-2.19c-.11-.63-.2-1.27-.25-1.92L103 300v-2l4.1-1.17c.05-.65.14-1.29.25-1.92l-3.65-2.2.51-1.92 4.26-.07c.22-.61.47-1.21.74-1.8l-2.96-3.05 1-1.74 4.13 1.04c.38-.53.77-1.04 1.18-1.54l-2.07-3.72 1.42-1.42 3.72 2.07c.5-.41 1.01-.8 1.54-1.18l-1.04-4.13 1.74-1 3.05 2.96c.59-.27 1.19-.52 1.8-.74l.07-4.26 1.93-.51 2.19 3.65c.63-.11 1.27-.2 1.92-.25L130 271h2l1.17 4.1c.65.05 1.29.14 1.92.25l2.2-3.65 1.92.51.07 4.26c.61.22 1.21.47 1.8.74l3.05-2.96 1.74 1-1.04 4.13c.53.38 1.04.77 1.54 1.18l3.72-2.07 1.42 1.42-2.07 3.72c.41.5.8 1.01 1.18 1.54l4.13-1.04 1 1.74-2.96 3.05c.27.59.52 1.19.74 1.8zM109 299a22 22 0 1 0 44 0 22 22 0 0 0-44 0zm27.11-10.86l-3 5.22a6 6 0 0 0-4.21 0l-3.01-5.22a11.95 11.95 0 0 1 10.22 0zm1.74 1a12 12 0 0 1 5.1 8.86h-6.01a6.01 6.01 0 0 0-2.1-3.64l3-5.22h.01zm-13.7 0l3.02 5.22a6.01 6.01 0 0 0-2.1 3.64h-6.03a12 12 0 0 1 5.11-8.86zm-5.1 10.86h6.01a6.01 6.01 0 0 0 2.1 3.64l-3 5.22a12 12 0 0 1-5.12-8.86h.01zm6.84 9.86l3-5.22a6 6 0 0 0 4.21 0l3.01 5.22a11.95 11.95 0 0 1-10.22 0zm11.96-1l-3.02-5.22a6.01 6.01 0 0 0 2.1-3.64h6.03a12 12 0 0 1-5.11 8.86zm-4.68-19.62a10.04 10.04 0 0 0-4.34 0l1.05 1.82c.74-.1 1.5-.1 2.24 0l1.05-1.82zm5.2 3l-1.05 1.82c.46.59.84 1.24 1.12 1.94h2.1a9.99 9.99 0 0 0-2.17-3.76zm-14.74 0a9.99 9.99 0 0 0-2.17 3.76h2.1c.28-.7.66-1.35 1.12-1.94l-1.05-1.82zm-2.17 9.76a9.99 9.99 0 0 0 2.17 3.76l1.05-1.82a8.01 8.01 0 0 1-1.12-1.94h-2.1zm7.37 6.76c1.43.32 2.91.32 4.34 0l-1.05-1.82c-.74.1-1.5.1-2.24 0l-1.05 1.82zm9.54-3a9.99 9.99 0 0 0 2.17-3.76h-2.1c-.28.7-.66 1.35-1.12 1.94l1.05 1.82zM127 299a4 4 0 1 1 8 0 4 4 0 0 1-8 0zm2 0a2 2 0 1 0 4 0 2 2 0 0 0-4 0zm15 0a4 4 0 1 1 8 0 4 4 0 0 1-8 0zm-6.5 11.26a4 4 0 1 1 4 6.93 4 4 0 0 1-4-6.93zm-13 0a4 4 0 1 1-4 6.93 4 4 0 0 1 4-6.93zM118 299a4 4 0 1 1-8 0 4 4 0 0 1 8 0zm6.5-11.26a4 4 0 1 1-4-6.93 4 4 0 0 1 4 6.93zm13 0a4 4 0 1 1 4-6.93 4 4 0 0 1-4 6.93zM146 299a2 2 0 1 0 4 0 2 2 0 0 0-4 0zm-7.5 12.99a2 2 0 1 0 1.66 3.64 2 2 0 0 0-1.66-3.64zm-15 0a2 2 0 1 0-2.15 3.38 2 2 0 0 0 2.15-3.38zM116 299a2 2 0 1 0-4 0 2 2 0 0 0 4 0zm7.5-12.99a2 2 0 1 0-1.66-3.64 2 2 0 0 0 1.66 3.64zm15 0a2 2 0 1 0 2.15-3.38 2 2 0 0 0-2.15 3.38zm103.8-61.7l-.8-8.22 5.8-1.55 3.42 7.52c2.26-.43 4.57-.74 6.92-.9L259 213h6l1.36 8.16c2.35.16 4.66.47 6.92.9l3.42-7.52 5.8 1.55-.8 8.22c2.21.77 4.37 1.66 6.45 2.68l5.25-6.38 5.2 3-2.9 7.74a60.25 60.25 0 0 1 5.53 4.25l6.73-4.8 4.24 4.24-4.8 6.73a60.25 60.25 0 0 1 4.25 5.53l7.74-2.9 3 5.2-6.38 5.25a59.62 59.62 0 0 1 2.68 6.45l8.22-.8 1.55 5.8-7.52 3.42c.43 2.26.74 4.57.9 6.92L330 278v6l-8.16 1.36a60.03 60.03 0 0 1-.9 6.92l7.52 3.42-1.55 5.8-8.22-.8a59.62 59.62 0 0 1-2.68 6.45l6.38 5.25-3 5.2-7.74-2.9a60.25 60.25 0 0 1-4.25 5.53l4.8 6.73-4.24 4.24-6.73-4.8a60.25 60.25 0 0 1-5.53 4.25l2.9 7.74-5.2 3-5.25-6.38a59.62 59.62 0 0 1-6.45 2.68l.8 8.22-5.8 1.55-3.42-7.52c-2.26.43-4.57.74-6.92.9L265 349h-6l-1.36-8.16a60.03 60.03 0 0 1-6.92-.9l-3.42 7.52-5.8-1.55.8-8.22a59.62 59.62 0 0 1-6.45-2.68l-5.25 6.38-5.2-3 2.9-7.74a60.25 60.25 0 0 1-5.53-4.25l-6.73 4.8-4.24-4.24 4.8-6.73a60.25 60.25 0 0 1-4.25-5.53l-7.74 2.9-3-5.2 6.38-5.25a59.62 59.62 0 0 1-2.68-6.45l-8.22.8-1.55-5.8 7.52-3.42c-.43-2.29-.73-4.6-.9-6.92L194 284v-6l8.16-1.36c.16-2.35.47-4.66.9-6.92l-7.52-3.42 1.55-5.8 8.22.8c.77-2.2 1.66-4.35 2.68-6.45l-6.38-5.25 3-5.2 7.74 2.9a60.25 60.25 0 0 1 4.25-5.53l-4.8-6.73 4.24-4.24 6.73 4.8a60.25 60.25 0 0 1 5.53-4.25l-2.9-7.74 5.2-3 5.25 6.38a59.62 59.62 0 0 1 6.45-2.68zm2.12 1.4c-3.15 1-6.19 2.27-9.08 3.77l-5.19-6.3-2.3 1.33 2.86 7.65a58.24 58.24 0 0 0-7.79 5.98l-6.65-4.75-1.88 1.88 4.75 6.65a58.24 58.24 0 0 0-5.98 7.79l-7.65-2.86-1.33 2.3 6.3 5.2a57.64 57.64 0 0 0-3.77 9.07l-8.12-.79-.69 2.58 7.43 3.38a58 58 0 0 0-1.27 9.73l-8.06 1.35v2.66l8.06 1.35c.15 3.32.58 6.58 1.27 9.73l-7.43 3.38.7 2.58 8.11-.79c1 3.15 2.27 6.19 3.77 9.08l-6.3 5.19 1.33 2.3 7.65-2.86a58.24 58.24 0 0 0 5.98 7.79l-4.75 6.65 1.88 1.88 6.65-4.75a60.3 60.3 0 0 0 7.79 5.98l-2.86 7.65 2.3 1.33 5.2-6.3a56.99 56.99 0 0 0 9.07 3.77l-.79 8.12 2.58.69 3.38-7.43c3.15.69 6.4 1.12 9.73 1.27l1.35 8.06h2.66l1.35-8.06c3.32-.15 6.58-.58 9.73-1.27l3.38 7.43 2.58-.7-.79-8.11c3.15-1 6.19-2.27 9.08-3.77l5.19 6.3 2.3-1.33-2.86-7.65a58.24 58.24 0 0 0 7.79-5.98l6.65 4.75 1.88-1.88-4.75-6.65a60.3 60.3 0 0 0 5.98-7.79l7.65 2.86 1.33-2.3-6.3-5.2a56.99 56.99 0 0 0 3.77-9.07l8.12.79.69-2.58-7.43-3.38a58 58 0 0 0 1.27-9.73l8.06-1.35v-2.66l-8.06-1.35a58.04 58.04 0 0 0-1.27-9.73l7.43-3.38-.7-2.58-8.11.79c-1-3.15-2.27-6.19-3.77-9.08l6.3-5.19-1.33-2.3-7.65 2.86a58.24 58.24 0 0 0-5.98-7.79l4.75-6.65-1.88-1.88-6.65 4.75a58.24 58.24 0 0 0-7.79-5.98l2.86-7.65-2.3-1.33-5.2 6.3a57.64 57.64 0 0 0-9.07-3.77l.79-8.12-2.58-.69-3.38 7.43a58 58 0 0 0-9.73-1.27l-1.35-8.06h-2.66l-1.35 8.06c-3.32.15-6.58.58-9.73 1.27l-3.38-7.43-2.58.7.79 8.11zm4.58 50.1a13.96 13.96 0 0 0 0 10.39l-33.88 19.55A52.77 52.77 0 0 1 209 281c0-8.94 2.21-17.37 6.12-24.75L249 275.8v.01zm2-3.47l-33.87-19.56A52.97 52.97 0 0 1 260 228.04v39.1a13.99 13.99 0 0 0-9 5.2zm0 17.32a13.99 13.99 0 0 0 9 5.2v39.1a52.97 52.97 0 0 1-42.87-24.74L251 289.66zm13 5.2a13.99 13.99 0 0 0 9-5.2l33.87 19.56A52.97 52.97 0 0 1 264 333.96v-39.1zm11-8.66a13.96 13.96 0 0 0 0-10.4l33.88-19.55A52.77 52.77 0 0 1 315 281c0 8.94-2.21 17.37-6.12 24.75L275 286.2zm-2-13.86a13.99 13.99 0 0 0-9-5.2v-39.1a52.97 52.97 0 0 1 42.87 24.74L273 272.34zm-57.04-13.3A50.8 50.8 0 0 0 211 281a50.8 50.8 0 0 0 4.96 21.96l30.62-17.68c-.78-2.8-.78-5.76 0-8.56l-30.62-17.68zm4-6.93l30.62 17.68a16.08 16.08 0 0 1 7.42-4.29v-35.35a50.96 50.96 0 0 0-38.04 21.96zm0 57.78A50.96 50.96 0 0 0 258 331.85V296.5a15.98 15.98 0 0 1-7.42-4.29l-30.62 17.68zM266 331.85a50.96 50.96 0 0 0 38.04-21.96l-30.62-17.68a16.08 16.08 0 0 1-7.42 4.29v35.35zm42.04-28.89A50.8 50.8 0 0 0 313 281a50.8 50.8 0 0 0-4.96-21.96l-30.62 17.68c.78 2.8.78 5.76 0 8.56l30.62 17.68zm-4-50.85A50.96 50.96 0 0 0 266 230.15v35.35c2.86.74 5.41 2.25 7.42 4.29l30.62-17.68zM262 290a9 9 0 1 1 0-18 9 9 0 0 1 0 18zm0-2a7 7 0 1 0 0-14 7 7 0 0 0 0 14zM0 242.64l2.76.4 4.75 2.27a38.2 38.2 0 0 1 2.85-3.4l-3.06-4.28-1.69-5.11 3.07-2.58 4.74 2.55 3.69 3.76a37.96 37.96 0 0 1 3.84-2.22l-1.42-5.07.17-5.38 3.76-1.37 3.6 4.02 2.17 4.79c1.42-.34 2.88-.6 4.37-.77L34 225l2-5h4l2 5 .4 5.25c1.49.17 2.95.43 4.37.77l2.18-4.8 3.59-4 3.76 1.36.17 5.38-1.42 5.07c1.33.67 2.6 1.41 3.84 2.22l3.69-3.76 4.74-2.55 3.07 2.58-1.69 5.11-3.06 4.29a38.2 38.2 0 0 1 2.85 3.4l4.75-2.28 5.33-.77 2 3.46-3.33 4.23-4.34 2.98c.59 1.36 1.1 2.75 1.52 4.17l5.23-.52 5.27 1.1.7 3.94-4.58 2.84-5.1 1.31a38.6 38.6 0 0 1 0 4.44l5.1 1.3 4.58 2.85-.7 3.93-5.27 1.1-5.23-.5a36.3 36.3 0 0 1-1.52 4.16l4.34 2.98 3.33 4.23-2 3.46-5.33-.77-4.75-2.27a38.2 38.2 0 0 1-2.85 3.4l3.06 4.28 1.69 5.11-3.07 2.58-4.74-2.55-3.69-3.76a37.96 37.96 0 0 1-3.84 2.22l1.42 5.07-.17 5.38-3.76 1.37-3.6-4.02-2.17-4.79c-1.42.34-2.88.6-4.37.77L42 311l-2 5h-4l-2-5-.4-5.25a37.87 37.87 0 0 1-4.37-.77l-2.18 4.8-3.59 4-3.76-1.36-.17-5.38 1.42-5.07c-1.32-.66-2.6-1.4-3.84-2.22l-3.69 3.76-4.74 2.55-3.07-2.58 1.69-5.11 3.06-4.29a38.2 38.2 0 0 1-2.85-3.4l-4.75 2.28-2.76.4v-8.17l3.1-2.13a37.72 37.72 0 0 1-1.52-4.17l-1.58.16v-8.82l.06-.01a38.6 38.6 0 0 1 0-4.44l-.06-.01v-8.82l1.58.16c.43-1.43.94-2.82 1.52-4.17L0 250.8v-8.17.01zm0 1.87v3.89l5.62 3.84a35.74 35.74 0 0 0-2.55 7.02l-3.07-.3v4.75l2.2.56a36.42 36.42 0 0 0 0 7.46l-2.2.56v4.75l3.07-.3a35.2 35.2 0 0 0 2.55 7.02L0 287.6v3.89l1.76-.26 6.41-3.07c1.4 2.06 3 3.98 4.8 5.71l-4.14 5.78-1.01 3.07 1.22 1.03 2.85-1.52 4.98-5.08c2 1.45 4.16 2.7 6.45 3.73l-1.9 6.84.1 3.23 1.5.55 2.15-2.4 2.94-6.48a35.9 35.9 0 0 0 7.34 1.3L36 311l1.2 3h1.6l1.2-3 .55-7.09a35.9 35.9 0 0 0 7.34-1.29l2.94 6.47 2.15 2.4 1.5-.54.1-3.23-1.9-6.84a35.96 35.96 0 0 0 6.45-3.73l4.98 5.08 2.85 1.52 1.22-1.03-1-3.07-4.15-5.78a35.8 35.8 0 0 0 4.8-5.7l6.4 3.06 3.2.46.8-1.38-2-2.54-5.85-4.01c1.1-2.24 1.95-4.6 2.55-7.02l7.07.7 3.16-.66.28-1.58-2.75-1.7-6.88-1.77c.26-2.48.26-4.98 0-7.46l6.88-1.77 2.75-1.7-.28-1.58-3.16-.66-7.07.7a35.74 35.74 0 0 0-2.55-7.02l5.86-4 2-2.55-.8-1.38-3.2.46-6.41 3.07c-1.4-2.06-3-3.98-4.8-5.71l4.14-5.78 1.01-3.07-1.22-1.03-2.85 1.52-4.98 5.08c-2-1.45-4.16-2.7-6.45-3.73l1.9-6.84-.1-3.23-1.5-.55-2.15 2.4-2.94 6.48a35.9 35.9 0 0 0-7.34-1.3L40 225l-1.2-3h-1.6l-1.2 3-.55 7.09c-2.48.17-4.94.6-7.34 1.29l-2.94-6.47-2.15-2.4-1.5.54-.1 3.23 1.9 6.84a35.96 35.96 0 0 0-6.45 3.73l-4.98-5.08-2.85-1.52-1.22 1.03 1 3.07 4.15 5.78a36.18 36.18 0 0 0-4.8 5.7l-6.4-3.06L0 244.5v.01zM38 272a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0-26a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm24 24a4 4 0 1 1 8 0 4 4 0 0 1-8 0zm-24 24a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm-24-24a4 4 0 1 1-8 0 4 4 0 0 1 8 0zm24-26a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm26 26a2 2 0 1 0 4 0 2 2 0 0 0-4 0zm-26 26a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-26-26a2 2 0 1 0-4 0 2 2 0 0 0 4 0zm3.37 22.63a12 12 0 1 1 16.17-17.74 12 12 0 0 1-16.17 17.74zm0-45.26a12 12 0 1 1 17.74 16.17 12 12 0 0 1-17.74-16.17zm45.26 0a12 12 0 1 1-16.17 17.74 12 12 0 0 1 16.17-17.74zm0 45.26a12 12 0 1 1-17.74-16.17 12 12 0 0 1 17.74 16.17zm-15.56-29.7a10 10 0 1 0 14.39-13.9 10 10 0 0 0-14.39 13.9zm0 14.14a10 10 0 1 0 13.9 14.39 10 10 0 0 0-13.9-14.39zm-14.14 0a10 10 0 1 0-14.39 13.9 10 10 0 0 0 14.39-13.9zm0-14.14a10 10 0 1 0-13.9-14.39 10 10 0 0 0 13.9 14.39zm230.9-245.4l-.08-4.18 1.93-.52 2.04 3.67c1.07-.2 2.16-.35 3.26-.43L270 10h2l1.02 4.07c1.1.08 2.2.22 3.26.43l2.04-3.67 1.93.52-.07 4.19a27 27 0 0 1 3.04 1.26l2.91-3.01 1.74 1-1.16 4.03c.91.62 1.78 1.29 2.61 2l3.6-2.15 1.41 1.41-2.16 3.6c.72.83 1.4 1.7 2 2.6l4.04-1.15 1 1.74-3.01 2.91c.48.98.9 2 1.26 3.04l4.2-.07.5 1.93-3.66 2.04c.2 1.07.35 2.16.43 3.26L303 41v2l-4.07 1.02a26.9 26.9 0 0 1-.43 3.26l3.67 2.04-.52 1.93-4.19-.07a27.82 27.82 0 0 1-1.26 3.04l3.01 2.91-1 1.74-4.03-1.16c-.62.91-1.29 1.78-2 2.61l2.15 3.6-1.41 1.41-3.6-2.16c-.83.72-1.7 1.4-2.6 2l1.15 4.04-1.74 1-2.91-3.01a27 27 0 0 1-3.04 1.26l.07 4.2-1.93.5-2.04-3.66c-1.07.2-2.16.35-3.26.43L272 74h-2l-1.02-4.07a26.9 26.9 0 0 1-3.26-.43l-2.04 3.67-1.93-.52.07-4.19a27.82 27.82 0 0 1-3.04-1.26l-2.91 3.01-1.74-1 1.16-4.03c-.9-.62-1.78-1.29-2.61-2l-3.6 2.15-1.41-1.41 2.16-3.6c-.72-.83-1.4-1.7-2-2.6l-4.04 1.15-1-1.74 3.01-2.91a27 27 0 0 1-1.26-3.04l-4.2.07-.5-1.93 3.66-2.04c-.2-1.07-.35-2.16-.43-3.26L239 43v-2l4.07-1.02c.08-1.1.22-2.2.43-3.26l-3.67-2.04.52-1.93 4.19.07a27 27 0 0 1 1.26-3.04l-3.01-2.91 1-1.74 4.03 1.16c.62-.91 1.29-1.78 2-2.61l-2.15-3.6 1.41-1.41 3.6 2.16c.83-.72 1.7-1.4 2.6-2l-1.15-4.04 1.74-1 2.91 3.01a27 27 0 0 1 3.04-1.26l.01-.01zM271 68a26 26 0 1 0 0-52 26 26 0 0 0 0 52zm0-9a17 17 0 1 1 0-34 17 17 0 0 1 0 34zm0-2a15 15 0 1 0 0-30 15 15 0 0 0 0 30zm0-8a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm0-2a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0-14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm9 9a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm-9 9a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm-9-9a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm47.93 53.79l-1.8-3.91 1.63-1.18 3.15 2.92c.4-.17.82-.3 1.25-.4L315 89h2l.84 4.21c.43.1.85.24 1.25.4l3.15-2.9 1.62 1.17-1.8 3.9c.3.33.55.69.78 1.06l4.26-.5.62 1.9-3.75 2.1c.04.44.04.87 0 1.31l3.75 2.1-.62 1.9-4.26-.5c-.23.38-.49.74-.77 1.06l1.8 3.91-1.63 1.18-3.15-2.92c-.4.17-.82.3-1.25.4L317 113h-2l-.84-4.21c-.43-.1-.85-.24-1.25-.4l-3.15 2.9-1.62-1.17 1.8-3.9a8.03 8.03 0 0 1-.78-1.06l-4.26.5-.62-1.9 3.75-2.1a8.1 8.1 0 0 1 0-1.31l-3.75-2.1.62-1.9 4.26.5c.23-.38.49-.74.77-1.06zM316 106a5 5 0 1 0 0-10 5 5 0 0 0 0 10zM75.73 179.2l-.6-2.1 1.74-1 1.51 1.57a9.93 9.93 0 0 1 2.1-.55L81 175h2l.53 2.12c.72.1 1.42.3 2.09.55l1.51-1.56 1.74 1-.6 2.1c.56.45 1.07.96 1.52 1.52l2.1-.6 1 1.74-1.56 1.51c.25.67.44 1.37.55 2.1L94 186v2l-2.12.53a9.9 9.9 0 0 1-.55 2.09l1.56 1.51-1 1.74-2.1-.6a9.93 9.93 0 0 1-1.52 1.52l.6 2.1-1.74 1-1.51-1.56c-.67.25-1.37.44-2.1.55L83 199h-2l-.53-2.12c-.71-.1-1.42-.3-2.09-.55l-1.51 1.56-1.74-1 .6-2.1a9.93 9.93 0 0 1-1.52-1.52l-2.1.6-1-1.74 1.56-1.51a9.93 9.93 0 0 1-.55-2.1L70 188v-2l2.12-.53c.1-.72.3-1.42.55-2.09l-1.56-1.51 1-1.74 2.1.6c.45-.56.96-1.07 1.52-1.52v-.01zm2.15.94a8.04 8.04 0 0 0-2.74 2.74l-.14.25a7.96 7.96 0 0 0 0 7.74l.14.25a8.04 8.04 0 0 0 2.74 2.74l.25.14a7.96 7.96 0 0 0 7.74 0l.25-.14a8.04 8.04 0 0 0 2.74-2.74l.14-.25a7.96 7.96 0 0 0 0-7.74l-.14-.25a8.04 8.04 0 0 0-2.74-2.74l-.25-.14a7.96 7.96 0 0 0-7.74 0l-.25.14zM82 193a6 6 0 1 1 0-12 6 6 0 0 1 0 12zm0-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm278 3.18l-3.8 5.6-7.18-3.51 2.6-8.07a32.15 32.15 0 0 1-3.07-2.46l-7.27 4.35-5.04-6.22 5.82-6.26c-.64-1.13-1.2-2.3-1.7-3.52l-8.45.73-1.8-7.8 7.95-3.07a32.5 32.5 0 0 1 0-3.9l-7.95-3.07 1.8-7.8 8.45.73a31.7 31.7 0 0 1 1.7-3.52l-5.82-6.26 5.04-6.22 7.27 4.35c.97-.88 2-1.7 3.07-2.46l-2.6-8.07 7.19-3.5 3.79 5.59v64.36zm0-3.53v-57.3l-4.46-6.58-4.1 2 2.53 7.87a30.14 30.14 0 0 0-5.13 4.1l-7.08-4.24-2.88 3.55 5.65 6.09a29.87 29.87 0 0 0-2.82 5.86l-8.24-.7-1.03 4.46 7.73 2.99a30.34 30.34 0 0 0 0 6.5l-7.73 3 1.03 4.45 8.24-.7a29.87 29.87 0 0 0 2.82 5.86l-5.65 6.1 2.88 3.54 7.08-4.23a30.14 30.14 0 0 0 5.13 4.09l-2.54 7.86 4.11 2 4.46-6.57zm0-51.57v5.71l-3.56-3.8a24.94 24.94 0 0 1 3.56-1.91zm0 22.68l-14.17 6.64c-2.5-9.5.77-19.57 8.38-25.78l5.79 10.5v8.64zm0 23.16a25.08 25.08 0 0 1-13.32-13.9l13.32-2.55v16.45zm0-43.64l-.39.2.39.4v-.6zm0 18.29v-2.35l-6.3-11.44a22.93 22.93 0 0 0-6.43 19.76l12.73-5.97zm0 23.15v-12.23l-10.47 2.01A23.1 23.1 0 0 0 360 182.72zM0 129.82l1 1.46a31.8 31.8 0 0 1 3.8-.86L6 122h8l1.2 8.42c1.3.21 2.57.5 3.8.86l4.8-7.06 7.18 3.51-2.6 8.07c1.07.76 2.1 1.58 3.07 2.46l7.27-4.35 5.04 6.22-5.82 6.26c.64 1.13 1.2 2.3 1.7 3.52l8.45-.73 1.8 7.8-7.95 3.07c.08 1.3.08 2.6 0 3.9l7.95 3.07-1.8 7.8-8.45-.73a33.5 33.5 0 0 1-1.7 3.52l5.82 6.26-5.04 6.22-7.27-4.35c-.97.88-2 1.7-3.07 2.46l2.6 8.07-7.19 3.5-4.78-7.05c-1.24.36-2.51.65-3.8.86L14 202H6l-1.2-8.42a31.8 31.8 0 0 1-3.8-.86l-1 1.46v-64.36zm0 3.53v57.3l.2-.29c2.02.7 4.15 1.2 6.34 1.44l1.17 8.2h4.58l1.17-8.2c2.2-.25 4.32-.74 6.35-1.44l4.65 6.87 4.1-2-2.53-7.87a30.14 30.14 0 0 0 5.13-4.1l7.08 4.24 2.88-3.55-5.65-6.09c1.14-1.83 2.1-3.8 2.82-5.86l8.24.7 1.03-4.46-7.73-2.99a30.7 30.7 0 0 0 0-6.5l7.73-3-1.03-4.45-8.24.7a29.87 29.87 0 0 0-2.82-5.86l5.65-6.1-2.88-3.54-7.08 4.23a30.14 30.14 0 0 0-5.13-4.09l2.54-7.86-4.11-2-4.65 6.86a29.82 29.82 0 0 0-6.35-1.44l-1.17-8.2H7.7l-1.17 8.2c-2.2.25-4.32.74-6.35 1.44l-.19-.29H0zm34.17 35.05l-16.26-7.62a7.94 7.94 0 0 0-.8-2.44l8.68-15.72a24.95 24.95 0 0 1 8.38 25.78zm-.85 2.63a25.01 25.01 0 0 1-21.94 15.93l2.23-17.82a8.3 8.3 0 0 0 2.07-1.5l17.64 3.39zM0 139.08A24.92 24.92 0 0 1 10 137c5 0 9.65 1.47 13.56 4l-12.28 13.1a8.06 8.06 0 0 0-2.56 0L0 144.8v-5.72zm0 22.68v-8.65l2.88 5.23c-.4.77-.66 1.59-.79 2.44l-2.09.98zm0 23.16v-16.45l4.32-.83c.6.6 1.3 1.11 2.07 1.5l2.23 17.82c-2.97-.16-5.9-.85-8.62-2.04zM10 156a6 6 0 1 1 0 12 6 6 0 0 1 0-12zm0 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM0 141.28v.6l9.48 10.13c.35-.02.7-.02 1.04 0l9.87-10.54A22.9 22.9 0 0 0 10 139c-3.58 0-6.98.82-10 2.28zm0 18.29l.34-.16c.09-.34.2-.67.32-.99l-.66-1.2v2.35zm0 23.15c1.97.95 4.1 1.63 6.34 1.99l-1.8-14.33a11.6 11.6 0 0 1-.83-.6l-3.71.7v12.24zm13.66 1.99a23.03 23.03 0 0 0 16.8-12.21l-14.17-2.72c-.27.21-.55.42-.84.6l-1.79 14.33zm19.07-19.17a22.93 22.93 0 0 0-6.42-19.75l-6.97 12.63c.12.32.23.65.32.99l13.07 6.13zM137.54 360l-4.07-5.7 2.83-2.83 6.73 4.8c.7-.56 1.4-1.11 2.14-1.63l-2.9-7.75 3.46-2 5.25 6.38c.82-.37 1.65-.72 2.5-1.03l-.8-8.22 3.86-1.04 3.43 7.52c.88-.15 1.77-.26 2.67-.35L164 340h4l1.36 8.15c.9.09 1.8.2 2.67.35l3.43-7.52 3.86 1.04-.8 8.22c.85.31 1.68.66 2.5 1.03l5.25-6.38 3.46 2-2.9 7.75c.74.52 1.45 1.07 2.14 1.64l6.73-4.8 2.83 2.82-4.07 5.7h-56.92zm2.43 0h52.06l3.9-5.46-.47-.47-6.63 4.73a34.14 34.14 0 0 0-4.4-3.38l2.86-7.63-.58-.33-5.18 6.29a33.79 33.79 0 0 0-5.12-2.13l.78-8.1-.64-.18-3.37 7.42a34.02 34.02 0 0 0-5.5-.72l-1.35-8.04h-.66l-1.34 8.04c-1.88.1-3.72.33-5.5.72l-3.38-7.42-.64.18.78 8.1a33.88 33.88 0 0 0-5.12 2.13l-5.18-6.29-.58.33 2.86 7.63c-1.56 1-3.03 2.14-4.4 3.38l-6.63-4.73-.47.47 3.9 5.46zm9.75 0a28.83 28.83 0 0 1 13.28-4.85V360h-13.28zm32.56 0H169v-4.85c4.9.5 9.42 2.22 13.28 4.85zm-28.66 0H161v-2.54a26.8 26.8 0 0 0-7.38 2.54zm24.76 0a26.8 26.8 0 0 0-7.38-2.54V360h7.38zM358.79 0h-1.21l1.5 3.28a48.3 48.3 0 0 0-5.8 5.8l-9.38-4.3-1.65 2.26 7 7.58a47.84 47.84 0 0 0-3.74 7.33l-10.24-1.2-.86 2.66 8.99 5.05a47.91 47.91 0 0 0-1.28 8.12L332 38.6v2.8l10.12 2.02c.2 2.78.63 5.5 1.28 8.12l-9 5.05.87 2.66 10.24-1.2c1.04 2.54 2.29 5 3.74 7.33l-7 7.58 1.65 2.26 9.38-4.3a48.3 48.3 0 0 0 5.8 5.8l-4.3 9.38 2.26 1.65 2.96-2.73v2.66l-2.84 2.62-4.85-3.52 4.36-9.5a50.31 50.31 0 0 1-3.95-3.95l-9.5 4.36-3.52-4.85 7.08-7.68a49.83 49.83 0 0 1-2.54-4.98l-10.38 1.21-1.85-5.7 9.11-5.12a49.9 49.9 0 0 1-.87-5.52L330 43v-6l10.25-2.05c.19-1.87.48-3.72.87-5.52l-9.11-5.12 1.85-5.7 10.38 1.21c.75-1.71 1.6-3.37 2.54-4.98l-7.08-7.68 3.52-4.85 9.5 4.36a50.31 50.31 0 0 1 3.95-3.95L355.42 0h3.37zM360 52.7l-6.48 3.74A39.86 39.86 0 0 1 350 40a39.9 39.9 0 0 1 3.52-16.44L360 27.3v25.4zm0-39.16v4.52l-2.47-1.43c.77-1.07 1.6-2.1 2.47-3.09zm0 52.92c-.87-.99-1.7-2.02-2.47-3.1l2.47-1.42v4.52zm0-16.07V29.61l-5.5-3.18a37.91 37.91 0 0 0 0 27.14l5.5-3.18zM62.42 360h2.16l3.11-6.78-4.85-3.52-7.68 7.08a49.83 49.83 0 0 0-4.98-2.54l1.21-10.38-5.7-1.85-5.12 9.11a49.9 49.9 0 0 0-5.52-.87L33 340h-6l-2.05 10.25c-1.85.19-3.7.48-5.52.87l-5.12-9.11-5.7 1.85 1.21 10.38c-1.71.75-3.37 1.6-4.98 2.54L0 352.32v5.17-2.5l4.62 4.26a47.84 47.84 0 0 1 7.33-3.74l-1.2-10.24 2.66-.86 5.05 8.99a47.91 47.91 0 0 1 8.12-1.28L28.6 342h2.8l2.02 10.12c2.78.2 5.5.63 8.12 1.28l5.05-9 2.66.87-1.2 10.24c2.54 1.04 5 2.29 7.33 3.74l7.58-7 2.26 1.65-2.8 6.1zM360 244.51l-1.44-.2-.8 1.38 2 2.54.24.17v-3.89zm0 14.45l-4-.4-3.16.66-.28 1.58 2.75 1.7 4.69 1.2v-4.74zm0 13.33l-4.7 1.2-2.74 1.71.28 1.58 3.16.66 4-.4v-4.75zm0 15.31l-.24.17-2 2.54.8 1.38 1.44-.2v-3.89zm0 5.76l-2.57.37-2-3.46 3.33-4.23 1.24-.85v8.17zm0-14.31l-3.65.36-5.27-1.1-.7-3.94 4.58-2.84 5.04-1.3v8.82zm0-13.28l-5.04-1.3-4.58-2.84.7-3.93 5.27-1.1 3.65.35v8.82zm0-14.96l-1.24-.85-3.33-4.23 2-3.46 2.57.37v8.17zm0 101.5V360h-4.58l-3.11-6.78 4.85-3.52 2.84 2.62v-.01zm0 2.67l-2.96-2.73-2.26 1.65 2.8 6.1H360v-5.02z'%3E%3C/path%3E%3C/svg%3E");
}

.my-table {
	border: 1px solid #d9d9d9 !important;
	border-radius: 3px;
}
 .my-table th {
	box-shadow: unset !important;
	border: 1px solid #d9d9d9 !important;
}
 .xtab-content .table td,  .xtab-content .table th {
	padding: 7px 12px;
}
.xtab-content .table td:last-child {
	text-align: center;
}
.text-red {
	color:red;
}
 .text-green {
	color:green;
}





@media (min-width: 1360px) {
	.container.container-left-padding {
		padding-left: 7rem !important;
	}
}
.adap_day{
 margin-right: -80px;
}

.modal-backdrop {
	opacity: 0.4;
}

#other_data{
	width: 100%;
}
.none-check{
	color: #28a745;
}

.my-label-6{
	display: flex;
	flex-direction: column;
	text-align: center;
	width: fit-content;
	align-items: center;
	background: #e9ecef;
	margin: 0 auto;
	border-radius: 3px
}

.listSearchResult{
	border: 1px solid #d9d9d9;
	margin-top: 10px;
	overflow-x: hidden;
	max-height: 200px;
	display:none;
}



.adaptation_talk {
	display: flex;
}
.adaptation_talk .div_1 {
	/*margin-right: 7px;*/
	font-size: 12px;
	width: 69px;
	font-weight: 600;
}
.adaptation-title {
	font-size: 14px;
	color: #045e91;
	font-weight: 700;
}
.adaptation_talk .div_2 input{
	width: 120px;
	padding: 2px 5px !important;
	height: 30px;
	font-size: 12px;

}
.adaptation_talk .div_2 {
	display: flex;
	flex-direction:column;
	margin-bottom: 10px;
}
.adaptation_talk .div_3 {
	flex: 4;
	margin-left: 7px;
}
.adaptation_talk .div_3 textarea{
	width: 100%;


	border-radius:3px;
	font-size: 12px;
}
input[type="checkbox"],
input[type="radio"] {
	width: auto !important;
}

.xtab-pane{
	margin-bottom: 0!important;
}


</style>

<style lang="scss">
.card-profile-edit{
	padding: 20px;
	// margin: 20px 0;
	border: 1px solid #ddd;

	border-radius: 10px;
	background-color: #fff;

	.add-info-title{
		margin: 5px 20px;

		font-size: 16px;
		font-weight: 500;
		color: #999;
	}
	.table{
		margin: 0;
		border: none;

		th,td{
			padding: 5px 20px;
			border-left: 1px solid #ddd;

			&:first-child{
				border-left: none;
			}
		}
	}
}

.UserEditView{
	&-scrollCard{
		height: 200px;
		max-height: 200px;
	}
}
</style>
