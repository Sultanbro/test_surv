<template>
	<div v-if="token">
		<div
			v-if="activeBook === null"
			class="upbooks-page"
		>
			<div class="lp">
				<h1 class="page-title">
					Темы книг
				</h1>

				<div
					v-for="(cat, c_index) in categories"
					:key="cat.id"
					class="section d-flex aic jcsb"
					:class="{'active': selectedCategory === c_index}"
					:style="'position:relative;'"
					@click="selectCategory(c_index)"
				>
					<p>{{ cat.name }}</p>
					<div
						class="d-flex aic ml-2"
						:style="'position:absolute; right: 0; z-index: 2'"
					>
						<i
							v-if="cat.id != 0 && mode == 'edit'"
							class="fa fa-pen"
							@click.stop="editCat(c_index)"
						/>

						<i
							v-if="cat.id != 0 && mode == 'edit'"
							class="fa fa-trash"
							@click.stop="deleteCat(c_index)"
						/>
					</div>
				</div>

				<button
					v-if="mode == 'edit'"
					class="btn-add"
					@click="modals.add_category.show = true"
				>
					Добавить категорию
				</button>
			</div>

			<div class="cont">
				<div class="hat">
					<div class="d-flex jsutify-content-between hat-top">
						<div class="bc">
							<p
								v-if="activeCategory"
								class="mb-0"
							>
								<b>{{ activeCategory.name }}</b>
							</p>
							<!---->
						</div>
						<div class="control-btns d-flex">
							<button
								v-if="mode == 'edit' && activeCategory != null"
								class="btn btn-success"
								@click="modals.upload_book.show = true"
							>
								Добавить книгу
							</button>


							<div
								v-if="can_edit"
								class="mode_changer ml-2"
							>
								<i
									v-b-popover.hover.top="'Включить редактирование книг'"
									class="fa fa-pen"
									:class="{'active': mode == 'edit'}"
									@click="toggleMode"
								/>
							</div>

							<div
								v-if="can_edit"
								class="mode_changer ml-2"
							>
								<i
									class="icon-nd-settings"
									@click="get_settings()"
								/>
							</div>
						</div>
					</div>
					<div><!----></div>
				</div>

				<div
					v-if="activeCategory != null"
					class="boxes"
				>
					<div
						v-for="(book, b_index) in activeCategory.books"
						:key="book.id"
						class="box-content"
						@click="go(book)"
					>
						<div class="box">
							<div class="left">
								<img
									:src="book.img != '' ? imageCrutch(book.img) : '/images/book_cover.jpg'"
									alt="book"
								>
								<div class="buttons">
									<i
										v-if="mode == 'edit'"
										class="fa fa-trash mr-1"
										@click.stop="deleteBook(b_index)"
									/>
									<i
										v-if="mode == 'edit'"
										class="fa fa-pen mr-1"
										@click.stop="editBook(book)"
									/>
									<i
										class="fa fa-info"
										@click.stop="showDetails(book)"
									/>
								</div>
							</div>
							<div class="right">
								<p class="title">
									{{ book.title }}
								</p>
								<p class="author">
									{{ book.author }}
								</p>
								<div class="text">
									{{ book.description }}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<UpbooksRead
			v-else
			:book_id="activeBook.id"
			mode="read"
			:show-back-btn="true"
			@back="back"
		/>

		<b-modal
			v-model="modals.add_category.show"
			title="Новая категория книг"
			size="md"
			class="modalle"
			hide-footer
			hide-header
		>
			<input
				v-model="modals.add_category.name"
				type="text"
				placeholder="Название категории..."
				class="form-control mb-2"
			>

			<button
				class="btn btn-primary rounded m-auto"
				@click="createCategory"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>

		<!-- Загрузить книгу -->
		<SimpleSidebar
			title="Загрузить книгу"
			:open="modals.upload_book.show"
			width="50%"
			@close="modals.upload_book.show = false"
		>
			<template #body>
				<UploadFiles
					:id="0"
					:token="token"
					type="book"
					:file_types="['pdf']"
					@onupload="onupload"
				/>

				<!-- after upload -->
				<div v-if="modals.upload_book.file">
					<br>
					<hr>
					<div class="form-group">
						<label>Название книги</label>
						<input
							v-model="modals.upload_book.file.model.title"
							type="text"
							placeholder="Название книги..."
							class="form-control"
						>
					</div>
					<div class="form-group">
						<label>Название автора</label>
						<input
							v-model="modals.upload_book.file.model.author"
							type="text"
							placeholder="Название автора..."
							class="form-control"
						>
					</div>
					<div class="form-group">
						<label>Категория</label>
						<select
							v-model="modals.upload_book.file.model.group_id"
							class="form-control"
						>
							<option
								v-for="cat in categories"
								:key="cat.id"
								:value="cat.id"
							>
								{{ cat.name }}
							</option>
						</select>
					</div>
					<div class="form-group">
						<label>Описание книги</label>
						<textarea
							v-model="modals.upload_book.file.model.description"
							class="form-control mt-2 mb-2"
							placeholder="Описание..."
						/>
					</div>
					<div class="form-group">
						<label>Обложка книги</label>
						<b-form-file
							v-if="modals.upload_book.file.model.img == ''"
							v-model="file_img"
							:state="Boolean(file_img)"
							placeholder="Выберите или перетащите файл сюда..."
							drop-placeholder="Перетащите файл сюда..."
							class="mt-3"
						/>
					</div>
					<div class="img-preview">
						<img
							v-if="modals.upload_book.file.model.img != ''"
							class="book-img"
							:src="modals.upload_book.file.model.img"
						>
					</div>
				</div>
			</template>

			<template #footer>
				<button
					v-if="modals.upload_book.file"
					class="btn btn-primary rounded m-auto"
					@click="saveBook"
				>
					<span>Сохранить</span>
				</button>
			</template>
		</SimpleSidebar>

		<!-- Details -->
		<SimpleSidebar
			title="О книге"
			:open="details != null"
			width="40%"
			@close="details = null"
		>
			<template #body>
				<div
					v-if="details != null"
					class="d-flex"
				>
					<div class="left f-70">
						<p class="mb-2 font-bold">
							{{ details.title }}
						</p>
						<div class="text">
							{{ details.description }}
						</div>
					</div>
					<div class="right f-30 pl-4">
						<img
							v-if="details.img != ''"
							class="book-img mb-5"
							:src="details.img"
						>
					</div>
				</div>
			</template>
		</SimpleSidebar>


		<!-- Edit book -->
		<SimpleSidebar
			title="Редактировать книгу"
			:open="modals.edit_book.show"
			width="70%"
			class="upbook-edit-book"
			@close="modals.edit_book.show = false"
		>
			<template #body>
				<div v-if="modals.edit_book.item != null">
					<div class="d-flex">
						<div class="left f-70">
							<p class="mb-2 font-bold">
								Название книги
							</p>
							<input
								v-model="modals.edit_book.item.title"
								type="text"
								placeholder="Название книги..."
								class="form-control mt-2 mb-2"
							>
							<p class="mb-2 font-bold">
								Название автора
							</p>
							<input
								v-model="modals.edit_book.item.author"
								type="text"
								placeholder="Название автора..."
								class="form-control mt-2 mb-2"
							>
							<p class="mb-2 font-bold">
								Описание книги
							</p>
							<textarea
								v-model="modals.edit_book.item.description"
								class="form-control mt-2 mb-2"
								placeholder="Описание..."
							/>
							<p class="mb-2 font-bold">
								Категория
							</p>
							<select
								v-model="modals.edit_book.item.group_id"
								class="form-control mb-2"
							>
								<option
									v-for="cat in categories"
									:key="cat.id"
									:value="cat.id"
								>
									{{ cat.name }}
								</option>
							</select>
						</div>

						<div class="right f-30 pl-4">
							<img
								v-if="modals.edit_book.item.img != ''"
								class="book-img mb-5"
								:src="modals.edit_book.item.img"
							>
							<b-form-file
								ref="edit_img"
								v-model="file_img"
								:state="Boolean(file_img)"
								placeholder="Выберите или перетащите файл сюда..."
								drop-placeholder="Перетащите файл сюда..."
								class="mt-3"
							/>
						</div>
					</div>

					<div
						v-if="modals.edit_book.segments.length > 0"
						class="segments mt-4"
					>
						<div class="row mb-3">
							<div class="col-3">
								<b>Страница книги</b>
							</div>
							<div class="col-9">
								<b>Вопросы</b>
							</div>
						</div>

						<BookSegment
							v-for="(segment, s) in modals.edit_book.segments"
							:key="s"
							:segment="segment"
							:book_id="modals.edit_book.item.id"
							@deleteSegment="deleteSegment(s)"
						/>
					</div>
				</div>
			</template>
			<template
				v-if="modals.edit_book.item != null"
				#footer
			>
				<button
					class="btn btn-success mr-2 rounded"
					@click="saveSegments"
				>
					<span>Сохранить книгу</span>
				</button>
				<button
					class="btn rounded"
					@click="addSegment"
				>
					<span>Добавить тест</span>
				</button>
			</template>
		</SimpleSidebar>

		<!-- Настройки раздела -->
		<SimpleSidebar
			title="Настройки книг"
			:open="showSettings"
			width="400px"
			@close="showSettings = false"
		>
			<template #body>
				<label class="d-flex">
					<input
						v-model="allow_save_book_without_test"
						type="checkbox"
						class="form- mb-2 mr-2"
					>
					<p>Разрешить сохранять книги без тестовых вопросов</p>
				</label>
			</template>

			<template #footer>
				<button
					class="btn btn-primary rounded m-auto"
					@click="save_settings()"
				>
					<span>Сохранить</span>
				</button>
			</template>
		</SimpleSidebar>



		<!-- Переименовать категорию -->
		<b-modal
			v-model="showEditCat"
			title="Переименовать категорию"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				v-model="editcat_name"
				type="text"
				placeholder="Название категории..."
				class="form-control mb-2"
			>
			<button
				class="btn btn-primary rounded m-auto"
				@click="saveCat"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

const UpbooksRead = () => import(/* webpackChunkName: "UpbooksRead" */ '@/pages/UpbooksRead') // книга чтение
import UploadFiles from '@/components/UploadFiles' // загрузка файлов
import BookSegment from '@/components/BookSegment' // загрузка файлов
import SimpleSidebar from '@/components/ui/SimpleSidebar'
export default {
	name: 'PageUpbooks',
	components: {
		UpbooksRead,
		UploadFiles,
		BookSegment,
		SimpleSidebar
	},
	props: {
		token: {
			type: String,
			default: ''
		},
		can_edit: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return {
			selectedCategory: 0,
			activeBook: null,
			editcat_name: '',
			editcat_id: '',
			showEditCat: false,
			activeCategory: null,
			details: null,
			showSettings: false,
			allow_save_book_without_test: false,
			categories: [],
			mode: 'read',
			file_img: null,
			modals: {
				add_category: {
					show: false,
					name: '',
				},
				upload_book: {
					show: false,
					file: null,
				},
				edit_book: {
					show: false,
					item: null,
					segments: [],
				},
			},
		};
	},
	watch: {
		token(){
			this.init()
		},
		watch: {
			'$route.params.id': {
				immediate: true,
				handler(value) {
					if (value) {
						this.fetchData();
					}
				}
			}
		}
	},
	created() {
		if(this.token){
			this.init()
		}
		const bookId = this.$route.params.id;
		if (bookId) {
			this.fetchData()
		}
	},
	methods: {
		init(){
			this.fetchData();
		},
		deleteSegment(i) {
			this.modals.edit_book.segments.splice(i,1);
		},

		selectCategory(index) {
			this.selectedCategory = index;
			this.activeCategory = this.categories[index];
		},

		chooseImage(ref) {
			this.$refs[ref][0].click();
		},

		showDetails(book) {
			this.details = book;
		},

		fetchData() {
			let loader = this.$loading.show();

			this.axios
				.get('/admin/upbooks/get', {})
				.then((response) => {
					this.categories = response.data.categories;
					if (this.categories.length > 0) {
						this.activeCategory = this.categories[0];
					}
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		go(book) {
			this.activeBook = book;
		},

		back() {
			this.activeBook = null;
		},

		editCat(i) {
			this.editcat_id = this.categories[i].id
			this.editcat_name = this.categories[i].name
			this.showEditCat = true
		},

		deleteCat(i) {
			if (confirm('Вы уверены удалить категорию книг?')) {

				let loader = this.$loading.show();

				this.axios
					.post('/admin/upbooks/category/delete', {
						id: this.categories[i].id
					})
					.then(() => {
						this.$toast.success('Категория успешно удалена!');
						this.categories.splice(i,1)
						loader.hide();
					})
					.catch((error) => {
						loader.hide();
						alert(error);
					});
			}
		},

		createCategory() {
			if (this.modals.add_category.name.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/admin/upbooks/category/create', {
					name: this.modals.add_category.name,
				})
				.then((response) => {
					this.modals.add_category.show = false;
					this.modals.add_category.name = '';

					this.categories.push({
						id: response.data.id,
						name: response.data.name,
						books: [],
					});

					this.$toast.success('Категория успешно создана!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		create_book() {},

		onupload(item) {
			this.modals.upload_book.file = item;
			this.modals.upload_book.file.model.group_id = this.activeCategory.id
		},

		deleteBook(i) {
			if (confirm('Вы уверены удалить книгу?')) {

				let loader = this.$loading.show();

				this.axios
					.post('/admin/upbooks/delete', {
						id: this.activeCategory.books[i].id
					})
					.then(() => {
						let c = this.categories.findIndex(i => i.id == this.activeCategory.id);
						this.$toast.success('Книга успешно удалена!');

						if(c != -1) {
							this.categories[c].books.splice(i, 1);
						}

						loader.hide();
					})
					.catch((error) => {
						loader.hide();
						alert(error);
					});
			}


		},

		editBook(book) {
			let loader = this.$loading.show();

			this.modals.edit_book.show = true;
			this.modals.edit_book.item = book;

			this.axios
				.post('/admin/upbooks/segments/get', {
					id: book.id,
				})
				.then((response) => {
					this.modals.edit_book.segments = response.data.segments;
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		saveBook() {
			let loader = this.$loading.show();

			let data = this.modals.upload_book.file.model;
			let formData = new FormData();
			formData.append('id', data.id);
			formData.append('author', data.author);
			formData.append('title',  data.title);
			formData.append('description',  data.description);
			formData.append('group_id',  data.group_id);
			formData.append('file', this.file_img);

			this.axios.post( '/admin/upbooks/save', formData)
				.then((response) => {

					this.modals.upload_book.show = false;
					this.modals.upload_book.file = null;
					data.img  = response.data;


					if(data.group_id != this.activeCategory.id) {
						let i = this.activeCategory.books.findIndex(el => el.id == data.id);
						let j = this.categories.findIndex(el => el.id == data.group_id);

						if(i != -1) {
							this.activeCategory.books.splice(i, 1)
						}

						if(j != -1) {
							this.categories[j].books.push(data);
						}

					} else {
						this.activeCategory.books.push(data);
					}

					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		addSegment() {
			this.modals.edit_book.segments.push({
				id: 0,
				page_start: 1,
				pages: 1,
				questions: [],
			});
		},

		saveSegments() {
			let loader = this.$loading.show();

			let formData = new FormData();
			formData.append('file', this.file_img);
			formData.append('book', JSON.stringify(this.modals.edit_book.item));
			formData.append('segments', JSON.stringify(this.modals.edit_book.segments));
			formData.append('cat_id', this.activeCategory.id);

			this.axios.post( '/admin/upbooks/update', formData, {
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			})
				.then((response) => {
					let b = this.activeCategory.books.findIndex(i => i.id == this.modals.edit_book.item.id);
					let c = this.categories.findIndex(i => i.id == this.activeCategory.id);
					let nc = this.categories.findIndex(i => i.id == this.modals.edit_book.item.group_id);
					if(b != -1 && c != -1 && nc != -1) {
						this.categories[c].books.splice(b, 1);
						this.modals.edit_book.item.img = response.data
						this.categories[nc].books.push(this.modals.edit_book.item);
					}


					this.modals.edit_book.show = false;
					this.modals.edit_book.item = null;
					this.modals.edit_book.segments = [];


					this.$toast.success('Сохранено');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		toggleMode() {
			this.mode = (this.mode == 'read') ? 'edit' : 'read';
		},

		get_settings() {
			this.axios
				.post('/settings/get', {
					type: 'book'
				})
				.then((response) => {
					this.allow_save_book_without_test = response.data.settings.allow_save_book_without_test;
					this.showSettings = true;
				})
				.catch((error) => {
					alert(error);
				});
		},

		save_settings() {
			this.axios
				.post('/settings/save', {
					type: 'book',
					allow_save_book_without_test: this.allow_save_book_without_test,
				})
				.then(() => {
					this.showSettings = false;
				})
				.catch((error) => {
					alert(error);
				});
		},

		saveCat() {
			if (this.editcat_name.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/upbooks/save-cat', {
					title: this.editcat_name,
					id: this.editcat_id,
				})
				.then(() => {


					let i = this.categories.findIndex(el => el.id == this.editcat_id)
					if(i != -1) this.categories[i].name = this.editcat_name

					this.showEditCat = false;
					this.editcat_name = '';

					this.$toast.success('Сохранено!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		imageCrutch(url){
			return url.substring(0, 4) === 'http' ? url : `https://storage.oblako.kz${url}`
		}
	},
};
</script>
