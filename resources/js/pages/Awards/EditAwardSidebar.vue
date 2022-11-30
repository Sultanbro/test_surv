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
                    label-cols-sm="3"
                    label-align-sm="left"
                    description="Например, сертификаты, грамоты и т.п."
            >
                <template #label>
                    <label class="with-info">Название награды <i class="fa fa-info" id="info1"></i></label>
                    <b-popover target="info1" triggers="hover" placement="right">
                        <p style="font-size: 14px">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, repellendus?</p>
                    </b-popover>
                </template>
                <BFormInput
                        id="input-1"
                        v-model="name"
                        type="text"
                        placeholder="Название"
                        required
                ></BFormInput>

            </BFormGroup>
            <BFormGroup
                    id="input-group-2"
                    label-cols-sm="3"
                    label-align-sm="left"
            >
                <template #label>
                    <label class="with-info">Описание награды <i class="fa fa-info" id="info2"></i></label>
                    <b-popover target="info2" triggers="hover" placement="right">
                        <p style="font-size: 14px">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, repellendus?</p>
                    </b-popover>
                </template>
                <BFormTextarea
                        id="input-2"
                        v-model="description"
                        type="text"
                        placeholder="Сертификаты выдаются каждому сотруднику, который прошел курс в профиле сотрудника и набрал проходной балл"
                        rows="3"
                        max-rows="6"
                        required
                ></BFormTextarea>
            </BFormGroup>

            <BFormGroup
                    id="input-group-3"
                    label-cols-sm="3"
                    label-align-sm="left"
            >
                <template #label>
                    <label class="with-info">Тип награды <i class="fa fa-info" id="info3"></i></label>
                    <b-popover target="info3" triggers="hover" placement="right">
                        <p style="font-size: 14px">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, repellendus?</p>
                    </b-popover>
                </template>
                <BDropdown id="input-3" :text="dropDownText" required class="dropdown-select-type">
                    <BDropdownItem href="#" @click="setFileType(1)">
                        Загрузка картинки
                        <i class="fa fa-info" id="info4"></i>
                        <b-popover target="info4" triggers="hover" placement="top">
                            <p style="font-size: 14px">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, repellendus?</p>
                        </b-popover>
                    </BDropdownItem>
                    <BDropdownItem href="#" @click="setFileType(2)">
                        Конструктор сертификата
                          <i class="fa fa-info" id="info5"></i>
                        <b-popover target="info5" triggers="hover" placement="top">
                            <p style="font-size: 14px">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, repellendus?</p>
                        </b-popover>
                    </BDropdownItem>
                    <BDropdownItem href="#" @click="setFileType(3)">
                        Данные начислений
                          <i class="fa fa-info" id="info6"></i>
                        <b-popover target="info6" triggers="hover" placement="top">
                            <p style="font-size: 14px">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, repellendus?</p>
                        </b-popover>
                    </BDropdownItem>
                </BDropdown>
                <p class="text-danger" v-if="!selectedType">Выберите тип награды*</p>
            </BFormGroup>

            <BFormGroup class="file-type">
                <UploadFile
                        @image-download="formFile"
                        v-if="type === 1"
                        :awardsObj="awards"
                        required
                />

                <UploadSertificate
                        @image-download="formFileCertificate"
                        @styles-change="styleChange"
                        @add-course="addCourse"
                        @remove-course="removeCourse"
                        v-if="type === 2"
                        :awards="awards"
                        :id="category_id"
                        required
                />

                <superselect
                        v-if="type === 3"
                        class="w-50 mb-4"
                        :key="1"
                        :onlytype="2"
                        @choose="superselectChoice"
                        :single="true"
                        :placeholder="'Выберите должность или отдел'"
                        :disable_type="1"
                        :value_id="targetable_id"
                        :pre_build="true"
                        :select_all_btn="false"/>
            </BFormGroup>

            <BFormGroup class="custom-switch custom-switch-sm" id="input-group-4" v-if="type === 1 || type === 2 ">
                <b-form-checkbox v-model="hide" switch>Отображать пользователям награды других участников</b-form-checkbox>
            </BFormGroup>
            <BButton type="submit" variant="primary">Сохранить</BButton>
        </BForm>
    </sidebar>
</template>

<script>
    import UploadFile from "./types/UploadFile.vue";
    import FormUsers from "./types/FormUsers.vue";
    import UploadSertificate from "./types/UploadSertificate.vue";
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";

    export default {
        name: "EditAwardSidebar",
        components: {
            UploadFile,
            FormUsers,
            UploadSertificate,
            VuePdfEmbed
        },
        props: {
            open: Boolean,
            item: {
                type: Object,
                default: {}
            },
        },
        data() {
            return {
                dropDownText: 'Выберите тип награды',
                category_id: null,
                selectedType: true,
                uploadFiles: [],
                fileCertificate: null,
                targetable_id: null,
                targetable_type: null,
                name: '',
                description: '',
                hide: false,
                type: null,
                course_ids: [],
                styles: '',
                awards: []
            };
        },
        methods: {
            addCourse(id){
                this.course_ids.push(id);
            },
            removeCourse(id){
                this.course_ids = this.course_ids.filter(n => n !== id);
            },
            superselectChoice(val) {
                console.log(val);
                this.targetable_id = val.id;
                if (val.type === 2) {
                    this.targetable_type = 'App\\ProfileGroup';
                }
                if (val.type === 3) {
                    this.targetable_type = 'App\\Position';
                }
            },
            async saveCategory() {
                const formDataCategories = new FormData();
                formDataCategories.append('name', this.name);
                formDataCategories.append('description', this.description);
                formDataCategories.append('hide', this.hide);
                if (Object.keys(this.item).length === 0) {
                    formDataCategories.append('type', this.type);
                    await this.axios
                        .post('/award-categories/store', formDataCategories, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                        })
                        .then(response => {
                            this.category_id = response.data.data.id;
                        })
                        .catch(error => {
                            console.log(error);
                        })
                } else {
                    if(this.category_id || this.name !== this.item.name || this.description !== this.item.description || this.hide !== this.item.hide){
                        formDataCategories.append('_method', 'put');
                        await this.axios
                            .post('/award-categories/update/' + this.category_id, formDataCategories, {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                            })
                            .then(() => {})
                            .catch(error => {
                                console.log(error);
                            })
                    }
                }
            },
            async saveAwards() {
                const formData = new FormData();
                if (this.type === 2) {
                    for (let j = 0; j < this.course_ids.length; j++) {
                        formData.append('course_ids[]', this.course_ids[j]);
                    }
                    formData.append('styles', this.styles);
                    if(this.fileCertificate){
                        formData.append('file', this.fileCertificate);
                    }
                }
                if (this.type === 3) {
                    formData.append('targetable_type', this.targetable_type);
                    formData.append('targetable_id', this.targetable_id);
                }
                formData.append('award_category_id', this.category_id);
                if (this.type === 1 && this.uploadFiles.length > 0) {
                    for (let i = 0; i < this.uploadFiles.length; i++) {
                        formData.append('file[]', this.uploadFiles[i]);
                    }
                }

                if(Object.keys(this.item).length > 0 && this.type !== 1){
                    formData.append('_method', 'put');
                    await this.axios
                        .post("/awards/update/" + this.awards[0].id, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                        })
                        .then(response => {
                            this.$emit('update:open', false);
                            this.$emit('save-award', response.data.data);
                            this.$refs.newSertificateForm.reset();
                        })
                        .catch(function (error) {
                            console.log("error");
                            console.log(error);
                        });
                } else if (Object.keys(this.item).length === 0 || this.type === 1){
                    await this.axios
                        .post("/awards/store", formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                        })
                        .then(response => {
                            this.$emit('update:open', false);
                            this.$emit('save-award', response.data.data);
                            this.$refs.newSertificateForm.reset();
                        })
                        .catch(function (error) {
                            console.log("error");
                            console.log(error);
                        });
                }
            },
            async onSubmit() {
                if (this.type) {
                    let loader = this.$loading.show();
                    if (this.hide) {
                        this.hide = 0;
                    } else {
                        this.hide = 1;
                    }
                    await this.saveCategory();
                    await this.saveAwards();
                    loader.hide();
                }
            },
            setFileType(type) {
                this.type = type;
                this.selectedType = true;
                if (type === 1) {
                    this.dropDownText = 'Загрузка картинки';
                }
                if (type === 2) {
                    this.dropDownText = 'Конструктор сертификата';
                }
                if (type === 3) {
                    this.dropDownText = 'Данные начислений';
                }
            },
            formFile(files) {
                this.uploadFiles = files;
            },
            formFileCertificate(file){
                this.fileCertificate = file;
            },
            styleChange(styles) {
                this.styles = JSON.stringify(styles);
            }
        },
        async mounted() {
            if (Object.keys(this.item).length > 0) {
                let loader = this.$loading.show();
                await this.axios
                    .get('/award-categories/get/awards/' + this.item.id)
                    .then(response => {
                        this.awards = response.data.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });

                this.category_id = this.item.id;
                this.type = this.item.type;
                this.name = this.item.name;
                this.description = this.item.description;
                if(this.item.hide === 1){
                    this.hide = false;
                }
                if(this.item.hide === 0){
                    this.hide = true;
                }

                if(this.type === 2){
                    // this.targetable_type = this.awards[0].targetable_type;
                    // this.targetable_id = this.awards[0].targetable_id;
                }

                if(this.type === 3){
                    this.targetable_type = this.awards[0].targetable_type;
                    this.targetable_id = this.awards[0].targetable_id;
                }
                // this.form.path = this.item.path;
                // this.form.format = this.item.format;
                // this.form.styles = this.item.styles;
                // this.form.targetable_type = this.item.targetable_type;
                // this.form.targetable_id = this.item.targetable_id;

                if (this.item.type === 1) {
                    this.dropDownText = 'Загрузка картинки';
                }
                if (this.item.type === 2) {
                    this.dropDownText = 'Конструктор сертификата';
                }
                if (this.item.type === 3) {
                    this.dropDownText = 'Данные начислений';
                }
                loader.hide();
            }
        }
    };
</script>

<style lang="scss">
    #edit-award-sidebar {
        .with-info, .dropdown-item{
            position: relative;
            i{
                width: 16px;
                font-size: 10px;
                height: 16px;
                background-color: #ffc107;
                color: #333;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
                position: absolute;
                top: -3px;
                margin-left: 3px;
            }
        }
        .dropdown-item{
            i{
                top: 7px;
                right: -7px;
            }
        }
        .custom-file-label {
            span {
                display: flex !important;
            }
        }

        .multiselect__tags-wrap {
            display: flex !important;
        }

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

        .dropdown-select-type {
            li {
                a {
                    font-size: 16px;
                    padding: 8px 14px;

                    &:hover {
                        background-color: #ebebec;
                    }
                }
            }
        }

        .custom-switch{
            padding-left: 0;
            input[type="checkbox"] {
                position: absolute;
                margin: 8px 0 0 16px;
            }
            input[type="checkbox"] + label {
                position: relative;
                padding: 5px 0 0 50px;
                line-height: 1;
                margin: 10px 0;
            }
            input[type="checkbox"] + label:before {
                content: "";
                position: absolute;
                display: block;
                left: 0;
                top: 0;
                width: 40px; /* x*5 */
                height: 24px; /* x*3 */
                border-radius: 16px; /* x*2 */
                background: #fff;
                border: 1px solid #d9d9d9;
                -webkit-transition: all 0.3s;
                transition: all 0.3s;
            }
            input[type="checkbox"] + label:after {
                content: "";
                position: absolute;
                display: block;
                left: 0px;
                top: 0px;
                width: 24px; /* x*3 */
                height: 24px; /* x*3 */
                border-radius: 16px; /* x*2 */
                background: #fff;
                border: 1px solid #d9d9d9;
                -webkit-transition: all 0.3s;
                transition: all 0.3s;
            }
            input[type="checkbox"] + label:hover:after {
                box-shadow: 0 0 5px rgba(0,0,0,0.3);
            }
            input[type="checkbox"]:checked + label:after {
                margin-left: 16px;
            }
            input[type="checkbox"]:checked + label:before {
                background: #55D069;
            }
            &.custom-switch-small{
                input[type="checkbox"] {
                    margin: 5px 0 0 10px;
                }
                input[type="checkbox"] + label {
                    position: relative;
                    padding: 0 0 0 32px;
                    line-height: 1.3em;
                }
                input[type="checkbox"] + label:before {
                    width: 25px; /* x*5 */
                    height: 15px; /* x*3 */
                    border-radius: 10px; /* x*2 */
                }
                input[type="checkbox"] + label:after {
                    width: 15px; /* x*3 */
                    height: 15px; /* x*3 */
                    border-radius: 10px; /* x*2 */
                }
                input[type="checkbox"] + label:hover:after {
                    box-shadow: 0 0 3px rgba(0,0,0,0.3);
                }
                input[type="checkbox"]:checked + label:after {
                    margin-left: 10px; /* x*2 */
                }
            }
        }
    }
</style>