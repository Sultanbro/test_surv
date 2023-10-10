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
					<Draggable
						v-if="!search.items.length && !search.input.length"
						:id="null"
						class="dragArea ml-0"
						tag="div"
						handle=".fa-bars"
						:list="books"
						:group="{ name: 'g1' }"
						@start="startChangeOrder"
						@end="saveOrder"
					>
						<template v-for="(book, b_index) in books">
							<div
								:id="book.id"
								:key="book.id"
								class="section d-flex aic jcsb"
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
									v-b-popover.hover.top="'Включить редактирование Базы знаний'"
									class="fa fa-pen"
									:class="{'active': mode == 'edit'}"
									@click="toggleMode"
								/>
							</div>
							<div
								v-if="can_edit"
								class="mode_changer"
							>
								<i
									class="icon-nd-settings"
									@click="get_settings()"
								/>
							</div>
						</div>
					</div>
					<div />
				</div>

				<!-- Глоссарий -->
				<div class="content mt-3">
					<GlossaryComponent
						v-if="show_glossary"
						:mode="mode"
						:terms="glossary"
						@addTerm="addTerm"
						@saveTerm="saveTerm"
						@deleteTerm="deleteTerm"
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
				:mode="mode"
				:enable_url_manipulation="true"
				:auth_user_id="auth_user_id"
				:glossary="glossary"
				@back="back"
				@toggleMode="toggleMode"
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
				v-model="section_name"
				type="text"
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
			width="400px"
			@close="showBookSettings = false"
		>
			<template #body>
				<label class="d-flex mb-2">
					<input
						v-model="send_notification_after_edit"
						type="checkbox"
						class="form- mb-2 mr-2"
					>
					<p>Отправлять уведомления сотрудникам об изменениях в базе знаний</p>
				</label>
				<label class="d-flex mb-2">
					<input
						v-model="show_page_from_kb_everyday"
						type="checkbox"
						class="form- mb-2 mr-2"
					>
					<p>Показывать одну из страниц базы знаний каждый день, после нажатия на кнопку "начать рабочий день"</p>
				</label>
				<label class="d-flex mb-2">
					<input
						v-model="allow_save_kb_without_test"
						type="checkbox"
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
					v-model="update_book.title"
					type="text"
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
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import Draggable from 'vuedraggable'
import GlossaryComponent from '../components/Glossary.vue'
const Booklist = () => import(/* webpackChunkName: "Booklist" */ '@/pages/booklist') // база знаний разде
import SimpleSidebar from '@/components/ui/SimpleSidebar' // сайдбар table
import SuperSelect from '@/components/SuperSelect' // with User ProfileGroup and Position

import {
	fetchSettings,
	updateSettings,
	fetchKBBooks,
	fetchKBBook,
	deleteKBBook,
	restoreKBBook,
	searchKBBook,
	fetchKBAccess,
	createKBBook,
	fetchKBArchived,
	updateKBBook,
	updateKBOrder,
	fetchGlossary,
	saveGlossaryTerm,
	deleteGlossaryTerm,
} from '@/stores/api.js'

const API = {
	fetchSettings,
	updateSettings,
	fetchKBBooks,
	fetchKBBook,
	deleteKBBook,
	restoreKBBook,
	searchKBBook,
	fetchKBAccess,
	createKBBook,
	fetchKBArchived,
	updateKBBook,
	updateKBOrder,
	fetchGlossary,
	saveGlossaryTerm,
	deleteGlossaryTerm,
}

export default {
	name: 'KBPage',
	components: {
		Draggable,
		GlossaryComponent,
		Booklist,
		SimpleSidebar,
		SuperSelect,
	},
	props: {
		auth_user_id: {
			type:Number,
			default: 0
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
			},

			show_glossary: false,
			newGlossaryId: 0,
			glossary: [],
		};
	},
	watch: {
		auth_user_id(){
			this.init()
		}
	},

	created() {
		if(this.auth_user_id) this.init()
	},

	methods: {
		searchCheck() {
			if (this.search.input.length === 0) this.clearSearch()
		},
		clearSearch() {
			this.search = {
				input: '',
				items: []
			}
		},
		init(){
			this.fetchData()
			this.fetchGlossary()

			const urlParams = new URLSearchParams(window.location.search)
			// const search = urlParams.get('search')
			// if(search){
			// 	this.search.input = search
			// }

			// бывор группы
			const section = urlParams.get('s')
			if(section) this.selectSection({id: section})
		},
		async fetchData() {
			try {
				this.books = await API.fetchKBBooks()
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось получить список разделов')
			}
		},

		async get_settings() {
			try {
				const {settings} = await API.fetchSettings('kb')
				this.send_notification_after_edit = settings.send_notification_after_edit
				this.show_page_from_kb_everyday = settings.show_page_from_kb_everyday
				this.allow_save_kb_without_test = settings.allow_save_kb_without_test
				this.showBookSettings = true
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось получить настройки')
			}
		},

		async save_settings() {
			try {
				await API.updateSettings({
					type: 'kb',
					send_notification_after_edit: this.send_notification_after_edit,
					show_page_from_kb_everyday: this.show_page_from_kb_everyday,
					allow_save_kb_without_test: this.allow_save_kb_without_test,
				})
				this.showBookSettings = false
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось сохранить настройки')
			}
		},

		async selectSection(book, page_id = 0) {
			try {
				const data = await API.fetchKBBook(book.id)

				if(data.error) return this.$toast.info('Раздел не найден')

				this.trees = data.trees
				this.activeBook = data.book
				this.show_page_id = page_id
				this.showSearch = false
				this.search.input = ''
				this.search.items = []

				// change URL
				const urlParams = new URLSearchParams(window.location.search)
				const b = urlParams.get('b')
				let uri = '/kb?s=' + book.id
				if(b) uri += '&b=' + b
				window.history.replaceState({}, 'База знаний', uri)
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось получить раздел')
			}
		},

		async deleteSection(i) {
			if (!confirm('Вы уверены что хотите архивировать раздел?')) return
			try {
				await API.deleteKBBook(this.books[i].id)
				this.books.splice(i, 1)
				this.$toast.success('Раздел удален')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось удалить раздел')
			}
		},

		async restoreSection(i) {
			if (!confirm('Вы уверены что хотите восстановить раздел?')) return
			try {
				await API.restoreKBBook(this.archived_books[i].id)
				this.books.push(this.archived_books[i])
				this.archived_books.splice(i, 1)
				this.$toast.success('Раздел восстановлен')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось восстановить раздел')
			}
		},

		back() {
			if(!this.can_edit) {
				this.mode = 'read'
				this.clearSearch()
			}
			this.activeBook = null
			window.history.replaceState({ id: '100' }, 'База знаний', '/kb')
		},

		async searchInput() {
			if(this.search.input.length <= 2) return null
			try {
				const data = await API.searchKBBook(this.search.input)
				this.search.items = data.items
				this.emphasizeTexts()
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Поиск не удался')
			}
		},

		emphasizeTexts() {
			this.search.items.forEach(item => {
				item.text = item.text.replace(new RegExp(this.search.input,'gi'), '<b>' + this.search.input +  '</b>')
			})
		},

		async editAccess(book) {
			this.showEdit = true
			this.update_book = book

			try {
				const {who_can_edit, who_can_read} = await API.fetchKBAccess(book.id)
				this.who_can_edit = who_can_edit
				this.who_can_read = who_can_read
				this.superselectKey++
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось получить доступы')
			}
		},

		async addSection() {
			if (this.section_name.length <= 2) return this.$toast.error('Слишком короткое название!')

			const loader = this.$loading.show()

			try {
				const book = await API.createKBBook(this.section_name)
				this.showCreate = false
				this.section_name = ''

				this.books.push(book)

				this.$toast.success('Раздел успешно создан!')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не создать раздел')
			}
			loader.hide()
		},

		async getArchivedBooks() {
			const loader = this.$loading.show()

			try {
				const books = await API.fetchKBArchived()
				this.archived_books = books
				this.showArchive = true
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось получить архивные разделы')
			}
			loader.hide()
		},

		async updateSection() {
			if (this.update_book.title.length <= 2) return this.$toast.error('Слишком короткое название!')

			const loader = this.$loading.show()

			try {
				await API.updateKBBook({
					title: this.update_book.title,
					who_can_read: this.who_can_read,
					who_can_edit: this.who_can_edit,
					id: this.update_book.id,
				})

				this.showEdit = false
				const index = this.books.findIndex(b => b.id == this.update_book.id)

				if(index != -1) this.books[index].title = this.update_book.title

				this.update_book = null
				this.who_can_read = []
				this.who_can_edit = []

				this.$toast.success('Изменения сохранены!')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось созранить изменения')
			}

			loader.hide()
		},

		async saveOrder(event) {
			try {
				await API.updateKBOrder({
					id: event.item.id,
					order: event.newIndex,
					parent_id: null
				})
				this.$toast.success('Очередь сохранена')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось сохранить порядок')
			}
		},


		toggleMode() {
			this.mode = (this.mode == 'read') ? 'edit' : 'read'
			this.clearSearch()
		},

		startChangeOrder() {},

		openGlossary() {
			this.show_glossary = true
		},
		async fetchGlossary(){
			try {
				this.glossary = await API.fetchGlossary()
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.success('Не удалось загрузить глоссарий')
			}
		},
		addTerm(){
			this.glossary.unshift({
				id: --this.newGlossaryId,
				word: '',
				definition: '',
			})
		},
		async saveTerm(saveTerm){
			try {
				const id = await API.saveGlossaryTerm({
					...saveTerm,
					id: saveTerm.id < 0 ? 0 : saveTerm.id,
				})

				const term = this.glossary.find(term => term.id === saveTerm.id)
				if(term) term.id = id
				this.$toast.success('Термин сохранен')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.success('Не удалось сохранить термин')
			}
		},
		async deleteTerm(deleteTerm){
			if (!confirm('Вы уверены что хотите удалить термин?')) return
			const index = this.glossary.findIndex(term => term.id === deleteTerm.id)
			if(~index) this.glossary.splice(index, 1)
			if(deleteTerm.id > 0) await API.deleteGlossaryTerm(deleteTerm.id)
			this.$toast.success('Термин удален')
		},
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
