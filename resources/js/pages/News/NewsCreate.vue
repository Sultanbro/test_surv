<template>
	<div class="d-flex flex-column">
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
				placeholder="Заголовок новости"
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
					v-show="availableToEveryone"
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
						v-show="item.image != null"
						class="access-item__img"
						:src="item.image"
					>
					<!-- eslint-disable-next-line -->
					<span v-html="item.name" />
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

		<div
			v-show="showAccessModal"
			v-scroll-lock="showAccessModal"
			class="access-modal-bg"
			@click.self="toggleAccessModal(false)"
		>
			<div class="access-modal">
				<div class="access-modal__search">
					<img
						class="news-icon"
						src="/icon/news/filter/search.svg"
					>
					<input
						v-model="accessSearch"
						type="text"
						class="access-modal__search-input"
						placeholder="Быстрый поиск"
					>
				</div>

				<div class="access-modal__tabs">
					<div
						:class="'access-modal__tab ' + (currentAccessTab == 1 ?'access-modal__tab--active' : '')"
						@click="changeAccessTab(1)"
					>
						Сотрудники
					</div>
					<div
						:class="'access-modal__tab ' + (currentAccessTab == 2 ?'access-modal__tab--active' : '')"
						@click="changeAccessTab(2)"
					>
						Отделы
					</div>
					<div
						:class="'access-modal__tab ' + (currentAccessTab == 3 ?'access-modal__tab--active' : '')"
						@click="changeAccessTab(3)"
					>
						Должности
					</div>
					<div
						:class="'access-modal__tab'"
						@click="toggleAvailableToEveryone(true)"
					>
						Все
					</div>
				</div>

				<div class="user-list">
					<div
						v-show="currentAccessTab == 1"
						class="user-list__container"
					>
						<div
							v-for="item in accessDictionaries.users"
							v-show="item.name && item.last_name ? item.name.toLowerCase().includes(accessSearch.toLowerCase()) || item.last_name.toLowerCase().includes(accessSearch.toLowerCase()) : null"
							:key="item.id"
							class="user-item"
							@click="changeAccessList(item.id, item.name, 1, item ? item.avatar : null)"
						>
							<img
								:src="item ? item.avatar : null"
								class="user-item__avatar"
							>
							<div class="user-item__info">
								<div class="user-item__sub">
									{{ item.position }}
								</div>
								<div class="user-item__name">
									{{ item.name }} {{ item.last_name }}
								</div>
							</div>
							<label class="news-checkbox">
								<input
									type="checkbox"
									:checked="checked(item, 1) ? 'checked' : ''"
									@click="changeAccessList(item.id, item.name, 1, item ? item.avatar : null)"
								>
								<span class="news-checkmark" />
							</label>
						</div>
					</div>
					<div
						v-show="currentAccessTab == 2"
						class="user-list__container"
					>
						<div
							v-for="item in accessDictionaries.profile_groups"
							v-show="item.name ? item.name.toLowerCase().includes(accessSearch.toLowerCase()) : null"
							:key="item.id"
							class="user-item"
							@click="changeAccessList(item.id, item.name, 2)"
						>
							<div class="user-item__info">
								<div class="user-item__name">
									{{ item.name }}
								</div>
							</div>
							<label class="news-checkbox">
								<input
									type="checkbox"
									:checked="checked(item, 2) ? 'checked' : ''"
									@click="changeAccessList(item.id, item.name, 2)"
								>
								<span class="news-checkmark" />
							</label>
						</div>
					</div>
					<div
						v-show="currentAccessTab == 3"
						class="user-list__container"
					>
						<div
							v-for="item in accessDictionaries.positions"
							v-show="item.name ? item.name.toLowerCase().includes(accessSearch.toLowerCase()) : null"
							:key="item.id"
							class="user-item"
							@click="changeAccessList(item.id, item.name, 3)"
						>
							<div class="user-item__info">
								<div class="user-item__name">
									{{ item.name }}
								</div>
							</div>
							<label class="news-checkbox">
								<input
									type="checkbox"
									:checked="checked(item, 3) ? 'checked' : ''"
									@click="changeAccessList(item.id, item.name, 3)"
								>
								<span class="news-checkmark" />
							</label>
						</div>
					</div>
				</div>

				<div class="access-modal__footer">
					<span class="access-modal__selected-count">
						{{ enumerate(accessList.length, ['Добавлен', 'Добавлено', 'Добавлено']) + ' ' + accessList.length + ' ' + enumerate(accessList.length, ['элемент', 'элемента', 'элементов']) }}
					</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import ClassicEditor from '/ckeditor5-custom/build/ckeditor';
import DropZone from '@/pages/News/DropZone'
import {
	mapState,
	mapActions,
} from 'pinia'
import { useCompanyStore } from '@/stores/Company'

class UploadAdapter {
	constructor(loader) {
		this.loader = loader;
	}

	upload() {
		return this.loader.file
			.then(file => new Promise((resolve, reject) => {
				this._initRequest();
				this._initListeners(resolve, reject, file);
				this._sendRequest(file);
			}))
	}

	abort() {
		if (this.xhr) {
			this.xhr.abort();
		}
	}

	_initRequest() {
		const xhr = this.xhr = new XMLHttpRequest();
		xhr.open('POST', '/uploads', true);
		xhr.setRequestHeader('x-csrf-token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
		xhr.responseType = 'json';
	}

	_initListeners(resolve, reject, file) {
		const xhr = this.xhr;
		const loader = this.loader;
		const genericErrorText = `Couldn't upload file: ${file.name}.`;

		xhr.addEventListener('error', () => reject(genericErrorText));
		xhr.addEventListener('abort', () => reject());
		xhr.addEventListener('load', () => {
			const response = xhr.response;

			if (!response || response.error) {
				return reject(response && response.error ? response.error.message : genericErrorText);
			}

			resolve({
				default: response.data.url
			});
		});

		if (xhr.upload) {
			xhr.upload.addEventListener('progress', evt => {
				if (evt.lengthComputable) {
					loader.uploadTotal = evt.total;
					loader.uploaded = evt.loaded;
				}
			});
		}
	}

	_sendRequest(file) {
		const data = new FormData();
		data.append('file', file);
		this.xhr.send(data);
	}
}


function SimpleUploadAdapterPlugin(editor) {
	editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
		return new UploadAdapter(loader);
	};
}

export default {
	name: 'NewsCreate',
	components: {
		DropZone,
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
		}
	},
	computed: {
		...mapState(useCompanyStore, { accessDictionaries: 'dictionaries' })
	},
	mounted() {
		this.fetchDictionaries();
	},
	methods: {
		...mapActions(useCompanyStore, ['fetchDictionaries']),

		toggleAvailableToEveryone(value) {
			this.accessList = [];
			this.availableToEveryone = value;
			if (value) {
				this.toggleAccessModal();
			}
		},

		enumerate(num, dec) {
			if (num > 100) num = num % 100;
			if (num <= 20 && num >= 10) return dec[2];
			if (num > 20) num = num % 10;
			return num === 1 ? dec[0] : num > 1 && num < 5 ? dec[1] : dec[2];
		},

		toggleAccessModal(show) {
			this.showAccessModal = show;
		},

		toggleInput(editorOpen, fileInputOpen, fakeClick = null) {
			this.editorOpen = editorOpen;

			if (editorOpen && fileInputOpen == null) {
				this.$refs.newsCreateInput.focus();
			}

			if (fakeClick != null) {
				this.$refs.dropZone.fakeClick();
			}

			if (fileInputOpen != null) {
				this.fileInputOpen = fileInputOpen;
			}
		},

		changeAccessTab(newTab) {
			this.currentAccessTab = newTab;
		},

		updateFileList(data) {
			this.postFiles = data.newList;
		},

		changeAccessList(id, name, type, image = null) {
			let element = this.accessList.find(item => ((item.id == id) && (item.type == type)));

			if (!element) {
				this.$set(this.accessList, this.accessList.length, {
					id: id,
					name: name,
					image: image,
					type: type,
				});

				if(this.accessList.length > 0) {
					this.availableToEveryone = false;
				}

				return;
			}


			const el = this.accessList.filter(item => {
				return !((item.id != id) || (item.type != type));
			})[0];
			this.accessList.splice(this.accessList.indexOf(el), 1);

			if(this.accessList.length > 0) {
				this.availableToEveryone = false;
			}
		},

		clearAccessList() {
			this.accessList = [];
		},

		checked(item, type) {
			return this.accessList.some(el => {
				return el.id === item.id && el.type === type
			});
		},

		getOldData(data) {
			this.toggleInput(true, data.files.length != 0);

			this.accessList = [];

			if (data.available_for != null) {

				if(data.available_for.length > 0) {
					this.availableToEveryone = false;
				}

				data.available_for.forEach(item => {
					let dictionaries = [];
					let image = null;

					switch (item.type) {
					case 1: {
						dictionaries = this.accessDictionaries.users;
						break;
					}
					case 2: {
						dictionaries = this.accessDictionaries.profile_groups;
						break;
					}
					case 3: {
						dictionaries = this.accessDictionaries.positions;
						break;
					}
					}

					dictionaries.forEach(el => {
						if (el.id == item.id && item.type == 1) {
							image = el ? el.avatar : '';
						}
					});

					this.$set(this.accessList, this.accessList.length, {
						id: item.id,
						name: item.name,
						image: image ?? null,
						type: item.type,
					});
				});
			} else {
				this.availableToEveryone = true;
			}

			this.editableId = data.id;
			this.postTitle = data.title;
			this.editorData = data.content;

			this.$refs.dropZone.manualyAddFiles(data.files);

			this.isEdit = true;
		},

		async createPost() {
			let formData = new FormData;

			this.titleError = false;
			this.contentError = false;
			this.availableError = false;

			if (this.availableToEveryone == false && this.accessList.length == 0) {
				this.availableError = true;
			}

			if (this.postTitle == '') {
				this.titleError = true;
			}

			if (this.editorData == '') {
				this.contentError = true;
			}

			if (this.titleError || this.contentError || this.availableError) {
				return;
			}

			if (this.availableToEveryone) {
				formData.append('available_for', '');
			} else {
				formData.append('available_for', JSON.stringify(this.accessList));
			}

			this.accessList = [];
			this.availableToEveryone = false;

			if (this.postFiles.length != 0) {
				let fileIds = [];

				this.postFiles.forEach(item => {
					fileIds.push(item.id);
				});

				formData.append('files', JSON.stringify(fileIds));
			}

			formData.append('title', this.postTitle);
			formData.append('content', this.editorData);

			await this.axios.post('/news', formData, {
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json'
				}
			})
				.then(() => {
					this.$emit('update-news-list');
					this.postFiles = [];
					this.postTitle = '';
					this.editorData = '';
					this.clearAccessList();
					this.$refs.dropZone.removeAllFiles();
					this.toggleInput(false, false);
				})
				.catch(response => {
					console.error(response)
				});
			this.isEdit = false;
		},

		async updatePost() {
			let formData = new FormData;

			this.titleError = false;
			this.contentError = false;
			this.availableError = false;

			if (this.availableToEveryone == false && this.accessList.length == 0) {
				this.availableError = true;
			}

			if (this.postTitle == '') {
				this.titleError = true;
			}

			if (this.editorData == '') {
				this.contentError = true;
			}

			if (this.titleError || this.contentError || this.availableError) {
				return;
			}

			if (this.availableToEveryone) {
				formData.append('available_for', '');
			} else {
				formData.append('available_for', JSON.stringify(this.accessList));
			}

			this.accessList = [];
			this.availableToEveryone = false;


			if (this.postFiles.length != 0) {
				let fileIds = [];

				this.postFiles.forEach(item => {
					fileIds.push(item.id);
				});

				formData.append('files', JSON.stringify(fileIds));
			}

			formData.append('title', this.postTitle);
			formData.append('content', this.editorData);
			formData.append('_method', 'put');

			await this.axios.post('/news/' + this.editableId, formData, {
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json'
				}
			})
				.then(() => {
					this.$emit('update-news-list');
					this.postFiles = [];
					this.postTitle = '';
					this.editorData = '';
					this.editableId = null;
					this.clearAccessList();
					this.$refs.dropZone.removeAllFiles();
					this.toggleInput(false, false);
				})
				.catch(response => {
					console.error(response)
				});
			this.isEdit = false;
		},
	}
}
</script>
