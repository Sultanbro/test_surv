<template>
	<div v-if="auth_user_id">
		<!-- PAGE -->
		<div
			v-if="activeBook === null"
			class="kb-sections d-flex"
		>
			<!-- Левая часть -->
			<aside
				id="left-panel"
				class="lp"
			>
				<div class="form-search-kb">
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
						v-if="search.input.length"
						class="search-clear"
						@click="clearSearch"
					>x</i>
				</div>

				<div
					v-if="activeBook === null"
					class="btn btn-grey mb-3"
					@click="openGlossary"
				>
					<span>Глоссарий</span>
				</div>

				<div
					v-if="showArchive"
					class="btn btn-grey mb-3"
					@click="showArchive = false"
				>
					<i class="fa fa-arrow-left" />
					<span>Выйти из архива</span>
				</div>

				<!-- Существующие разделы -->
				<div
					v-if="!showArchive"
					class="sections-wrap noscrollbar"
					:class="{ 'expand' : mode == 'read'}"
				>
					<div class="search-content">
						<template v-if="search.items.length">
							<div
								v-for="item in search.items"
								:key="item.id"
								class="search-item"
								@click="selectSection(item.book, item.id)"
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
					<Draggable
						v-if="!search.items.length && !search.input.length"
						class="dragArea ml-0"
						tag="div"
						handle=".fa-bars"
						:list="books"
						:id="null"
						:group="{ name: 'g1' }"
						@start="startChangeOrder"
						@end="saveOrder"
					>
						<template v-for="(book, b_index) in books">
							<div
								:key="book.id"
								class="section d-flex aic jcsb"
								:id="book.id"
								@click.stop="selectSection(book)"
							>
								<div class="d-flex aic">
									<i
										v-if="mode == 'edit'"
										class="fa fa-bars mover mr-2"
									/>
									<p>{{ book.title }}</p>
								</div>

								<div
									v-if="mode == 'edit'"
									class="section-btns"
								>
									<i
										class="fa fa-trash mr-1"
										@click.stop="deleteSection(b_index)"
									/>
									<i
										class="fa fa-cog "
										@click.stop="editAccess(book)"
									/>
								</div>
							</div>
						</template>
					</Draggable>
				</div>

				<!-- Архивные разделы -->
				<div
					v-else
					class="sections-wrap noscrollbar"
				>
					<template v-for="(book, b_index) in archived_books">
						<div
							v-if="can_edit"
							:key="b_index"
							class="section d-flex aic jcsb"
							@click.stop="selectSection(book)"
						>
							<p>{{ book.title }}</p>
							<div class="section-btns">
								<i
									class="fa fa-trash-restore mr-1"
									@click.stop="restoreSection(b_index)"
								/>
							</div>
						</div>
					</template>
				</div>

				<!-- Кнопки внизу сайдбара -->
				<div v-if="mode == 'edit'">
					<div
						v-if="!showArchive"
						class="d-flex jscb"
					>
						<div
							v-if="can_edit"
							class="btn btn-grey w-full mr-1"
							@click="showCreate = true"
						>
							<i class="fa fa-plus" />
							<span>Добавить</span>
						</div>
						<div
							v-if="can_edit"
							class="btn btn-grey"
							title="Архив"
							@click="getArchivedBooks"
						>
							<i class="fa fa-box" />
						</div>
					</div>
				</div>
			</aside>

			<!-- Правая часть -->
			<div
				class="rp"
				style="flex: 1 1 0%; padding-bottom: 50px;"
			>
				<div class="hat">
					<div class="d-flex jsutify-content-between hat-top">
						<div class="bc">
							<a href="#">База знаний</a>
							<!---->
						</div>

						<!-- Кнопки на правом верхнем углу -->
						<div class="control-btns d-flex">
							<div
								v-if="can_edit"
								class="mode_changer mr-2"
							>
								<i
									class="fa fa-pen"
									@click="toggleMode"
									:class="{'active': mode == 'edit'}"
									v-b-popover.hover.top="'Включить редактирование Базы знаний'"
								/>
							</div>
							<div
								v-if="can_edit"
								class="mode_changer"
							>
								<i
									class="fa fa-cog"
									@click="get_settings()"
								/>
							</div>
						</div>
					</div>
					<div />
				</div>

				<!-- Глоссарий -->
				<div class="content mt-3">
					<Glossary
						v-if="show_glossary"
						:mode="mode"
					/>
				</div>
			</div>
		</div>

		<!-- PAGE -->
		<div v-if="activeBook">
			<Booklist
				ref="booklist"
				:trees="trees"
				:can_edit="activeBook.access == 2 || can_edit"
				:parent_name="activeBook.title"
				:parent_id="activeBook.id"
				:show_page_id="show_page_id"
				:course_item_id="0"
				@back="back"
				@toggleMode="toggleMode"
				:mode="mode"
				:enable_url_manipulation="true"
				:auth_user_id="auth_user_id"
			/>
		</div>




		<!-- Новый раздел -->
		<b-modal
			v-model="showCreate"
			title="Новый раздел"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				type="text"
				v-model="section_name"
				placeholder="Название раздела..."
				class="form-control mb-2"
			>
			<button
				class="btn btn-primary rounded m-auto"
				@click="addSection"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>


		<!-- Настройки раздела -->
		<SimpleSidebar
			title="Настройки базы знаний"
			:open="showBookSettings"
			@close="showBookSettings = false"
			width="400px"
		>
			<template #body>
				<label class="d-flex mb-2">
					<input
						type="checkbox"
						v-model="send_notification_after_edit"
						class="form- mb-2 mr-2"
					>
					<p>Отправлять уведомления сотрудникам об изменениях в базе знаний</p>
				</label>
				<label class="d-flex mb-2">
					<input
						type="checkbox"
						v-model="show_page_from_kb_everyday"
						class="form- mb-2 mr-2"
					>
					<p>Показывать одну из страниц базы знаний каждый день, после нажатия на кнопку "начать рабочий день"</p>
				</label>
				<label class="d-flex mb-2">
					<input
						type="checkbox"
						v-model="allow_save_kb_without_test"
						class="form- mb-2 mr-2"
					>
					<p>Разрешить вносить изменения без тестовых вопросов в разделах базы знаний</p>
				</label>
			</template>
			<template #footer>
				<button
					class="btn btn-primary rounded m-auto"
					@click="save_settings()"
				>
					Сохранить
				</button>
			</template>
		</SimpleSidebar>

		<!-- Редактирование раздела  -->
		<b-modal
			v-model="showEdit"
			title="Редактирование раздела"
			size="md"
			dialog-class="modallxe"
			hide-footer
		>
			<div v-if="update_book != null">
				<input
					type="text"
					v-model="update_book.title"
					placeholder="Название раздела..."
					class="form-control mb-2"
				>

				<div :key="superselectKey">
					<p class="mb-2">
						Кто может видеть
					</p>
					<SuperSelect
						:values="who_can_read"
						class="w-full mb-4"
						:select_all_btn="true"
					/>
					<p class="mb-2">
						Кто может редактировать
					</p>
					<SuperSelect
						:values="who_can_edit"
						class="w-full mb-4"
						:select_all_btn="true"
					/>
				</div>
				<button
					class="btn btn-primary rounded m-auto"
					@click="updateSection"
				>
					<span>Сохранить</span>
				</button>
			</div>
		</b-modal>
	</div>
</template>

<script>
import Draggable from 'vuedraggable'
import Glossary from '../components/Glossary.vue'
const Booklist = () => import(/* webpackChunkName: "Booklist" */ '@/pages/booklist') // база знаний разде
import SimpleSidebar from '@/components/ui/SimpleSidebar' // сайдбар table
import SuperSelect from '@/components/SuperSelect' // with User ProfileGroup and Position

export default {
	name: 'KBPage',
	components: {
		Draggable,
		Glossary,
		Booklist,
		SimpleSidebar,
		SuperSelect,
	},
	props: {
		auth_user_id: {
			type:Number
		},
		can_edit: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			books: [],
			mode: 'read',
			archived_books: [],
			trees: [],
			settings: null,
			section: 0,
			activeBook: null,
			showCreate: false,
			show_glossary: false,
			send_notification_after_edit: false,
			show_page_from_kb_everyday: false,
			allow_save_kb_without_test: false,
			showBookSettings: false,
			showArchive: false,
			showSearch: false,
			who_can_read: [],
			who_can_edit: [],
			showEdit: false,
			show_page_id: 0,
			superselectKey: 1,
			section_name: '',
			update_book: null,
			search: {
				input: '',
				items: []
			}
		};
	},
	watch: {
		auth_user_id(){
			this.init()
		}
	},

	created() {
		if(this.auth_user_id){
			this.init()
		}
	},

	methods: {
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
		init(){
			this.fetchData();

			// бывор группы
			const urlParams = new URLSearchParams(window.location.search);
			let section = urlParams.get('s');
			if(section) {
				this.selectSection({id: section})
			}
		},
		fetchData() {
			this.axios
				.get('/kb/get', {})
				.then((response) => {
					this.books = response.data.books;
				})
				.catch((error) => {
					alert(error);
				});
		},

		get_settings() {

			this.axios
				.post('/settings/get', {
					type: 'kb'
				})
				.then((response) => {
					this.send_notification_after_edit = response.data.settings.send_notification_after_edit;
					this.show_page_from_kb_everyday = response.data.settings.show_page_from_kb_everyday;
					this.allow_save_kb_without_test = response.data.settings.allow_save_kb_without_test;
					this.showBookSettings = true;
				})
				.catch((error) => {
					alert(error);
				});
		},

		save_settings() {
			this.axios
				.post('/settings/save', {
					type: 'kb',
					send_notification_after_edit: this.send_notification_after_edit,
					show_page_from_kb_everyday: this.show_page_from_kb_everyday,
					allow_save_kb_without_test: this.allow_save_kb_without_test,
				})
				.then(() => {
					this.showBookSettings = false;
				})
				.catch((error) => {
					alert(error);
				});
		},

		selectSection(book, page_id = 0) {
			this.axios
				.post('kb/tree', {
					id: book.id,
				})
				.then((response) => {
					if(response.data.error) {
						this.$toast.info('Раздел не найден');
					}
					this.trees = response.data.trees;
					this.activeBook = response.data.book;
					this.show_page_id = page_id;
					this.showSearch = false;
					this.search.input = '';
					this.search.items = [];
					// change URL
					const urlParams = new URLSearchParams(window.location.search);
					let b = urlParams.get('b');
					let uri = '/kb?s=' + book.id;
					if(b) uri+= '&b=' + b;
					window.history.replaceState({}, 'База знаний', uri);

				})
				.catch((error) => {
					alert(error);
				});
		},

		deleteSection(i) {
			if (confirm('Вы уверены что хотите архивировать раздел?')) {
				this.axios
					.post('/kb/page/delete-section', {
						id: this.books[i].id
					})
					.then(() => {
						this.books.splice(i, 1);
						this.$toast.success('Удалено');
					});
			}
		},

		restoreSection(i) {
			if (confirm('Вы уверены что хотите восстановить раздел?')) {
				this.axios
					.post('/kb/page/restore-section', {
						id: this.archived_books[i].id
					})
					.then(() => {
						this.books.push(this.archived_books[i]);
						this.archived_books.splice(i, 1);
						this.$toast.success('Восстановлен');
					});
			}
		},

		back() {
			this.activeBook = null;
			window.history.replaceState({ id: '100' }, 'База знаний', '/kb');
		},

		searchInput() {
			if(this.search.input.length <= 2) return null;

			this.axios
				.post('kb/search', {
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

		editAccess(book) {
			this.showEdit = true;

			this.update_book = book;
			console.log(book)
			this.axios
				.post('/kb/page/get-access', {
					id: book.id,
				})
				.then((response) => {
					this.who_can_edit = response.data.who_can_edit;
					this.who_can_read = response.data.who_can_read;
					this.superselectKey++;
				})
				.catch((error) => {
					alert(error);
				});
		},

		addSection() {
			if (this.section_name.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/kb/page/add-section', {
					name: this.section_name,
				})
				.then((response) => {
					this.showCreate = false;
					this.section_name = '';

					this.books.push(response.data);

					this.$toast.success('Раздел успешно создан!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		getArchivedBooks() {
			let loader = this.$loading.show();

			this.axios
				.get('/kb/get-archived')
				.then((response) => {

					this.archived_books = response.data.books
					this.showArchive = true
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		updateSection() {
			if (this.update_book.title.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/kb/page/update-section', {
					title: this.update_book.title,
					who_can_read: this.who_can_read,
					who_can_edit: this.who_can_edit,
					id: this.update_book.id,
				})
				.then(() => {
					this.showEdit = false;
					let index = this.books.findIndex(b => b.id == this.update_book.id);

					if(index != -1) {
						this.books[index].title = this.update_book.title;
					}

					this.update_book = null;
					this.who_can_read = [];
					this.who_can_edit = [];

					this.$toast.success('Изменения сохранены!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		saveOrder(event) {
			console.log(event)
			this.axios.post('/kb/page/save-order', {
				id: event.item.id,
				order: event.newIndex, // oldIndex
				parent_id: null
			})
				.then(() => {
					this.$toast.success('Очередь сохранена');
				})
		},


		toggleMode() {
			this.mode = (this.mode == 'read') ? 'edit' : 'read';
			this.clearSearch();
		},

		startChangeOrder(event) {
			console.log(event)
		},

		openGlossary() {
			this.show_glossary = true;
		}

	},
};
</script>

<style lang="scss">
	.form-search-kb{
		position: relative;
		margin-bottom: 10px;
		.fa-search{
			position: absolute;
			top: 10px;
			left: 10px;
			color: #bdcadf;
		}
		input{
			padding: 0 35px !important;
		}
		.search-clear{
			position: absolute;
			top: 8px;
			right: 12px;
			font-style: normal;
			font-size: 16px;
			line-height: 1;
			color: red;
			cursor: pointer;
		}
	}
	.search-content{
		.search-item{
			margin-bottom: 10px;
			border-bottom: 1px solid #ddd;
			padding: 3px 5px 10px 5px;
			font-size: 14px;
			cursor: pointer;
			&:hover{
				background-color: #f2f2f2;
			}
			&-book{
				color: #1272aa;
			}
			&-title{
				font-size: 16px;
				color: #666;
				font-weight: 700;
			}
			&-text{
				font-size: 12px;
				color: #999;
				margin-top: 5px;
			}
			b{
				color: #333;
				background-color: yellow;
			}
		}
	}
</style>
