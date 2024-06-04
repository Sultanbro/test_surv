<template>
	<nav class="KBToolbar">
		<div class="KBToolbar-breadcrumbs">
			<router-link
				to="/kb"
				class="KBToolbar-breadcrumb"
				:class="{ 'purple-color': breadcrumbs.length }"
			>
				База знаний
			</router-link>
			<template v-for="(breadcrumb, index) in breadcrumbs">
				<ArrowIcon :key="index" />
				<router-link
					:key="'l' + index"
					:to="breadcrumb.link"
					:class="{
						'KBToolbar-breadcrumb': true,
						'purple-color': index !== lastIndex,
					}"
				>
					{{ breadcrumb.title }}
				</router-link>
			</template>
		</div>
		<div class="KBToolbar-actions">
			<template v-if="activeBook">
				<template v-if="editBook">
					<div
						v-if="isShowDropdown"
						class="KBToolbar-dropdown"
						@mouseenter="showDropdown"
						@mouseleave="unShowDropdown"
					>
						<ul>
							<label @click="triggerFileInput">
								<DownloadImageIcon />Загрузить картинку
							</label>
							<label @click="triggerAudioInput">
								<DownloadAudioIcon />Загрузить аудио
							</label>
							<label @click="triggerFileInput">
								<DownloadFileIcon />Загрузить файл
							</label>
						</ul>
					</div>
					<button
						class="KBToolbar-action KBToolbar-button-dropdown"
						@mouseenter="showDropdown"
						@mouseleave="unShowDropdown"
					>
						<BurgerMenuIcon />
					</button>
					<button
						v-if="!isActiveCategory"
						class="KBToolbar-action KBToolbar-action_info"
						title="Поделиться ссылкой"
						@click="copyLink()"
					>
						<ShareLinkIcon />
					</button>

					<button
						v-if="!isActiveCategory"
						class="KBToolbar-action KBToolbar-action_info"
						title="Удалить"
						@click="$emit('delete-page', activeBook)"
					>
						<DeleteIcon />
					</button>

					<button
						v-if="!isActiveCategory"
						class="KBToolbar-action KBToolbar-action_info"
						title="Сохранить изменения"
						@click="$emit('save-page')"
					>
						<SaveIcon />
					</button>
				</template>
				<template v-else>
					<button
						v-if="isEdit && !isActiveCategory"
						v-b-popover.hover.top="'Редактировать'"
						class="KBToolbar-action KBToolbar-action_save"
						@click="$emit('edit-page')"
					>
						<EditIcon />
					</button>
					<button
						v-if="!isActiveCategory"
						v-b-popover.hover.top="'Поделиться ссылкой'"
						class="KBToolbar-action KBToolbar-action_info"
						@click="copyLink()"
					>
						<ShareLinkIcon />
					</button>
					<button
						v-if="isEdit && !isActiveCategory"
						v-b-popover.hover.top="'Удалить страницу'"
						class="KBToolbar-action KBToolbar-action_remove"
						@click="$emit('delete-page', activeBook)"
					>
						<RemoveIcon />
					</button>
				</template>
			</template>
			<template v-else>
				<button
					v-if="isAdmin"
					class="KBToolbar-action"
					@click="$emit('settings')"
				>
					<SettingsToolbarIcon />
				</button>
			</template>
			<button
				v-if="canEdit"
				v-b-popover.hover.top="'Включить редактирование Базы знаний'"
				class="KBToolbar-checkbox-action"
			>
				<input
					id="switch"
					type="checkbox"
					:checked="isEdit"
					:disabled="!canEdit"
					@click="toggleMode"
				>
				<label
					class="switch"
					for="switch"
				>Toggle</label>
			</button>
		</div>
		<form
			ref="uploadForm"
			action="/upload/images/"
			enctype="multipart/form-data"
			method="post"
			style="display: none"
		>
			<div class="form-group">
				<div class="custom-file">
					<input
						ref="fileInput"
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

		<form
			ref="audioUploadForm"
			action="/upload/audio/"
			enctype="multipart/form-data"
			method="post"
			style="display: none"
		>
			<div class="form-group">
				<div class="custom-file">
					<input
						ref="audioFileInput"
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
	</nav>
</template>

<script>
import { mapState } from 'pinia';
import { usePortalStore } from '@/stores/Portal';
import { copy2clipboard } from '@/composables/copy2clipboard.js';

import SettingsToolbarIcon from '../../../../assets/icons/SettingsToolbarIcon.vue';
import ArrowIcon from '../../../../assets/icons/ArrowIcon.vue';
import ShareLinkIcon from '../../../../assets/icons/ShareLinkIcon.vue';
import RemoveIcon from '../../../../assets/icons/RemoveIcon.vue';
import EditIcon from '../../../../assets/icons/EditIcon.vue';
import BurgerMenuIcon from '../../../../assets/icons/BurgerMenuIcon.vue';
import DeleteIcon from '../../../../assets/icons/DeleteIcon.vue';
import SaveIcon from '../../../../assets/icons/SaveIcon.vue';

import DownloadFileIcon from '../../../../assets/icons/DownloadFileIcon.vue';
import DownloadAudioIcon from '../../../../assets/icons/DownloadAudioIcon.vue';
import DownloadImageIcon from '../../../../assets/icons/DownloadImageIcon.vue';

export default {
	name: 'KBToolbar',
	components: {
		SettingsToolbarIcon,
		ArrowIcon,
		ShareLinkIcon,
		RemoveIcon,
		EditIcon,
		BurgerMenuIcon,
		DeleteIcon,
		SaveIcon,
		DownloadFileIcon,
		DownloadAudioIcon,
		DownloadImageIcon,
	},
	props: {
		mode: {
			type: String,
			default: 'read',
		},
		activeBook: {
			type: Object,
			default: null,
		},
		breadcrumbs: {
			type: Array,
			default: () => [],
		},
		canEdit: {
			type: Boolean,
		},
		editBook: {
			type: Boolean,
		},
	},
	data() {
		return {
			isShowDropdown: false,
			attachment: null,
			imageAttachProgress: 0,
		};
	},
	computed: {
		...mapState(usePortalStore, ['isOwner', 'isAdmin']),
		isEdit() {
			return this.mode === 'edit';
		},
		isActiveCategory() {
			if (!this.activeBook) return false;
			return !this.activeBook.parent_id || this.activeBook.is_category;
		},
		lastIndex() {
			return this.breadcrumbs.length - 1;
		},
	},
	watch: {},
	created() {},
	mounted() {},
	methods: {
		toggleMode() {
			// this.mode = this.isEdit ? 'read' : 'edit';
			this.$emit('mode', this.isEdit ? 'read' : 'edit');
		},
		showDropdown() {
			this.isShowDropdown = true;
		},
		unShowDropdown() {
			this.isShowDropdown = false;
		},
		copyLink() {
			try {
				copy2clipboard(
					window.location.origin + '/corp_book/' + this.activeBook.hash
				);
				this.$toast.info('Ссылка на страницу скопирована');
			} catch (error) {
				this.$toast.error('Не удалось скопировать ссылку');
			}
		},
		triggerFileInput() {
			this.$refs.fileInput.click();
		},
		triggerAudioInput() {
			this.$refs.audioFileInput.click();
		},
		async onAttachmentChange(event) {
			this.attachment = event.target.files[0];
			const loader = this.$loading.show();

			const config = {
				onUploadProgress: (progressEvent) => {
					const progress = (progressEvent.loaded / progressEvent.total) * 100;
					this.imageAttachProgress = progress;
				},
			};

			const formData = new FormData();
			formData.append('attachment', this.attachment);
			formData.append('id', this.activeBook.id);

			try {
				const { data } = await this.axios.post(
					'/upload/images/',
					formData,
					config
				);
				/* eslint-disable */
				tinymce.activeEditor.insertContent(
					`<img alt="картинка" src="${data.location}"/>`
				);
				this.imageAttachProgress = 0;
				this.isUploadImage = false;
			} catch (error) {
				console.error(error);
				this.$toast.error("Не удалось загрузить изображение");
				window.onerror && window.onerror(error);
			}

			loader.hide();
		},

		async onAttachmentChangeAudio(event) {
			this.attachment = event.target.files[0];
			const loader = this.$loading.show();

			const formData = new FormData();
			formData.append("attachment", this.attachment);
			formData.append("id", this.activeBook.id);

			try {
				const { data } = await this.axios.post("/upload/audio/", formData);
				/* eslint-disable */
				tinymce.activeEditor.insertContent(
					`<audio controls src="${data.location}"></audio>`
				);
				this.isUploadAudio = false;
			} catch (error) {
				console.error(error);
				this.$toast.error("Не удалось загрузить аудио");
				window.onerror && window.onerror(error);
			}

			loader.hide();
		},
	},
};
</script>

<style scoped lang="scss">
$iconColor: #8da0c1;

input[type="checkbox"] {
	height: 0;
	width: 0;
	visibility: hidden;
}

.switch {
	cursor: pointer;
	text-indent: -9999px;
	width: 44px;
	height: 25px;
	background: $iconColor;
	display: block;
	border-radius: 100px;
	position: relative;
	padding: 1%;
}

.switch:after {
	content: "";
	position: absolute;
	top: 5px;
	left: 6px;
	width: 15px;
	height: 15px;
	background: #fff;
	border-radius: 90px;
	transition: 0.3s;
}

input:checked + .switch {
	background: #156ae8;
}

input:checked + .switch:after {
	left: calc(100% - 5px);
	transform: translateX(-100%);
}

.switch:active:after {
	width: 12px;
}

.KBToolbar {
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	&-breadcrumbs {
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;
		.fa-chevron-right {
			position: relative;
			top: 1px;
			font-size: 8px;
			color: #ccc;
		}
	}

	.purple-color {
		color: $iconColor;
	}

	.button-dropdown {
		position: relative;
	}

	&-breadcrumb {
		display: block;
		font-size: 12px;
		font-weight: 500;
		color: #0b172d;
		&:last-child {
			color: #000;
		}
	}
	&-actions {
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;

		margin-left: auto;

		.KBToolbar-dropdown {
			z-index: 5;
			position: absolute;
			top: 41px;
			right: 220px;
			padding: 1%;
			border-radius: 12px;
			background-color: white;
			box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;
			ul {
				li {
					cursor: pointer;
				}
				li:not(:first-child) {
					margin-top: 20px;
				}
			}
		}
	}
	&-checkbox-action {
		display: flex;
		outline: none;
		background: #f7f7f7;
		margin-top: 3%;
	}
	&-action {
		padding: 6px;
    height: 28px;
    width: 28px;
		display: flex;
		justify-content: center;
		outline: none;
		align-items: center;
		border: 1px solid #8da0c1;
		font-size: 14px;
		background: #f7f7f7;
		border-radius: 5px;
		cursor: pointer;
		&_info {
			border-color: $iconColor;
		}
		&_remove {
			border-color: $iconColor;
		}
		&_save {
			border-color: $iconColor;
		}
		&_active {
			background: #bbd6ff;
			color: #007bff;
		}
	}
}
</style>
