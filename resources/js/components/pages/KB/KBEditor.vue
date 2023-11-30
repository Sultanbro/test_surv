<template>
	<div class="KBEditor">
		<input
			v-model="bookForm.title"
			type="text"
			class="KBEditor-title px-4 py-3"
		>
		<Editor
			v-model="bookForm.text"
			api-key="mve9w0n1tjerlwenki27p4wjid4oqux1xp0yu0zmapbnaafd"
			:init="{
				...editorConfig,
				height: editorHeight,
				images_upload_handler: submitImage,
			}"
			@onKeyUp="onChangeText"
			@onChange="onChangeText"
		/>

		<Questions
			:id="bookForm.id"
			:questions="bookForm.questions"
			type="kb"
			mode="edit"
			:count_points="true"
			:pass_grade="bookForm.pass_grade"
			@changePassGrade="changePassGrade"
		/>

		<b-modal
			v-model="isUploadImage"
			title="Загрузить изображение"
		>
			<form
				action="/upload/images/"
				enctype="multipart/form-data"
				method="post"
				style=" max-width: 300px; margin: 0 auto;"
			>
				<div class="form-group">
					<div class="custom-file">
						<input
							id="customFile"
							type="file"
							class="custom-file-input"
							accept="image/*"
							@change="onAttachmentChange"
						>
						<label
							class="custom-file-label"
							for="customFile"
						>Выберите файл</label>
					</div>
				</div>
			</form>
			<ProgressBar
				:percentage="imageAttachProgress"
				label="Загрузка"
			/>
		</b-modal>

		<b-modal
			v-model="isUploadAudio"
			title="Загрузить аудио"
		>
			<form
				action="/upload/audio/"
				enctype="multipart/form-data"
				method="post"
				style=" max-width: 300px;margin: 0 auto;"
			>
				<div class="form-group">
					<div class="custom-file">
						<input
							id="customFile"
							type="file"
							class="custom-file-input"
							accept="audio/mp3"
							@change="onAttachmentChangeAudio"
						>
						<label
							class="custom-file-label"
							for="customFile"
						>Выберите файл</label>
					</div>
				</div>
			</form>
		</b-modal>
	</div>
</template>

<script>
/* global tinymce */
import Editor from '@tinymce/tinymce-vue'
import Questions from '@/pages/Questions'
import ProgressBar from '@/components/ProgressBar'
import { editorConfig } from '@/components/pages/KB/helper.js'

export default {
	name: 'KBEditor',
	components: {
		Editor,
		Questions,
		ProgressBar,
	},
	props: {
		activeBook: {
			type: Object,
			default: null
		},
		uploadImage: {
			type: Boolean
		},
		uploadAudio: {
			type: Boolean
		}
	},
	data(){
		return {
			bookForm: JSON.parse(JSON.stringify(this.activeBook)),
			editorConfig,
			editorHeight: window.innerHeight - 128,
			imageAttachProgress: 0,
		}
	},
	computed: {
		isUploadImage: {
			get() {
				return this.uploadImage
			},
			set(value){
				this.$emit('toggle-image', value)
			}
		},
		isUploadAudio: {
			get() {
				return this.uploadAudio
			},
			set(value){
				this.$emit('toggle-audio', value)
			}
		},
	},
	watch: {
		activeBook: {
			deep: true,
			handler(){
				this.bookForm = JSON.parse(JSON.stringify(this.activeBook))
			}
		},
		bookForm: {
			deep: true,
			handler(){
				this.$emit('update', this.bookForm)
			}
		},
	},
	created(){
		if(this.$debug){
			this.editorConfig.toolbar = [
				'styleselect',
				'bold italic underline strikethrough',
				'table',
				'fontselect fontsizeselect formatselect',
				'alignleft aligncenter alignright alignjustify',
				'outdent indent',
				'numlist bullist',
				'forecolor backcolor removeformat',
				'fullscreen preview',
				'media link',
				'undo redo code',
			].join(' | ')
		}
	},
	mounted(){},
	methods: {
		async submitImage(blobInfo, success) {
			const loader = this.$loading.show()

			const formData = new FormData()
			formData.append('attachment', blobInfo.blob())
			formData.append('id', this.activeBook.id)
			try {
				const {data} = await this.axios.post('/upload/images/', formData)
				success(data.location)
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось загрузить изображение')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},
		onChangeText(){},
		async onAttachmentChange(e) {
			this.attachment = e.target.files[0]
			const loader = this.$loading.show()

			const config = {
				onUploadProgress: progressEvent => {
					const progress = (progressEvent.loaded / progressEvent.total) * 100
					this.imageAttachProgress = progress
				}
			}

			const formData = new FormData()
			formData.append('attachment', this.attachment)
			formData.append('id', this.activeBook.id)

			try {
				const {data} = await this.axios.post('/upload/images/', formData, config)
				tinymce.activeEditor.insertContent(`<img alt="картинка" src="${data.location}"/>`)
				this.imageAttachProgress = 0
				this.isUploadImage = false
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось загрузить изображение')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},
		async onAttachmentChangeAudio(e) {
			this.attachment = e.target.files[0]
			const loader = this.$loading.show()

			const formData = new FormData()
			formData.append('attachment', this.attachment)
			formData.append('id', this.activeBook.id)

			try {
				const {data} = await this.axios.post('/upload/audio/', formData)
				tinymce.activeEditor.insertContent(`<audio controls src="${data.location}"></audio>`)
				this.isUploadAudio = false
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось загрузить аудио')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},
		changePassGrade(grade) {
			/* eslint-disable camelcase */
			const len = this.bookForm.questions.length
			this.bookForm.pass_grade = Math.min(grade, len)

			if(grade < 1) this.bookForm.pass_grade = 1
			/* eslint-enable camelcase */
		},
	},
}
</script>

<style lang="scss">
.KBEditor{
	&-title{
		display: block;
		width: 100%;
		max-width: 960px;
		height: max-content;
		padding: 15px 0 !important;
		margin: 0 auto;
		border: none;

		font-size: 20px;
		font-weight: 600;
		line-height: 20px;
		text-align: center;

		outline: 0;
	}
	.questions{
		max-width: 960px;
		margin: 0 auto;
	}
}
</style>
