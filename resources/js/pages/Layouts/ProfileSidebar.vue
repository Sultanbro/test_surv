<template>
	<div
		v-if="isVisible"
		class="ProfileSidebar header__profile _anim _anim-no-hide custom-scroll-y"
		:class="{
			'v-loading': loading,
			hidden: hide,
			'_active': inViewport
		}"
	>
		<div class="profile__content">
			<div class="profile__col">
				<div
					v-if="logo || canChangeLogo"
					class="profile__logo logo-img-wrap"
				>
					<template v-if="canChangeLogo && !logo">
						<img
							src="/images/dist/logo-download.svg"
							alt="logo download"
						>
						Загрузить логотип
					</template>
					<img
						v-if="logo"
						:src="logo"
						class="logo-img"
					>
					<template v-if="canChangeLogo">
						<input
							id="inputGroupFile04"
							ref="file"
							type="file"
							class="hidden-file-input"
							aria-describedby="inputGroupFileAddon04"
							accept="image/*"
							@change="handleFileUpload()"
						>
						<label
							class="hidden-file-label"
							for="inputGroupFile04"
						/>
					</template>
				</div>
				<StartDayBtn
					v-if="showButton"
					:status="buttonStatus"
					:workday-status="status"
					@clickStart="startDay"
				/>
				<div class="profile__balance">
					Текущий баланс
					<p
						v-if="!balance.loading"
						class="profile__balance-value"
					>
						{{ separateNumber(totalBalance) }} <span class="profile__balance-currecy">{{ balance.currency }}</span>
					</p>
				</div>

				<!-- <b-form-select
					v-model="selectedDate"
					:options="monthOptions"
					:disabled="salaryLoading"
					class="mt-4"
				/> -->

				<DateSelect
					v-model="selectedDate"
					:disabled="salaryLoading"
					:only-month="true"
					:start-year="Math.max(new Date(user.created_at).getFullYear(), 2020)"
					class="ProfileSidebar-date mt-4"
				/>

				<b-modal
					id="modal-sm"
					:header-class="{'border-radius':'1rem'}"
					title="Загрузить логотип"
					size="lg"
					hide-footer
				>
					<form class="logo-upload-modal">
						<Cropper
							ref="mycrop"
							class="cropper"
							:src="imagePreview"
							:stencil-props="{ aspectRatio: 32/10 }"
							@change="change"
						/>
						<div class="clearfix mt-3">
							<a
								href="javascript:"
								class="add-btn float-right"
								@click.prevent="uploadLogo()"
							>
								<p>Добавить</p>
							</a>
						</div>
					</form>
				</b-modal>
			</div>

			<div class="profile__col">
				<ProfileInfo :data="userInfo" />
			</div>

			<div
				v-if="videoUrl && isVideoDaysNotGone"
				v-b-modal.modal-youtube
				class="profile-video-image"
			>
				<img
					:src="'https://img.youtube.com/vi/' + youtubeVideoId + '/mqdefault.jpg'"
					alt="youtube"
				>
				<i class="fa fa-play" />
			</div>

			<!-- Статус: скрыто. Компонент: ProfileSidebar. Дата скрытия: 27.01.2023 14:13 -->
			<div
				v-if="false"
				class="profile__col"
			>
				<div class="profile__active profile-box">
					<div class="profile__title _slicked">
						График активности
					</div>
					<div
						class="tabs__include profile-slick"
						style="display: none;"
					>
						<div class="tab__content-include">
							<div
								class="tab__content-item-include is-active"
								data-content="1"
							>
								<img
									src="/images/dist/schedule.png"
									alt="schedule image"
								>
							</div>
							<div
								class="tab__content-item-include"
								data-content="2"
							>
								<img
									src="/images/dist/profile-active.png"
									alt="schedule image"
								>
							</div>
							<div
								class="tab__content-item-include"
								data-content="3"
							>
								<img
									src="/images/dist/schedule.png"
									alt="schedule image"
								>
							</div>
						</div>
						<div class="tabs__wrapper">
							<div
								class="tab__item-include is-active"
								onclick="switchTabsInclude(this)"
								data-index="1"
							>
								День
							</div>
							<div
								class="tab__item-include"
								onclick="switchTabsInclude(this)"
								data-index="2"
							>
								месяц
							</div>
							<div
								class="tab__item-include"
								onclick="switchTabsInclude(this)"
								data-index="3"
							>
								год
							</div>
						</div>
					</div>
					<img
						src="/images/dist/close.svg"
						alt="close icon"
						class="point-close"
					>
				</div>
			</div>
		</div>

		<!-- Corp book page when day has started -->
		<b-modal
			v-model="showCorpBookPage"
			title="Н"
			size="xl"
			class="modalle"
			modal-class="header__profile-corpbook"
			hide-footer
			hide-header
			no-close-on-backdrop
			scrollable
		>
			<div
				v-if="corp_book !== undefined && corp_book !== null"
				class="corpbook"
			>
				<div class="inner">
					<h5 class="text-center aet mb-3">
						Ознакомьтесь с одной из страниц Вашей базы знаний
					</h5>
					<h3 class="text-center">
						{{ corp_book.title }}
					</h3>

					<!-- eslint-disable-next-line -->
					<div v-html="corp_book.text" />

					<button
						id="readCorpBook"
						:disabled="!!bookTimer"
						class="button-blue m-auto mt-5"
						@click="testBook"
					>
						<span
							v-if="bookTimer"
							class="timer"
						>{{ bookTimer }}</span>
						<span
							v-else
							class="text"
						>Я прочитал</span>
					</button>
				</div>
			</div>
		</b-modal>

		<b-modal
			v-model="isBookTest"
			size="xl"
			class="modalle"
			hide-footer
			hide-header
			no-close-on-backdrop
			no-close-on-esc
		>
			<Questions
				v-if="corp_book"
				:course_item_id="0"
				:questions="corp_book.questions"
				:pass_grade="corp_book.questions.length"
				type="kb"
				:dont-repat="true"
				@passed="hideBook"
				@failed="repeatBook"
			/>
		</b-modal>

		<b-modal
			v-if="videoUrl && isVideoDaysNotGone"
			id="modal-youtube"
			modal-class="modal-youtube"
			size="xl"
			centered
			hide-header
			hide-footer
		>
			<iframe
				:src="'https://www.youtube.com/embed/' + youtubeVideoId"
				title="YouTube video player"
				frameborder="0"
				allowfullscreen
			/>
		</b-modal>
	</div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import { mapState, mapActions } from 'pinia'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
import ProfileInfo from '@/pages/Widgets/ProfileInfo'
import StartDayBtn from '@/pages/Widgets/StartDayBtn'
import Questions from '@/pages/Questions'
import { useSettingsStore } from '@/stores/Settings'
import { useProfileStatusStore } from '@/stores/ProfileStatus'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { useProfileCoursesStore } from '@/stores/ProfileCourses'
import { usePersonalInfoStore } from '@/stores/PersonalInfo'
import { usePaymentTermsStore } from '@/stores/PaymentTerms'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions, useMonthOptions } from '@/composables/yearOptions'
import DateSelect from '../Profile/DateSelect'

export default {
	name: 'ProfileSidebar',
	components: {
		ProfileInfo,
		StartDayBtn,
		Questions,
		Cropper,
		DateSelect,
	},
	props: {},
	data: function () {
		return {
			fields: [],
			file: '',
			showPreview: false,
			imagePreview: '',
			loading: false,
			hide: false,
			inViewport: false,
			// corp book
			showCorpBookPage: false,
			bookTimer: 0,
			bookTimerInterval: 0,
			isBookTest: false,
			isRoot: false,
			isProfile: false,
			canvas: null,
			selectedDate: this.$moment(Date.now()).format('DD.MM.YYYY'),
			isDatePicker: false,
			needUpdateSalary: false,
		};
	},
	computed: {
		...mapState(useSettingsStore, ['logo']),
		...mapState(usePersonalInfoStore, ['position', 'groups', 'salary', 'workingDay', 'schedule', 'workingTime']),
		...mapState(useProfileStatusStore, ['status', 'balance', 'corp_book', 'message', 'buttonStatus']),
		...mapState(useSettingsStore, {settingsReady: 'isReady'}),
		...mapState(useProfileStatusStore, {statusReady: 'isReady'}),
		...mapState(useProfileSalaryStore, {salaryReady: 'isReady', salaryLoading: 'isLoading'}),
		...mapState(useProfileCoursesStore, {coursesReady: 'isReady'}),
		...mapState(usePersonalInfoStore, {infoReady: 'isReady'}),
		...mapState(usePaymentTermsStore, {termsReady: 'isReady'}),
		...mapState(usePortalStore, ['portal']),
		...mapState(useProfileSalaryStore, ['user_earnings']),
		...mapGetters(['user']),
		totalBalance(){
			if(!this.user_earnings) return 0
			return (this.user_earnings.sumSalary || 0) + (this.user_earnings.sumKpi || 0) + (this.user_earnings.sumBonuses || 0)
		},
		userInfo(){
			return {
				user: this.user,
				position: this.position,
				groups: this.groups,
				salary: this.salary,
				workingDay: this.workingDay,
				schedule: this.schedule,
				workingTime: this.workingTime,
			}
		},
		canChangeLogo(){
			return this.$laravel.is_admin == 1 || this.$laravel.is_admin == 18
		},
		showButton(){
			if(this.$can('ucalls_view') && !this.$laravel.is_admin) return false
			return this.status === 'started' || (this.userInfo.user && this.userInfo.user.user_type === 'remote')
		},
		isReady(){
			return this.settingsReady
				&& this.statusReady
				&& this.salaryReady
				&& this.coursesReady
				&& this.infoReady
				&& this.termsReady
		},
		isVisible(){
			return this.isReady || this.$viewportSize.width > 900
		},
		videoUrl(){
			return this.portal.main_page_video
		},
		videoDays(){
			return this.portal.main_page_video_show_days_amount
		},
		youtubeVideoId() {
			if(this.videoUrl){
				const urlObj = new URL(this.videoUrl);
				if (urlObj.pathname.indexOf('embed') > -1) return urlObj.pathname.split('/')[2];
				return urlObj.searchParams.get('v');
			} else {
				return null;
			}
		},
		isVideoDaysNotGone(){
			return this.$moment().diff(this.$moment(new Date(this.user.created_at)), 'days') <= this.videoDays;
		},
		monthOptions(){
			if(!this.user) return []
			const monthOptions = useMonthOptions()
			const monthOptionsReversed = useMonthOptions().reverse()
			const monthNames = monthOptions.map(month => this.$moment([0, month]).format('MMMM'))
			const now = new Date()
			const currentYear = now.getFullYear()
			const currentMonth = now.getMonth()

			const createdYear = Math.max(new Date(this.user.created_at).getFullYear(), 2020)

			return useYearOptions(createdYear).reverse().reduce((options, year) => {
				options.push(...monthOptionsReversed.map(month => ({
					value: { year, month },
					text: `${year} ${monthNames[month]}`
				})))
				return options
			}, []).filter(({value}) => !(value.year === currentYear && value.month > currentMonth))
		},
		selectedMonth(){
			return this.$moment(this.selectedDate, 'DD.MM.YYYY').format('MM.YYYY')
		},
	},
	watch: {
		/* eslint-disable-next-line camelcase */
		corp_book(){
			this.initCorpBook()
		},
		async selectedMonth(){
			if(this.salaryLoading) {
				this.needUpdateSalary = true
				return
			}
			await this.updateSalary()
			if(this.needUpdateSalary){
				await this.updateSalary()
			}
		}
	},
	mounted(){
		if(!this.isRoot && !this.isProfile){
			this.hide = true
		}
		if(this.$el && this.$el.parentNode){
			const scrollObserver = new IntersectionObserver(() => {
				this.inViewport = true
			});
			scrollObserver.observe(this.$el);
		}
		this.initCorpBook();
		this.fetchPortal()
	},
	created(){
		this.isRoot = window.location.pathname === '/'
		this.isProfile = window.location.pathname === '/profile'

		window.addEventListener('blur', this.pauseBookTimer)
		window.addEventListener('focus', this.unpauseBookTimer)
	},
	methods: {
		...mapActions(useSettingsStore, ['updateSettings']),
		...mapActions(useProfileStatusStore, ['updateStatus', 'resetCorpBookAnswers']),
		...mapActions(usePortalStore, ['fetchPortal']),
		...mapActions(useProfileSalaryStore, ['fitchSalaryCrutch']),
		/**
		 * Загрузить лого открыть модальный окно
		 */
		modalLogo() {
			this.$bvModal.show('modal-sm');
		},

		modalHideLogo() {
			this.$bvModal.hide('modal-sm');
		},

		change({ /* coordinates, */ canvas }) {
			this.canvas = canvas;
			// console.log(coordinates, canvas)
		},

		/**
		 * Загрузить лого
		 */
		uploadLogo(){
			this.canvas.toBlob(blob => {
				this.loading = true

				const formData = new FormData();
				formData.append('file', blob);
				formData.append('type', 'company');

				this.updateSettings(formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
					}
				}).then(() => {
					this.$toast.success('Логотип сохранен')
				}).catch(error => {
					console.error('uploadLogo', error)
				}).finally(() => {
					this.loading = false
				})
			})
			this.modalHideLogo();
			this.imagePreview = '';
			this.showPreview = false;
		},

		handleFileUpload(){
			this.file = this.$refs.file.files[0];
			let reader = new FileReader();

			reader.addEventListener('load', function () {
				this.showPreview = true;
				this.imagePreview = reader.result;
				this.modalLogo();
			}.bind(this), false);

			if( this.file ){
				if ( /\.(jpe?g|png|gif)$/i.test( this.file.name ) ) {
					reader.readAsDataURL( this.file );
				}
			}
			else{
				this.$toast.error('Неподдерживаемый формат: ' + this.file.name.split('.').reverse()[0])
			}
		},

		initCorpBook(){
			if((this.isProfile || this.isRoot) && this.status === 'started' && this.corp_book) {
				this.showCorpBookPage = this.corp_book !== null
				this.bookCounter()
			}
		},

		/**
		 * private
		 *
		 * Получить параметры для начатия и завершетия дня
		 */
		getParams() {
			const now = this.$moment().format('HH:mm:ss')
			if(this.status === 'started') return {stop: now}
			return {start: now}
		},

		/**
		 * Начать или завершить день
		 */
		async startDay() {
			if(this.buttonStatus === 'loading') return
			const profileStatusStore = useProfileStatusStore()
			profileStatusStore.buttonStatus = 'loading'
			try{
				await this.updateStatus(this.getParams())
				if(this.status === 'workdone') this.$toast.info(this.message)
				if(this.status === 'started') this.$toast.info('День начат')
				if(this.status === 'stopped' || this.status === '') this.$toast.info('Установленный вам график не позволяет в текущее время начать рабочий день')
				profileStatusStore.buttonStatus = 'init'
			}
			catch(error){
				profileStatusStore.buttonStatus = 'error'
				console.error('startDay', error)
			}
		},

		/**
		 *  Time to read book before "I have read" btn became active
		 */
		bookCounter() {
			if(!this.isRoot && !this.isProfile) return
			this.bookTimer = 60
			this.unpauseBookTimer()
		},

		pauseBookTimer(){
			clearInterval(this.bookTimerInterval)
			this.bookTimerInterval = null
		},

		unpauseBookTimer(){
			if(this.bookTimer === 0) return
			if(this.bookTimerInterval) return
			this.bookTimerInterval = setInterval(() => {
				--this.bookTimer
				if(this.bookTimer === 0) {
					clearInterval(this.bookTimerInterval)
					this.bookTimerInterval = null
				}
			}, 1000)
		},

		/**
		 * Set read corp book page
		 */
		hideBook() {
			this.showCorpBookPage = false
			this.isBookTest = false
			axios.post('/corp_book/set-read/', {}).catch(error => {
				this.$toast.error('Не удолось отметить страницу базы занний как прочитанную')
				console.error(error)
			})
		},

		repeatBook(){
			this.showCorpBookPage = true
			this.isBookTest = false
			this.bookCounter()
			this.resetCorpBookAnswers()
			this.$toast.error('Неправильный ответ')
		},

		testBook(){
			if(this.bookTimer) return
			if(!(this.corp_book.questions && this.corp_book.questions.length)){
				return this.hideBook()
			}
			this.isBookTest = true
			this.showCorpBookPage = false
		},
		separateNumber(x){
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		},
		async updateSalary(){
			const splited = this.selectedDate.split('.')
			await this.fitchSalaryCrutch(+splited[2], +splited[1] - 1)
		}
	}
};
</script>

<style lang="scss">
.ProfileSidebar{
	&-date{
		width: 100%;
	}
}
	.modal-youtube{
		.modal-xl{
			max-width: 900px;
		}
		.modal-body{
			padding: 0;
			position: relative;
			padding-bottom: 56.25%;
			iframe{
				position: absolute;
				width: 100%!important;
				height: 100%!important;
			}
		}
	}
	.profile-video-image {
		position: relative;
		margin-top: 20px;
		border-radius: 1rem;
		overflow: hidden;
		cursor: pointer;
		.fa-play {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			z-index: 22;
			color: #fff;
			font-size: 30px;
			transition: 0.3s all ease;
		}
		&:hover{
			.fa-play{
				transform: translate(-50%, -50%) scale(1.2);
			}
			img{
				filter: grayscale(0.5);
			}
		}

		img {
			width: 100%;
			height: auto;
			transition: 0.3s all ease;
		}
	}
.header__profile{
	margin: 0 auto;
	padding: 2rem 2rem 1rem;
	overflow-y:auto;
	overflow-x:hidden;
	font-family: "Open Sans",sans-serif;
	background: #F6F7FC;
	.tab__item-include{
		font-family: "Inter",sans-serif;
		font-size:1.2rem;
		font-weight: 600;
		padding: .7rem 1.2rem;
		min-height: 2rem;
		border-radius:1.5rem;
		text-transform: lowercase;
		&.is-active,
		&:hover{
			background: #608EE9;
		}
	}
	.tabs__wrapper{
		gap:1rem;
		border:none;
		justify-content: center;
	}
	&-corpbook{
		table{
			max-width: 100%;
		}
	}
}
.profile__logo {
	font-size: 21px;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 24px 0;
	min-height: 80px;
	width: 100%;
	margin-bottom: 2rem;
	position: relative;
	background-image: url("data:image/svg+xml;utf8,<svg width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><rect width='98%' height='98%' x='1%' y='1%' ry='12%' rx='12%' style='fill: none; stroke: %23D9D9D9; stroke-width: 1; stroke-dasharray: 8'/></svg>");
}
.profile__balance{
	width: 100%;
	max-width: 28rem;
	display: flex;
	flex-direction: column;
	align-items: center;
	border-radius:1rem;
	background: #608EE9;
	min-height:8rem;
	color:#fff;
	font-size:1.4rem;
	text-transform: uppercase;
	padding-top: .9rem;
	&-value{
		font-size:3.5rem;
	}
	&-currency{
		font-size:2.8rem;
	}
}

.profile__more{
  background-image: url("data:image/svg+xml;utf8,<svg width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><rect width='98%' height='98%' x='1%' y='1%' ry='12%' rx='12%' style='fill: none; stroke: %23D9D9D9; stroke-width: 1; stroke-dasharray: 8'/></svg>");
  min-height: 8rem;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 2rem;
}

.profile__point,
.profile__active{
  min-width:25rem;
}

.profile__active{
  margin-top: 2rem;
  background: #fff;
  border-radius:1rem;
  padding: 1.5rem 1.5rem 1.2rem 1.5rem;
  position:relative;

  .profile__title{
    padding-left: .5rem;
  }
  .tab__content-include{
    padding-left: .5rem;
  }
}

.logo-img-wrap{
	width: 100%;
	max-width: 30rem;
}

.logo-img{
	display: block;
	width: 100%;
	border-radius: 12%/18%;
	position: absolute;
	top: 0;
	left: 0;
	bottom: 0;
	object-fit: cover;
}

.hidden-file-wrapper{
	position: relative;
}
.hidden-file-input{
	display: none;
}
.hidden-file-label{
	position: absolute;
	z-index: 1;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	opacity: 0;
}

.logo-upload-modal{
	background: #fff;
	border-radius: 1rem;
	padding: 1.7rem 1rem;
}
.add-btn{
	background: #8FAF00;
	color: #fff;
	text-align: center;
	padding: 1rem;
	border-radius: 1rem;
}
.modal-title{
	color: #62788B;
}
.custom-file-label::after{
	color:inherit;
}
.modal-content{
	border-radius: 0.5rem;
}
@media(max-width:1359px){
	.header__profile{
		border-radius: 1.5rem;
	}
	.profile__content{
		display: flex;
		flex-flow: row nowrap;
		padding: 2rem 7rem 1rem;
		justify-content: space-evenly;
	}
	.ProfileInfo{
		margin-top: 0;
	}
	.profile__col{
		flex: 0 1 28rem;
	}
}
@media(max-width:1200px){
	.profile__content{
		gap: 2rem;
		padding: 2rem 2rem 1rem;
	}
	.profile__col{
		flex: 0 1 50%;
	}
}
@media(max-width:900px){
	.profile__content{
		flex-flow: row wrap;
	}
	.profile__col{
		flex: 0 0 28rem;
	}
}
@media(max-width:440px){
	.header__profile{
		top: 0rem;
		height: 100%;
		width: 100%;
		left: 0;
		display: flex;
		padding: 2rem;
		z-index: 1001;
	}
	.profile__content{
		padding: 6rem 0 2rem;
		display: flex;
		flex-direction: column;
		max-width: 28rem;
		margin: auto;
		max-height: 100%;
		position:relative;
	}
	.profile__col{
		flex: 1 1 auto;
	}
}
@media(min-width:1360px){
	.header__profile {
		position:fixed;
		height: 100%;
		left: 7rem;
		top: 0;
		width: 32rem;
		z-index: 1001;
		min-height: 100vh;
		transform:translateX(-20px);
		transition: all .7s;
		&::-webkit-scrollbar {
			width: 6px; /* высота для горизонтального скролла */
		}
		&.opened{
			opacity:1;
			visibility: visible;
			transform:translateX(0);
		}
		opacity: 1;
		-webkit-transform: translateX(0);
		-ms-transform: translateX(0);
		transform: translateX(0);
		visibility: visible;
		&-corpbook{
			.modal-dialog{
				max-width: 75%;
			}
			table{
				max-width: 100%;
			}
		}
	}
	.header__profile._active{
		.profile__logo{
			opacity:1;
			visibility: visible;
			transform:translateY(0);
		}
		.profile__balance{
			opacity:1;
			visibility: visible;
			transform:translateY(0);
		}
		.ProfileInfo{
			opacity:1;
			visibility: visible;
			transform:translateY(0);
		}
		.profile__active{
			opacity:1;
			visibility: visible;
			transform:translateY(0);
		}
	}
	.profile__logo{
		opacity:0;
		visibility: hidden;
		transform:translateY(10px);
		transition:1s;
	}
	.profile__active{
    transition: all 1s .8s;
    opacity:0;
    visibility: hidden;
    transform:translateY(10px);
  }


	.profile__balance{
		transition: all 1s .4s;
		opacity:0;
		visibility: hidden;
		transform:translateY(10px);
	}
}
// @media(max-width:1909px){
//   .header__profile{}
// }

.profile-page{
	.corpbook{
		ol, ul{
			margin-left: 2rem;
		}
		table{
			table-layout: auto !important;
		}
	}
}
</style>
