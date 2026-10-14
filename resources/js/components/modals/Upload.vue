<template>
	<div>
		<b-modal
			id="upload-files-modal"
			class="modalle"
			title="Загрузить файл"
			:visible="open"
			@hidden="$emit('update:open', false)"
			@ok="okHandler"
		>
			<div
				id="upload-modal-body"
				@dragover.prevent.self
				@dragenter.prevent.self
				@drop.prevent="dropHandler"
				@change="changeHandler"
			>
				<div class="small mb-1 d-flex justify-content-between">
					<div>
						<b>Формат файлов:</b>
						<span>{{ allowedFileTypes.join(', ') }}</span>
					</div>
					<div>
						<b>Файл:</b>
						<span>{{ filename || 'не выбран' }}</span>
					</div>
				</div>

				<form id="upload-modal-form">
					<input
						id="upload-modal-input"
						ref="input"
						type="file"
					>
					<label
						id="upload-modal-label"
						for="upload-modal-input"
					>
						<div><b>Переместите</b></div>
						<div>или</div>
						<div><b>выберите</b> файл</div>
					</label>
				</form>

				<b-alert
					class="mb-0 mt-3"
					:value="!!error.text"
					variant="danger"
				>
					{{ error.text }}
				</b-alert>
			</div>
		</b-modal>
	</div>
</template>

<script>
export default {
	name: 'UploadModal',
	props: {
		open: Boolean,
		allowedFileTypes: {
			type: Array,
			default: () => ['png']
		}
	},
	emits: ['update:open', 'data'],
	/* TS
    {
      base64: string;
      file: File
    }
  */
	data() {
		return {
			error: {
				text: ''
			},
			filename: ''
		}
	},
	watch: {
		open (value) {
			if (!value) {
				this.error.text = ''
				this.file = undefined
				this.filename = ''
			}
		}
	},
	created () {
		this.file = undefined
	},
	methods: {

		// EVENT HANDLERS

		async okHandler (e) {
			try {
				if (!this.filename) {
					e.preventDefault()
					this.error.text = 'Необходимо выбрать файл'
					return
				}
				const file = this.getFile()
				const base64 = await this.getBase64(file)
				this.$emit('data', { base64, file })
			} catch (error) {
				console.error(error)
			}
		},
		dropHandler (e) {
			const data = e.dataTransfer
			if (data) {
				this.handleFile(data.files[0])
			}
			this.resetFileInput()
		},
		changeHandler (e) {
			if (e.target) {
				const target = e.target
				const files = target.files
				if (files) {
					this.handleFile(files[0])
				}
			}
			this.resetFileInput()
		},

		// HELPERS

		resetFileInput () {
			const input = this.$refs.input
			input.value = ''
		},
		handleFile (file) {
			this.error.text = ''
			this.filename = ''
			const typeAllowed = this.isFileTypeAllowed(file)
			const sizeAllowed = this.isFileSizeAllowed(file)
			if (typeAllowed && sizeAllowed) {
				this.filename = file.name
				this.setFile(file)
			} else {
				if (!typeAllowed) {
					this.error.text = 'Недопустимый формат файла'
				}
			}
		},
		isFileTypeAllowed (file) {
			const extension = file.name.split('.').pop() || ''
			return this.allowedFileTypes.includes(extension)
		},
		isFileSizeAllowed () {
			// TODO
			return true
		},
		getBase64 (file) {
			return new Promise((resolve, reject) => {
				const reader = new FileReader()
				reader.readAsDataURL(file)
				reader.onload = () => resolve(reader.result)
				reader.onerror = error => reject(error)
			})
		},
		setFile (file) {
			this.file = file
		},
		getFile () {
			return this.file
		}
	}
}
</script>

<style lang="scss">
#upload-modal-body {
  width: 100%;
  display: flex;
  flex-direction: column;
}
#upload-modal-footer {
  display: flex;
  justify-content: flex-end;
}
#upload-modal-form {
  width: 100%;
  height: 100%;
  flex-grow: 1;
  transition: background-color 0.3s;
  &:hover {
    background-color: whitesmoke;
  }
}
#upload-modal-input {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}
#upload-modal-label {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 1rem 0;
  border: 1px dotted black;
  margin: 0;
  cursor: pointer;
}
#upload-modal-name-input {
  padding-bottom: 0.4rem;
  display: flex;
}
</style>