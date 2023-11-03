<template>
	<div class="NewsCreate d-flex flex-column">
		<ProfileTabs
			v-model="editorType"
			:tabs="['Опубликовать новость', 'Провести опрос']"
			bottom
			head-only
		/>
		<div
			:class="'news-create ' + (editorOpen ? 'news-create--column' : '')"
			@click="toggleInput(true, null)"
		>
			<div
				v-show="!editorOpen"
				class="news-create__img-placeholder"
			>
				<img
					class="news-create__avatar"
					alt="img"
					:src="me ? me.avatar : null"
				>
				<span class="news-create__placeholder">Что у вас нового?</span>
			</div>

			<img
				v-show="!editorOpen"
				class="news-create__link news-icon"
				alt="img"
				src="/icon/news/create-post/link.svg"
				@click="toggleInput(true, true, true)"
			>

			<input
				v-show="editorOpen"
				id="newsCreateInput"
				ref="newsCreateInput"
				v-model="postTitle"
				type="text"
				:placeholder="['Напишите тут заголовок вашей новости', 'Напишите тут заголовок вашего опроса'][editorType]"
				class="news-create__title"
			>

			<span
				v-show="titleError"
				class="news-create__title-error"
			>
				Необходимо заполнить заголовок.
			</span>
		</div>

		<div
			v-show="editorOpen"
			class="news-create__form"
		>
			<ckeditor
				v-model="editorData"
				:editor="editor"
				:config="editorConfig"
			/>

			<span
				v-show="contentError"
				class="news-create__content-error"
			>
				Необходимо заполнить контент новости.
			</span>

			<div class="news-create__access-container">
				<div
					v-if="availableToEveryone"
					:class="'access-item ' + 'access-item--active'"
					@click="toggleAvailableToEveryone(!availableToEveryone)"
				>
					<span>Всем пользователям</span>
					<img src="/icon/news/create-post/remove.svg">
				</div>

				<div
					v-for="item in accessList"
					:key="item.id"
					:class="'access-item access-item--active ' + (item.image == null ? '' : 'access-item--with-img')"
				>
					<img
						v-if="item.image"
						class="access-item__img"
						:src="item.image"
					>
					<span>
						{{ item.name }}
					</span>
					<img
						src="/icon/news/create-post/remove.svg"
						@click="changeAccessList(item.id, item.name, item.type)"
					>
				</div>

				<div
					class="access-item__add"
					@click="toggleAccessModal(true)"
				>
					<img src="/icon/news/create-post/add-new.svg">
					<span>Добавить получателей</span>
				</div>
			</div>

			<span
				v-show="availableError"
				class="news-create__content-error"
			>
				Необходимо указать для кого предназначена новость.
			</span>
		</div>

		<NewsCreateQNA
			v-if="editorOpen && editorType === 1"
			v-model="QNA"
			@add-question="onAddQuestion"
			@remove-question="onRemoveQuestion"
		/>

		<div
			v-show="editorOpen"
			:class="'news-create__bottom-menu ' + (fileInputOpen == true ? 'without-border-radius' : '')"
		>
			<img
				class="news-icon"
				src="/icon/news/create-post/link.svg"
				@click="toggleInput(true, !fileInputOpen)"
			>
			<a
				class="news-create__submit"
				@click="!isEdit ? createPost() : updatePost()"
			>
				<span>{{ isEdit ? 'Сохранить' : 'Отправить' }}</span>
			</a>
		</div>

		<div
			v-show="editorOpen && fileInputOpen"
			class="news-create__files"
		>
			<DropZone
				ref="dropZone"
				@sendFiles="updateFileList"
			/>
		</div>

		<JobtronOverlay
			v-if="showAccessModal"
			v-scroll-lock="showAccessModal"
			:z="99999"
			@close="showAccessModal = false"
		>
			<AccessSelect
				v-model="accessList"
				:access-dictionaries="accessDictionaries"
				search-position="beforeTabs"
				submit-button=""
				absolute
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
import {
	mapState,
	mapActions,
} from 'pinia'
import { useCompanyStore } from '@/stores/Company'
import { getEmptyQuestion } from './helper.js'
import * as API from '@/stores/api/news.js'

import ClassicEditor from '/ckeditor5-custom/build/ckeditor';
import DropZone from '@/pages/News/DropZone'
import JobtronOverlay from '@ui/Overlay.vue'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import ProfileTabs from '@ui/ProfileTabs.vue'
import NewsCreateQNA from './NewsCreateQNA.vue'

class UploadAdapter {
	constructor(loader) {
		this.loader = loader
	}

	upload() {
		return this.loader.file
			.then(file => new Promise((resolve, reject) => {
				this._initRequest()
				this._initListeners(resolve, reject, file)
				this._sendRequest(file)
			}))
	}

	abort() {
		if (this.xhr) {
			this.xhr.abort()
		}
	}

	_initRequest() {
		const xhr = this.xhr = new XMLHttpRequest()
		xhr.open('POST', '/uploads', true)
		xhr.setRequestHeader('x-csrf-token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
		xhr.responseType = 'json'
	}

	_initListeners(resolve, reject, file) {
		const xhr = this.xhr
		const loader = this.loader
		const genericErrorText = `Couldn't upload file: ${file.name}.`

		xhr.addEventListener('error', () => reject(genericErrorText))
		xhr.addEventListener('abort', () => reject());
		xhr.addEventListener('load', () => {
			const response = xhr.response

			if (!response || response.error) {
				return reject(response && response.error ? response.error.message : genericErrorText)
			}

			resolve({
				default: response.data.url
			})
		})

		if (xhr.upload) {
			xhr.upload.addEventListener('progress', evt => {
				if (evt.lengthComputable) {
					loader.uploadTotal = evt.total
					loader.uploaded = evt.loaded
				}
			})
		}
	}

	_sendRequest(file) {
		const data = new FormData()
		data.append('file', file)
		this.xhr.send(data)
	}
}

function SimpleUploadAdapterPlugin(editor) {
	editor.plugins.get('FileRepository').createUploadAdapter = loader => new UploadAdapter(loader)
}

export default {
	name: 'NewsCreate',
	components: {
		DropZone,
		JobtronOverlay,
		AccessSelect,
		ProfileTabs,
		NewsCreateQNA,
	},
	props: {
		me: {
			type: Object,
			required: true
		},
	},
	data() {
		return {
			csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

			editor: ClassicEditor,
			editorData: '',
			editorConfig: {
				extraPlugins: [SimpleUploadAdapterPlugin,],
				mediaEmbed: {
					previewsInData: true
				},
				allowedContent: true
			},

			editorType: 0,
			editorOpen: false,
			fileInputOpen: false,

			accessSearch: '',

			currentAccessTab: 1,

			showAccessModal: false,
			accessList: [],

			availableToEveryone: false,

			postTitle: '',
			postFiles: [],

			isEdit: false,
			editableId: null,

			//Ошибки валидации
			titleError: false,
			contentError: false,
			availableError: false,

			QNA: [getEmptyQuestion()],
		}
	},
	computed: {
		...mapState(useCompanyStore, { accessDictionaries: 'dictionaries' })
	},
	mounted() {
		this.fetchDictionaries()
	},
	methods: {
		...mapActions(useCompanyStore, ['fetchDictionaries']),

		toggleAvailableToEveryone(value) {
			this.accessList = []
			this.availableToEveryone = value
			if (value) this.toggleAccessModal()
		},

		toggleAccessModal(show) {
			this.showAccessModal = show
		},

		toggleInput(editorOpen, fileInputOpen, fakeClick) {
			this.editorOpen = editorOpen

			if (editorOpen && fileInputOpen) {
				this.$refs.newsCreateInput.focus()
			}

			if (fakeClick) {
				this.$refs.dropZone.fakeClick()
			}

			this.fileInputOpen = fileInputOpen || !!this.postFiles.length
		},

		updateFileList(data) {
			this.postFiles = data.newList
		},

		changeAccessList(id, name, type, image = null) {
			const index = this.accessList.findIndex(item => ((item.id == id) && (item.type == type)))

			if (!~index) {
				this.$set(this.accessList, this.accessList.length, {
					id: id,
					name: name,
					image: image,
					type: type,
				})

				if(this.accessList.length) {
					this.availableToEveryone = false
				}

				return
			}

			this.accessList.splice(index, 1)

			if(this.accessList.length) {
				this.availableToEveryone = false
			}
		},

		clearAccessList() {
			this.accessList = []
			this.availableToEveryone = false
		},

		getOldData(data) {
			this.toggleInput(true, data.files.length)

			this.accessList = []

			if (data.available_for) {
				if(data.available_for.length > 0) {
					this.availableToEveryone = false
				}

				data.available_for.forEach(item => {
					let dictionaries = []
					let image = null

					switch (item.type) {
					case 1:
						dictionaries = this.accessDictionaries.users
						break
					case 2:
						dictionaries = this.accessDictionaries.profile_groups
						break
					case 3:
						dictionaries = this.accessDictionaries.positions
						break
					}

					dictionaries.forEach(el => {
						if (el.id == item.id && item.type == 1) {
							image = el ? el.avatar : ''
						}
					})

					this.$set(this.accessList, this.accessList.length, {
						id: item.id,
						name: item.name,
						image: image ?? null,
						type: item.type,
					})
				})
			}
			else {
				this.availableToEveryone = true
			}

			this.editableId = data.id
			this.postTitle = data.title
			this.editorData = data.content

			this.$refs.dropZone.manualyAddFiles(data.files)

			this.isEdit = true
		},

		async createPost() {
			const formData = new FormData

			this.availableError = !this.availableToEveryone && !this.accessList.length
			this.titleError = !this.postTitle
			this.contentError = !this.editorData

			if (this.titleError || this.contentError || this.availableError) return

			const allChecked = this.accessList.length && !this.accessList[0].type

			formData.append('available_for', this.availableToEveryone || allChecked ? '' : JSON.stringify(this.accessList))

			if (this.postFiles.length) {
				const fileIds = []

				this.postFiles.forEach(item => fileIds.push(item.id))
				formData.append('files', JSON.stringify(fileIds))
			}

			formData.append('title', this.postTitle)
			formData.append('content', this.editorData)

			try {
				await API.newsCreate(formData)
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось сохранить новость')
				window.onerror && window.onerror(error)
				return
			}

			this.$emit('update-news-list')
			this.postFiles = []
			this.postTitle = ''
			this.editorData = ''
			this.clearAccessList()
			this.$refs.dropZone.removeAllFiles()
			this.toggleInput(false, false)
			this.$toast.success('Новость сохранена')
			this.isEdit = false
		},

		async updatePost() {
			const formData = new FormData

			this.availableError = !this.availableToEveryone && !this.accessList.length
			this.titleError = !this.postTitle
			this.contentError = !this.editorData

			if (this.titleError || this.contentError || this.availableError) return

			const allChecked = this.accessList.length && !this.accessList[0].type

			formData.append('available_for', this.availableToEveryone || allChecked ? '' : JSON.stringify(this.accessList))


			if (this.postFiles.length != 0) {
				const fileIds = []

				this.postFiles.forEach(item => fileIds.push(item.id))
				formData.append('files', JSON.stringify(fileIds))
			}

			formData.append('title', this.postTitle)
			formData.append('content', this.editorData)

			try {
				await API.newsUpdate(this.editableId, formData)
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось сохранить новость')
				window.onerror && window.onerror(error)
				return
			}

			this.$emit('update-news-list')
			this.postFiles = []
			this.postTitle = ''
			this.editorData = ''
			this.editableId = null
			this.clearAccessList()
			this.$refs.dropZone.removeAllFiles()
			this.toggleInput(false, false)
			this.$toast.success('Новость сохранена')
			this.isEdit = false
		},

		// QWRTRT
		onAddQuestion(){
			this.QNA.push(getEmptyQuestion())
		},
		onRemoveQuestion(index){
			if(!confirm('Удалить вопрос?')) return
			this.QNA.splice(index, 1)
		},
	}
}
</script>

<style lang="scss">
.NewsCreate{
	.ProfileTabs{
		padding-top: 20px;
		padding-bottom: 0;
		margin-left: 20px;
		margin-right: 20px;
		&-tab{
			&_active{
				border-top: 4px solid #156AE8;
				color: #156AE8;
			}
			&:hover{
				color: #156AE8;
			}
		}
		&_bottom{
			.ProfileTabs{
				&-tab{
					&_active{
						border-top: none;
						border-bottom: 4px solid #156AE8;
					}
				}
			}
		}
	}
}
</style>
