<template>
	<div class="award-type-1">
		<div class="d-flex file">
			<BFormFile
				id="file"
				ref="file"
				v-model="images"
				class="form-file"
				placeholder="Выберите Файл(ы)"
				drop-placeholder="Перетащите файл сюда..."
				accept=".jpg, .png, .pdf"
				multiple
				type="file"
				:state="true"
				:file-name-formatter="formatNames"
			/>
			<BButton
				v-if="hasImage"
				variant="danger"
				class="ml-3 clear-btn"
				size="sm"
				@click="clearImage"
			>
				Очистить
			</BButton>
		</div>
		<small>Загрузите одну или несколько картинок в формате PNG, JPG или PDF</small>

		<b-row v-if="hasImage">
			<b-col
				v-for="(image, index) in imageSrc"
				:key="index"
				cols="12"
				md="4"
				lg="3"
				xl="2"
				class="mt-4"
			>
				<div class="image-preview">
					<BImg
						v-if="image.format !== 'pdf'"
						v-b-modal="'myModal'"
						:src="image.path"
						class="mb-3 img"
						fluid
						block
						rounded
						@click="modalOpen(image)"
					/>
					<div
						v-else
						@click="modalOpen(image)"
					>
						<vue-pdf-embed :source="image.path" />
					</div>
				</div>
			</b-col>
		</b-row>

		<template v-if="awards.length > 0">
			<hr class="my-5">
			<h4 class="uploaded-title">
				Загруженные шаблоны
			</h4>
			<b-row>
				<b-col
					v-for="award in awards"
					:key="award.id"
					cols="12"
					md="4"
					xl="2"
					lg="3"
					class="mt-4"
				>
					<div class="image-preview active">
						<div
							v-if="award.format !== 'pdf'"
							class="image-preview-container"
						>
							<BImg
								v-b-modal="'myModal'"
								:src="award.tempPath"
								class="mb-3 img"
								fluid
								block
								rounded
								@click="modalOpen(award)"
							/>
							<i
								class="fa fa-times"
								@click="removeImage(award.id)"
							/>
						</div>
						<div
							v-else
							class="image-preview-container"
						>
							<div @click="modalOpen(award)">
								<vue-pdf-embed :source="award.tempPath" />
							</div>
							<i
								class="fa fa-times"
								@click="removeImage(award.id)"
							/>
						</div>
					</div>
				</b-col>
			</b-row>
		</template>
		<BModal
			v-if="selectedModal"
			v-model="modal"
			size="lg"
			centered
		>
			<BImg
				v-if="selectedModal.format !== 'pdf'"
				:src="selectedModal.tempPath"
				fluid
				block
			/>
			<vue-pdf-embed
				v-else
				:source="selectedModal.tempPath"
			/>
			<template #modal-footer>
				<b-button
					variant="secondary"
					@click="modal = !modal"
				>
					Закрыть
				</b-button>
			</template>
		</BModal>
	</div>
</template>

<script>
const base64Encode = (data) =>
	new Promise((resolve, reject) => {
		const reader = new FileReader();
		reader.readAsDataURL(data);
		reader.onload = () => resolve(reader.result);
		reader.onerror = (error) => reject(error);
	});

import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';

export default {
	name: 'UploadFile',
	components: {VuePdfEmbed},
	props: {
		awardsObj: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {
			images: null,
			imageSrc: [],
			selectedModal: null,
			modal: false,
			awards: []
		};
	},
	computed: {
		hasImage() {
			if (this.images) {
				return !!this.images;
			}
			return false
		},
	},
	watch: {
		images(newValue) {
			if (newValue) {
				this.imageSrc = [];
				newValue.forEach(item => {
					base64Encode(item)
						.then((base64) => {
							this.imageSrc.push({
								path: base64,
								format: item.type.split('/')[1]
							});
						})
						.catch(() => {
							this.imageSrc = [];
						});
				});
				this.$emit('image-download', this.images);
			}
		},
	},
	mounted() {
		this.awards = this.awardsObj;
	},
	methods: {
		formatNames(files) {
			return files.length === 1 ? files[0].name : `Выбрано файлов - ${files.length}`
		},
		modalOpen(image) {
			this.selectedModal = image;
			this.modal = !this.modal;
		},
		async clearImage() {
			this.images = null;
			this.imageSrc = [];
			this.$emit('image-download', this.images);
		},
		removeImage(id) {
			let loader = this.$loading.show();
			this.axios
				.delete('/awards/delete/' + id)
				.then(() => {
					this.$toast.success('Удалено');
					this.awards = this.awards.filter(n => n.id !== id);
					loader.hide();
				})
				.catch(error => {
					console.error(error);
					loader.hide();
				})
		},
	},
};
</script>

<style lang="scss">
    .award-type-1 {
        .uploaded-title {
            font-size: 20px;
            color: green;
        }

        .image-preview {
            height: 100px;
            overflow: hidden;
            border: 2px solid #ddd;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.15s all ease;

            &:hover {
                transform: scale(1.05);
            }

            img {
                width: 100%;
                height: 100px;
                object-fit: cover;
            }

            canvas {
                width: 100% !important;
                height: 100px!important;
            }

            .image-preview-container {
                position: relative;
                width: 100%;
                height: 100px;

                i {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    z-index: 22;
                    color: #dc3545;
                    width: 35px;
                    height: 35px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    background-color: rgba(220,53,69,0.2);
                    border-radius: 6px;
                    transition: 0.2s all ease;
                    &:hover{
                        color: #fff;
                        background-color: rgba(220,53,69,1);
                    }
                }
            }
        }

        .clear-btn {
            height: 50px;
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
