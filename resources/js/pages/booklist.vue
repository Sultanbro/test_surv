<template>
	<div class="Booklist d-flex">
		<aside
			id="left-panel"
			class="lp"
		>
			<div
				v-if="can_edit"
				class="form-search-kb"
			>
				<i class="fa fa-search" />
				<input
					v-model="search.input"
					type="text"
					placeholder="Искать в базе..."
					class="form-control"
					@input="searchInput"
					@blur="searchCheck"
				>
				<i
					v-if="search.input.length"
					class="search-clear"
					@click="clearSearch"
				>x</i>
			</div>
			<div
				v-if="!course_page"
				class="btn btn-grey mb-3"
				@click="$emit('back')"
			>
				<i class="fa fa-arrow-left" />
				<span>Вернуться к разделам</span>
			</div>

			<div class="kb-wrap noscrollbar">
				<div
					v-if="!course_page && !search.items.length && !search.input.length"
					class="chapter opened mb-3"
				>
					<div class="d-flex">
						<span class="font-16 font-bold">{{ parent_title }}</span>
						<div class="chapter-btns">
							<i
								v-if="mode =='edit'"
								class="fa fa-plus"
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
							@click="showPage(item.id, true, false)"
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
							<!-- eslint-disable -->
							<div
								class="search-item-text"
								v-html="item.text"
							/>
							<!-- eslint-enable -->
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
						:parent_id="id"
						@showPage="showPage"
						@addPage="addPage"
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
					v-if="!course_page"
					class="d-flex jsutify-content-between hat-top"
				>
					<div class="bc">
						<a
							href="#"
							@click="$emit('back')"
						>База знаний</a>
						<template v-for="(bc, bc_index) in breadcrumbs">
							<i
								:key="bc_index"
								class="fa fa-chevron-right"
							/>
							<a
								:key="'a' + bc_index"
								href="#"
								@click="showPage(bc.id)"
							>{{ bc.title }}</a>
						</template>
					</div>

					<div
						v-if="can_edit"
						class="mode_changer"
					>
						<i
							class="fa fa-pen"
							:class="{'active': mode == 'edit'}"
							@click="toggleMode"
						/>
					</div>

					<div
						v-if="can_edit"
						class="control-btns"
					>
						<div
							v-if="activesbook != null"
							class="d-flex justify-content-end"
							:asd="auth_user_id"
						>
							<input
								:ref="'mylink' + activesbook.id"
								type="text"
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
							v-model="activesbook.title"
							type="text"
							class="article_title px-4 py-3"
						>
					</template>
				</div>
			</div>

			<div class="content mt-3">
				<template v-if="activesbook != null && edit_actives_book">
					<Editor
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
						@onKeyUp="editorSave"
						@onChange="editorSave"
					/>


					<Questions
						:id="activesbook.id"
						:key="questions_key"
						:course_item_id="course_item_id"
						:questions="activesbook.questions"
						type="kb"
						:mode="mode"
						:count_points="true"
						:pass_grade="activesbook.pass_grade"
						@passed="passed"
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
						<!-- eslint-disable -->
						<div
							class="bp-text"
							v-html="activesbook.text"
						/>
						<!-- eslint-enable -->

						<Questions
							:id="activesbook.id"
							:key="questions_key"
							:questions="activesbook.questions"
							type="kb"
							:mode="mode"
							:count_points="true"
							:pass="activesbook.item_model !== null"
							:pass_grade="activesbook.pass_grade"
							:course_item_id="course_item_id"
							@passed="passed"
							@changePassGrade="changePassGrade"
							@nextElement="nextElement"
						/>
						<div class="pb-5" />


						<button
							v-if="course_page && activesbook.questions.length == 0"
							class="next-btn btn btn-primary"
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
				action="/upload/images/"
				enctype="multipart/form-data"
				method="post"
				style=" max-width: 300px;margin: 0 auto;"
				@submit.prevent="submit"
			>
				<div class="form-group">
					<div class="custom-file">
						<input
							id="customFile"
							type="file"
							class="custom-file-input"
							accept="image/*"
							@change="onAttachmentChange"
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
				action="/upload/audio/"
				enctype="multipart/form-data"
				method="post"
				style=" max-width: 300px;margin: 0 auto;"
				@submit.prevent="submit"
			>
				<div class="form-group">
					<div class="custom-file">
						<input
							id="customFile"
							type="file"
							class="custom-file-input"
							accept="audio/mp3"
							@change="onAttachmentChangeaudio"
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
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import { mapGetters } from 'vuex'
import NestedDraggable from '@/components/nested'
import NestedCourse from '@/components/nested_course'
import Editor from '@tinymce/tinymce-vue'
import Questions from '@/pages/Questions'
import ProgressBar from '@/components/ProgressBar'
import Mark from 'mark.js/dist/mark.es6.js'

const quotes = ['«»', '“”', '""', '()']
const enders = '.,!?:;'.split('')
const markOptions = {
	element: 'span',
	className: 'Booklist-mark',
	exclude: ['.Booklist-definition'],
	accuracy: 'exactly',
}
function createDefinition(text){
	const span = document.createElement('span')
	span.innerText = text
	span.classList.add('Booklist-definition')
	return span
}
function getSynonims(term){
	const result = []
	enders.forEach(char => {
		result.push(term + char)
	})
	quotes.forEach(pair => {
		result.push(pair[0] + term)
		result.push(term + pair[1])
		result.push(pair[0] + term + pair[1])
		enders.forEach(char => {
			result.push(pair[0] + term + char)
			result.push(term + pair[1] + char)
			result.push(pair[0] + term + pair[1] + char)
		})
	})
	return result
}

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
		trees: {
			type: Array,
			default: () => []
		},
		parent_id: {
			type: Number,
			default: 0
		},
		auth_user_id: {
			type: Number,
			default: 0
		},
		parent_name: {
			type: String,
			default: '',
		},
		show_page_id: {
			type: Number,
			default: 0,
		},
		can_edit: {
			type: Boolean,
			default: false,
		},
		mode: {
			type: String,
			default: 'read'
		},
		course_page: {
			type: Number,
			default: 0,
		},
		enable_url_manipulation: {
			type: Boolean,
			default: true,
		},
		course_item_id: {
			type: Number,
			default: 0
		},
		all_stages: {
			type: Number,
			default: 0
		},
		completed_stages: {
			type: Number,
			default: 0
		},
		glossary: {
			type: Array,
			default: () => []
		}
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
				items: [],
				timeout: null,
			},
			editorHeight: window.innerHeight - 128,
			attachment: null,
			breadcrumbs: [],
			highlight: '',

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
	computed: {
		...mapGetters(['user']),
	},

	watch: {
		activesbook: {
			handler(){
				const urlParams = new URLSearchParams(window.location.search)
				const hl = urlParams.get('hl')
				this.$nextTick(() => {
					const instance = new Mark(document.querySelector('.Booklist .bp-text'))
					instance.unmark({
						element: 'span',
						className: 'Booklist-mark',
						done: () => {
							if(hl){
								instance.mark(hl, {
									...markOptions,
									each: el => {
										this.$nextTick(() => el.classList.add('Booklist-mark_justmark'))
									}
								})
							}
							if(!this.glossary) return
							this.glossary.forEach(term => {
								instance.mark(term.word, {
									...markOptions,
									each: el => {
										this.$nextTick(() => el.appendChild(createDefinition(term.definition)))
									}
								})
								getSynonims(term.word).forEach(word => {
									instance.mark(word, {
										...markOptions,
										each: el => {
											this.$nextTick(() => el.appendChild(createDefinition(term.definition)))
										}
									})
								})
							})
						}
					})
				})
			},
			deep: true
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
			clearTimeout(this.search.timeout)
			this.search = {
				input: '',
				items: [],
				timeout: null
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
					this.highlight = urlParams.get('hl')
					let book_id = urlParams.get('b');
					this.breadcrumbs = [{id:this.id, title: this.parent_title}];

					// create array of books ids
					this.ids = [];
					this.returnArray(this.tree);

					if(this.search.input) this.highlight = this.search.input

					if(this.course_page) {
						book_id = this.show_page_id

						if(this.show_page_id == 0 || this.show_page_id == null) {
							this.showPage(this.tree[0].id, false, false);
						} else {
							// find element
							let index = this.ids.findIndex(el => el.id == this.show_page_id);

							if(index != -1) {
								let el = this.findItem(this.ids[index]);
								this.showPage(el.id, false, false);
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
			clearTimeout(this.search.timeout)
			this.search.timeout = setTimeout(this.runSearch, 500)
		},

		runSearch(){
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
			}).then(({data}) => this.addPageHandler(data, book.children));
		},

		addPageToTree() {
			this.axios.post('/kb/page/create', {
				id: this.id
			}).then(({data}) => this.addPageHandler(data, this.tree));
		},

		addPageHandler(book, parent){
			book.created = this.$moment.utc(book.created_at).local().format('DD.MM.YYYY HH:mm')
			book.edited_at = this.$moment.utc(book.updated_at).local().format('DD.MM.YYYY HH:mm')
			book.editor_avatar = this.$laravel.avatar
			const name = `${this.user.last_name} ${this.user.name}`
			book.author = name
			book.editor = name

			this.activesbook = book;
			this.edit_actives_book = true;
			parent.push(this.activesbook);
			this.$toast.info('Добавлена страница');
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
					this.clearSearch()
				}

				// for course
				this.passedTest = false;
				if(this.activesbook != null && this.activesbook.questions.length == 0) {
					this.passedTest = true;
				}

				if(expand) this.expandTree();
				this.setTargetBlank();

				if(this.enable_url_manipulation) {
					window.history.replaceState({ id: '100' }, 'База знаний', '/kb?s=' + this.id + '&b=' + id + (this.highlight ? `&hl=${this.highlight}` : ''));
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
				.catch((error) => console.error(error));
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
				.catch((error) => console.error(error));
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
				.catch((error) => console.error(error));
		},

	},
};

</script>
<style lang="scss">
.content {
	max-height: unset;
	overflow: unset;
}
.Booklist{
	&-mark{
		display: inline-flex;

		position: relative;
		font-weight: inherit;
		font-size: inherit;
		font-family: inherit;
		cursor: help;
		&:after{
			content: '*';
			color: #00F;
		}
		&:hover{
			.Booklist{
				&-definition{
					transform: translate(-50%, 0);
					visibility: visible;
					opacity: 1;
				}
			}
		}
		&_justmark{
			background-color: #fcf8e3;
			padding: 0 0.2em;
			&:after{
				content: none;
			}
		}
	}
	&-definition{
		flex: 0 1 content;
		width: max-content;
		max-width: 400px;
		padding: 5px 10px;
		border: 1px solid #000;

		position: absolute;
		bottom: 100%;
		left: 50%;

		font-size: 14px;
		font-weight: 400;
		color: #333;

		background-color: #fff;
		transform: translate(-50%, -50%);
		visibility: hidden;
		opacity: 0;
		transition: all 0.2s;
	}
	.tox.tox-tinymce--toolbar-sticky-on .tox-editor-header{
		left: 0 !important;
	}
}
</style>
