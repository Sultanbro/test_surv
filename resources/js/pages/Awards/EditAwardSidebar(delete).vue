<template>
    <sidebar
            id="edit-award-sidebar"
            title="Сертификат"
            :open="open"
            @close="$emit('update:open', false)"
            width="60%"
    >
        <BForm ref="newSertificateForm" @submit.prevent="onSubmit">
            <BFormGroup
                    id="input-group-1"
                    label="Название награды"
                    label-for="input-1"
                    label-cols-sm="3"
                    label-align-sm="left"
                    description="Например, сертификаты, грамоты и т.п."
            >
                <BFormInput
                        id="input-1"
                        v-model="form.name"
                        type="text"
                        placeholder="Название"
                        required
                ></BFormInput>
            </BFormGroup>
            <BFormGroup
                    id="input-group-2"
                    label="Описание"
                    label-for="input-2"
                    label-cols-sm="3"
                    label-align-sm="left"
                    description="Например, сертификаты, грамоты и т.п."
            >
                <BFormTextarea
                        id="input-2"
                        v-model="form.description"
                        type="text"
                        placeholder="Сертификаты выдаются каждому сотруднику, который прошел курс в профиле сотрудника и набрал проходной балл"
                        rows="3"
                        max-rows="6"
                        required
                ></BFormTextarea>
            </BFormGroup>

            <BFormGroup>
                <BDropdown text="Выберете тип награды" required>
                    <BDropdownItem href="#" @click="setFileType(1)"
                    >Загрузка картинки
                    </BDropdownItem
                    >
                    <BDropdownItem href="#" @click="setFileType(2)"
                    >Конструктор сертификата
                    </BDropdownItem
                    >
                    <BDropdownItem href="#" @click="setFileType(3)"
                    >Данные начислений
                    </BDropdownItem
                    >
                </BDropdown>
            </BFormGroup>
            <p class="text-danger" v-if="!selectFileType">Выберите тип награды*</p>

            <BFormGroup class="file-type">
                <UploadFile
                        @image-download="formFile"
                        v-if="form.awardTypeId === 1"
                        :fileType="form.awardTypeId"
                        :uploadImage="form.imagesData"
                        required
                />

                <UploadSertificate
                        @image-download="formFile"
                        v-if="form.awardTypeId === 2"
                        :fileType="form.awardTypeId"
                        :sertificate="form.formFile"
                        required
                />

<!--                <FormUsers v-if="form.fileType === 3" required/>-->
                <superselect
                        v-if="form.awardTypeId === 3"
                        class="w-50 mb-4"
                        :key="1"
                        :select_all_btn="true" />
            </BFormGroup>

            <BFormGroup id="input-group-4" switches>
                <BFormCheckbox v-model="form.visibleToOthers" required>
                    Отображать пользователям награды других участников
                </BFormCheckbox>
            </BFormGroup>
            <BButton type="submit" variant="primary">Сохранить</BButton>
        </BForm>
    </sidebar>
</template>

<script>
    import UploadFile from "./types/UploadFile.vue";
    import FormUsers from "./types/FormUsers.vue";
    import UploadSertificate from "./types/UploadSertificate.vue";

    export default {
        name: "EditAwardSidebar",
        components: {
            UploadFile,
            FormUsers,
            UploadSertificate,
        },
        props: {
            open: Boolean,
            item: Object | Boolean,
        },
        data() {
            return {
                userName: 'Тимур Хайруллин',
                selectFileType: true,
                form: {
                    id: null,
                    name: '',
                    description: '',
                    awardTypeId: null,
                    images: [],
                    imagesData: null,
                    visibleToOthers: false,
                    awardCreator: '',
                    date: '',
                },
            };
        },
        methods: {
            async onSubmit() {
                if (this.form.awardTypeId) {
                    let loader = this.$loading.show();
                    this.formFile;
                    // await this.uploadFiles();
                    if (this.item) {
                        this.$emit('update-award', this.form);
                    } else {
                        this.$emit('save-award', this.form);
                    }
                    this.$emit('update:open', false);
                    this.$refs.newSertificateForm.reset();
                    loader.hide();
                } else {
                    this.selectFileType = false;
                }
            },
            setFileType(id) {
                this.form.awardTypeId = id;
                this.selectFileType = true;
            },
            formFile(val) {
                this.form.images = [];
                this.form.imagesData = val;
                return val;
            },
            async uploadFiles(){
                let formData = new FormData();
                for (let i = 0; i < this.form.imagesData.length; i++) {
                    formData.append("file[]", this.form.imagesData[i]);
                    const dataObj = {
                        path: '/upload/sertificates/' + this.form.imagesData[i].name,
                        format: this.form.imagesData[i].type
                    };
                    this.form.images.push(dataObj);
                }
                await this.axios
                    .post("/upload.php", formData, {
                        headers: {
                            "Content-Type":
                                "multipart/form-data; charset=utf-8; boundary=" +
                                Math.random().toString().substr(2),
                        },
                    })
                    .then(function (response) {
                        if (!response.data) {
                            console.log("File not uploaded.");
                        } else {
                            console.log("File uploaded successfully.");
                        }
                    })
                    .catch(function (error) {
                        console.log("error");
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.form.id = Date.now();
            this.form.date = new Date().toLocaleDateString();
            this.form.awardCreator = this.userName;
            if (this.item) {
                this.form.id = this.item.id;
                this.form.name = this.item.name;
                this.form.description = this.item.description;
                this.form.awardTypeId = this.item.awardTypeId;
                this.form.images = this.item.images;
                this.form.imagesData = this.item.imagesData;
                this.form.visibleToOthers = this.item.visibleToOthers;
                this.form.awardCreator = this.item.awardCreator;
                this.form.date = this.item.date;
            }
        }
    };
</script>

<style lang="scss">
    #edit-award-sidebar {
        .ui-sidebar__body {
            overflow: visible;
            display: flex;
            flex-direction: column;
        }

        .ui-sidebar__content {
            flex: 1;
            max-height: 100%;
            overflow: auto;
        }

        .file-type {
            margin-bottom: 20px;
        }

        .dropItems {
            display: flex;
            flex-direction: column;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            width: 80%;
            margin: auto 0 !important;
        }
    }
</style>
