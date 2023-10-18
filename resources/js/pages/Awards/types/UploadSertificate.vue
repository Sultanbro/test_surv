<template>
	<div class="upload-certificate">
		<div class="d-flex file">
			<BFormFile
				id="file"
				ref="file"
				v-model="image"
				class="form-file"
				placeholder="Выберите Сертификат"
				drop-placeholder="Перетащите файл сюда..."
				accept=".pdf"
				type="file"
			/>
			<BButton
				v-if="imageSrc"
				variant="danger"
				class="ml-3 clear-btn"
				@click="clearImage"
			>
				Очистить
			</BButton>
		</div>
		<small class="mb-4 d-block mt-1">Загрузите подготовленный шаблон в формате PDF</small>
		<br>
		<div
			v-if="imageSrc"
			class="sertificate-prewiev"
		>
			<div class="sertificate-modal">
				<div
					class="preview-canvas"
					@click="openModalCertificate"
				>
					<vue-pdf-embed
						v-if="imageSrc"
						ref="vuePdfUploadCertificate"
						:source="imageSrc"
					/>
				</div>
				<div class="info-type2">
					<i class="fa fa-info" />
					<span> Внимание! Нажмите на картинку, чтобы отредактировать загруженный шаблон.
						Обязательно расположите текст в нужные Вам места. В противном случае сертификат будет
						сгенерирован неправильно!</span>
				</div>
				<BModal
					v-model="modalCertificate"
					modal-class="upload-certificate-modal"
					title="Контсруктор сертификата"
					size="xl"
					hide-footer
					centered
				>
					<UploadSertificateModal
						:styles="styles"
						:img="imageSrc"
						:modal-certificate.sync="modalCertificate"
						@save-changes="saveStyles"
					/>
				</BModal>
			</div>
		</div>
		<b-row>
			<b-col
				cols="12"
				md="7"
			>
				<Multiselect
					v-model="value"
					:options="options"
					:multiple="true"
					:close-on-select="false"
					:clear-on-select="false"
					:preserve-search="true"
					placeholder="Выберите курсы"
					label="name"
					track-by="name"
					:preselect-first="false"
					@select="onSelect"
					@remove="onRemove"
				/>
			</b-col>
			<b-col
				cols="12"
				md="5"
			>
				<div class="d-flex">
					<b-button
						variant="outline-success"
						class="ml-2 btn-multiselect"
						@click="selectAll"
					>
						Выбрать все <i
							class="fa fa-check ml-2"
						/>
					</b-button>
					<b-button
						variant="outline-danger"
						class="ml-2 btn-multiselect"
						@click="clearAll"
					>
						Убрать все <i
							class="fa fa-trash ml-2"
						/>
					</b-button>
				</div>
			</b-col>
		</b-row>
	</div>
</template>

<script>
import UploadSertificateModal from '../types/UploadSertificateModal.vue';
// import RegenerateCertificates from './RegenerateCertificates';
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';
import Multiselect from 'vue-multiselect';

const base64Encode = data => new Promise((resolve, reject) => {
	const reader = new FileReader();
	reader.readAsDataURL(data);
	reader.onload = () => resolve(reader.result);
	reader.onerror = error => reject(error);
});

export default {
	name: 'UploadSertificate',
	components: {
		UploadSertificateModal,
		// RegenerateCertificates,
		Multiselect,
		VuePdfEmbed
	},
	props: {
		awards: {
			type: Array,
			default: () => []
		},
		id: {
			type: Number,
			default: null
		}
	},
	data() {
		return {
			modalCertificate: false,
			value: [],
			options: [],
			image: null,
			imageSrc: null,
			imagePreview: null,
			styles: '',
			textAll: 'Выбрать все',
			variant: 'success'
		}
	},
	watch: {
		image(newValue) {
			this.imageSrc = null
			this.$emit('has-change-constructor', false)
			if (!newValue) return
			base64Encode(newValue).then(val => {
				this.imageSrc = val
				this.$emit('image-download', {
					iamge: this.image,
					preview: this.imagePreview,
				}, true)
			}).catch(() => {
				this.imageSrc = null
			})
		},
	},
	mounted() {
		if (this.awards.length) {
			this.imageSrc = this.awards[0].tempPath
			this.styles = this.awards[0].styles
			this.$emit('has-change-constructor', true)
		}
		this.getCourses()
	},
	methods: {
		openModalCertificate() {
			this.modalCertificate = !this.modalCertificate
			this.$emit('has-change-constructor', true)
		},
		selectAll() {
			this.value = []
			this.$emit('remove-course-all')
			this.value = this.options
			this.$emit('add-course-all', this.value)
		},
		clearAll() {
			this.value = []
			this.$emit('remove-course-all')
		},
		onSelect(val) {
			this.$emit('add-course', val.id)
		},
		onRemove(val) {
			this.$emit('remove-course', val.id)
		},
		async getCourses() {
			const loader = this.$loading.show()
			try {
				const {data} = await this.axios.get('/admin/courses/get')
				const courses = data.courses
				for (let i = 0; i < courses.length; i++) {
					this.options.push(courses[i])
					if (this.awards.length && courses[i].award_id === this.awards[0].id) {
						this.value.push(courses[i])
					}
				}
				this.$emit('add-course-all', this.value);
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},
		saveStyles(fullName, courseName, date) {
			const styles = {}
			styles.fullName = fullName
			styles.courseName = courseName
			styles.date = date
			this.styles = JSON.stringify(styles)
			this.$emit('styles-change', styles)
		},
		clearImage() {
			this.image = null
			this.imageSrc = null
			this.$emit('image-download', null, false)
			this.$emit('has-change-constructor', false)
		},
	},
}
</script>

<style lang="scss">
    .upload-certificate-modal {
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            height: 100vh !important;
            margin: 0 !important;

            .modal-content {
                height: 100vh !important;
            }

            .modal-footer {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                padding: 10px 20px;
                border-top: 1px solid #ddd;
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }
        }
    }

    .upload-certificate {
        .info-type2{
            padding: 20px;
            margin-left: 50px;
            border-radius: 10px;
            background-color: rgba(224,168,0,0.2);
            display: flex;
            align-items: flex-start;
            span{
                font-size: 14px;
                line-height: 1.5;
                font-weight: 600;
                margin-left: 20px;
            }
            i{
                background-color: #ffc107;
                min-width: 50px;
                min-height: 50px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
        .multiselect__tags {
            overflow: hidden;
        }

        .btn-multiselect {
            height: 40px;
            width: 100%;
        }

        .preview-canvas {
            min-width: 250px;
            cursor: pointer;
            border: 1px solid #999;
            border-radius: 10px;
            overflow: hidden;
            display: inline-block;
            transition: 0.2s all ease;

            &:hover {
                transform: scale(1.05);
                box-shadow: 0 0 6px 0 #999;
            }

            canvas {
                height: 170px !important;
                width: 100% !important;
                object-fit: cover;
            }
        }

        .sertificate-modal {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }

        .form-file {
            height: 50px;

            .custom-file-input {
                height: 50px;
            }

            .custom-file-label {
                height: 40px;
                padding: 0 20px;
                display: inline-flex;
                align-items: center;

                &:after {
                    display: inline-flex;
                    align-items: center;
                    padding: 0 20px;
                }
            }
        }
    }

</style>
