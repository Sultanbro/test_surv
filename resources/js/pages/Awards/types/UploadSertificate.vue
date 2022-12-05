<template>
    <div class="upload-certificate">
        <div class="d-flex file">
            <BFormFile
                    v-model="image"
                    class="form-file"
                    placeholder="Выберите Сертификат"
                    drop-placeholder="Перетащите файл сюда..."
                    accept=".pdf"
                    type="file"
                    id="file"
                    ref="file"
            >
            </BFormFile>
            <BButton
                    v-if="hasImage || awards.length > 0"
                    variant="danger"
                    class="ml-3 clear-btn"
                    @click="clearImage"
            >
                Очистить
            </BButton>
        </div>
        <small class="mb-4 d-block mt-1">Загрузите подготовленный шаблон в формате PDF</small>
        <br>
        <div v-if="hasImage || awards.length > 0" class="sertificate-prewiev">
            <div class="sertificate-modal">
                <div class="preview-canvas" @click="modalCertificate = !modalCertificate">
                    <vue-pdf-embed v-if="imageSrc" ref="vuePdfUploadCertificate" :source="imageSrc"/>
                </div>
                <BModal v-model="modalCertificate" modal-class="upload-certificate-modal"
                        title="Контсруктор сертификата"
                        size="xl" hide-footer centered>
                    <UploadSertificateModal :styles="styles" :img="imageSrc" :modalCertificate.sync="modalCertificate"
                                            @save-changes="saveStyles"/>
                </BModal>
            </div>
        </div>
        <b-row>
            <b-col cols="12" md="7">
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
                        @select="onSelect"
                        @remove="onRemove"
                        :preselect-first="false"

                />
            </b-col>
            <b-col cols="12" md="5">
                <div class="d-flex">
                    <b-button variant="outline-success" class="ml-2 btn-multiselect" @click="selectAll">Выбрать все <i
                            class="fa fa-check ml-2"></i></b-button>
                    <b-button variant="outline-danger" class="ml-2 btn-multiselect" @click="clearAll">Убрать все <i
                            class="fa fa-trash ml-2"></i></b-button>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import UploadSertificateModal from "../types/UploadSertificateModal.vue";
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";
    import Multiselect from "vue-multiselect";

    const base64Encode = (data) =>
        new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(data);
            reader.onload = () => resolve(reader.result);
            reader.onerror = (error) => reject(error);
        });

    export default {
        name: "UploadSertificate",
        components: {
            UploadSertificateModal,
            Multiselect,
            VuePdfEmbed
        },
        props: {
            awards: {
                type: Array,
                default: []
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
                styles: '',
                textAll: 'Выбрать все',
                variant: 'success'
            };
        },
        mounted() {
            if (this.awards.length > 0) {
                this.imageSrc = this.awards[0].tempPath;
                this.styles = this.awards[0].styles;
                console.log(this.$refs.vuePdfUploadCertificate);
            }
            this.getCourses();
        },
        computed: {
            hasImage() {
                return !!this.image;
            },
        },
        watch: {
            image(newValue) {
                this.imageSrc = null;
                if (newValue) {
                    base64Encode(newValue)
                        .then((val) => {
                            this.imageSrc = val;
                            this.$emit("image-download", this.image);
                        })
                        .catch(() => {
                            this.imageSrc = null;
                        });
                }
            },
        },
        methods: {
            selectAll() {
                this.value = [];
                this.$emit("remove-course-all");
                this.value = this.options;
                this.$emit("add-course-all", this.value);
            },
            clearAll() {
                this.value = [];
                this.$emit("remove-course-all");
            },
            onSelect(val) {
                this.$emit("add-course", val.id);
            },
            onRemove(val) {
                this.$emit("remove-course", val.id);
            },
            async getCourses() {
                let loader = this.$loading.show();
                await this.axios
                    .get('/admin/courses/get')
                    .then(response => {
                        const data = response.data.courses;
                        for (let i = 0; i < data.length; i++) {
                            this.options.push(data[i]);
                            if (this.awards.length > 0) {
                                if (data[i].award_id === this.awards[0].id) {
                                    this.value.push(data[i]);
                                }
                            }
                        }
                        this.$emit("add-course-all", this.value);
                        loader.hide();
                    })
                    .catch(error => {
                        console.log(error);
                        loader.hide();
                    })
            },
            saveStyles(fullName, courseName, hours, date) {
                const styles = {};
                styles.fullName = fullName;
                styles.courseName = courseName;
                styles.hours = hours;
                styles.date = date;
                this.$emit("styles-change", styles);
            },
            clearImage() {
                this.image = null;
                this.imageSrc = null;
            },
        },
    };
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
        .multiselect__tags {
            overflow: hidden;
        }

        .btn-multiselect {
            height: 40px;
            width: 100%;
        }

        .preview-canvas {
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
                width: auto !important;
            }
        }

        .sertificate-modal {
            margin-bottom: 20px;
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
