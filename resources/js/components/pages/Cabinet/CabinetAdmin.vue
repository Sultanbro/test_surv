<template>
	<div class="CabinetAdmin">
		<!--  -->
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

		<!--  -->
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

		<!--  -->
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

		<!--  -->
		<div class="d-flex aic video-add-content">
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
import JobtronOverlay from '@ui/Overlay'
import AccessSelect from '@ui/AccessSelect/AccessSelect'

import { mapGetters, mapActions } from 'vuex'
import API from '@/components/Chat/Store/API.vue'

const camelFix = {
	profileGroups: 'profile_groups'
}
const regex = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|v=)([^#]*).*/;

export default {
	name: 'CabinetAdmin',
	components: {
		JobtronOverlay,
		AccessSelect,
	},
	props: {},
	data(){
		return {
			domain: window.location.hostname.split('.')[0],

			isAdminsSelect: false,
			admins: [],
			adminsForm: [],

			isGeneralChatUsersOpen: false,
			generalChatUsers: [],
			generalChatUsersOld: [],

			videoUrl: null,
			videoDays: 7,
		}
	},
	computed: {
		...mapGetters([
			'user',
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
				[camelFix.profileGroups]: this.accessDictionaries.profile_groups,
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
				[camelFix.profileGroups]: this.accessDictionaries.profile_groups,
				users: newUsers,
			}
		},
		isYoutubeLinkValid() {
			return regex.test(this.videoUrl);
		},
		videoId() {
			if (!this.videoUrl) return '';
			return this.getYoutubeVideoId(this.videoUrl)
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
	watch: {},
	created(){
		this.init()
	},
	mounted(){},
	beforeDestroy(){},
	methods: {
		...mapActions(['loadCompany']),
		init(){
			this.fetchData()
			this.fetchGeneralChat()
			this.axios.get('/portal/current').then(res => {
				this.videoUrl = res.data.data.main_page_video
				this.videoDays = res.data.data.main_page_video_show_days_amount
			})
		},
		async fetchData(){
			try {
				const {data} = await this.axios.get('/cabinet/get')
				this.admins = data.admins;
				this.adminsForm = data.admins.map(admin => ({
					...admin,
					name: admin.email,
					type: 1,
				}))
			}
			catch (error) {
				console.error(error)
			}

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
		getYoutubeVideoId(url) {
			const urlObj = new URL(url)
			if (urlObj.pathname.indexOf('embed') > -1) return urlObj.pathname.split('/')[2]
			return urlObj.searchParams.get('v')
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
		onSubmitGeneralChatUsers(users){
			this.$nextTick(() => {
				this.generalChatUsers = users.map(user => ({
					id: user.id,
					email: user.name,
				}))
				this.isGeneralChatUsersOpen = false
			})
		},
		async saveGeneralChat(){
			for(const user of this.generalChatUserToAdd){
				await API.addUserToChat(0, user.id)
			}
			for(const user of this.generalChatUserToRemove){
				await API.removeUserFromChat(0, user.id)
			}
		},
	},
}
</script>

<style lang="scss">
//.CabinetAdmin{}
</style>
