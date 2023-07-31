<template>
	<div
		v-if="auth_role"
		class="d-flex"
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
							@click="page = 'profile'"
							:class="{ active: page == 'profile' }"
							tabindex="0"
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
							@click="page = 'admin'"
							:class="{ active: page == 'admin' }"
							tabindex="0"
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
							class="form-control mt-1 input-surv"
							id="view_own_orders"
							type="text"
							v-model="domain"
							:disabled="true"
						>
					</div>

					<!-- Статус: скрыто. Компонент: pages/Cabinet. Дата скрытия: 21.02.2023 15:30 -->
					<div
						class="form-group d-flex aic"
						v-if="false"
					>
						<label class="mb-0 mr-3 w-200px">Часовой пояс</label>
						<input
							class="form-control mt-1 input-surv"
							id="view_own_orders"
							type="text"
						>
					</div>

					<div class="form-group d-flex aic">
						<label class="mb-0 mr-3 w-200px">Администраторы</label>
						<Multiselect
							v-model="admins"
							:options="users"
							:multiple="true"
							:close-on-select="false"
							:clear-on-select="true"
							:preserve-search="true"
							placeholder="Выберите"
							label="email"
							track-by="email"
							:taggable="true"
							@tag="addTag"
							class="multiselect-surv"
						/>
					</div>

					<div
						class="d-flex aic video-add-content"
						v-if="auth_role.is_admin === 1"
					>
						<label class="w-200px mb-0 mr-3">Вводное видео</label>
						<div class="d-flex aic w-100">
							<div class="form-group w-100">
								<img
									src="/images/dist/profit-info.svg"
									class="img-info"
									alt="info icon"
									id="info1"
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
									class="form-control videoDays"
									id="videoUrl"
									type="text"
									placeholder="Вставьте ссылку на youtube"
									v-model="videoUrl"
								>
							</div>
							<div class="form-group w-25 ml-4">
								<img
									src="/images/dist/profit-info.svg"
									class="img-info"
									alt="info icon"
									id="info2"
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
									class="form-control"
									id="videoTime"
									type="number"
									v-model="videoDays"
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
							class="col-12 col-md-6"
							v-if="videoId"
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
									class="form-control input-surv"
									type="text"
									name="name"
									id="firstName"
									required
									placeholder="Имя сотрудника"
									v-model="user.name"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Фамилия <span class="red">*</span></label>
							<div class="col-sm-8 p-0">
								<input
									class="form-control input-surv"
									type="text"
									name="last_name"
									id="lastName"
									required
									placeholder="Фамилия сотрудника"
									v-model="user.last_name"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Email <span class="red">*</span></label>
							<div class="col-sm-8 p-0">
								<input
									class="form-control input-surv"
									type="text"
									name="email"
									id="email"
									required
									placeholder="email"
									v-model="user.email"
								>
							</div>
						</div>
						<div class="form-group row">
							<label
								class="col-sm-4 col-form-label font-weight-bold label-surv"
							>Новый пароль</label>
							<div class="col-sm-8 p-0">
								<input
									v-model="password"
									minlength="5"
									class="form-control input-surv"
									type="password"
									name="new_pwd"
									id="new_pwd"
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
									v-model="birthday"
									class="form-control input-surv"
									type="date"
									name="birthday"
									id="birthday"
									required
								>
							</div>
						</div>
						<div class="form-group row">
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
											Страна: {{ result.country }} Город: {{ result.city }}</a>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<!-- profile image -->
					<div class="col-3">
						<div class="form-group mb-0 text-center">
							<!-- <canvas id="myCanvas" width="250" height="250" @click="chooseProfileImage()">
							</canvas>-->
							<div class="profile-img-wrap hidden-file-wrapper">
								<img
									v-if="!crop_image.hide"
									alt="Profile image"
									:src="crop_image.image"
									class="profile-img"
								>
								<div
									class="my-4 text-left"
									v-else
								>
									Загрузите свою фотографию
								</div>
								<input
									type="file"
									class="hidden-file-input"
									id="CabinetProfileImage"
									aria-describedby="CabinetProfileImage"
									ref="file"
									accept="image/*"
									@change="handleFileUpload()"
								>
								<label
									class="hidden-file-label"
									for="CabinetProfileImage"
								/>
							</div>




							<!--              <croppa-->
							<!--                v-model="myCroppa"-->
							<!--                :width="250"-->
							<!--                :height="250"-->
							<!--                :canvas-color="'default'"-->
							<!--                :placeholder="'Выберите изображение'"-->
							<!--                :placeholder-font-size="0"-->
							<!--                :placeholder-color="'default'"-->
							<!--                :accept="'image/*'"-->
							<!--                :file-size-limit="0"-->
							<!--                :quality="2"-->
							<!--                :zoom-speed="20"-->
							<!--                :initial-image="crop_image"-->
							<!--                :disable-drag-to-move="true"-->
							<!--                :disable-scroll-to-zoom="true"-->
							<!--                @new-image-drawn="hasImage = true"-->
							<!--                @image-remove="hasImage = false"-->
							<!--                v-on="hasImage ? { click:chooseProfileImage } : {}"-->

							<!--              ></croppa>-->
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
								class="col-12 p-0 row payment-profile"
								v-for="(payment, index) in payments"
								:key="index"
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
										class="form-control card-number input-surv"
										placeholder="Номер карты"
										v-mask="`#### #### #### ####`"
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
							class="mt-2 p-0"
							v-if="cardValidatre.error"
						>
							<div class="alert alert-danger">
								<span>Заполните все поля</span>
							</div>
						</div>

						<div class="p-0 row mt-5">
							<div class="col-3">
								<button
									@click="addPayment()"
									style="color: white"
									class="btn btn-phone btn-primary"
								>
									Добавить карту
								</button>
							</div>

							<div class="col-3">
								<button
									@click.prevent="editProfileUser()"
									style="color: white"
									class="btn btn-success"
									type="button"
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
			<!-- <cropper
				ref="mycrop"
				class="cropper"
				:src="imagePreview"
				:stencil-props="{
					aspectRatio: 12/12
				}"
				@change="change"
			/> -->
		</b-modal>
	</div>
</template>
<script>
// import VueAvatar from '../components/vue-avatar-editor/src/components/VueAvatar.vue'
// import VueAvatarScale from '../components/vue-avatar-editor/src/components/VueAvatarScale'
import Multiselect from 'vue-multiselect'
// import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
import { bus } from '../bus'
import {mask} from 'vue-the-mask'

const regex = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|v=)([^#]*).*/;
export default {
	name: 'PageCabinet',
	directives: {mask},
	components:{
		// Cropper,
		Multiselect,
	},
	props: {
		auth_role: {},
	},
	data() {
		return {
			domain: window.location.hostname.split('.')[0],
			videoUrl: null,
			videoDays: 7,
			// my_crop_image: "",
			crop_image: {
				canvas: '',
				image: '',
				hide: false
			},
			imagePreview: '',
			file: '',
			// hasImage: true,
			// canvas_image: new Image(),
			showChooseProfileModal: false,
			test: 'dsa',
			items: [],
			myCroppa: {},
			users: [],
			user: [],
			user_card: [],
			admins: [],
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
			croppie: null
		};
	},
	computed: {
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
		validateYouTubeLink() {
			return regex.test(this.videoUrl);
		},
	},
	watch: {
		keywords() {
			this.fetch();
		},
		auth_role() {
			this.init()
		}
	},
	mounted() {
		this.axios.get('/portal/current').then(res => {
			this.videoUrl = res.data.data.main_page_video;
			this.videoDays = res.data.data.main_page_video_show_days_amount;
		})
	},
	created() {
		if (this.auth_role) {
			this.init()
		}
	},
	methods: {
		getYoutubeVideoId(url) {
			const urlObj = new URL(url)
			if (urlObj.pathname.indexOf('embed') > -1) return urlObj.pathname.split('/')[2]
			return urlObj.searchParams.get('v')
		},
		init() {
			this.fetchData();
			this.user = this.auth_role;
			this.format_date(this.user.birthday);

			if (this.user.img_url != null) {
				this.image = '/users_img/' + this.user.img_url;
			}

			if (this.user.cropped_img_url != null && this.user.cropped_img_url !== '') {
				this.crop_image.image = '/cropped_users_img/' + this.user.cropped_img_url;
			} else if (this.user.img_url != null && this.user.img_url !== '') {
				this.crop_image.image = '/users_img/' + this.user.img_url;
			} else {
				this.crop_image.hide = true;
			}
		},
		drawProfile() {
			// this.canvas_image.src = this.image;
			//this.myCanvas.drawImage(this.canvas_image, 0, 0, 250, 250);
		},
		change({coordinates, canvas}) {
			this.crop_image.canvas = canvas;
			//this.canvas = canvas;
			//this.myCanvas.clearRect(0, 0, canvas.width, canvas.height);
			//var can = canvas;
			//this.myCanvas.drawImage(this.canvas_image, coordinates.left,  coordinates.top, coordinates.width, coordinates.height, 0, 0, 250, 250);
			console.log(coordinates, canvas)
		},
		save_picture(){
			// this.crop_image.canvas.toBlob(function(blob) {
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
			// });

			this.saveCropped();
		},
		chooseProfileImage(){
			//console.log(this.myCroppa);
			// this.my_crop_image = this.myCroppa.canvas.toDataURL();
			/*axios.post("/getnewimage", {id : this.user.id}).then( (response) => {
				this.image = "/users_img/" + response.data;
			});*/
			this.showChooseProfileModal = true;
		},
		saveCropped() {
			const _this = this;
			// this.crop_image.canvas.toBlob(function(blob) {
			this.croppie.result({
				type: 'blob',
				format: 'jpeg',
				quality: 0.8
			}).then(blob => {
				let loader = _this.$loading.show();
				const formData = new FormData();
				formData.append('file', blob);
				this.axios
					.post('/profile/save-cropped-image', formData)
					.then(function (res) {
						loader.hide();
						_this.crop_image.image = '/cropped_users_img/' + res.data.filename;
						_this.crop_image.hide=false;
					})
					.catch(function (err) {
						console.log(err, 'error');
					});
			})
			//   },
			//   "image/jpeg",
			//   0.8
			// ); // 80% compressed jpeg file
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
						type:'square' //circle
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

			if( this.file ){
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
				this.$toast.success('Успешно Удалено');

				if (type_id != 'dev') {
					this.axios
						.post('/profile/remove/card/', {
							card_id: type_id,
						})
						.then(() => {})
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

					this.cardValidatre.type = false;
					this.cardValidatre.type = true;

					if (
						el['bank'] != null &&
							el['cardholder'] != null &&
							el['country'] != null &&
							el['number'] != null &&
							el['phone'] != null
					) {
						if (
							el['bank'].length > 2 &&
								el['cardholder'].length > 2 &&
								el['country'].length > 2 &&
								el['number'].length > 2 &&
								el['phone'].length > 2
						) {
							this.cardValidatre.type = true;
						}
					}
				});
			}else {
				this.cardValidatre.type = true;
			}


			if (this.cardValidatre.type) {
				this.axios
					.post('/profile/edit/user/cart/', {
						cards: this.payments,
						query: this.user,
						password: this.password,
						birthday: this.birthday,
						working_city: this.working_city,
						working_country: this.keywords,
					})
					.then((response) => {
						if (response.data.success) {
							this.$toast.success('Успешно Сохранено');
						}
					});
			} else {
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
					this.users = data.users;
					this.user = data.user;
					this.keywords = data.user.working_country;
					this.working_city = data.user.working_city;

					if (data.user_payment) {
						if (data.user_payment.length > 0) {
							this.payments = data.user_payment;
							this.payments_view = true
						} else {
							this.payments = [];
							this.payments_view = false
						}

					}

					if (this.user.img_url) {
						this.img = '/users_img/' + data.user.img_url;
					} else {
						this.img = '/users_img/noavatar.png';
					}
					this.drawProfile();
				})
				.catch((error) => {
					alert(error);
				});
		},
		save() {
			try{
				if ((this.videoDays || this.videoUrl) && this.auth_role.is_admin === 1) {
					if(this.videoDays && this.videoUrl){
						const formData = new FormData();
						formData.append('mainPageVideo', this.videoUrl);
						formData.append('mainPageVideoShowDaysAmount', this.videoDays);
						this.validateYouTubeLink ? this.axios.post('/portal/update', formData) : this.$toast.error('Некорректная ссылка youtube');
					} else {
						this.$toast.error('Заполните все поля');
					}
				}
				this.axios
					.post('/cabinet/save', {
						admins: this.admins,
					})
					.then(() => {
						this.$toast.success('Сохранено');
					})
					.catch((error) => {
						alert(error, '6565');
					});
			} catch(err){
				console.log(err);
				this.$toast.err('Ошибка сохранения');
			}

		},

		fetch() {
			if (this.keywords != null && this.keywords != undefined) {
				if (this.keywords.length === 0) {
					this.keywords = '';
					this.country_results = [];
				} else {
					this.axios
						.post('/profile/country/city/', {
							keyword: this.keywords,
						})
						.then((response) => {
							this.country_results = response.data;
						});
				}
			}

			// axios.get('/profile/country/city/', { params: { keywords: this.keywords } })
			//     .then(response => this.results = response.data)
			//     .catch(error => {});
		}
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
</style>
