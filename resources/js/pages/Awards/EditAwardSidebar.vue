<template>
	<Sidebar
		id="edit-award-sidebar"
		:title="name ? name : 'Сертификат'"
		:open="open"
		:class="isShow ? 'show' : ''"
		width="70%"
		@close="$emit('update:open', false)"
	>
		<BForm
			ref="newSertificateForm"
			@submit.prevent="onSubmit"
		>
			<BFormGroup
				id="input-group-1"
				label-cols-sm="3"
				label-align-sm="left"
				description="Например, сертификаты, грамоты и т.п."
			>
				<template #label>
					<label class="with-info">Название награды <img
						id="info1"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
					></label>
					<b-popover
						target="info1"
						triggers="hover"
						placement="right"
					>
						<p style="font-size: 15px">
							Название вида награды, которое будет отображаться в профиле
							сотрудника
						</p>
					</b-popover>
				</template>
				<BFormInput
					id="input-1"
					v-model="name"
					type="text"
					placeholder="Название"
					:state="invalidName"
					aria-describedby="input-live-feedback"
					required
				/>
				<b-form-invalid-feedback id="input-live-feedback">
					Введите не менее 3 символов
				</b-form-invalid-feedback>
			</BFormGroup>
			<BFormGroup
				id="input-group-2"
				label-cols-sm="3"
				label-align-sm="left"
			>
				<template #label>
					<label class="with-info">Описание награды <img
						id="info2"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
					></label>
					<b-popover
						target="info2"
						triggers="hover"
						placement="right"
					>
						<p style="font-size: 15px">
							Краткое или детальное описание награды.
						</p>
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
				/>
			</BFormGroup>

			<BFormGroup
				id="input-group-3"
				label-cols-sm="3"
				label-align-sm="left"
			>
				<template #label>
					<label class="with-info">Тип награды <img
						id="info3"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
					></label>
					<b-popover
						target="info3"
						triggers="hover"
						placement="right"
					>
						<p style="font-size: 15px">
							Доступно 3 типа награды, где каждый отвечает за тот или иной успех
							сотрудников
						</p>
					</b-popover>
				</template>
				<BDropdown
					v-if="!readonly"
					id="input-3"
					:text="dropDownText"
					required
					class="dropdown-select-type"
				>
					<BDropdownItem
						href="#"
						@click="setFileType(1)"
					>
						Загрузка картинки
						<img
							id="info4"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
						>
						<b-popover
							target="info4"
							triggers="hover"
							placement="top"
						>
							<p style="font-size: 15px">
								Загрузка шаблонов (картинок) для дальнейшего использования.
								Служит для хранения Ваших шаблонов и быстрой их загрузки при награждении сотрудника
							</p>
						</b-popover>
					</BDropdownItem>
					<BDropdownItem
						href="#"
						@click="setFileType(2)"
					>
						Конструктор сертификата
						<img
							id="info5"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
						>
						<b-popover
							target="info5"
							triggers="hover"
							placement="top"
						>
							<p style="font-size: 15px">
								Служит для создания уникального шаблона для одного или нескольких
								курсов. По окончаю того или иного курса, сотрудник будет награжден созданным
								сертификатом
							</p>
						</b-popover>
					</BDropdownItem>
					<BDropdownItem
						href="#"
						@click="setFileType(3)"
					>
						Данные начислений
						<img
							id="info6"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
						>
						<b-popover
							target="info6"
							triggers="hover"
							placement="top"
						>
							<p style="font-size: 15px">
								Служит для вывода трёх лучших сотрудников по отделу или
								должности. Если Вы создали этот тип, то сотрудник сможет увидеть топ 3 лучших
								сотрудников по своему отделу и (или) своей должности в своем профиле
							</p>
						</b-popover>
					</BDropdownItem>
				</BDropdown>
				<div v-else>
					<span class="disable-select">
						<b-button
							disabled
							variant="secondary"
						>{{ dropDownText }}</b-button>
						<img
							id="info7"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
						>
						<b-popover
							target="info7"
							triggers="hover"
							placement="top"
						>
							<p style="font-size: 15px">Вы уже не можете сменить тип награды. Создайте новую награду с нужным Вам типом.</p>
						</b-popover>
					</span>
				</div>
			</BFormGroup>

			<BFormGroup class="file-type">
				<UploadFile
					v-if="type === 1"
					:awards-obj="awards"
					required
					@image-download="formFile"
				/>

				<UploadSertificate
					v-if="type === 2"
					:id="category_id"
					:awards="awards"
					required
					@image-download="formFileCertificate"
					@styles-change="styleChange"
					@add-course="addCourse"
					@remove-course="removeCourse"
					@add-course-all="addCourseAll"
					@remove-course-all="removeCourseAll"
					@has-change-constructor="hasChangeConstructor"
				/>

				<ChoiceTop
					v-if="type === 3"
					:targetable_id="targetable_id"
					@choiced-top="choicedTop"
				/>
			</BFormGroup>

			<BFormGroup
				v-if="type === 1 || type === 2 "
				id="input-group-4"
				class="custom-switch custom-switch-sm"
			>
				<b-form-checkbox
					v-model="hide"
					switch
				>
					Отображать пользователям награды других участников
				</b-form-checkbox>
			</BFormGroup>
			<hr class="mb-4">
			<BButton
				type="submit"
				variant="primary"
			>
				Сохранить
			</BButton>
		</BForm>
	</Sidebar>
</template>

<script>
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import UploadFile from './types/UploadFile.vue';
import ChoiceTop from './types/ChoiceTop.vue';
import UploadSertificate from './types/UploadSertificate.vue';
// import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';

export default {
	name: 'EditAwardSidebar',
	components: {
		Sidebar,
		UploadFile,
		ChoiceTop,
		UploadSertificate,
		// VuePdfEmbed,
	},
	props: {
		open: Boolean,
		item: {
			type: Object,
			default: () => {}
		},
	},
	data() {
		return {
			constructorChange: false,
			isShow: false,
			readonly: false,
			dropDownText: 'Выберите тип награды',
			category_id: null,
			selectedType: false,
			uploadFiles: [],
			fileCertificate: null,
			targetable_id: null,
			targetable_type: null,
			name: '',
			description: '',
			hide: true,
			type: null,
			course_ids: [],
			styles: '',
			awards: [],
			invalidName: true,
			hasFileCertificate: false
		};
	},
	async mounted() {
		setTimeout(() => {
			this.isShow = true;
		}, 20);
		if (Object.keys(this.item).length > 0) {
			let loader = this.$loading.show();
			this.hasFileCertificate = true;
			await this.axios
				.get('/award-categories/get/awards/' + this.item.id)
				.then(response => {
					this.awards = response.data.data;
				})
				.catch(error => {
					console.error(error);
				});
			this.readonly = true;
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
			if (this.type === 2){
				this.styles = this.awards[0].styles;
			}

			if (this.type === 3) {
				this.targetable_type = this.awards[0].targetable_type;
				this.targetable_id = this.awards[0].targetable_id;
			}


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
	},
	methods: {
		choicedTop(data){
			this.targetable_id = data.id;
			this.targetable_type = data.type;
		},
		hasChangeConstructor(arg){
			this.constructorChange = arg;
		},
		addCourse(id) {
			this.course_ids.push(id);
		},
		addCourseAll(arr){
			for(let i = 0; i < arr.length; i++){
				this.course_ids.push(arr[i].id);
			}
		},
		removeCourse(id) {
			this.course_ids = this.course_ids.filter(n => n !== id);
		},
		removeCourseAll(){
			this.course_ids = [];
		},
		async saveCategory() {
			let hidePhp = null;
			if (this.hide) {
				hidePhp = 0;
			} else {
				hidePhp = 1;
			}

			const formDataCategories = new FormData();
			formDataCategories.append('name', this.name);
			formDataCategories.append('description', this.description);
			formDataCategories.append('hide', hidePhp);
			if (Object.keys(this.item).length === 0) {
				formDataCategories.append('type', this.type);
				await this.axios
					.post('/award-categories/store', formDataCategories, {
						headers: {
							'Content-Type': 'multipart/form-data',
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
					})
					.then(response => {
						this.category_id = response.data.data.id;
					})
					.catch(error => {
						console.error(error);
					})
			} else {
				if (this.category_id || this.name !== this.item.name || this.description !== this.item.description || this.hide !== this.item.hide) {
					formDataCategories.append('_method', 'put');
					await this.axios
						.post('/award-categories/update/' + this.category_id, formDataCategories, {
							headers: {
								'Content-Type': 'multipart/form-data',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
							},
						})
						.then(() => {
						})
						.catch(error => {
							console.error(error);
						})
				}
			}
		},
		async saveAwards() {
			const formData = new FormData();
			formData.append('award_category_id', this.category_id);
			if (this.type === 1 && this.uploadFiles.length > 0) {
				for (let i = 0; i < this.uploadFiles.length; i++) {
					formData.append('file[]', this.uploadFiles[i]);
				}
			}
			if (this.type === 2) {
				for (let j = 0; j < this.course_ids.length; j++) {
					formData.append('course_ids[]', this.course_ids[j]);
				}
				formData.append('styles', this.styles);
				if (this.fileCertificate) {
					formData.append('file', this.fileCertificate);
				}
			}
			if (this.type === 3) {
				formData.append('targetable_type', this.targetable_type);
				formData.append('targetable_id', this.targetable_id);
			}

			if (Object.keys(this.item).length > 0 && this.type !== 1) {
				formData.append('_method', 'put');
				await this.axios
					.post('/awards/update/' + this.awards[0].id, formData, {
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
						console.error(error);
					});
			} else if (Object.keys(this.item).length === 0 || this.type === 1) {
				await this.axios
					.post('/awards/store', formData, {
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
						console.error(error);
					});
			}
		},
		async onSubmit() {
			if (!this.selectedType && Object.keys(this.item).length === 0) {
				this.$toast.error('Выберите тип награды', {
					timeout: 5000
				});
			} else {
				if (this.type) {
					if (this.name.length > 2) {
						let loader = this.$loading.show();
						this.invalidName = true;

						if (this.type === 1) {
							await this.saveCategory();
							await this.saveAwards();
						}

						if (this.type === 2) {
							if(this.hasFileCertificate){
								if (this.constructorChange) {
									if(this.course_ids.length > 0){
										await this.saveCategory();
										await this.saveAwards();
									} else {
										this.$toast.error('Выберите один или несколько курсов', {
											timeout: 5000
										});
									}
								} else {
									this.$toast.error('Сперва отредактируйте выбранный шаблон', {
										timeout: 5000
									});
								}
							} else {
								this.$toast.error('Загрузите шаблон', {
									timeout: 5000
								});
							}
						}

						if (this.type === 3) {
							if(this.targetable_type && this.targetable_id){
								await this.saveCategory();
								await this.saveAwards();
							} else{
								this.$toast.error('Выберите должность или отдел', {
									timeout: 5000
								});
							}
						}


						loader.hide();
					} else {
						this.invalidName = false;
					}
				}
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
		formFileCertificate(file, bool) {
			this.fileCertificate = file;
			this.hasFileCertificate = bool;
		},
		styleChange(styles) {
			this.styles = JSON.stringify(styles);
		}
	}
};
</script>

<style lang="scss">
    #edit-award-sidebar {
        .form-control, .custom-file-label{
            height: 50px;
            border-radius: 6px;
            background-color: #f7fafc;
            padding: 10px 20px;
            border: 1px solid #ddd!important;
            &::placeholder{
                color: #a9b6cb;
            }
            &:active, &:focus{
                background-color: #fff;
                box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            }
            .multiselect{
                .multiselect__tags{
                    height: 50px;
                    border-radius: 6px;
                    background-color: #f7fafc;
                    padding: 10px 20px;
                }
            }
        }
        .ui-sidebar__body{
            border-radius: 20px 0 0 20px;
            overflow: hidden !important;
        }
        .ui-sidebar__header{
            padding: 20px 25px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #ddd;
            span{
                font-size: 24px;
                color: #333 !important;
                font-weight: 700;
            }
        }
        .ui-sidebar__content{
            padding: 20px 25px!important;
        }
        .img-info{
            margin-top: -2px;
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

        .custom-switch {
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
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            }

            input[type="checkbox"]:checked + label:after {
                margin-left: 16px;
            }

            input[type="checkbox"]:checked + label:before {
                background: #55D069;
            }

            &.custom-switch-small {
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
                    box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
                }

                input[type="checkbox"]:checked + label:after {
                    margin-left: 10px; /* x*2 */
                }
            }
        }
    }
</style>
