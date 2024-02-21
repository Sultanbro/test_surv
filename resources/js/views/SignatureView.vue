<script>
import { mapState, mapActions } from 'pinia'
import { useSettingsStore } from '@/stores/Settings'

import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
import InputText from '@ui/InputText.vue'

export default {
	name: 'SignatureVerification',
	components: {
		VuePdfEmbed,
		InputText,
	},
	data(){
		return {
			documents: [],
			isVerifyModal: false,
			userCode: '',
			user: null,
			buttonPressed: false,
			isDebug: true,
			activeDoc: 0,
			requisites: {
				fio: '',
				phone: '',
				address: '',
				pasport: '',
				uin: '',
				file1: '',
				file2: '',
			},
			isFinish: false,
		}
	},
	computed: {
		...mapState(useSettingsStore, ['logo']),
		unsigned(){
			return this.documents.filter(doc => !doc.signed)
		},
		doc(){
			return this.documents.find(doc => doc.id === (+this.$route?.query?.doc || ''))
		},
	},
	mounted(){
		this.fetchSettings()
		this.fetchDocs()
		this.fetchUser()
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
					name: doc.local_name || 'Без названия',
					file: this.isDebug ? '/static/td.pdf' : doc.url,
					signed: doc.signed,
				}))
			}
			catch (error) {
				this.$onError(error)
			}
		},
		async onSign(){
			if(!this.user.phone) return alert('Заполните неомер телефона в настройках профиля')
			if(this.buttonPressed) return
			this.buttonPressed = true
			try {
				const {data} = await this.axios.post(`/signature/users/${this.user.id}/sms`, {
					phone: this.user.phone
				})
				this.isVerifyModal = data.status
			}
			catch (error) {
				this.$onError(error)
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
				this.$onError(error)
			}
		},
	},
}
</script>

<template>
	<div class="SignatureVerification">
		<div
			v-if="user && doc"
			class="SignatureVerification-body"
		>
			<div class="SignatureVerification-header">
				<img
					:src="logo"
					class="SignatureVerification-logo"
				>
				<div class="SignatureVerification-title">
					{{ unsigned[activeDoc].name || 'Без названия' }}
				</div>
				<div class="SignatureVerification-bar" />
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
							disabled
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
							disabled
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
							disabled
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
							disabled
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
							disabled
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="6">
						<div class="SignatureVerification-label text-center">
							Лицевая сторона удостоверения / паспорта
						</div>
						<a
							:href="requisites.file1"
							target="_blank"
							class="SignatureVerification-file"
							:class="{
								'SignatureVerification-file_check': requisites.file1
							}"
						>
							<i
								v-if="requisites.file1"
								class="fa fa-check"
							/>
						</a>
					</b-col>
					<b-col cols="6">
						<div class="SignatureVerification-label text-center">
							Вторая сторона удостоверения / паспорта
						</div>
						<a
							:href="requisites.file2"
							target="_blank"
							class="SignatureVerification-file"
							:class="{
								'SignatureVerification-file_check': requisites.file2
							}"
						>
							<i
								v-if="requisites.file2"
								class="fa fa-check"
							/>
						</a>
					</b-col>
				</b-row>
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
		margin: 0 auto 20px;
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
		width: 100%;
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
