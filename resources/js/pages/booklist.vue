<template>
	<div class="d-flex">
		<aside
			id="left-panel"
			class="lp"
		>
			<div
				class="form-search-kb"
				v-if="can_edit"
			>
				<i class="fa fa-search" />
				<input
					type="text"
					v-model="search.input"
					@input="searchInput"
					@blur="searchCheck"
					placeholder="Искать в базе..."
					class="form-control"
				>
				<i
					class="search-clear"
					v-if="search.input.length"
					@click="clearSearch"
				>x</i>
			</div>
			<div
				class="btn btn-grey mb-3"
				@click="$emit('back')"
				v-if="!course_page"
			>
				<i class="fa fa-arrow-left" />
				<span>Вернуться к разделам</span>
			</div>

			<div class="kb-wrap noscrollbar">
				<div
					class="chapter opened mb-3"
					v-if="!course_page && !search.items.length && !search.input.length"
				>
					<div class="d-flex">
						<span class="font-16 font-bold">{{ parent_title }}</span>
						<div class="chapter-btns">
							<i
								class="fa fa-plus"
								v-if="mode =='edit'"
								@click="addPageToTree"
							/>
						</div>
					</div>
				</div>

				<div class="search-content">
					<template v-if="search.items.length">
						<div
							v-for="item in search.items"
							:key="item.id"
							class="search-item"
							@click="showPage(item.id, true)"
						>
							<p
								v-if="item.book"
								class="search-item-book"
							>
								{{ item.book.title }}
							</p>
							<p class="search-item-title">
								{{ item.title }}
							</p>
							<div
								class="search-item-text"
								v-html="item.text"
							/>
						</div>
					</template>

					<div
						v-else-if="search.input.length <= 2 && search.input.length !== 0"
						class="text-muted"
					>
						Введите минимум 3 символа
					</div>

					<div
						v-else-if="search.input.length > 2"
						class="text-muted"
					>
						Ничего не найдено
					</div>
				</div>
				<template v-if="!search.items.length && !search.input.length">
					<NestedCourse
						v-if="course_page"
						:tasks="tree"
						:active="activesbook != null ? activesbook.id : 0"
						@showPage="showPage"
					/>

					<NestedDraggable
						v-else
						:active="activesbook != null ? activesbook.id : 0"
						:tasks="tree"
						:mode="mode"
						:auth_user_id="auth_user_id"
						:opened="true"
						@showPage="showPage"
						@addPage="addPage"
						:parent_id="id"
					/>
				</template>
			</div>
		</aside>
		<!-- /#left-panel -->


		<!-- Right Panel -->

		<div
			class="rp"
			style="flex: 1;padding-bottom: 0px;flex: 1 1 0%;height: 100vh;overflow-y: auto;"
		>
			<div class="hat">
				<div
					class="d-flex jsutify-content-between hat-top"
					v-if="!course_page"
				>
					<div class="bc">
						<a
							href="#"
							@click="$emit('back')"
						>База знаний</a>
						<template v-for="(bc, bc_index) in breadcrumbs">
							<i
								class="fa fa-chevron-right"
								:key="bc_index"
							/>
							<a
								href="#"
								@click="showPage(bc.id)"
								:key="'a' + bc_index"
							>{{ bc.title }}</a>
						</template>
					</div>

					<div
						class="mode_changer"
						v-if="can_edit"
					>
						<i
							class="fa fa-pen"
							@click="toggleMode"
							:class="{'active': mode == 'edit'}"
						/>
					</div>

					<div
						class="control-btns"
						v-if="can_edit"
					>
						<div
							class="d-flex justify-content-end"
							:asd="auth_user_id"
							v-if="activesbook != null"
						>
							<input
								type="text"
								:ref="'mylink' + activesbook.id"
								class="hider"
							>

							<button
								v-if="activesbook != null && activesbook.parent_id == null"
								class="form-control btn-action btn-medium ml-2"
								@click="showPermissionModal = true"
							>
								<i class="fa fa-cog" />
							</button>

							<template v-if="edit_actives_book">
								<button
									class="form-control btn-action btn-medium ml-2"
									@click="showImageModal = true"
								>
									<i class="far fa-image" />
								</button>

								<button
									class="form-control btn-action btn-medium ml-2"
									@click="showAudioModal = true"
								>
									<i class="fas fa-volume-up" />
								</button>

								<button
									class="form-control btn-delete btn-medium ml-2"
									@click="deletePage"
								>
									Удалить
								</button>

								<button
									class="form-control btn-save btn-medium ml-2"
									@click="saveServer"
								>
									Сохранить
								</button>
							</template>

							<template v-else>
								<button
									class="form-control btn-action btn-medium ml-2"
									title="Поделиться ссылкой"
									@click="copyLink(activesbook)"
								>
									<i class="fa fa-clone" />
								</button>

								<button
									v-if="mode == 'edit'"
									class="form-control btn-danger btn-medium ml-2"
									@click="deletePage"
								>
									<i class="fa fa-trash" />
								</button>

								<button
									v-if="mode == 'edit'"
									class="form-control btn-save btn-medium ml-2"
									@click="edit_actives_book = true"
								>
									Редактировать
								</button>
							</template>
						</div>
					</div>
				</div>

				<div>
					<template v-if="activesbook != null">
						<input
							type="text"
							class="article_title px-4 py-3"
							v-model="activesbook.title"
						>
					</template>
				</div>
			</div>

			<div class="content mt-3">
				<template v-if="activesbook != null && edit_actives_book">
					<Editor
						@onKeyUp="editorSave"
						@onChange="editorSave"
						v-model="activesbook.text"
						api-key="mve9w0n1tjerlwenki27p4wjid4oqux1xp0yu0zmapbnaafd"
						:init="{
							images_upload_url: '/upload/images/',
							automatic_uploads: true,
							height: editorHeight,
							setup: function (editor) {
								editor.on('init change', function () {
									editor.uploadImages();
								});
							},
							images_upload_handler: submit_tinymce,
							//paste_data_images: false,
							resize: true,
							autosave_ask_before_unload: true,
							powerpaste_allow_local_images: true,
							browser_spellcheck: true,
							contextmenu: true,
							spellchecker_whitelist: ['Ephox', 'Moxiecode'],
							language: 'ru',
							convert_urls: false,
							relative_urls: false,
							language_url: '/static/langs/ru.js',
							content_css: '/static/css/mycontent.css',
							fontsize_formats:
								'8pt 10pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 36pt',
							lineheight_formats:
								'8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt',
							plugins: [
								' advlist anchor autolink codesample colorpicker fullscreen help image imagetools ',
								' lists link media noneditable  preview',
								' searchreplace table template textcolor  visualblocks wordcount ',
							],
							menubar: false, //'file edit view insert format tools table help',
							toolbar:
								'styleselect  | bold italic underline strikethrough | table | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | fullscreen  preview |  media  link | undo redo',
							toolbar_sticky: true,
							content_style:
								'.lineheight20px { line-height: 20px; }' +
								'.lineheight22px { line-height: 22px; }' +
								'.lineheight24px { line-height: 24px; }' +
								'.lineheight26px { line-height: 26px; }' +
								'.lineheight28px { line-height: 28px; }' +
								'.lineheight30px { line-height: 30px; }' +
								'.lineheight32px { line-height: 32px; }' +
								'.lineheight34px { line-height: 34px; }' +
								'.lineheight36px { line-height: 36px; }' +
								'.lineheight38px { line-height: 38px; }' +
								'.lineheight40px { line-height: 40px; }' +
								'body { padding: 20px;max-width: 960px;margin: 0 auto; }' +
								'.tablerow1 { background-color: #D3D3D3; }',
							formats: {
								lineheight20px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight20px',
								},
								lineheight22px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight22px',
								},
								lineheight24px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight24px',
								},
								lineheight26px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight26px',
								},
								lineheight28px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight20px',
								},
								lineheight30px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight30px',
								},
								lineheight32px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight32px',
								},
								lineheight34px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight34px',
								},
								lineheight36px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight36px',
								},
								lineheight38px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight38px',
								},
								lineheight40px: {
									selector:
										'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
									classes: 'lineheight40px',
								},
							},
							style_formats: [
								{ title: 'lineheight20px', format: 'lineheight20px' },
								{ title: 'lineheight22px', format: 'lineheight22px' },
								{ title: 'lineheight24px', format: 'lineheight24px' },
								{ title: 'lineheight26px', format: 'lineheight26px' },
								{ title: 'lineheight28px', format: 'lineheight28px' },
								{ title: 'lineheight30px', format: 'lineheight30px' },
								{ title: 'lineheight32px', format: 'lineheight32px' },
								{ title: 'lineheight34px', format: 'lineheight34px' },
								{ title: 'lineheight36px', format: 'lineheight36px' },
								{ title: 'lineheight38px', format: 'lineheight38px' },
								{ title: 'lineheight40px', format: 'lineheight40px' },
							],
							content_css: [
								'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
							],
						}"
					/>


					<Questions
						:course_item_id="course_item_id"
						:questions="activesbook.questions"
						:id="activesbook.id"
						type="kb"
						:mode="mode"
						:count_points="true"
						@passed="passed"
						:key="questions_key"
						:pass_grade="activesbook.pass_grade"
						@changePassGrade="changePassGrade"
					/>
				</template>

				<template v-if="activesbook != null && !edit_actives_book">
					<div class="book_page">
						<div class="author mb-5 d-flex aic justify-end">
							<img
								:src="activesbook.editor_avatar"
								alt="avatar icon"
							>
							<div class="text">
								<div class="edited">
									<p class="author-time">
										<span>Cоздан:</span> {{ activesbook.created }}
									</p>
									<i class="fa fa-chevron-right" />
									<p class="author-author">
										{{ activesbook.author }}
									</p>
								</div>
								<div class="edited">
									<p class="author-time">
										<span>Изменен:</span> {{ activesbook.edited_at }}
									</p>
									<i class="fa fa-chevron-right" />
									<p class="author-author">
										{{ activesbook.editor }}
									</p>
								</div>
							</div>
						</div>
						<div
							class="bp-text"
							v-html="activesbook.text"
						/>

						<Questions
							:questions="activesbook.questions"
							:id="activesbook.id"
							type="kb"
							:mode="mode"
							:count_points="true"
							@passed="passed"
							:pass="activesbook.item_model !== null"
							:key="questions_key"
							:pass_grade="activesbook.pass_grade"
							@changePassGrade="changePassGrade"
							:course_item_id="course_item_id"
							@nextElement="nextElement"
						/>
						<div class="pb-5" />


						<button
							class="next-btn btn btn-primary"
							v-if="course_page && activesbook.questions.length == 0"
							@click="nextElement()"
						>
							Продолжить курс
							<i class="fa fa-angle-double-right ml-2" />
						</button>
					</div>
				</template>
			</div>
		</div>


		<!-- .content -->

		<!-- Right Panel -->

		<b-modal
			v-model="showImageModal"
			title="Загрузить изображение"
		>
			<form
				@submit.prevent="submit"
				action="/upload/images/"
				enctype="multipart/form-data"
				method="post"
				style=" max-width: 300px;margin: 0 auto;"
			>
				<div class="form-group">
					<div class="custom-file">
						<input
							type="file"
							class="custom-file-input"
							id="customFile"
							@change="onAttachmentChange"
							accept="image/*"
						>
						<label
							class="custom-file-label"
							for="customFile"
						>Выберите файл</label>
					</div>
				</div>
			</form>
			<ProgressBar
				:percentage="myprogress"
				label="Загрузка"
			/>
		</b-modal>

		<b-modal
			v-model="showAudioModal"
			title="Загрузить аудио"
		>
			<form
				@submit.prevent="submit"
				action="/upload/audio/"
				enctype="multipart/form-data"
				method="post"
				style=" max-width: 300px;margin: 0 auto;"
			>
				<div class="form-group">
					<div class="custom-file">
						<input
							type="file"
							class="custom-file-input"
							id="customFile"
							@change="onAttachmentChangeaudio"
							accept="audio/mp3"
						>
						<label
							class="custom-file-label"
							for="customFile"
						>Выберите файл</label>
					</div>
				</div>
			</form>
		</b-modal>


		<b-modal
			v-model="showPermissionModal"
			:title="'Настройка доступа к разделу'"
		>
			<div v-if="activesbook != null">
				{{ activesbook.title }}
			</div>
			Пока не сделано
		</b-modal>
	</div>
</template>

<script>
import NestedDraggable from '@/components/nested'
import NestedCourse from '@/components/nested_course'
import Editor from '@tinymce/tinymce-vue'
import Questions from '@/pages/Questions'
import ProgressBar from '@/components/ProgressBar'

export default {
	name: 'PageBooklist',
	components: {
		NestedDraggable,
		NestedCourse,
		Editor,
		Questions,
		ProgressBar,
	},
	props: {
		trees: Array,
		parent_id: Number,
		auth_user_id: Number,
		parent_name: String,
		show_page_id: {
			default: 0,
		},
		can_edit: {
			default: false,
		},
		mode: {
			default: 'read'
		},
		course_page: {
			default: 0,
		},
		enable_url_manipulation: {
			default: true,
		},
		course_item_id: {
			default: 0
		},
		all_stages: {
			default: 0
		},
		completed_stages: {
			default: 0
		},
	},
	data() {
		return {
			activesbook: null,
			tree: [],
			ids: [], // array of books ids

			// misc
			can_save: false, // сохранять без тестов
			myprogress: 0,
			id: 0,
			loader: false,
			parent_title: '',
			search: {
				input: '',
				items: []
			},
			editorHeight: window.innerHeight - 128,
			attachment: null,
			breadcrumbs: [],

			// modals
			showImageModal: false,
			showAudioModal: false,
			showPermissionModal: false,
			edit_actives_book: false,
			showSearch: false,

			// courses
			passedTest: false,
			questions_key: 1,
			text_was: '',
			title_was: '',
			item_models: []
		}
	},

	created() {
		this.getTree();
		this.parent_title = this.parent_name;
		this.id = this.parent_id;
	},

	mounted() {
		if(!this.course_page) {
			window.addEventListener('beforeunload', e => this.beforeunloadFn(e))
		}
	},

	methods: {
		toggleMode(){
			this.clearSearch();
			this.$emit('toggleMode');
		},
		searchCheck() {
			if (this.search.input.length === 0) {
				this.clearSearch();
			}
		},
		clearSearch() {
			this.search = {
				input: '',
				items: []
			}
		},
		beforeunloadFn(e) {
			if(this.text_was != this.activesbook.text || this.title_was != this.activesbook.title) {
				e.returnValue = 'Are you sure you want to leave?';
			}
		},

		nextElement() {


			this.setSegmentPassed();



			// find next element
			let index2 = this.ids.findIndex(el => el.id == this.activesbook.id);

			if(index2 != -1 && this.ids.length - 1 > index2) {
				let el = this.findItem(this.ids[index2 + 1]);
				this.showPage(el.id);
			} else {
				// move to next course item
				this.$parent.after_click_next_element();
			}

			this.scrollToTop();
		},

		scrollToTop() {
			document.getElementsByClassName('rp')[0].scrollTo(0,0);
			if(this.course_item_id != 0) document.getElementsByClassName('rp')[1].scrollTo(0,0);
		},

		passed() {

			this.passedTest = true;

			// find element

			let index = this.ids.findIndex(el => el.id == this.activesbook.id);

			if(index != -1 && this.ids.length - 1 > index) {

				let el = this.findItem(this.ids[index + 1]);

				// pass if its not course.  cos there not nextElement button
				if(el.item_model == null && this.course_item_id == 0) {
					this.setSegmentPassed();
				}
			}


			//test
			let i = this.item_models.findIndex(im => im.item_id == this.activesbook.id);
			if(i == -1) this.item_models.push({
				item_id: this.activesbook.id,
				status: 1
			});

			this.connectItemModels(this.tree)

		},

		setSegmentPassed() {

			let el = null;
			// find element
			let index = this.ids.findIndex(el => el.id == this.activesbook.id);
			if(index != -1) {
				el = this.findItem(this.ids[index]);
				// if(el.item_model != null) return;
			}

			// pass
			this.axios
				.post('/my-courses/pass', {
					id: this.activesbook.id,
					type: 3,
					course_item_id: this.course_item_id,
					questions: this.activesbook.questions,
					all_stages: this.all_stages,
					completed_stages: this.completed_stages + 1,
				})
				.then((response) => {
					this.$emit('changeProgress');
					this.$emit('forGenerateCertificate', response.data.item_model);
					if(el != null) el.item_model = {status: 1};
					this.activesbook.item_model = response.data.item_model;
				})
				.catch((error) => {
					alert(error);
				});
		},

		findItem(el) {
			if(el.i.length == 1) return this.tree[el.i[0]];
			if(el.i.length == 2) return this.tree[el.i[0]].children[el.i[1]];
			if(el.i.length == 3) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]];
			if(el.i.length == 4) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]].children[el.i[3]];
			if(el.i.length == 5) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]].children[el.i[3]].children[el.i[4]];
			if(el.i.length == 6) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]].children[el.i[3]].children[el.i[4]].children[el.i[5]];
		},

		getTree() {
			this.axios
				.post('/kb/tree', {
					id: this.parent_id,
					can_read: this.course_page,
					course_item_id: this.course_item_id
				})
				.then((response) => {
					this.tree = response.data.trees;
					this.item_models = response.data.item_models;

					this.can_save = response.data.can_save; // without test

					// set active book
					const urlParams = new URLSearchParams(window.location.search);
					let book_id = urlParams.get('b');
					this.breadcrumbs = [{id:this.id, title: this.parent_title}];

					// create array of books ids
					this.ids = [];
					this.returnArray(this.tree);

					if(this.course_page) {

						book_id = this.show_page_id

						if(this.show_page_id == 0 || this.show_page_id == null) {
							this.showPage(this.tree[0].id);
						} else {
							// find element
							let index = this.ids.findIndex(el => el.id == this.show_page_id);

							if(index != -1) {
								let el = this.findItem(this.ids[index]);
								this.showPage(el.id);
							}
						}


					} else { // not course page


						let result = null
						this.tree.every(obj => {
							result = this.deepSearchId(obj, book_id)


							if (result != null) {

								this.showPage(book_id, false, true);
								return false;
							}
							return true;
						});

					}

					// passed steps
					this.connectItemModels(this.tree)

				})
				.catch((error) => {
					alert(error);
				});
		},

		returnArray(items, indexes = []) {
			items.forEach((item, i_index) => {
				let arr = [...indexes, i_index];
				this.ids.push({
					id: item.id,
					i: arr
				})

				if(item.children !== undefined) this.returnArray(item.children, arr);
			});
		},

		connectItemModels(tree) {
			tree.forEach(el => {

				let i = this.item_models.findIndex(im => im.item_id == el.id);
				if(i != -1) {
					el.item_model = this.item_models[i];
				} else {
					el.item_model = null;
				}
				if(el.children !== undefined) {
					this.connectItemModels(el.children)
				}
			});
		},

		searchInput() {
			if(this.search.input.length <= 2) return null;

			this.axios
				.post('/kb/search', {
					text: this.search.input,
				})
				.then((response) => {

					this.search.items = response.data.items;
					this.emphasizeTexts();

				})
				.catch((error) => {
					alert(error);
				});
		},

		emphasizeTexts() {
			this.search.items.forEach(item => {
				item.text = item.text.replace(new RegExp(this.search.input,'gi'), '<b>' + this.search.input +  '</b>');
			});
		},

		saveServer() {
			if(this.activesbook.questions.length == 0 && !this.can_save) {
				this.$toast.error('Нельзя вносить изменения без тестов');
				return;
			}

			let loader = this.$loading.show();
			this.axios
				.post('/kb/page/update', {
					text: this.activesbook.text,
					title: this.activesbook.title,
					pass_grade: this.activesbook.pass_grade,
					id: this.activesbook.id,
				})
				.then(() => {
					this.text_was = this.activesbook.text;
					this.title = this.activesbook.title;
					this.edit_actives_book = false;
					this.$toast.info('Сохранено');
					this.renameNode(this.tree, this.activesbook.id, this.activesbook.title);
					loader.hide()

				})
				.catch(() => {loader.hide()})
		},

		addPage(book) {
			this.axios.post('/kb/page/create', {
				id: book.id
			}).then((response) => {
				this.activesbook = response.data;
				this.edit_actives_book = true;
				book.children.push(this.activesbook);
				this.$toast.info('Добавлена страница');
			});
		},

		addPageToTree() {
			this.axios.post('/kb/page/create', {
				id: this.id
			}).then((response) => {
				this.activesbook = response.data;
				this.edit_actives_book = true;
				this.tree.push(this.activesbook);
				this.$toast.info('Добавлена страница');
			});
		},

		deletePage() {
			if(confirm('Вы уверены?')) {
				this.axios
					.post('/kb/page/delete', {
						id: this.activesbook.id,
					})
					.then(() => {
						this.$toast.success('Удалено');
						this.removeNode(this.tree, this.activesbook.id)
						this.activesbook = null;
					});
			}
		},

		deepSearch(array, item) {
			return array.some(function s(el) {
				return el == item || ((el instanceof Array) && el.some(s));
			})
		},

		deepSearchId(obj, targetId) {

			if (obj.id == targetId) {
				return obj
			}

			for (let item of obj.children) {
				let check = this.deepSearchId(item, targetId)
				if (check) {
					return check
				}
			}

			return null
		},

		removeNode(arr, id) {
			arr.forEach((it, index) => {
				if (it.id === id) {
					arr.splice(index, 1)
				}
				this.removeNode(it.children, id)
			})
		},

		renameNode(arr, id, title) {
			arr.forEach(it => {
				if (it.id === id) {
					it.title = title;
				}
				this.renameNode(it.children, id, title)
			})
		},

		showPage(id, refreshTree = false, expand = false) {
			this.questions_key++;
			if(this.activesbook != null && (this.text_was != this.activesbook.text || this.title_was != this.activesbook.title)) {
				if(!this.course_page) {
					if(!confirm('У вас на странице остались несохранненные изменения. Точно хотите выйти?'))  {
						return;
					}
				}
			}

			if(this.activesbook && this.activesbook.id == id) return '';

			let loader = this.$loading.show();
			this.axios.post('/kb/get', {
				id: id,
				course_item_id: this.course_item_id,
				refresh: refreshTree
			}).then((response) => {
				loader.hide()

				// @TODO
				this.activesbook = response.data.book;



				this.questions_key++
				this.text_was = this.activesbook.text;
				this.title_was = this.activesbook.title;
				this.breadcrumbs = response.data.breadcrumbs;
				this.edit_actives_book = false;

				if(refreshTree) {
					this.id = response.data.top_parent.id;
					this.parent_title = response.data.top_parent.title
					this.tree = response.data.tree
					this.showSearch = false;
					this.search.input = false;
					this.search.items = [];
				}

				// for course
				this.passedTest = false;
				if(this.activesbook != null && this.activesbook.questions.length == 0) {
					this.passedTest = true;
				}

				if(expand) this.expandTree();
				this.setTargetBlank();

				if(this.enable_url_manipulation) {
					window.history.replaceState({ id: '100' }, 'База знаний', '/kb?s=' + this.id + '&b=' + id);
				}


			})
				.catch(() => {loader.hide()})

		},

		expandTree() {
			let item = null;

			this.breadcrumbs.forEach(bc => {

				let s_index = this.tree.findIndex(t => t.id == bc.id);

				if(s_index != -1) {

					if(item != null) {
						item = item.children[s_index];
					} else {
						item = this.tree[s_index]
					}
					item.opened = true;

				}

			});
		},

		setTargetBlank() {
			this.$nextTick(() => {
				var links = document.querySelectorAll('.bp-text a');
				links.forEach(l => l.setAttribute('target', '_blank'));
			})

		},

		editorSave() {},

		changePassGrade(grade) {
			this.activesbook.pass_grade = grade;
			let len = this.activesbook.questions.length;

			if(grade > len) this.activesbook.pass_grade = len;
			if(grade < 1) this.activesbook.pass_grade = 1;
		},

		addaudio(url) {
			// where tinymce???
			// eslint-disable-next-line no-undef
			tinymce.activeEditor.insertContent(
				'<audio controls src="' + url + '"></audio>'
			);
		},

		addimage(url) {
			// where tinymce???
			// eslint-disable-next-line no-undef
			tinymce.activeEditor.insertContent(
				'<img alt="картинка" src="'+ url + '"/>'
			);
		},

		submit_tinymce(blobInfo, success) {

			this.loader = true;
			const formData = new FormData();
			formData.append('attachment', blobInfo.blob());
			formData.append('id', this.activesbook.id);
			this.axios
				.post('/upload/images/', formData)
				.then((response) => {
					success(response.data.location);
					this.loader = false;
				})
				.catch((error) => console.log(error));
		},

		submit() {
			this.loader = true;
			const config = {
				onUploadProgress: progressEvent => {
					let { progress } = this.myprogress;
					progress = (progressEvent.loaded / progressEvent.total) * 100;
					this.myprogress = progress;
				}
			};
			const formData = new FormData();
			formData.append('attachment', this.attachment);
			formData.append('id', this.activesbook.id);
			this.axios
				.post('/upload/images/', formData, config)
				.then((response) => {

					this.addimage(response.data.location);

					if(this.myprogress >= 100){
						this.showImageModal = false;
						this.loader = false;
						this.myprogress = 0;
					}
				})
				.catch((error) => console.log(error));
		},

		copyLink(book) {
			var Url = this.$refs['mylink' + book.id];
			Url.value = window.location.origin + '/corp_book/' + book.hash;

			Url.select();
			document.execCommand('copy');

			this.$toast.info('Ссылка на страницу скопирована!');
		},

		onAttachmentChange(e) {
			this.attachment = e.target.files[0];
			this.submit();
		},

		onAttachmentChangeaudio(e) {
			this.attachment = e.target.files[0];
			this.submitaudio();
		},

		submitaudio() {
			this.loader = true;
			const formData = new FormData();
			formData.append('attachment', this.attachment);
			formData.append('id', this.activesbook.id);
			this.axios
				.post('/upload/audio/', formData)
				.then((response) => {
					this.addaudio(response.data.location);
					this.showAudioModal = false;
					this.loader = false;
				})
				.catch((error) => console.log(error));
		},

	},
};

</script>
<style>
.content {
    max-height: unset;
    overflow: unset;
}
</style>


