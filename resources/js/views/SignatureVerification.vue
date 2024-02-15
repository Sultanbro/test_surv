<script>
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';
import JobtronButton from '../components/ui/Button.vue';

export default {
	name: 'SignatureVerification',
	components: {
		VuePdfEmbed,
		JobtronButton,
	},
	data(){
		return {
			documents: [],
			isVerifyModal: false,
			userCode: '',
			user: null,
		}
	},
	computed: {
		doc(){
			return this.documents.find(doc => doc.id === (+this.$route?.query?.doc || ''))
		},
	},
	mounted(){
		this.fetchDocs()
		this.fetchUser()
	},
	methods: {
		async fetchUser(){
			try {
				const {data} = await this.axios.get('/profile/personal-info')
				this.user = data.user
			}
			catch (error) {
				alert(error)
			}
		},
		async fetchDocs(){
			try {
				const {data} = await this.axios.get(`/signature/users/${this.$laravel.userId}/files`)
				const docs = data.data || []
				this.documents = docs.map(doc => ({
					id: doc.id,
					name: doc.local_name || 'Без названия',
					file: doc.url,
					signed: doc.signed,
				}))
			}
			catch (error) {
				console.error(error)
				alert(error)
			}
		},
		async onSign(){
			if(!this.user.phone) return alert('Заполните неомер телефона в настройках профиля')
			try {
				const {data} = await this.axios.post(`/signature/users/${this.user.id}/sms`, {
					phone: this.user.phone
				})
				this.isVerifyModal = data.data.code
			}
			catch (error) {
				alert(error)
			}
		},
		async onVerify(){
			try {
				await this.axios.post(`/signature/users/${this.user.id}/files/${this.doc.id}/verification`, {
					code: this.userCode
				})
				this.isVerifyModal = false
				window.close()
			}
			catch (error) {
				alert(error)
			}
		},
	},
}
</script>

<template>
	<div class="SignatureVerification">
		<div
			v-if="doc && user"
			class="SignatureVerification-body"
		>
			<VuePdfEmbed
				:source="doc.file"
			/>
			<div class="SignatureVerification-actions">
				<JobtronButton @click="onSign">
					Подписать
				</JobtronButton>
			</div>
		</div>
		<div
			v-else-if="!doc"
			class="SignatureVerification-error"
		>
			Документ не найден
		</div>
		<div
			v-else-if="!user"
			class="SignatureVerification-error"
		>
			Не удалось получит информацию о сотруднике
		</div>
		<!--  -->
		<b-modal
			v-model="isVerifyModal"
			@ok="onVerify"
		>
			<div class="">
				На ваш основной номер было отпревлено смс с кодом подверждения
			</div>
			<b-input
				v-model="userCode"
				class="form-control mr-2"
			/>
		</b-modal>
	</div>
</template>

<style lang="scss">
.SignatureVerification{
	&-body{
		padding-bottom: 50px;
	}
	&-actions{
		display: flex;
		align-items: center;
		justify-content: center;

		height: 50px;

		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;

		box-shadow: 0 0 3px rgba(#000, 0.25);
	}
}
</style>
