<template>
    <div>
        <div class="d-flex file">
            <BFormFile
                    v-model="image"
                    class="form-file"
                    placeholder="Выберите Файл"
                    drop-placeholder="Перетащите файл сюда..."
                    accept=".jpg, .png, .pdf"
                    multiple
                    type="file"
                    id="file"
                    ref="file"
                    :state="true"
            >
                <template slot="file-name" slot-scope="{ names }">
                    <div class="file-name" v-for="(name, key) in names" :key="key">
                        <BBadge class="badge-img" variant="dark">{{ name }}</BBadge>
                    </div>
                </template>
            </BFormFile>
            <BButton
                    v-if="hasImage || imageSrc.length > 0"
                    variant="danger"
                    class="ml-3 clear-btn"
                    @click="clearImage"
            >Очистить
            </BButton
            >
        </div>

        <template v-if="imageSrc.length > 0">
            <BImg :src="imageSrc"
                  alt="картинка"
                  class="mb-3 img"
                  fluid
                  block
                  rounded
                  ref="imgcur"
                  v-b-modal="'myModalPath'"
            ></BImg>
            <BModal id="myModalPath" title="BootstrapVue" class="w-80%">
                <BImg :src="imageSrc" class="mb-3 img" fluid block rounded></BImg>
            </BModal>
        </template>
       <template v-else>
           <div v-if="hasImage" class="images-prewiev">
               <BImg
                       v-b-modal="'myModal'"
                       :src="imageSrc"
                       class="mb-3 img"
                       fluid
                       block
                       rounded
               ></BImg>
               <BModal id="myModal" title="BootstrapVue" class="w-80%">
                   <BImg :src="imageSrc" class="mb-3 img" fluid block rounded></BImg>
               </BModal>
           </div>
           <div v-else>
               <p class="text-danger">Выберите файл</p>
           </div>
       </template>
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

    export default {
        name: "UploadFile",
        components: {},
        props: {
            path: String,
            format: String
        },
        data() {
            return {
                image: null,
                imageSrc: [],
                imageSrcPdf: [],
            };
        },
        computed: {
            hasImage() {
                if (this.image) {
                    return !!this.image;
                }
            },
        },
        mounted() {
            this.imageSrc = this.path;
        },
        watch: {
            image(newValue, oldValue) {
                this.$emit("image-download", this.image);
                if(newValue !== null){
                    this.imageSrc = [];
                    let newValueString = newValue.name + newValue.size;
                    let oldValueString = null;
                    if (oldValue !== null) {
                        oldValueString = oldValue.name + oldValue.size;
                    }
                    if (newValueString !== oldValueString) {
                        if (newValue) {
                            base64Encode(newValue)
                                .then((val) => {
                                    this.imageSrc = val;

                                })
                                .catch(() => {
                                    this.imageSrc = '';
                                });
                        } else {
                            this.imageSrc = '';
                        }
                    }
                }
            },
        },
        methods: {
            async clearImage() {
                this.image = null;
                this.imageSrc = '';
                this.$emit("image-download", this.image);
            },
            toClickImg() {
                console.log("ok");
            },
            onSubmit() {
                if (!this.image) {
                    alert("Please select an image.");
                    return;
                }

                alert("Form submitted!");
            },
            openModal(id) {
                console.log("openModal");
                console.log(id);
            },
        },
    };
</script>

<style>
    .image-modal {
        cursor: pointer;
        width: 150px;
        height: 150px;
        padding: 10px;
    }

    .img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    .custom-file .custom-file-label {
        position: relative;
        height: auto;
    }

    .custom-file-input {
        display: none;
    }

    .file {
        height: auto;
        margin: auto;
    }

    .custom-file {
        height: auto;
    }

    .form-file {
        margin: auto;
    }

    .file-name {
        text-overflow: ellipsis;
        overflow: hidden;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .badge-img {
        text-overflow: ellipsis;
        overflow: hidden;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .images-prewiev {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .clear-btn {
        height: 40px;
    }
</style>
