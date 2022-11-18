<template>
    <BContainer class="" fluid>
        <div class="d-flex file">
            <BFormFile
                    v-model="image"
                    class="form-file"
                    placeholder="Выберите Файл"
                    drop-placeholder="Перетащите файл сюда..."
                    accept=".jpg, .png"
                    :required="image === null"
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
            </BFormFile
            >
            <BButton
                    v-if="hasImage"
                    variant="danger"
                    class="ml-3 clear-btn"
                    @click="clearImage"
            >Очистить
            </BButton
            >
        </div>
        <div v-if="hasImage" class="images-prewiev">
            <div v-for="(img, id) in imageSrc" :key="id" class="image-modal">
                <BImg
                        v-b-modal="'myModal' + id"
                        :src="img"
                        class="mb-3 img"
                        fluid
                        block
                        rounded
                        @click="openModal(id)"
                ></BImg>
                <BModal :id="'myModal' + id" title="BootstrapVue" class="w-80%">
                    <BImg :src="img" class="mb-3 img" fluid block rounded></BImg>
                </BModal>
            </div>
        </div>
        <div v-else>
            <p class="text-danger">Выберите файл(ы)</p>
        </div>
    </BContainer>
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
            fileType: Number,
            uploadImage: Array | Boolean,
            imagesPath: Array | Boolean
        },
        data() {
            return {
                image: null,
                imageSrc: [],
                imagesList: []
            };
        },
        computed: {
            hasImage() {
                if (this.image) {
                    this.$emit("image-download", this.image);
                    return !!this.image;
                }
            },
        },
        mounted() {
            if (this.uploadImage) {
                this.image = this.uploadImage;
                this.imagesList = this.imagesPath;
            }
        },
        watch: {
            hasImage(val) {
            },
            image(newValue, oldValue) {

                const newValueString = [];
                for (let i = 0; i < newValue.length; i++) {
                    newValueString.push(newValue[i].name + newValue[i].size);
                }

                const oldValueString = [];
                if (oldValue !== null) {
                    for (let i = 0; i < oldValue.length; i++) {
                        oldValueString.push(oldValue[i].name + oldValue[i].size);
                    }
                }
                if (newValueString.toString() !== oldValueString.toString()) {
                    if (oldValue !== null) {
                        this.$emit("images-remove", oldValue);
                    }
                    this.imageSrc = [];
                    if (newValue) {
                        newValue.forEach((el) => {
                            base64Encode(el)
                                .then((val) => {
                                    this.imageSrc.push(val);
                                })
                                .catch(() => {
                                    this.imageSrc = [];
                                });
                        });
                    } else {
                        this.imageSrc = [];
                    }
                }
            },
        },
        methods: {
            async clearImage() {
                this.image = null;
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
