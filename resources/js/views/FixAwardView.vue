<script>
import {fetchProfileAwards,} from '@/stores/api.js'
import { resizeImageSrc } from '@/composables/images'

import DefaultLayout from '@/layouts/DefaultLayout'
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'

export default {
	name: 'FixAwardView',
	components: {
		DefaultLayout,
		VuePdfEmbed,
	},
	data(){
		const editImg = document.createElement('img')
		editImg.onload = this.previewImg

		const editUserImg = document.createElement('img')
		editUserImg.onload = this.previewUserImg
		return {
			categories: [],
			personalData: {},
			editedAward: null,
			editedUserAward: null,
			editLoader: null,
			editImg,
			editUserImg,
		}
	},
	mounted(){
		this.getAwards()
	},
	methods: {
		async getAwards() {
			const loader = this.$loading.show()
			this.categories = []
			try {
				const {data} = await this.axios.get('/award-categories/get')
				for(const cat of data.data){
					const {data} = await this.axios.get('/award-categories/get/awards/' + cat.id)
					cat.items = data.data
				}
				this.categories = data.data

				const personalData = {
					nominations: await fetchProfileAwards('nomination'),
					// certificates: await fetchProfileAwards('certificate'),
					accrual: await fetchProfileAwards('accrual'),
				}
				this.personalData = personalData
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},
		previewImg(){
			const _ = undefined
			resizeImageSrc(this.editImg.src, 400, _, true).then(path => {
				this.onSuccess({
					path,
					format: 'jpg',
				})
			}).catch(this.onError)
		},
		previewPdf(){
			const _ = undefined
			const canvas = this.$refs.preview?.querySelector('canvas')
			if(canvas){
				resizeImageSrc(canvas.toDataURL('image/jpeg', 0.92), 400, _, true).then(path => {
					this.onSuccess({
						path,
						format: 'jpg',
					})
				}).catch(this.onError)
				return
			}

			return this.onError()
		},
		async onSuccess({path}){
			const data = new FormData()
			data.append('id', this.editedAward.id)
			data.append('preview', path)
			try {
				await this.axios.post('/awards/add-preview', data)
				this.editLoader && this.editLoader.hide()
				alert('Успешно')
			}
			catch (error) {
				this.onError(error)
			}
		},
		previewUserImg(){
			const _ = undefined
			resizeImageSrc(this.editUserImg.src, 400, _, true).then(path => {
				this.onUserSuccess({
					path,
					format: 'jpg',
				})
			}).catch(this.onError)
		},
		previewUserPdf(){
			const _ = undefined
			const canvas = this.$refs.previewUser?.querySelector('canvas')
			if(canvas){
				resizeImageSrc(canvas.toDataURL('image/jpeg', 0.92), 400, _, true).then(path => {
					this.onUserSuccess({
						path,
						format: 'jpg',
					})
				}).catch(this.onError)
				return
			}

			return this.onError()
		},
		async onUserSuccess({path}){
			const data = new FormData()
			data.append('user_id', this.editedUserAward.user_id)
			data.append('award_id', this.editedUserAward.award_id)
			data.append('path', this.getFileName(this.editedUserAward.tempPath))
			data.append('preview', path)
			try {
				await this.axios.post('/awards/add-preview-second', data)
				this.editLoader && this.editLoader.hide()
				alert('Успешно')
			}
			catch (error) {
				this.onError(error)
			}
		},
		onError(){
			this.editLoader && this.editLoader.hide()
			alert('Ошибка')
		},

		async fix(award){
			this.editLoader = this.$loading.show()
			this.editedAward = award
			if(this.editedAward.format !== 'pdf'){
				this.editImg.src = this.editedAward.tempPath
			}
		},
		async fixUser(userAward){
			this.editLoader = this.$loading.show()
			this.editedUserAward = userAward
			if(this.editedUserAward.format !== 'pdf'){
				this.editUserImg.src = this.editedUserAward.tempPath
			}
		},
		getFileName(href){
			const url = new URL(href)
			return url.pathname.split('/').reverse()[0]
		}
	},
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<div class="FixAwardView">
				<h2>Награды</h2>
				<div
					v-for="cat in categories"
					:key="cat.id"
					class="FixAwardView-category mb-4"
				>
					<h3
						:title="cat.id"
						class="FixAwardView-title"
					>
						{{ cat.type }} {{ cat.name }}
					</h3>
					<p class="FixAwardView-subtitle">
						{{ cat.description }}
					</p>
					<template v-if="cat.items">
						<div
							v-for="item in cat.items"
							:key="item.id"
							class="FixAwardView-item d-flex gap-4 mb-4 py-2"
							:class="item.preview_path ? '' : 'FixAwardView-item_error'"
						>
							<!--  -->
							<div class="FixAwardView-path flex-1">
								{{ item.path }}
							</div>
							<div class="FixAwardView-preview flex-1">
								{{ item.preview_path }}
							</div>
							<div class="FixAwardView-actions">
								<button
									@click="fix(item)"
								>
									Создать превью
								</button>
							</div>
						</div>
					</template>
				</div>
				<h2>Выданные</h2>
				<div
					v-for="cats in personalData"
					:key="cats.id"
				>
					<template v-for="cat in cats.data">
						<h3
							:key="cat.id"
							:title="cat.id"
							class="FixAwardView-title"
						>
							{{ cat.name }}
						</h3>
						<p
							:key="'p' + cat.id"
							class="FixAwardView-subtitle"
						>
							{{ cat.description }}
						</p>
						<template v-if="cat.other">
							<div
								v-for="item in cat.other"
								:key="item.award_id + '-' + item.user_id"
								class="FixAwardView-item d-flex gap-4 mb-4 py-2"
								:class="getFileName(item.previewPath) ? '' : 'FixAwardView-item_error'"
							>
								<!--  -->
								<div class="FixAwardView-path flex-1">
									{{ getFileName(item.tempPath) }}
								</div>
								<div class="FixAwardView-preview flex-1">
									{{ getFileName(item.previewPath) }}
								</div>
								<div class="FixAwardView-actions">
									<button
										@click="fixUser(item)"
									>
										Создать превью
									</button>
								</div>
							</div>
						</template>
					</template>
				</div>

				<div
					v-if="editedAward"
					ref="preview"
				>
					<vue-pdf-embed
						v-if="editedAward.format === 'pdf'"
						:source="editedAward.tempPath"
						@rendered="previewPdf"
					/>
				</div>
				<div
					v-if="editedUserAward"
					ref="previewUser"
				>
					<vue-pdf-embed
						v-if="editedUserAward.format === 'pdf'"
						:source="editedUserAward.tempPath"
						@rendered="previewUserPdf"
					/>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<style lang="scss">
.FixAwardView{
	&-title{
		font-size: 24px;
	}
	&-subtitle{
		font-size: 16px;
	}
	&-item{
		background-color: #6a6;
		&_error{
			background-color: #a66;
		}
	}
	h2{
		font-size: 28px;
		font-weight: 700;
	}
}
</style>
