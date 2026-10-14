<template>
	<div class="news-create__dropzone">
		<vue-dropzone
			id="dropzone"
			ref="myVueDropzone"
			:options="dropzoneOptions"
			:use-custom-slot="true"
			:include-styling="false"
			@vdropzone-sending="sendingEvent"
			@vdropzone-thumbnail="thumbnail"
			@vdropzone-success="thumbnail"
			@vdropzone-queue-complete="changeRemoveIcon"
			@vdropzone-removed-file="removeFile"
		>
			<div class="dropzone-custom-content">
				<div class="dropzone-custom-title">
					Перетащите файлы сюда
				</div>
			</div>
		</vue-dropzone>
	</div>
</template>

<script>
import vue2Dropzone from '/vue2-dropzone'
import '/vue2-dropzone/dist/vue2Dropzone.min.css'
export default {
	name: 'DropZone',
	components: {
		vueDropzone: vue2Dropzone
	},
	data() {
		return {
			dropzoneOptions: {
				url: 'https://httpbin.org/post',
				thumbnailWidth: 200,
				maxFilesize: 5,
				addRemoveLinks: true,
			},
			files: [],
		}
	},
	methods: {
		fakeClick() {
			document.getElementById('dropzone').click();
		},

		async sendingEvent(file, xhr, formData) {
			this.changeRemoveIcon();

			formData.append('file', file);

			await this.axios.post('/files', formData)
				.then(res => {
					this.$set(this.files, this.files.length, {
						id: res.data.data.id,
						uuid: file.upload.uuid,
						name: file.upload.uuid,
					});

					this.$emit('sendFiles', {
						newList: this.files
					})
				})
				.catch(res => {
					console.error(res);
				})
		},

		async removeFile(file) {
			if (file.manuallyAdded) {
				const el = this.files.filter(item => {
					return item.name == file.name;
				})[0];

				this.files.splice(this.files.indexOf(el), 1);
			} else {

				const el = this.files.filter(item => {
					return item.uuid == file.upload.uuid;
				})[0];

				this.files.splice(this.files.indexOf(el), 1);
			}

			this.$emit('sendFiles', {
				newList: this.files
			})
		},

		removeAllFiles() {
			this.$refs.myVueDropzone.removeAllFiles(true);
		},

		thumbnail: function (file, dataUrl) {
			let j, len, ref, thumbnailElement, removeElement;
			if (file.previewElement) {
				file.previewElement.classList.remove('dz-file-preview');
				ref = file.previewElement.querySelectorAll('[data-dz-thumbnail-bg]');
				for (j = 0, len = ref.length; j < len; j++) {
					thumbnailElement = ref[j];
					thumbnailElement.alt = file.name;
					thumbnailElement.style.backgroundImage = 'url("' + dataUrl + '")';
				}

				ref = file.previewElement.querySelectorAll('[data-dz-thumbnail]');
				for (j = 0, len = ref.length; j < len; j++) {
					thumbnailElement = ref[j];
					thumbnailElement.alt = file.name;
					if (file.type == 'image/bmp' ||
                        file.type == 'image/gif' ||
                        file.type == 'image/jpeg' ||
                        file.type == 'image/png' ||
                        file.type == 'image/tiff' ||
                        file.type == 'image/webp') {
						thumbnailElement.style.backgroundImage = 'url("' + dataUrl + '")';
					} else {
						thumbnailElement.style.backgroundImage = 'url("/images/some-files/img.png")';
					}

				}

				let removeIcon = file.previewElement.querySelectorAll('[data-dz-remove]');
				for (j = 0; j < removeIcon.length; j++) {
					removeElement = removeIcon[j];
					removeElement.classList.add('remove-icon');
					removeElement.innerHTML = '<img src=\'/icon/news/create-post/remove.svg\'>';
				}
				return setTimeout(((function () {
					return function () {
						return file.previewElement.classList.add('dz-image-preview');
					};
				})(this)), 1);
			}
		},

		getFileTypeByExtension(extension) {
			switch (extension) {
			case 'png': {
				return 'image/png';
			}
			case 'bmp': {
				return 'image/bmp';
			}
			case 'gif': {
				return 'image/git';
			}
			case 'jpg': {
				return 'image/jpeg';
			}
			case 'jpeg': {
				return 'image/jpeg';
			}
			case 'tif': {
				return 'image/tiff';
			}
			case 'tiff': {
				return 'image/tiff';
			}
			case 'webp': {
				return 'image/webp';
			}
			default: {
				return 'file';
			}
			}
		},

		manualyAddFiles(files) {
			files.forEach(file => {
				this.$set(this.files, this.files.length, {
					id: file.id,
					uuid: file.id,
					name: file.original_name,
				});

				this.$refs.myVueDropzone.manuallyAddFile({
					size: 123,
					name: file.original_name,
					type: this.getFileTypeByExtension(file.extension)
				}, file.url);
			});

			this.$emit('sendFiles', {
				newList: this.files
			});
		},

		changeRemoveIcon() {
			let j, removeElement;
			let removeIcon = document.getElementsByClassName('dz-remove');
			for (j = 0; j < removeIcon.length; j++) {
				removeElement = removeIcon[j];
				removeElement.classList.add('remove-icon');
				removeElement.innerHTML = '<img src=\'/icon/news/create-post/remove.svg\'>';
			}
		}
	}
}
</script>
