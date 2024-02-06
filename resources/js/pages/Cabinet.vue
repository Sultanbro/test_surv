<template>
	<div
		v-if="authRole"
		class="d-flex PageCabinet"
	>
		<!-- left sidebar -->
		<div class="lp cabinet-lp">
			<h1 class="page-title">
				Настройка кабинета
			</h1>
			<div class="settingCabinet">
				<ul class="p-0">
					<li class="position-relative lp-item">
						<a
							class="lp-link"
							:class="{ active: page == 'profile' }"
							tabindex="0"
							@click="page = 'profile'"
						>
							<i class="fa fa-user" />
							Настройка собственного профиля
						</a>
					</li>
					<li
						v-if="user.is_admin === 1"
						class="lp-item"
					>
						<a
							class="lp-link"
							:class="{ active: page == 'admin' }"
							tabindex="0"
							@click="page = 'admin'"
						>
							<i class="fa fa-key" />
							Административные настройки
						</a>
					</li>
				</ul>
			</div>
		</div>

		<!-- Cabinet page -->
		<div
			v-if="page == 'admin'"
			class="rp cabinet-page-admin"
			style="flex: 1 1 0%"
		>
			<div class="hat">
				<div class="d-flex jsutify-content-between hat-top">
					<div class="bc">
						<a href="#">Настройка кабинета</a>
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div class="p-3">
					<div class="form-group d-flex aic">
						<label class="mr-3 mb-0 w-200px">Субдомен</label>
						<input
							id="view_own_orders"
							v-model="domain"
							class="form-control mt-1 input-surv"
							type="text"
							:disabled="true"
						>
					</div>

					<!-- Статус: скрыто. Компонент: pages/Cabinet. Дата скрытия: 21.02.2023 15:30 -->
					<div
						v-if="false"
						class="form-group d-flex aic"
					>
						<label class="mb-0 mr-3 w-200px">Часовой пояс</label>
						<input
							id="view_own_orders"
							class="form-control mt-1 input-surv"
							type="text"
						>
					</div>

					<div class="form-group d-flex aic">
						<label class="mb-0 mr-3 w-200px">Администраторы</label>
						<div
							class="PageCabinet-badges form-control"
							@click="isAdminsSelect = true"
						>
							<template v-for="admin, index in admins">
								<b-badge
									v-if="admin.email"
									:key="index"
								>
									{{ admin.email }}
								</b-badge>
							</template>
							&nbsp;
						</div>
					</div>

					<div class="form-group d-flex aic">
						<label class="mb-0 mr-3 w-200px">Кто может писать в&nbsp;общий чат</label>
						<div
							class="PageCabinet-badges form-control"
							@click="isGeneralChatUsersOpen = true"
						>
							<template v-for="chatUser, index in generalChatUsers">
								<b-badge
									v-if="chatUser.email"
									:key="index"
								>
									{{ chatUser.email }}
								</b-badge>
							</template>
							&nbsp;
						</div>
					</div>

					<div
						v-if="authRole.is_admin === 1"
						class="d-flex aic video-add-content"
					>
						<label class="w-200px mb-0 mr-3">Вводное видео</label>
						<div class="d-flex aic w-100">
							<div class="form-group w-100">
								<img
									id="info1"
									src="/images/dist/profit-info.svg"
									class="img-info"
									alt="info icon"
								>
								<b-popover
									target="info1"
									triggers="hover"
									placement="right"
								>
									<p style="font-size: 15px">
										Вставьте ссылку на видео с YouTube. Каждому новому зарегистрированному пользователю будет показываться вступительное видео, которое вы загрузите.
									</p>
								</b-popover>
								<input
									id="videoUrl"
									v-model="videoUrl"
									class="form-control videoDays"
									type="text"
									placeholder="Вставьте ссылку на youtube"
								>
							</div>
							<div class="form-group w-25 ml-4">
								<img
									id="info2"
									src="/images/dist/profit-info.svg"
									class="img-info"
									alt="info icon"
								>
								<b-popover
									target="info2"
									triggers="hover"
									placement="right"
								>
									<p style="font-size: 15px">
										Сколько дней с даты регистрации пользователя отображать выбранное Вами видео в профиле
									</p>
								</b-popover>
								<input
									id="videoTime"
									v-model="videoDays"
									class="form-control"
									type="number"
								>
							</div>
						</div>
					</div>

					<hr>
					<div class="row">
						<div class="col-12 col-md-6">
							<button
								class="btn btn-success"
								@click="save"
							>
								Сохранить
							</button>
						</div>
						<div
							v-if="videoId"
							class="col-12 col-md-6"
						>
							<div class="youtube-content">
								<iframe
									:src="`https://www.youtube.com/embed/${videoId}`"
									title="YouTube video player"
									frameborder="0"
									allowfullscreen
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Profile page -->
		<div
			v-if="page == 'profile'"
			class="rp"
		>
			<div class="hat">
				<div class="d-flex jsutify-content-between hat-top">
					<div class="bc">
						<a href="#">Настройка профиля</a>
					</div>
				</div>
			</div>

			<div class="content">
				<div class="row m-0 mt-2">
					<!-- profile data -->
					<div class="col-8">
						<div class="form-group row mt-3">
							<label class="col-sm-4 col-form-label font-weight-bold label-surv">
								Имя <span class="red">*</span>
							</label>

							<div class="col-sm-8 p-0">
								<input
									id="firstName"
									v-model="user.name"
									class="form-control input-surv"
									type="text"
									name="name"
									required
									placeholder="Имя сотрудника"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Фамилия <span class="red">*</span></label>
							<div class="col-sm-8 p-0">
								<input
									id="lastName"
									v-model="user.last_name"
									class="form-control input-surv"
									type="text"
									name="last_name"
									required
									placeholder="Фамилия сотрудника"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>
								Email <span class="red">*</span>
								<img
									v-b-popover.click.blur.html="'При смене адреса email входить нужно через новый email'"
									src="/images/dist/profit-info.svg"
									width="20"
									class="img-info ml-2"
									alt="info icon"
									tabindex="-1"
								>
							</label>
							<div class="col-sm-8 p-0">
								<input
									id="email"
									v-model="user.email"
									class="form-control input-surv"
									type="text"
									name="email"
									required
									placeholder="email"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>
								Телефон <span class="red">*</span>
								<img
									v-b-popover.click.blur.html="'Так руководитель сможет с вами связаться, а также этот номер нужен для подписи договоров'"
									src="/images/dist/profit-info.svg"
									width="20"
									class="img-info ml-2"
									alt="info icon"
									tabindex="-1"
								>
							</label>
							<div class="col-sm-8 p-0">
								<input
									v-model="phone"
									class="form-control input-surv PageCabinet-phone"
									type="text"
									name="phone"
									required
									placeholder="телефон"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Новый пароль</label>
							<div class="col-sm-8 p-0">
								<input
									id="new_pwd"
									v-model="password"
									minlength="5"
									class="form-control input-surv"
									type="password"
									name="new_pwd"
									placeholder="********"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>День рождения <span class="red">*</span></label>
							<div class="col-sm-8 p-0">
								<input
									id="birthday"
									v-model="birthday"
									class="form-control input-surv"
									type="date"
									name="birthday"
									required
								>
							</div>
						</div>
						<!-- <div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Город<span class="red">*</span></label>
							<div class="col-sm-8 p-0">
								<input
									v-model="keywords"
									class="form-control input-surv"
									type="text"
									name="country"
									id="country"
									required
									placeholder="поиск городов"
								>
								<ul
									v-if="country_results.length > 0"
									class="p-0 countries"
								>
									<li
										v-for="(result, index) in country_results"
										:key="index"
									>
										<a @click="selectedCountry(index, result)">
											Страна: {{ result.country }} Город: {{ result.city }}
										</a>
									</li>
								</ul>
							</div>
						</div> -->
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Город<span class="red">*</span></label>
							<div class="col-sm-8 p-0">
								<LocalitySelect
									:value="keywords"
									@change="selectCity"
								/>
							</div>
						</div>
					</div>

					<!-- profile image -->
					<div class="col-3">
						<div class="form-group mb-0 text-center">
							<div class="profile-img-wrap hidden-file-wrapper">
								<img
									v-if="!crop_image.hide"
									alt="Profile image"
									:src="crop_image.image"
									class="profile-img"
								>
								<div
									v-else
									class="my-4 text-left"
								>
									Загрузите свою фотографию
								</div>
								<input
									id="CabinetProfileImage"
									ref="file"
									type="file"
									class="hidden-file-input"
									aria-describedby="CabinetProfileImage"
									accept="image/*"
									@change="handleFileUpload()"
								>
								<label
									class="hidden-file-label"
									for="CabinetProfileImage"
								/>
							</div>

							<div class="hidden-file-wrapper">
								<button class="btn btn-success w-100 mt-2">
									Выбрать фото
								</button>
								<label
									class="hidden-file-label"
									for="CabinetProfileImage"
								/>
							</div>
						</div>
					</div>
					<div class="col-12 mt-3">
						<!-- Cards -->
						<template v-if="payments_view">
							<div
								v-for="(payment, index) in payments"
								:key="index"
								class="col-12 p-0 row payment-profile"
							>
								<div class="col-2">
									<input
										v-model="payment.bank"
										class="form-control input-surv"
										placeholder="Банк"
									>
								</div>
								<div class="col-2">
									<input
										v-model="payment.country"
										class="form-control input-surv"
										placeholder="Страна"
									>
								</div>
								<div class="col-2">
									<input
										v-model="payment.cardholder"
										class="form-control input-surv"
										placeholder="Имя на карте"
									>
								</div>
								<div class="col-2">
									<input
										v-model="payment.phone"
										class="form-control input-surv"
										placeholder="Телефон"
									>
								</div>
								<div class="col-2">
									<input
										v-model="payment.number"
										v-mask="`#### #### #### ####`"
										class="form-control card-number input-surv"
										placeholder="Номер карты"
									>
								</div>
								<div class="col-2 position-relative">
									<button
										v-if="payment.id"
										style="position: absolute; left: 0px"
										class="btn btn-danger card-delete rounded mt-1"
										@click="removePaymentCart(index, payment.id)"
									>
										<span class="fa fa-trash" />
									</button>
									<button
										v-else
										style="position: absolute; left: 0px"
										class="btn btn-primary card-delete rounded mt-1"
										@click="removePaymentCart(index, 'dev')"
									>
										<span class="fa fa-trash" />
									</button>
								</div>
							</div>
						</template>

						<div
							v-if="cardValidatre.error"
							class="mt-2 p-0"
						>
							<div class="alert alert-danger">
								<span>Заполните все поля</span>
							</div>
						</div>

						<div class="p-0 row mt-5">
							<div class="col-3">
								<button
									style="color: white"
									class="btn btn-phone btn-primary"
									@click="addPayment()"
								>
									Добавить карту
									<img
										v-b-popover.hover.html="'Добавьте не заблокированную карту на которую вам будет перечисляться зарплата'"
										src="/images/dist/profit-info.svg"
										width="20"
										class="img-info ml-2 img-info-bg"
										alt="info icon"
										tabindex="-1"
									>
								</button>
							</div>

							<div class="col-3">
								<button
									style="color: white"
									class="btn btn-success"
									type="button"
									@click.prevent="editProfileUser()"
								>
									Сохранить
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<b-modal
			v-model="showChooseProfileModal"
			title="Изображение профиля"
			size="lg"
			class="modalle"
			@ok="save_picture()"
		>
			<div id="cabinet-croppie" />
		</b-modal>

		<JobtronOverlay
			v-if="isAdminsSelect"
			@close="isAdminsSelect = false"
		>
			<AccessSelect
				:value="adminsForm"
				:tabs="['Сотрудники']"
				:access-dictionaries="accessDictionariesAdmins"
				search-position="beforeTabs"
				:submit-button="'Применить'"
				class="PageCabinet-accessSelect"
				@submit="onSubmitAdmins"
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isGeneralChatUsersOpen"
			@close="isGeneralChatUsersOpen = false"
		>
			<AccessSelect
				:value="generalChatUsers"
				:tabs="[]"
				:access-dictionaries="accessDictionariesChat"
				search-position="beforeTabs"
				:submit-button="'Применить'"
				class="PageCabinet-accessSelect"
				@submit="onSubmitGeneralChatUsers"
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import { mapGetters, mapActions } from 'vuex'
import 'vue-advanced-cropper/dist/style.css'
import { bus } from '../bus'
import {mask} from 'vue-the-mask'
import LocalitySelect from '@ui/LocalitySelect.vue'
import JobtronOverlay from '@ui/Overlay'
import AccessSelect from '@ui/AccessSelect/AccessSelect'

import API from '@/components/Chat/Store/API.vue'

const regex = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|v=)([^#]*).*/;
export default {
	name: 'PageCabinet',
	directives: {mask},
	components:{
		LocalitySelect,
		JobtronOverlay,
		AccessSelect,
	},
	props: {
		authRole: {
			type: Object,
			default: () => ({})
		},
	},
	data() {
		return {
			domain: window.location.hostname.split('.')[0],
			videoUrl: null,
			videoDays: 7,
			crop_image: {
				canvas: '',
				image: '',
				hide: false
			},
			imagePreview: '',
			file: '',
			showChooseProfileModal: false,
			test: 'dsa',
			items: [],
			myCroppa: {},
			user: [],
			user_card: [],
			phone: '',

			activeCourse: null,
			page: 'profile',
			img: '',
			success: '',
			password: '',
			working_city: '',
			birthday: '',
			cardValidatre: {
				error: false,
				type: false,
			},
			payments: [
				{
					bank: '',
					cardholder: '',
					country: '',
					number: '',
					phone: '',
				},
			],
			keywords: null,
			country_results: [],
			image: '',
			payments_view:false,
			croppie: null,
			geo_lat: 0,
			geo_lon: 0,

			isAdminsSelect: false,
			admins: [],
			adminsForm: [],

			isGeneralChatUsersOpen: false,
			generalChatUsers: [],
			generalChatUsersOld: [],
		};
	},
	computed: {
		...mapGetters([
			'users',
			'positions',
			'profileGroups',
			'accessDictionaries',
		]),
		accessDictionariesAdmins(){
			const newUsers = JSON.parse(JSON.stringify(this.accessDictionaries.users))
			this.adminsForm.forEach(admin => {
				const exists = newUsers.find(user => user.id === admin.id)
				if(!exists) newUsers.push(admin)
			})
			return {
				positions: this.accessDictionaries.positions,
				profile_groups: this.accessDictionaries.profile_groups,
				users: newUsers,
			}
		},
		accessDictionariesChat(){
			const newUsers = JSON.parse(JSON.stringify(this.accessDictionaries.users))
			this.generalChatUsers.forEach(admin => {
				const exists = newUsers.find(user => user.id === admin.id)
				if(!exists) newUsers.push(admin)
			})
			return {
				positions: this.accessDictionaries.positions,
				profile_groups: this.accessDictionaries.profile_groups,
				users: newUsers,
			}
		},
		uploadedImage() {
			return Object.keys(this.myCroppa).length !== 0;
		},
		videoId() {
			if (!this.videoUrl) return '';
			return this.getYoutubeVideoId(this.videoUrl)
		},
		videoYoutube() {
			return this.videoId ? `https://www.youtube.com/watch?v=${this.videoId}` : null;
		},
		isYoutubeLinkValid() {
			return regex.test(this.videoUrl);
		},
		generalChatUserToAdd(){
			return this.generalChatUsers.filter(user => {
				return !this.generalChatUsersOld.find(oldUser => oldUser.id === user.id)
			})
		},
		generalChatUserToRemove(){
			return this.generalChatUsersOld.filter(oldUser => {
				return !this.generalChatUsers.find(user => user.id === oldUser.id)
			})
		},
	},
	watch: {
		keywords() {
			this.fetch();
		},
		authRole() {
			this.init()
		},
		page(){
			setTimeout(this.applyMask, 100)
		}
	},
	mounted() {
		this.axios.get('/portal/current').then(res => {
			this.videoUrl = res.data.data.main_page_video;
			this.videoDays = res.data.data.main_page_video_show_days_amount;
		})
		this.applyMask()
	},
	created() {
		this.initMask()
		if(!this.users.length) this.loadCompany()
		if (this.authRole) {
			this.init()
		}
	},
	methods: {
		...mapActions(['loadCompany']),
		getYoutubeVideoId(url) {
			const urlObj = new URL(url)
			if (urlObj.pathname.indexOf('embed') > -1) return urlObj.pathname.split('/')[2]
			return urlObj.searchParams.get('v')
		},
		init() {
			this.fetchGeneralChat();
			this.fetchData();
			this.user = this.authRole;
			this.format_date(this.user.birthday);

			if (this.user.img_url != null) {
				this.image = '/users_img/' + this.user.img_url;
			}

			if (this.user.cropped_img_url != null && this.user.cropped_img_url !== '') {
				this.crop_image.image = '/cropped_users_img/' + this.user.cropped_img_url;
			}
			else if (this.user.img_url != null && this.user.img_url !== '') {
				this.crop_image.image = '/users_img/' + this.user.img_url;
			}
			else {
				this.crop_image.hide = true;
			}


		},
		drawProfile() {},
		change({canvas}) {
			this.crop_image.canvas = canvas;
		},
		save_picture(){
			this.croppie.result({
				type: 'blob',
				format: 'jpeg',
				quality: 0.8
			}).then(blob => {
				const formData = new FormData();
				formData.append('file', blob);
				this.axios.post('/profile/upload/image/profile/', formData).then((response) => {
					bus.$emit('user-avatar-update', '/users_img/' + response.data.filename)
				});
			})

			this.saveCropped();
		},
		chooseProfileImage(){
			this.showChooseProfileModal = true;
		},
		saveCropped() {
			this.croppie.result({
				type: 'blob',
				format: 'jpeg',
				quality: 0.8
			}).then(blob => {
				let loader = this.$loading.show()
				const formData = new FormData()
				formData.append('file', blob)
				this.axios
					.post('/profile/save-cropped-image', formData)
					.then(res => {
						loader.hide()
						this.crop_image.image = '/cropped_users_img/' + res.data.filename
						this.crop_image.hide = false
					})
					.catch(err => {
						loader.hide()
						console.error(err, 'error')
					});
			})
		},
		handleFileUpload(){
			/* global Croppie */
			this.file = this.$refs.file.files[0];

			let reader  = new FileReader();
			reader.addEventListener('load', function () {
				this.imagePreview = reader.result;
				this.croppie = new Croppie(document.getElementById('cabinet-croppie'), {
					enableExif: true,
					viewport: {
						width:200,
						height:200,
						type:'square'
					},
					boundary:{
						width:300,
						height:300
					}
				})
				this.croppie.bind({
					url: reader.result
				})
			}.bind(this), false);

			if(this.file){
				// jfif может быть проблемой, но игнорить совсем нельзя в в11 хром так сохраняет изображения
				if ( /\.(jpe?g|png|gif|jfif)$/i.test( this.file.name ) ) {
					this.showChooseProfileModal = true
					reader.readAsDataURL( this.file );
				}
				else{
					this.$toast.error('Неподдерживаемый формат: ' + this.file.name.split('.').reverse()[0])
				}
			}
		},

		selectCity(res){
			this.keywords = 'Страна: ' + res.country + ' Город: ' + res.name
			this.geo_lat = res.coords[0]
			this.geo_lon = res.coords[1]
		},

		selectedCountry(index, arr) {
			this.keywords = 'Страна ' + arr.country + ' Город ' + arr.city;
			this.working_city = arr.id;
		},

		format_date(value) {
			if (value) {
				return (this.birthday = this.$moment(String(value)).format('YYYY-MM-DD'));
			}
		},

		addPayment() {
			this.payments_view = true;

			this.payments.push({
				bank: '',
				cardholder: '',
				country: '',
				number: '',
				phone: '',
			});
		},

		removePaymentCart(index, type_id) {
			let confirmDelte = confirm(
				'Вы действительно хотите безвозвратно удалить ?'
			);

			if (confirmDelte) {
				this.payments.splice(index, 1);

				if (type_id != 'dev') {
					this.axios
						.post('/profile/remove/card/', {
							card_id: type_id,
						})
						.then(() => {
							this.$toast.success('Успешно Удалено');
						})
						.catch((error) => {
							alert(error);
						});
				}
			}
		},

		editProfileUser() {
			this.cardValidatre.type = false;
			this.cardValidatre.error = false;

			if (this.payments.length > 0){
				this.payments.forEach((el) => {
					this.cardValidatre.type = true;

					if (
						el['bank'] != null
						&& el['cardholder'] != null
						&& el['country'] != null
						&& el['number'] != null
						&& el['phone'] != null
					) {
						if (
							el['bank'].length > 2
							&& el['cardholder'].length > 2
							&& el['country'].length > 2
							&& el['number'].length > 2
							&& el['phone'].length > 2
						) {
							this.cardValidatre.type = true;
						}
					}
				});
			}
			else {
				this.cardValidatre.type = true;
			}

			if (this.cardValidatre.type) {
				const request = {
					cards: this.payments,
					query: {
						...this.user,
						phone: this.phone.replace(/[^\d]+/g, ''),
					},
					password: this.password,
					birthday: this.birthday,
					working_city: this.working_city,
					working_country: this.keywords,
				}

				if(this.geo_lat || this.geo_lon){
					request.coordinates = {
						geo_lat: this.geo_lat,
						geo_lon: this.geo_lon,
					}
				}

				this.axios
					.post('/profile/edit/user/cart/', request)
					.then(({data}) => {
						if (data.success) {
							this.$toast.success('Успешно Сохранено');
						}
					});
			}
			else {
				this.cardValidatre.error = true;
			}
		},

		addTag(newTag) {
			const tag = {
				email: newTag,
				id: newTag,
			};
			this.users.push(tag);
		},

		fetchData() {
			this.axios
				.get('/cabinet/get')
				.then(({data}) => {
					this.admins = data.admins;
					this.adminsForm = data.admins.map(admin => ({
						...admin,
						name: admin.email,
						type: 1,
					}))
					this.user = JSON.parse(JSON.stringify(data.user))
					this.phone = data.user.phone
					this.keywords = data.user.working_country;
					this.working_city = data.user.working_city;

					if(data.user.coordinate){
						this.geo_lat = data.user.coordinate.geo_lat
						this.geo_lon = data.user.coordinate.geo_lon
					}

					if (data.user_payment) {
						if (data.user_payment.length > 0) {
							this.payments = data.user_payment;
							this.payments_view = true
						}
						else {
							this.payments = [];
							this.payments_view = false
						}
					}

					if (this.user.img_url) {
						this.img = '/users_img/' + data.user.img_url;
					}
					else {
						this.img = '/users_img/noavatar.png';
					}
					this.drawProfile();
				})
				.catch((error) => {
					alert(error);
				});
		},
		fetchGeneralChat(){
			API.getChatInfo(0, ({users}) => {
				this.generalChatUsers = JSON.parse(JSON.stringify(users)).map(user => {
					user.email = `${user.name} ${user.last_name}`
					user.type = 1
					return user
				})
				this.generalChatUsersOld = JSON.parse(JSON.stringify(users))
			})
		},
		save() {
			this.saveGeneralChat()
			try{
				if ((this.videoDays || this.videoUrl) && this.authRole.is_admin === 1) {
					if (this.videoDays && this.videoUrl) {
						const formData = new FormData()
						formData.append('mainPageVideo', this.videoUrl)
						formData.append('mainPageVideoShowDaysAmount', this.videoDays)
						this.isYoutubeLinkValid
							? this.axios.post('/portal/update', formData)
							: this.$toast.error('Некорректная ссылка youtube')
					}
					else {
						this.$toast.error('Заполните все поля')
					}
				}
				this.axios.post('/cabinet/save', {
					admins: this.admins,
				}).then(() => {
					this.$toast.success('Сохранено')
				}).catch((error) => {
					alert(error)
				})
			}
			catch(err) {
				console.error(err)
				this.$toast.err('Ошибка сохранения')
			}
		},
		async saveGeneralChat(){
			for(const user of this.generalChatUserToAdd){
				await API.addUserToChat(0, user.id)
			}
			for(const user of this.generalChatUserToRemove){
				await API.removeUserFromChat(0, user.id)
			}
		},
		fetch() {
			if (this.keywords != null && this.keywords != undefined) {
				if (this.keywords.length === 0) {
					this.keywords = '';
					this.country_results = [];
				}
				else {
					this.axios
						.post('/profile/country/city/', {
							keyword: this.keywords,
						})
						.then((response) => {
							this.country_results = response.data;
						});
				}
			}
		},
		onSubmitAdmins(newAdmins){
			this.$nextTick(() => {
				this.admins = newAdmins.map(admin => ({
					id: admin.id,
					email: admin.name,
				}))
				this.adminsForm = newAdmins.map(admin => ({
					id: admin.id,
					email: admin.name,
					name: admin.name,
					type: 1,
				}))
				this.isAdminsSelect = false
			})
		},
		onSubmitGeneralChatUsers(users){
			this.$nextTick(() => {
				this.generalChatUsers = users.map(user => ({
					id: user.id,
					email: user.name,
				}))
				this.isGeneralChatUsersOpen = false
			})
		},
		initMask(){
			if(window.intlTelInput) return
			const el = document.createElement('script')
			el.setAttribute('src', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js')
			document.head.appendChild(el)

			const link = document.createElement('link')
			link.rel = 'stylesheet'
			link.href = 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css'
			document.head.appendChild(link)
		},
		applyMask(){
			if(!window.intlTelInput) return setTimeout(this.applyMask, 100)
			const phones = this.$el.querySelectorAll('.PageCabinet-phone')
			phones.forEach(input => {
				window.intlTelInput(input, {
					utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js',
					autoInsertDialCode: true,
					preferredCountries: ['kz', 'ru', 'kg', 'uz'],
					nationalMode: false,
					autoPlaceholder: 'aggressive',
					numberType: 'MOBILE',
					// separateDialCode: true,
					// hiddenInput: true,
				})
			})
		},
	},
};
</script>

<style lang="scss">
.video-add-content{
	.form-group{
		position: relative;
	}
	.img-info{
		position: absolute;
		top: -9px;
		right: -8px;
		background: #fff;
		border-radius: 50%;
		z-index: 3;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
		appearance: textfield;
	}
}
.no-youtube{
	height: auto;
	img{
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
}
.youtube-content{
	position: relative;
	padding-bottom: 56.25%;
	iframe{
		position: absolute;
		width: 100%!important;
		height: 100%!important;
	}
}
.container-left-padding{
	padding-top: 0;
}
.cabinet-page-admin{
	.multiselect__tag{
		display: inline-block !important;
		max-width: 100% !important;
		margin-bottom: 0.5rem !important;
	}
}
.upload-to-server-example {
	.upload-example-cropper {
		border: solid 1px #eee;
		min-height: 300px;
		max-height: 500px;
		width: 100%;
	}
	.cropper-wrapper {
		position: relative;
	}
	.reset-button {
		position: absolute;
		right: 20px;
		bottom: 20px;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		height: 42px;
		width: 42px;
		background: rgba(#3fb37f, 0.7);
		transition: background 0.5s;
		&:hover {
			background: #3fb37f;
		}
	}
	.button-wrapper {
		display: flex;
		justify-content: center;
		margin-top: 17px;
	}
	.button {
		color: white;
		font-size: 16px;
		padding: 10px 20px;
		background: #3fb37f;
		cursor: pointer;
		transition: background 0.5s;
		width: 100%;
		text-align: center;
	}
	.button:hover {
		background: #38d890;
	}
	.button input {
		display: none;
	}
}

///////////////sss
.upload-example {
	margin-top: 20px;
	margin-bottom: 20px;
	user-select: none;
	&__cropper {
		border: solid 1px #eee;
		min-height: 300px;
		max-height: 500px;
		width: 100%;
	}
	&__cropper-wrapper {
		position: relative;
	}
	&__reset-button {
		position: absolute;
		right: 20px;
		bottom: 20px;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		height: 42px;
		width: 42px;
		background: rgba(#3fb37f, 0.7);
		transition: background 0.5s;
		&:hover {
			background: #3fb37f;
		}
	}
	&__buttons-wrapper {
		display: flex;
		justify-content: center;
		margin-top: 17px;
	}
	&__button {
		border: none;
		outline: solid transparent;
		color: white;
		font-size: 16px;
		padding: 10px 20px;
		background: #3fb37f;
		cursor: pointer;
		transition: background 0.5s;
		margin: 0 16px;
		&:hover,
		&:focus {
			background: #38d890;
		}
		input {
			display: none;
		}
	}
	&__file-type {
		position: absolute;
		top: 20px;
		left: 20px;
		background: #0d0d0d;
		border-radius: 5px;
		padding: 0px 10px;
		padding-bottom: 2px;
		font-size: 12px;
		color: white;
	}
}

.contacts-info {
	margin-top: 30px;
}
.lp-item{
	.fa{
		margin-right: 0.25em;
	}
}
a.lp-link {
	display: block;
	margin: 5px 0;
	padding: 5px 0;
	font-weight: bold;
	font-size: 1.4rem;
	cursor: pointer;
	color: #333;

	&.active {
		cursor: default;
		text-decoration: none;
		color: darken(#2459a4, 5%);
		i{
			color: darken(#2459a4, 5%);
		}
		&:hover{
			text-decoration: none;
		}

	}

	&:hover{
		text-decoration: underline dotted;
	}
}

.payment-profile {
	margin-top: 30px;
	margin-bottom: 10px;
}

.addPayment {
	padding: 15px;
	margin-top: -15px;
}

.countries {
	li {
		cursor: pointer;
		background-color: #f5f5f5;
		padding: 10px;
		border-bottom: 1px solid white;
	}
}
.profile-img{
	object-fit: cover;
	display: block;
	width: 100%;
	height: auto;
	border-radius: 1rem;
}
.profile-img-wrap{
	width: 100%;
	max-width: 30rem;
}
.cabinet-lp{
	padding: 4px 10px 10px 10px;
}

.PageCabinet{
	&-accessSelect{
		width: 420px;
		height: 720px;
		max-height: 80vh;
		padding: 20px;
		border-radius: 15px;

		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);

		background-color: #fff;
	}
	&-badges{
		display: flex;
		flex-flow: row wrap;
		align-items: center;
		justify-content: flex-start;
		gap: 5px;

    padding: 10px;
    border: 1px solid #e8e8e8;
		border-radius: 6px;

		font-size: 14px;
		line-height: 1.3;

		background-color: #F7FAFC;
	}
	&-phone{
		&.PageCabinet-phone{
			padding-left: 50px !important;
		}
	}
	.iti{
		display: block;
	}
	.img-info-bg{
		background-color: #fff;
		border-radius: 999em;
	}
}
</style>
