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
			errors: {},
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
				this.$onError({error})
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

				// const {data: hist} = await this.axios.post(`/signature/users/${this.$laravel.userId}/histories`)
				// if(hist?.data?.length){
				// 	const form = hist?.data[0]
				// 	const [phone, uin, pasport] = form.contract_number.split(SEPARATOR)
				// 	this.requisites = {
				// 		fio: form.name,
				// 		phone,
				// 		address: form.address,
				// 		pasport,
				// 		uin,
				// 		file1: form.images[0] || '',
				// 		file2: form.images[1] || '',
				// 	}
				// }
			}
			catch (error) {
				this.$onError({error})
			}
		},

		validateForm(){
			this.errors = {}

			if(this.requisites.fio.length < 5) this.errors.fio = 'Заполните ФИО'
			if(this.requisites.phone.replace(/[^\d+]/g, '').length < 10) this.errors.phone = 'Заполните номер телефона'
			if(this.requisites.uin.length < 10) this.errors.uin = 'Заполните ИНН'
			if(this.requisites.pasport.length < 10) this.errors.pasport = 'Заполните номер удостоверения/паспорта'
			if(this.requisites.address.length < 10) this.errors.address = 'Заполните адрес'
			if(!this.requisites.file1) this.errors.file1 = 'Загрузите лицевую сторону удостоверения/паспорта'
			if(!this.requisites.file2) this.errors.file2 = 'Загрузите оборотную сторону удостоверения/паспорта'

			const keys = Object.keys(this.errors)
			if(keys.length) this.$toast.error(Object.values(this.errors).join('\n'))

			return !keys.length
		},
		async onSign(){
			/* eslint-disable camelcase */
			// if(!this.user.phone) return alert('Заполните неомер телефона в настройках профиля')
			if(this.buttonPressed) return
			if(!this.validateForm()) return

			this.buttonPressed = true

			const formData = new FormData()
			formData.append('phone', this.requisites.phone.replace(/[^\d+]/g, ''))
			formData.append('name', this.requisites.fio)
			formData.append('contract_number', this.requisites.phone + SEPARATOR + this.requisites.uin + SEPARATOR + this.requisites.pasport)
			formData.append('address', this.requisites.address)

			if(this.requisites.upload1) formData.append('images[]', this.requisites.upload1[0])
			if(this.requisites.upload2) formData.append('images[]', this.requisites.upload2[0])

			try {
				/* const {data} =  */await this.axios.post(`/signature/users/${this.user.id}/sms`, formData)
				this.isVerifyModal = true
				this.timer = 120
			}
			catch (error) {
				this.$onError({error})
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
					this.buttonPressed = false
				}
				else{
					this.isFinish = true
					setTimeout(() => {
						location.assign('/cabinet?tab=documents')
					})
				}
			}
			catch (error) {
				this.$onError({error})
			}
		},
		getImage(file){
			return new Promise((resolve) => {
				const reader = new FileReader()
				reader.onload = function (e) {
					resolve(e.target.result)
				}
				reader.readAsDataURL(file)
			})
		},
		async onFile1(files){
			if(!files[0]) return
			this.requisites.upload1 = files
			this.requisites.file1 = await this.getImage(files[0])
		},
		async onFile2(files){
			if(!files[0]) return
			this.requisites.upload2 = files
			this.requisites.file2 = await this.getImage(files[0])
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
							ФИО<span class="red">*</span>
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.fio"
							primary
							small
							:error="errors.fio"
						/>
						<div
							v-if="errors.fio"
							class="SignatureVerification-error"
						>
							{{ errors.fio }}
						</div>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							Удостоверение/паспорт<span class="red">*</span>
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.pasport"
							primary
							small
							:error="errors.pasport"
						/>
						<div
							v-if="errors.pasport"
							class="SignatureVerification-error"
						>
							{{ errors.pasport }}
						</div>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							ИНН<span class="red">*</span>
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.uin"
							primary
							small
							:error="errors.uin"
						/>
						<div
							v-if="errors.uin"
							class="SignatureVerification-error"
						>
							{{ errors.uin }}
						</div>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							Сотовый номер<span class="red">*</span>
							<img
								v-b-popover.click.blur.html="'Укажите номер телефона на который придет смс для подписания'"
								src="/images/dist/profit-info.svg"
								class="img-info"
								width="20"
								alt="info icon"
								tabindex="-1"
							>
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.phone"
							primary
							small
							:error="errors.phone"
						/>
						<div
							v-if="errors.phone"
							class="SignatureVerification-error"
						>
							{{ errors.phone }}
						</div>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="3">
						<div class="SignatureVerification-label">
							Адрес<span class="red">*</span>
						</div>
					</b-col>
					<b-col cols="9">
						<InputText
							v-model="requisites.address"
							primary
							small
							:error="errors.address"
						/>
						<div
							v-if="errors.address"
							class="SignatureVerification-error"
						>
							{{ errors.address }}
						</div>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="6">
						<InputFile @change="onFile1">
							<div
								class="SignatureVerification-file"
								:class="{
									'SignatureVerification-file_check': requisites.file1 || requisites.upload1,
									'SignatureVerification-file_error': errors.file1,
								}"
								:style="[`--card-bg: url(${requisites.file1})`].join(';')"
							>
								<i
									v-if="requisites.file1 || requisites.upload1"
									class="fa fa-check"
								/>
								<span v-else>Загрузите лицевую сторону удостоверения или паспорта <span class="red">*</span></span>
							</div>
						</InputFile>
						<div
							v-if="errors.file1"
							class="SignatureVerification-error"
						>
							{{ errors.file1 }}
						</div>
					</b-col>
					<b-col cols="6">
						<InputFile @change="onFile2">
							<div
								class="SignatureVerification-file"
								:class="{
									'SignatureVerification-file_check': requisites.file2 || requisites.upload2,
									'SignatureVerification-file_error': errors.file2,
								}"
								:style="[`--card-bg: url(${requisites.file2})`].join(';')"
							>
								<i
									v-if="requisites.file2 || requisites.upload2"
									class="fa fa-check"
								/>
								<span v-else>Загрузите оборотную сторону удостоверения или паспорта <span class="red">*</span></span>
							</div>
						</InputFile>
						<div
							v-if="errors.file2"
							class="SignatureVerification-error"
						>
							{{ errors.file2 }}
						</div>
					</b-col>
				</b-row>

				<p class="SignatureVerification-confirm mt-4">
					Нажимая "Подписать" вы соглашаетесь с условиями данного договора и подтверждаете подлинность приложенных документов
				</p>

				<JobtronButton
					:disabled="buttonPressed"
					class="mt-2"
					@click="onSign"
				>
					Подписать {{ activeDoc + 1 }}й документ
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
			<div class="mb-4">
				На Ваш основной номер было отправлено смс с кодом подтверждения
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
		margin-bottom: 50px;

		background-color: #fff;
		box-shadow: 0 0 3px rgba(#000, 0.25);
	}
	&-header{
		padding: 20px 20px 0;
		margin: -20px -20px 20px;
		border-bottom: 3px solid #e0f0fe;
		background-color: #cdcbd3;
	}
	&-logo{
		display: block;
		max-width: 35%;
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

		padding: 20px;
		margin: 0 -20px -20px;
		border-top: 1px solid #ddd;
		background-color: #cdcbd3;
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
		padding: 24px;
		margin-top: 10px;
		border: 3px dashed #000;

		font-size: 14px;
		text-align: center;
		text-wrap: balance;

		border-radius: 16px;
		&_check{
			border-color: green;
			background-image: var(--card-bg);
			background-size: contain;
			background-position: center center;
			background-repeat: no-repeat;
		}
		&_error{
			border-color: red;
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
	&-finish{
		align-self: center;
		font-size: 16px;
	}
	&-error{
		color: red;
		font-size: 13px;
	}
	&-confirm{
		text-align: center;
		font-size: 11px;
		color: #006dae;
	}
}
</style>
