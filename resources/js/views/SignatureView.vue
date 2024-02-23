<script>
import { mapState, mapActions } from 'pinia'
import { useSettingsStore } from '@/stores/Settings'

import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
import InputText from '@ui/InputText.vue'

const SEPARATOR = '🤡'

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
		userId(){
			return +this.$route?.query?.user || this.$laravel.userId
		},
		groupId(){
			return +this.$route?.query?.group || 0
		},
		doc(){
			return this.documents.find(doc => doc.id === (+this.$route?.query?.doc || ''))
		},
	},
	mounted(){
		this.fetchSettings()
		this.fetchDocs()
	},
	methods: {
		...mapActions(useSettingsStore, ['fetchSettings']),
		async fetchDocs(){
			try {
				const url = this.groupId ? `/signature/groups/${this.groupId}/files` : `/signature/users/${this.userId}/files`
				const {data} = await this.axios.get(url)
				const docs = data.data || []
				this.documents = docs.map(doc => ({
					id: doc.id,
					name: doc.original_name || 'Без названия',
					file: this.isDebug ? '/static/td.pdf' : doc.url,
					signed: doc.signed_at,
				}))
				if(!this.groupId){
					const {data} = await this.axios.post(`/signature/users/${this.userId}/histories`)
					if(data?.data?.length){
						if(!this.doc?.signed){
							const form = data?.data[0]
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
						else{
							const date = this.$moment(this.doc.signed).add(1, 'm')
							const form = data?.data?.filter(req => date.diff(req.created_at.substring(0, 19)) > 0)?.at(0) || null
							if(form){
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
					}
				}
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
			v-if="doc"
			class="SignatureVerification-body"
		>
			<div class="SignatureVerification-header">
				<img
					:src="logo"
					class="SignatureVerification-logo"
				>
				<div class="SignatureVerification-title">
					{{ doc.name || 'Без названия' }}
				</div>
				<div class="SignatureVerification-bar" />
			</div>
			<VuePdfEmbed
				:source="doc.file"
			/>
			<div
				v-if="!groupId"
				class="SignatureVerification-footer"
			>
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
							readonly
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
							readonly
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
							readonly
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
							readonly
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
							readonly
						/>
					</b-col>
				</b-row>

				<b-row class="SignatureVerification-row">
					<b-col cols="6">
						<div class="SignatureVerification-label text-center">
							Лицевая сторона удостоверения / паспорта
						</div>
						<a
							:href="requisites.file1 ? requisites.file1.url : ''"
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
							:href="requisites.file2 ? requisites.file2.url : ''"
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
