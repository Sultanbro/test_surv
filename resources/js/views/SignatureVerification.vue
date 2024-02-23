<script>
import { mapState, mapActions } from 'pinia'
import { useSettingsStore } from '@/stores/Settings'

import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';
import JobtronButton from '@ui/Button.vue';
import InputText from '@ui/InputText.vue'
import InputFile from '@ui/InputFile.vue'

const SEPARATOR = '🤡'

export default {
	name: 'SignatureVerification',
	components: {
		VuePdfEmbed,
		JobtronButton,
		InputText,
		InputFile,
	},
	data(){
		return {
			isDebug: false,
			documents: [],
			isVerifyModal: false,
			userCode: '',
			user: null,
			buttonPressed: false,
			activeDoc: 0,
			requisites: {
				fio: '',
				phone: '',
				address: '',
				pasport: '',
				uin: '',
				file1: '',
				file2: '',
				upload1: null,
				upload2: null,
			},
			isFinish: false,
			timer: 0,
			timerLink: null,
			signCount: 0,
		}
	},
	computed: {
		...mapState(useSettingsStore, ['logo']),
		unsigned(){
			return this.documents.filter(doc => !doc.signed)
		},
		doc(){
			return this.documents[this.activeDoc] || null
		},
	},
	mounted(){
		this.fetchSettings()
		this.fetchDocs()
		this.fetchUser()
		this.timerLink = setInterval(this.countTimer, 1000)
	},
	beforeDestroy(){
		clearInterval(this.timerLink)
	},
	methods: {
		...mapActions(useSettingsStore, ['fetchSettings']),
		async fetchUser(){
			try {
				const {data} = await this.axios.get('/profile/personal-info')
				this.user = data.user
			}
			catch (error) {
				this.$onError(error)
			}
		},
		async fetchDocs(){
			try {
				const {data} = await this.axios.get(`/signature/users/${this.$laravel.userId}/files`)
				const docs = data.data || []
				this.documents = docs.map(doc => ({
					id: doc.id,
					name: doc.original_name || 'Без названия',
					file: this.isDebug ? '/static/td.pdf' : doc.url,
					signed: doc.signed_at,
				}))

				const {data: hist} = await this.axios.post(`/signature/users/${this.userId}/histories`)
				if(hist?.data?.length){
					const form = hist?.data[0]
					const [phone, uin, pasport] = form.contract_number.split(SEPARATOR)
					this.requisites = {
						fio: form.name,
						phone,
						address: form.address,
						pasport,
						uin,
						file1: form.images[0] || '',
						file2: form.images[1] || '',
					}
				}
			}
			catch (error) {
				this.$onError(error)
			}
		},
		async onSign(){
			/* eslint-disable camelcase */
			if(!this.user.phone) return alert('Заполните неомер телефона в настройках профиля')
			if(this.buttonPressed) return
			this.buttonPressed = true

			const formData = new FormData()
			formData.set('phone', this.user.phone)
			formData.set('name', this.requisites.fio)
			formData.set('contract_number', this.requisites.phone + SEPARATOR + this.requisites.uin + SEPARATOR + this.requisites.pasport)
			formData.set('address', this.requisites.address)

			if(this.requisites.upload1) formData.append('images[]', this.requisites.upload1[0])
			if(this.requisites.upload2) formData.append('images[]', this.requisites.upload2[0])

			try {
				/* const {data} =  */await this.axios.post(`/signature/users/${this.user.id}/sms`, formData)
				this.isVerifyModal = true
				this.timer = 60
			}
			catch (error) {
				this.$onError(error)
			}
			/* eslint-enable camelcase */
		},
		onSignAgain(){
			this.buttonPressed = false
			++this.signCount
			this.onSign()
		},
		async onVerify(){
			try {
				await this.axios.post(`/signature/users/${this.user.id}/files/${this.doc.id}/verification`, {
					code: this.userCode
				})
				this.isVerifyModal = false
				++this.activeDoc
				if(this.documents[this.activeDoc]){
					this.signCount = 0
					this.buttonPressed
				}
				else{
					this.isFinish = true
				}
				// window.close()
			}
			catch (error) {
				this.$onError(error)
			}
		},
		onFile1(file){
			this.requisites.upload1 = file
		},
		onFile2(file){
			this.requisites.upload2 = file
		},
		countTimer(){
			if(this.timer) --this.timer
		}
	},
}
</script>

<template>
	<div class="SignatureVerification">
		<div
			v-if="isFinish"
			class="SignatureVerification-finish"
		>
			Вы подписали все необходимые документы
		</div>
		<div
			v-else-if="doc && user"
			class="SignatureVerification-body"
		>
			<div class="SignatureVerification-header">
				<img
					:src="logo"
					class="SignatureVerification-logo"
				>
				<div class="SignatureVerification-title">
					Шаг {{ activeDoc + 1 }}: {{ unsigned[activeDoc].name || 'Без названия' }}
				</div>
				<div
					class="SignatureVerification-bar"
					:style="[`width: ${(activeDoc + 1) / unsigned.length * 100}%`].join(';')"
				/>
			</div>
			<VuePdfEmbed
				:source="doc.file"
			/>
			<div class="SignatureVerification-footer">
				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							ФИО
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.fio"
							primary
							small
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							Удостоверение/паспорт
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.pasport"
							primary
							small
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							ИИН/ИНН
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.uin"
							primary
							small
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							Сотовый номер
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.phone"
							primary
							small
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							Адресс
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.address"
							primary
							small
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="6">
						<div class="SignatureVerification-label text-center">
							Лицевая сторона удостоверения / паспорта
						</div>
						<InputFile @change="onFile1">
							<div
								class="SignatureVerification-file"
								:class="{
									'SignatureVerification-file_check': requisites.file1 || requisites.upload1
								}"
							>
								<i
									v-if="requisites.file1 || requisites.upload1"
									class="fa fa-check"
								/>
							</div>
						</InputFile>
					</b-col>
					<b-col cols="6">
						<div class="SignatureVerification-label text-center">
							Вторая сторона удостоверения / паспорта
						</div>
						<InputFile @change="onFile2">
							<div
								class="SignatureVerification-file"
								:class="{
									'SignatureVerification-file_check': requisites.file2 || requisites.upload2
								}"
							>
								<i
									v-if="requisites.file2 || requisites.upload2"
									class="fa fa-check"
								/>
							</div>
						</InputFile>
					</b-col>
				</b-row>

				<JobtronButton
					:disabled="buttonPressed"
					class="mt-4"
					@click="onSign"
				>
					Подписать
				</JobtronButton>
			</div>
		</div>
		<div
			v-else-if="!unsigned.length"
			class="SignatureVerification-error"
		>
			Нет не подписанных документов
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
			<template #modal-footer>
				<template v-if="signCount < 2">
					<span
						v-if="timer"
						class="mr-4"
					>
						{{ timer }}
					</span>
					<b-btn
						v-else
						variant="primary"
						class="mr-4"
						@click="onSignAgain"
					>
						Отправить заного
					</b-btn>
				</template>

				<b-btn
					variant="primary"
					:disabled="buttonPressed"
					@click="onVerify"
				>
					OK
				</b-btn>
			</template>
		</b-modal>
	</div>
</template>

<style lang="scss">
.SignatureVerification{
	display: flex;
	justify-content: center;
	align-items: flex-start;

	height: 100svh;
	padding: 20px;
	overflow-y: auto;
	background-color: #eee;

	&-body{
		max-width: 960px;
		width: 100%;
		padding: 20px;
		padding-bottom: 50px;

		background-color: #fff;
		box-shadow: 0 0 3px rgba(#000, 0.25);
	}
	&-header{
		margin-bottom: 20px;
		border-bottom: 3px solid #e0f0fe;
	}
	&-logo{
		display: block;
		max-width: 50%;
		margin: 0 auto 40px;
		border-radius: 24px;
	}
	&-title{
		padding: 0;
		margin: 0 0 20px;
		// text-align: center;
		font-size: 24px;
		font-weight: 700;
		color: #023b6d;
	}
	&-bar{
		height: 3px;
		transform: translateY(3px);
		background-color: #009bd9;
	}
	&-footer{
		display: flex;
		flex-flow: column nowrap;
		align-items: center;
		justify-content: center;
		gap: 10px;

		padding-block: 20px;
		margin-block: 20px;
		border-top: 1px solid #ddd;
	}
	&-row{
		width: 100%;
	}
	&-label{
		margin-top: 10px;
		font-size: 14px;
	}
	&-file{
		display: flex;
		align-items: center;
		justify-content: center;

		aspect-ratio: 2/1;
		margin-top: 10px;
		border: 3px dashed #000;
		border-radius: 16px;
		&_check{
			border-color: green;
		}
		.fa-check{
			display: block;
			aspect-ratio: 1;
			padding: 10px;
			border-radius: 999rem;

			font-size: 24px;
			color: #fff;

			background-color: green;
		}
	}
}
</style>
