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
                <BDropdown :text="dropDownText" required>
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
                        v-if="form.award_type_id === 1"
                        :path="form.path"
                        :format="form.format"
                        required
                />

                <UploadSertificate
                        @image-download="formFile"
                        @styles-change="styleChange"
                        v-if="form.award_type_id === 2"
                        :path="form.path"
                        :format="form.format"
                        :styles="form.styles"
                        required
                />

                <!--                <FormUsers v-if="form.fileType === 3" required/>-->
                <superselect
                        v-if="form.award_type_id === 3"
                        class="w-50 mb-4"
                        :key="1"
                        :select_all_btn="true"/>
            </BFormGroup>

            <BFormGroup v-if="form.award_type_id === 2">
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

                />
            </BFormGroup>

            <BFormGroup id="input-group-4" switches>
                <BFormCheckbox v-model="form.hide" required>
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
    import Multiselect from "vue-multiselect";

    export default {
        name: "EditAwardSidebar",
        components: {
            UploadFile,
            FormUsers,
            UploadSertificate,
            Multiselect
        },
        props: {
            open: Boolean,
            item: Object | Boolean,
        },
        data() {
            return {
                id: null,
                value: [],
                options: [],
                dropDownText: 'Выберите тип награды',
                userName: 'Тимур Хайруллин',
                selectFileType: true,
                file: null,
                form: {
                    award_type_id: null,
                    course_ids: [],
                    name: '',
                    description: '',
                    hide: false,
                    path: '',
                    format: '',
                    styles: ''
                },
            };
        },
        methods: {
            async onSubmit() {
                if(this.file !== null){
                  if (this.form.award_type_id) {
                      let loader = this.$loading.show();
                     if(this.form.award_type_id === 2){
                         for(let i = 0; i < this.value.length; i++){
                             this.form.course_ids.push(this.value[i].id);
                         }
                     }
                      if (this.form.hide) {
                          this.form.hide = 1;
                      } else {
                          this.form.hide = 0;
                      }
                      const formData = new FormData();
                      formData.append('award_type_id', this.form.award_type_id);
                      for(let j = 0; j < this.form.course_ids.length; j++){
                          formData.append('course_ids[]', this.form.course_ids[j]);
                      }
                      formData.append('name', this.form.name);
                      formData.append('description', this.form.description);
                      formData.append('hide', this.form.hide);
                      formData.append('file', this.file);
                      formData.append('styles', JSON.stringify(this.form.styles));
                      if (this.item) {
                          // this.$emit('update-award', this.form);
                          this.axios
                              .put("/awards/update/" + this.item.id, formData, {
                                  headers: {
                                      'Content-Type': 'multipart/form-data',
                                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                  },
                              })
                              .then(response =>  {
                                  console.log(response.data.data);
                                  this.$emit('update:open', false);
                                  this.$emit('save-award');
                                  this.$refs.newSertificateForm.reset();
                                  loader.hide();
                              })
                              .catch(function (error) {
                                  console.log("error");
                                  console.log(error);
                                  loader.hide();
                              });
                      } else {
                          // this.$emit('save-award', this.form);
                          this.axios
                              .post("/awards/store", formData, {
                                  headers: {
                                      'Content-Type': 'multipart/form-data"'
                                  },
                              })
                              .then(response =>  {
                                  this.$emit('update:open', false);
                                  this.$emit('save-award', response.data.data);
                                  this.$refs.newSertificateForm.reset();
                                  loader.hide();
                              })
                              .catch(function (error) {
                                  console.log("error");
                                  console.log(error);
                                  loader.hide();
                              });
                      }

                  } else {
                      this.selectFileType = false;
                  }
              }
            },
            setFileType(id) {
                this.form.award_type_id = id;
                this.selectFileType = true;
                if(id === 1){
                    this.dropDownText = 'Загрузка картинки';
                }
                if(id === 2){
                    this.dropDownText = 'Конструктор сертификата';
                    this.getCourses();
                }
                if(id === 3){
                    this.dropDownText = 'Данные начислений';
                }
            },
            async getCourses(){
                let loader = this.$loading.show();
                await this.axios
                    .get('/admin/courses/get')
                    .then(response => {
                        const data = response.data.courses;
                        for(let i = 0; i < data.length; i++){
                            this.options.push(data[i]);
                            if(this.id !== null){
                                if(data[i].award_id === this.id){
                                    this.value.push(data[i]);
                                }
                            }
                        }
                        loader.hide();
                    })
                    .catch(error => {
                        console.log(error);
                        loader.hide();
                    })
            },
            formFile(val) {
                this.file = val;
                return val;
            },
            styleChange(styles){
                this.form.styles = JSON.stringify(styles);
                console.log(this.form);
            }
        },
        mounted() {
            // this.form.awardCreator = this.userName;
            if (this.item) {
                this.id = this.item.id;
                this.form.award_type_id = this.item.award_type_id;
                this.form.course_ids = this.item.course_ids;
                this.form.name = this.item.name;
                this.form.description = this.item.description;
                this.form.hide = this.item.hide;
                this.form.path = this.item.path;
                this.form.format = this.item.format;
                this.form.styles = this.item.styles;
                if(this.item.award_type_id === 1){
                    this.dropDownText = 'Загрузка картинки';
                }
                if(this.item.award_type_id === 2){
                    this.dropDownText = 'Конструктор сертификата';
                    this.getCourses();
                }
                if(this.item.award_type_id === 3){
                    this.dropDownText = 'Данные начислений';
                }
            }
        }
    };
</script>

<style lang="scss">
    #edit-award-sidebar {
        .multiselect__tags-wrap{
            display: flex!important;
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
    }
</style>