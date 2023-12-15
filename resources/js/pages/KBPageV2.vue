<template>
	<div class="KBPageV2">
		<KBNav
			:mode="mode"
			:active-book="activeBook"
			:books="books"
			:pages="pages"
			:favorites="favorites"
			:root-book="rootBook"
			class="KBPageV2-nav"
			@glossary-open="showGlossary = true"
			@glossary-settings="isGlossaryAccessDialog = true"
			@back="back"
			@book="onBook"
			@search="onSearch"
			@page="onPage"
			@add-page="addPage"
			@page-order="savePageOrder"
			@create="onCreate"
			@settings="editAccess"
			@archive="archive"
			@unarchive="unarchive"
		/>
		<section class="KBPageV2-main">
			<KBToolbar
				:mode="mode"
				:active-book="activeBook"
				:breadcrumbs="breadcrumbs"
				:can-edit="!!(parentBook && parentBook.canEdit) || !!(activeBook && activeBook.canEdit) || isAdmin"
				:edit-book="editBook"
				:root-book="rootBook"
				:parent-book="parentBook"
				class="KBPageV2-toolbar"
				@mode="mode = $event"
				@upload-image="isUploadImage = true"
				@upload-audio="isUploadAudio = true"
				@delete-page="onDeletePage"
				@save-page="onSavePage"
				@edit-page="editBook = true"
				@settings="activeBook ? editAccess(activeBook) : getSettings()"
			/>
			<div class="KBPageV2-body">
				<GlossaryComponent
					v-if="showGlossary"
					:mode="mode"
					:terms="glossary"
					:access="glossaryEditAccess"
					@addTerm="addTerm"
					@saveTerm="saveTerm"
					@deleteTerm="deleteTerm"
				/>
				<KBArticle
					v-else-if="activeBook && !isActiveCategory && !editBook"
					:mode="mode"
					:active-book="activeBook"
					:glossary="glossary"
					@favorite="onFavorite"
				/>
				<KBEditor
					v-else-if="activeBook && !isActiveCategory && editBook"
					:active-book="activeBook"
					:upload-image="isUploadImage"
					:upload-audio="isUploadAudio"
					@update="bookForm = $event"
				/>
			</div>
		</section>

		<!-- Новый раздел -->
		<b-modal
			v-model="showCreate"
			title="Новый раздел"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				v-model="sectionName"
				type="text"
				placeholder="Название раздела..."
				class="form-control mb-2"
			>
			<div>
				<p class="mb-2">
					Кто может видеть (чтение)
				</p>
				<AccessSelectFormControl
					:items="whoCanReadActual"
					class="mb-2"
					@click="isReadSelect = true"
				/>
				<b-row>
					<b-col>
						<AccessSelectFormControl
							:items="whoCanReadPosition"
							class="mb-4"
							@click="isReadPositionSelect = true"
						>
							<template #placeholder>
								Укажите должность
								<img
									v-b-popover.hover.right="'Сотрудники с этой должностью будут видеть этот раздел'"
									src="/images/dist/profit-info.svg"
									class="img-info"
								>
							</template>
						</AccessSelectFormControl>
					</b-col>
					<b-col>
						<AccessSelectFormControl
							:items="whoCanReadGroup"
							class="mb-4"
							@click="isReadGroupSelect = true"
						>
							<template #placeholder>
								Укажите отдел
								<img
									v-b-popover.hover.right="'Сотрудники из этого отдела будут видеть этот раздел'"
									src="/images/dist/profit-info.svg"
									class="img-info"
								>
							</template>
						</AccessSelectFormControl>
					</b-col>
				</b-row>
				<p class="mb-2">
					Кто может редактировать
				</p>
				<AccessSelectFormControl
					:items="whoCanEditActual"
					class="mb-2"
					@click="isEditSelect = true"
				/>
				<b-row>
					<b-col>
						<AccessSelectFormControl
							:items="whoCanEditPosition"
							class="mb-4"
							@click="isEditPositionSelect = true"
						>
							<template #placeholder>
								Укажите должность
								<img
									v-b-popover.hover.right="'Сотрудники с этой должностью будут редактировать этот раздел'"
									src="/images/dist/profit-info.svg"
									class="img-info"
								>
							</template>
						</AccessSelectFormControl>
					</b-col>
					<b-col>
						<AccessSelectFormControl
							:items="whoCanEditGroup"
							class="mb-4"
							@click="isEditGroupSelect = true"
						>
							<template #placeholder>
								Укажите отдел
								<img
									v-b-popover.hover.right="'Сотрудники из этого отдела будут редактировать этот раздел'"
									src="/images/dist/profit-info.svg"
									class="img-info"
								>
							</template>
						</AccessSelectFormControl>
					</b-col>
				</b-row>
			</div>
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
					@click="saveSettings()"
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
			no-enforce-focus
		>
			<div v-if="updateBook != null">
				<input
					v-model="updateBook.title"
					type="text"
					placeholder="Название раздела..."
					class="form-control mb-4"
				>

				<div>
					<p class="mb-2">
						Кто может видеть (чтение)
					</p>
					<AccessSelectFormControl
						:items="whoCanReadActual"
						class="mb-2"
						@click="isReadSelect = true"
					/>
					<b-row>
						<b-col>
							<AccessSelectFormControl
								:items="whoCanReadPosition"
								class="mb-4"
								@click="isReadPositionSelect = true"
							>
								<template #placeholder>
									Укажите должность
									<img
										v-b-popover.hover.right="'Сотрудники с этой должностью будут видеть этот раздел'"
										src="/images/dist/profit-info.svg"
										class="img-info"
									>
								</template>
							</AccessSelectFormControl>
						</b-col>
						<b-col>
							<AccessSelectFormControl
								:items="whoCanReadGroup"
								class="mb-4"
								@click="isReadGroupSelect = true"
							>
								<template #placeholder>
									Укажите отдел
									<img
										v-b-popover.hover.right="'Сотрудники из этого отдела будут видеть этот раздел'"
										src="/images/dist/profit-info.svg"
										class="img-info"
									>
								</template>
							</AccessSelectFormControl>
						</b-col>
					</b-row>
					<p class="mb-2">
						Кто может редактировать
					</p>
					<AccessSelectFormControl
						:items="whoCanEditActual"
						class="mb-2"
						@click="isEditSelect = true"
					/>
					<b-row>
						<b-col>
							<AccessSelectFormControl
								:items="whoCanEditPosition"
								class="mb-4"
								@click="isEditPositionSelect = true"
							>
								<template #placeholder>
									Укажите должность
									<img
										v-b-popover.hover.right="'Сотрудники с этой должностью будут редактировать этот раздел'"
										src="/images/dist/profit-info.svg"
										class="img-info"
									>
								</template>
							</AccessSelectFormControl>
						</b-col>
						<b-col>
							<AccessSelectFormControl
								:items="whoCanEditGroup"
								class="mb-4"
								@click="isEditGroupSelect = true"
							>
								<template #placeholder>
									Укажите отдел
									<img
										v-b-popover.hover.right="'Сотрудники из этого отдела будут редактировать этот раздел'"
										src="/images/dist/profit-info.svg"
										class="img-info"
									>
								</template>
							</AccessSelectFormControl>
						</b-col>
					</b-row>
				</div>
				<button
					class="btn btn-primary rounded m-auto"
					@click="updateSection"
				>
					<span>Сохранить</span>
				</button>
			</div>
		</b-modal>

		<b-modal
			v-model="isGlossaryAccessDialog"
			title="Редактирование глоссания"
			size="md"
			dialog-class="modallxe"
			hide-footer
			no-enforce-focus
		>
			<div>
				<p class="mb-2">
					Кто может редактировать
				</p>
				<AccessSelectFormControl
					:items="glossaryEditAccessActual"
					class="mb-4"
					@click="isGlossaryAccess = true"
				/>
			</div>
			<button
				class="btn btn-primary rounded m-auto"
				@click="updateGlossaryAccess"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>

		<JobtronOverlay
			v-if="isReadSelect"
			:z="99999"
			@close="isReadSelect = false"
		>
			<AccessSelect
				v-model="who_can_read"
				:access-dictionaries="accessDictionaries"
				search-position="beforeTabs"
				submit-button=""
				absolute
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isEditSelect"
			:z="99999"
			@close="isEditSelect = false"
		>
			<AccessSelect
				v-model="who_can_edit"
				:access-dictionaries="accessDictionaries"
				search-position="beforeTabs"
				submit-button=""
				absolute
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isReadPositionSelect"
			:z="99999"
			@close="isReadPositionSelect = false"
		>
			<AccessSelect
				v-model="whoCanReadPosition"
				:access-dictionaries="{
					users: [],
					positions: accessDictionaries.positions,
					profile_groups: [],
				}"
				:tabs="['Должности']"
				search-position="beforeTabs"
				submit-button=""
				absolute
				single
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isReadGroupSelect"
			:z="99999"
			@close="isReadGroupSelect = false"
		>
			<AccessSelect
				v-model="whoCanReadGroup"
				:access-dictionaries="{
					users: [],
					positions: [],
					profile_groups: accessDictionaries.profile_groups
				}"
				:tabs="['Отделы']"
				search-position="beforeTabs"
				submit-button=""
				absolute
				single
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isEditPositionSelect"
			:z="99999"
			@close="isEditPositionSelect = false"
		>
			<AccessSelect
				v-model="whoCanEditPosition"
				:access-dictionaries="{
					users: [],
					positions: accessDictionaries.positions,
					profile_groups: [],
				}"
				:tabs="['Должности']"
				search-position="beforeTabs"
				submit-button=""
				absolute
				single
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isEditGroupSelect"
			:z="99999"
			@close="isEditGroupSelect = false"
		>
			<AccessSelect
				v-model="whoCanEditGroup"
				:access-dictionaries="{
					users: [],
					positions: [],
					profile_groups: accessDictionaries.profile_groups
				}"
				:tabs="['Отделы']"
				search-position="beforeTabs"
				submit-button=""
				absolute
				single
			/>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="isGlossaryAccess"
			:z="99999"
			@close="isGlossaryAccess = false"
		>
			<AccessSelect
				v-model="glossaryEditAccess"
				:tabs="['Сотрудники', 'Отделы', 'Должности']"
				:access-dictionaries="accessDictionaries"
				search-position="beforeTabs"
				submit-button=""
				absolute
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import { mapGetters, mapActions } from 'vuex'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'

import KBNav from '@/components/pages/KB/KBNav.vue'
import KBToolbar from '@/components/pages/KB/KBToolbar.vue'
import KBArticle from '@/components/pages/KB/KBArticle.vue'
import KBEditor from '@/components/pages/KB/KBEditor.vue'
import GlossaryComponent from '../components/Glossary.vue'
import SimpleSidebar from '@/components/ui/SimpleSidebar' // сайдбар table
import JobtronOverlay from '@ui/Overlay.vue'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import AccessSelectFormControl from '@ui/AccessSelect/AccessSelectFormControl.vue'

import {
	fetchSettings,
	updateSettings,
} from '@/stores/api.js'

import * as KBAPI from '@/stores/api/kb.js'

const API = {
	fetchSettings,
	updateSettings,
	...KBAPI
}

const types = [
	'all',
	'users',
	'profile_groups',
	'positions',
]

function routerPush(path){
	const currentPath = decodeURIComponent(this.$route.fullPath)
	if(path === currentPath) return
	this.$router.push(path)
}

export default {
	name: 'KBPageV2',
	components: {
		KBNav,
		KBToolbar,
		KBArticle,
		KBEditor,
		GlossaryComponent,
		SimpleSidebar,
		JobtronOverlay,
		AccessSelect,
		AccessSelectFormControl,
	},
	props: {},
	data() {
		return {
			mode: 'read',
			books: [],
			pages: [],
			allBooks: [],
			archived_books: [],
			itemModels: [],

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

			showEdit: false,
			show_page_id: 0,
			sectionName: '',
			updateBook: null,

			search: {
				input: '',
				items: [],
				timeout: null,
			},

			showGlossary: false,
			newGlossaryId: 0,
			glossary: [],
			isGlossaryAccess: false,
			isGlossaryAccessDialog: false,
			glossaryEditAccess: [],

			isReadSelect: false,
			who_can_read: [],

			isEditSelect: false,
			who_can_edit: [],

			isReadPositionSelect: false,
			isReadGroupSelect: false,
			whoCanReadPosition: [],
			whoCanReadGroup: [],

			isEditPositionSelect: false,
			isEditGroupSelect: false,
			whoCanEditPosition: [],
			whoCanEditGroup: [],

			isUploadImage: false,
			isUploadAudio: false,
			editBook: false,
			rootId: null,
			rootBook: null,
			bookForm: null,
			pagesMap: {},
			createParentId: null,
			favorites: [],
		};
	},
	computed: {
		...mapGetters([
			'user',
			'users',
			'accessDictionaries',
		]),
		...mapState(usePortalStore, ['isOwner', 'isAdmin']),
		whoCanReadActual(){
			return this.who_can_read.slice().filter(target => {
				if(types[target.type] === 'all') return true
				return ~this.accessDictionaries[types[target.type]].findIndex(item => item.id === target.id)
			})
		},
		whoCanEditActual(){
			return this.who_can_edit.slice().filter(target => {
				if(types[target.type] === 'all') return true
				return ~this.accessDictionaries[types[target.type]].findIndex(item => item.id === target.id)
			})
		},
		glossaryEditAccessActual(){
			return this.glossaryEditAccess.slice().filter(target => {
				if(types[target.type] === 'all') return true
				return ~this.accessDictionaries[types[target.type]].findIndex(item => item.id === target.id)
			})
		},
		currentUserGroups(){
			return this.accessDictionaries.profile_groups.slice().filter(group => ~group.users?.findIndex(user => user.id === this.user.id))
		},
		canEditBook(){
			if(!this.activeBook) return false
			if(this.isAdmin) return true
			return ~this.whoCanEditActual.findIndex(access => {
				switch(access.type){
				case 1:
					return access.id === this.user?.id
				case 2:
					return ~this.currentUserGroups.findIndex(group => group.id === access.id)
				case 3:
					return access.id === this.user?.position_id
				}
			})
		},
		allBooksMap(){
			const map = {}
			this.allBooks.forEach(book => {
				map[book.id] = book
			})
			return map
		},
		booksMap(){
			const map = {}
			this.getPages(map, this.allBooks)
			return map
		},
		breadcrumbs(){
			if(!this.activeBook) return []
			const breadcrumbs = []
			let currentId = this.activeBook.id
			while(currentId){
				const book = this.booksMap[currentId] || this.pagesMap[currentId]
				if(!book) return breadcrumbs.reverse()
				breadcrumbs.push({
					title: book.title,
					link: `/kb?s=${this.rootId}${book.parent_id ? '&b=' + currentId  : ''}`
				})
				currentId = book.parent_id
			}
			return breadcrumbs.reverse()
		},
		isActiveCategory(){
			if(!this.activeBook) return false
			return !this.activeBook.parent_id || this.activeBook.is_category
		},
		parentBook(){
			if(!this.activeBook) return null
			let book = this.activeBook
			while(book){
				if(book.is_category || !book.parent_id) return book
				book = this.pagesMap[book.parent_id] || this.allBooksMap[book.parent_id]
			}
			return null
		}
	},
	watch: {
		pages: {
			// computed сделать не получилось
			deep: true,
			handler(){
				this.pagesMap = this.pages.reduce((result, page) => {
					result[page.id] = page
					if(page.children && page.children.length) this.getPages(result, page.children)
					return result
				}, {})
			}
		}
	},

	created() {
		if(!this.users.length) this.loadCompany()
		this.init()
	},

	methods: {
		...mapActions(['loadCompany']),
		routerPush,

		/* === HELPERS === */
		async init(){
			this.fetchGlossary()
			this.fetchGlossaryAccess()

			await this.fetchData()
			const bookId = this.$route.query.s
			const pageId = this.$route.query.b

			if(bookId) {
				const book = this.booksMap[+bookId]
				if(!book) {
					this.routerPush('/kb')
					return this.$toast.error('Раздел удален')
				}
				// const top = this.getTopParent(book)
				// this.routerPush(`/kb?s=${bookId}${pageId ? '&b=' + pageId : ''}`)
				this.books = []
				await this.fetchBook(book, true)
				// if(book.id !== top.id) this.setParentsOpened(bookId)
			}
			if(this.rootBook && pageId){
				const page = this.pagesMap[+pageId]
				if(!page) {
					this.routerPush(`/kb?s=${bookId}`)
					return this.$toast.error('Страница удалена')
				}
				this.onPage(page, true)
				this.setParentsOpened(pageId)
			}
		},
		getPages(map, pages){
			pages.map(page => {
				map[page.id] = page
				if(page.children && page.children.length) this.getPages(map, page.children)
			})
		},
		back() {
			if(!this.isAdmin) {
				this.mode = 'read'
			}
			this.activeBook = null
			this.pages = []
			this.books = this.allBooks
			this.rootBook = null
			this.rootId =  null
			this.showGlossary = false
			this.fetchData()
			this.routerPush('/kb')
		},
		setTargetBlank(book){
			const div = document.createElement('div')
			div.innerHTML = book.text
			const links = div.querySelectorAll('a')
			links.forEach(link => link.setAttribute('target', '_blank'))
			book.text = div.innerHTML
			return book
		},
		getTopParent(book){
			const hasParent = book.parent_id && this.booksMap[book.parent_id]
			if(hasParent) return this.getTopParent(hasParent)
			return book
		},
		setParentsOpened(pageId){
			const page = this.pagesMap[pageId]
			if(page) {
				page.opened = true
				this.setParentsOpened(page.parent_id)
			}
		},
		treePluck(books, result = []){
			books.forEach(book => {
				if(book.is_category){
					result.push(book.id)
					if(book.children){
						this.treePluck(book.children, result)
					}
				}
			})
			return result
		},
		/* === HELPERS === */

		/* === SETTINGS === */
		async getSettings() {
			try {
				const {settings} = await API.fetchSettings('kb')
				this.send_notification_after_edit = settings.send_notification_after_edit
				this.show_page_from_kb_everyday = settings.show_page_from_kb_everyday
				this.allow_save_kb_without_test = settings.allow_save_kb_without_test
				this.showBookSettings = true
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить настройки')
				window.onerror && window.onerror(error)
			}
		},

		async saveSettings() {
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
				this.$toast.error('Не удалось сохранить настройки')
				window.onerror && window.onerror(error)
			}
		},
		/* === SETTINGS === */

		/* === BOOKS === */
		async fetchData() {
			// if(this.allBooks.length){
			// 	this.books = this.allBooks
			// 	this.itemModels = []
			// 	this.activeBook = null
			// 	return
			// }

			const loader = this.$loading.show()

			try {
				const { tree, orphans } = await API.fetchKBBooks()
				const { items } = await API.fetchKBFavorites()
				this.favorites = items
				const books = [...tree, ...orphans]
				this.booksAccess(books)
				this.books = books
				this.allBooks = this.books
				this.itemModels = []
				this.activeBook = null
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить список разделов')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},

		onBook(book){
			if(this.mode === 'edit') return
			return this.fetchBook(book)
		},

		async fetchBook(root, init){
			if(!root) return
			const loader = this.$loading.show()
			function setRootRights(root, page){
				page.canEdit = root.canEdit
				page.canRead = root.canRead
				if(page.children) page.children.forEach(child => setRootRights(root, child))
			}

			this.showGlossary = false

			try{
				const {trees, item_models, book, can_save} = await API.fetchKBBook(root.id)

				const ids = this.treePluck(trees)
				if(!ids.includes(root.id)) ids.push(root.id)
				const accessMap = await API.fetchKBAccesses(ids)
				await this.bookAccess(root, accessMap)

				this.books = []
				this.pages = []
				const pages = []

				book.canEdit = root.canEdit
				book.canRead = root.canRead || root.canEdit

				for(const page of trees){
					page.parent_id = +root.id
					if(page.is_category) await this.bookAccess(page, accessMap)
					else setRootRights(root, page)
					pages.push(page)
				}
				this.pages = pages
				this.rootId = root.id
				this.rootBook = root
				this.itemModels = item_models
				this.activeBook = book
				this.canSave = can_save

				if(!init) this.routerPush('/kb?s=' + root.id)
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить список разделов')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},

		async addSection() {
			if (this.sectionName.length <= 2) return this.$toast.error('Слишком короткое название!')

			const loader = this.$loading.show()

			try {
				const book = await API.createKBBook({
					name: this.sectionName,
					parent_id: this.createParentId || null,
				})
				book.canRead = true
				book.canEdit = true
				this.showCreate = false
				this.sectionName = ''

				if(this.createParentId){
					const parent = this.pagesMap[this.createParentId] || this.booksMap[this.createParentId]
					if(parent){
						if(!parent.children) parent.children = []
						parent.children.push(book)
					}
					else{
						this.pages.push(book)
					}
				}
				else{
					this.books.push(book)
					// this.allBooks.push(book)
				}

				this.updateBook = book
				await this.updateSection(true)

				this.createParentId = null

				this.$toast.success('Раздел успешно создан!')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не создать раздел')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},

		async updateSection(silent) {
			if (this.updateBook.title.length <= 2) return this.$toast.error('Слишком короткое название!')
			if(this.whoCanReadGroup.length !== this.whoCanReadPosition.length) return this.$toast.error('Заполните должность-отдел')

			const loader = this.$loading.show()
			const pairs = []
			for(let i = 0, l = this.whoCanReadGroup.length; i < l; ++i){
				pairs.push({
					position_id: this.whoCanReadPosition[i].id,
					group_id: this.whoCanReadGroup[i].id
				})
			}
			const editPairs = []
			for(let i = 0, l = this.whoCanEditGroup.length; i < l; ++i){
				editPairs.push({
					position_id: this.whoCanEditPosition[i].id,
					group_id: this.whoCanEditGroup[i].id
				})
			}

			try {
				await API.updateKBBook({
					id: this.updateBook.id,
					title: this.updateBook.title,
					who_can_read: this.whoCanReadActual,
					who_can_edit: this.whoCanEditActual,
					who_can_read_pairs: pairs,
					who_can_edit_pairs: editPairs,
					parent_id: this.updateBook.parent_id,
				})

				this.showEdit = false
				const index = this.books.findIndex(b => b.id == this.updateBook.id)

				if(index != -1) this.books[index].title = this.updateBook.title

				this.updateBook = null
				this.who_can_read = []
				this.who_can_edit = []
				this.whoCanReadPosition = []
				this.whoCanReadGroup = []
				this.whoCanEditPosition = []
				this.whoCanEditGroup = []

				if(!silent) this.$toast.success('Изменения сохранены')
				loader.hide()
			}
			catch (error) {
				loader.hide()
				console.error(error)
				if(!silent) this.$toast.error('Не удалось созранить изменения')
				window.onerror && window.onerror(error)
			}
		},

		async addPage(parent){
			try {
				const data = await API.addKBPage(parent.id)
				this.addPageHandler(data, parent)
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось создать страницу')
				window.onerror && window.onerror(error)
			}
		},

		addPageHandler(book, parent){
			book.created = this.$moment.utc(book.created_at).local().format('DD.MM.YYYY HH:mm')
			book.edited_at = this.$moment.utc(book.updated_at).local().format('DD.MM.YYYY HH:mm')
			book.editor_avatar = this.$laravel.avatar
			const name = `${this.user.last_name} ${this.user.name}`
			book.author = name
			book.editor = name
			book.canRead = true
			book.canEdit = true
			book.parent_id = parent.id

			if(parent.id === this.rootId){
				this.pages.push(book)
			}
			else{
				if(!parent.children) parent.children = []
				parent.children.push(book)
			}
			this.pages = this.pages.slice() // reactivity issue

			this.$nextTick(() => {
				this.activeBook = book
				this.editBook = true
				parent.opened = true
			})

			this.$toast.info('Добавлена страница')
		},

		onCreate(parent){
			this.clearAccess()
			this.showCreate = true
			this.createParentId = parent?.id || null
		},

		async onPage(page, init){
			const loader = this.$loading.show()
			this.showGlossary = false
			try {
				const {data} = await this.axios.post('/kb/get', {
					id: page.id,
					course_item_id: 0,
					refresh: false
				})
				data.book.isFavorite = page.isFavorite
				if(!page.canEdit) this.mode = 'read'
				this.activeBook = this.setTargetBlank(data.book)
				this.activeBook.canEdit = page.canEdit
				this.activeBook.canRead = page.canRead
				this.editBook = false
				// TODO: clear search
				if(!init) this.routerPush(`/kb?s=${this.rootBook.id}&b=${page.id}`)
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},

		async onSearch(page, search){
			if(!this.root || this.root.id !== page.book.id){
				await this.fetchBook(page.book)
			}
			this.$nextTick(async () => {
				await this.onPage(page, true)
				this.routerPush(`/kb?s=${this.rootBook.id}&b=${page.id}${search ? '&hl=' + search : ''}`)
			})
		},

		async onSavePage(){
			if(!this.bookForm.questions.length && !this.canSave) return this.$toast.error('Нельзя вносить изменения без тестов')

			const loader = this.$loading.show()
			try {
				this.axios.post('/kb/page/update', {
					id: this.bookForm.id,
					title: this.bookForm.title,
					text: this.bookForm.text,
					pass_grade: this.bookForm.pass_grade,
				})
				this.editBook = false
				this.pagesMap[this.bookForm.id].title = this.bookForm.title
				this.activeBook = this.bookForm
				this.activeBook.editor_id = this.user.id
				this.activeBook.editor = `${this.user.last_name} ${this.user.name}`
				this.activeBook.editor_avatar = `users_img/${this.user.img_url}`
				this.activeBook.edited_at = this.$moment().format('DD.MM.YYYY HH:mm')
				this.$toast.info('Сохранено')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось сохранить страницу')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},
		async onDeletePage(){
			if(!confirm('Вы уверены?')) return

			const id = this.activeBook.id
			const parent = this.rootBook ? this.pagesMap[this.activeBook.parent_id] :this.booksMap[this.activeBook.parent_id]
			try {
				await this.axios.post('/kb/page/delete', { id })
				if(parent){
					const index = parent.children.findIndex(page => page.id === id)
					if(~index) parent.children.splice(index, 1)
				}
				else{
					const index = this.pages.findIndex(page => page.id === id)
					if(~index) this.pages.splice(index, 1)
				}
				this.pages = this.pages.slice()
				this.activeBook = this.allBooksMap[this.rootId]
				this.$toast.success('Удалено')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось удалить страницу')
				window.onerror && window.onerror(error)
			}
		},
		archive(book){
			const parent = this.pagesMap[book.parent_id] || this.booksMap[book.parent_id]

			if(parent){
				const index = parent.children.findIndex(children => children.id === book.id)
				if(~index) parent.children.splice(index, 1)
			}
			else if(this.pages.length){
				const index = this.pages.findIndex(children => children.id === book.id)
				if(~index) this.pages.splice(index, 1)
			}
			else{
				const index = this.allBooks.findIndex(children => children.id === book.id)
				if(~index) this.allBooks.splice(index, 1)
				const index2 = this.books.findIndex(children => children.id === book.id)
				if(~index2) this.books.splice(index2, 1)
			}

			this.pages = this.pages.slice()
			this.books = this.books.slice()
		},
		unarchive(book){
			const parent = this.booksMap[book.parent_id]
			if(!parent) return
			parent.children.push(book)
		},
		async onFavorite(page){
			try {
				await API.toggleKBPageFavorite(page.id, {toggle: !page.isFavorite})
				page.isFavorite = !page.isFavorite
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось добавить в избранное')
				window.onerror && window.onerror(error)
			}
		},
		async savePageOrder({item, to, newIndex}){
			const id = +item.getAttribute('data-id')
			const parentId = +to.getAttribute('data-id')
			try {
				await API.updateKBOrder({
					id,
					order: newIndex,
					parent_id: parentId || null,
				})
				this[this.rootBook ? 'updatePageOrder' : 'updateBookOrder'](id, parentId, newIndex)

				this.$nextTick(() => {
					this.$forceUpdate()
					this.pages = this.pages.slice()
					this.books = this.books.slice()
					this.allBooks = this.allBooks.slice()
				})
				this.$toast.success('Очередь сохранена')
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				this.$toast.error('Не удалось сохранить очередь')
			}
		},
		updateBookOrder(id, parentId, newIndex){
			const book = this.booksMap[id]
			const prevParent = this.booksMap[book.parent_id]
			const parent = this.booksMap[parentId]

			if(prevParent){
				const index = prevParent.children.findIndex(children => children.id === id)
				if(~index) prevParent.children.splice(index, 1)
			}
			else{
				const index = this.books.findIndex(p => p.id === id)
				if(~index) this.books.splice(index, 1)
			}

			if(parent){
				if(!parent.children) parent.children = []
				parent.children.splice(newIndex, 0, book)
			}
			else{
				this.books.splice(newIndex, 0, book)
			}
			book.parent_id = parentId
		},
		updatePageOrder(id, parentId, newIndex){
			const page = this.pagesMap[id]
			const prevParent = this.pagesMap[page.parent_id]
			const parent = this.pagesMap[parentId]

			if(prevParent){
				const index = prevParent.children.findIndex(children => children.id === id)
				if(~index) prevParent.children.splice(index, 1)
			}
			else{
				const index = this.pages.findIndex(p => p.id === id)
				if(~index) this.pages.splice(index, 1)
			}

			if(parent){
				if(!parent.children) parent.children = []
				parent.children.splice(newIndex, 0, page)
			}
			else{
				this.pages.splice(newIndex, 0, page)
			}
			page.parent_id = parentId
		},
		/* === BOOKS === */

		/* === ACCESS === */
		async fetchAccess(book){
			try {
				const {
					whoCanEdit,
					whoCanRead,
					whoCanReadPairs,
					whoCanEditPairs,
				} = await API.fetchKBAccess(book.id)
				this.who_can_edit = whoCanEdit
				this.who_can_read = whoCanRead
				this.parseAccessPairs(whoCanReadPairs)
				this.parseEditPairs(whoCanEditPairs)
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить доступы')
				window.onerror && window.onerror(error)
			}
		},

		async editAccess(book) {
			const loader = this.$loading.show()
			this.clearAccess()
			await this.fetchAccess(book)
			this.updateBook = book
			this.showEdit = true
			loader.hide()
		},

		clearAccess(){
			this.who_can_read = []
			this.who_can_edit = []
			this.whoCanReadPosition = []
			this.whoCanReadGroup = []
			this.whoCanEditPosition = []
			this.whoCanEditGroup = []
		},

		parseAccessPairs(pairs){
			if(!pairs || !pairs.length) {
				this.whoCanReadPosition = []
				this.whoCanReadGroup = []
				return
			}

			const position = this.accessDictionaries.positions.find(pos => pos.id === pairs[0].position_id)
			const group = this.accessDictionaries.profile_groups.find(group => group.id === pairs[0].group_id)

			if(!position || !group){
				this.whoCanReadPosition = []
				this.whoCanReadGroup = []
				return
			}

			this.whoCanReadPosition = [{
				id: position.id,
				name: position.name,
				type: 3
			}]
			this.whoCanReadGroup = [{
				id: group.id,
				name: group.name,
				type: 2
			}]
		},

		parseEditPairs(pairs){
			if(!pairs || !pairs.length) {
				this.whoCanEditPosition = []
				this.whoCanEditGroup = []
				return
			}

			const position = this.accessDictionaries.positions.find(pos => pos.id === pairs[0].position_id)
			const group = this.accessDictionaries.profile_groups.find(group => group.id === pairs[0].group_id)

			if(!position || !group){
				this.whoCanEditPosition = []
				this.whoCanEditGroup = []
				return
			}

			this.whoCanEditPosition = [{
				id: position.id,
				name: position.name,
				type: 3
			}]
			this.whoCanEditGroup = [{
				id: group.id,
				name: group.name,
				type: 2
			}]
		},

		pageAccess(page, canRead, canEdit){
			page.canRead = canRead
			page.canEdit = canEdit
			if(page.children){
				page.children.forEach(child => {
					this.pageAccess(child, canRead, canEdit)
				})
			}
		},
		async bookAccess(book, accessMap){
			if(!accessMap){
				const ids = this.treePluck([book])
				accessMap = await API.fetchKBAccesses(ids)
			}

			const {
				whoCanEdit,
				whoCanRead,
				whoCanReadPairs,
				whoCanEditPairs,
			} = accessMap[book.id]

			const canRead = ~whoCanRead.findIndex(access => {
				switch(access.type){
				case 0:
					return true
				case 1:
					return access.id === this.user?.id
				case 2:
					return ~this.currentUserGroups.findIndex(group => group.id === access.id)
				case 3:
					return access.id === this.user?.position_id
				}
			})
			const canEdit = ~whoCanEdit.findIndex(access => {
				switch(access.type){
				case 1:
					return access.id === this.user?.id
				case 2:
					return ~this.currentUserGroups.findIndex(group => group.id === access.id)
				case 3:
					return access.id === this.user?.position_id
				}
			})
			const canReadPair = ~whoCanReadPairs.findIndex(pair => {
				const inGroup = ~this.currentUserGroups.findIndex(group => group.id === pair.group_id)
				return inGroup && (pair.position_id === this.user?.position_id)
			})

			const canEditPair = ~whoCanEditPairs.findIndex(pair => {
				const inGroup = ~this.currentUserGroups.findIndex(group => group.id === pair.group_id)
				return inGroup && (pair.position_id === this.user?.position_id)
			})

			/* eslint-disable require-atomic-updates */
			book.canRead = this.isAdmin || canRead || canEdit || canReadPair || canEditPair || (!whoCanRead.length && !whoCanReadPairs.length)
			book.canEdit = this.isAdmin || canEdit || canEditPair
			/* eslint-enable require-atomic-updates */

			if(book.children && book.children.length){
				for(const child of book.children){
					if(!child.is_category) {
						this.pageAccess(child, book.canRead, book.canEdit)
						continue
					}
					this.bookAccess(child, accessMap)
				}
			}
		},
		booksAccess(books){
			books.forEach(book => {
				book.canEdit = this.$can('books_edit')
				if(book.children) this.booksAccess(book.children)
			})
		},
		/* === ACCESS === */

		/* === GLOSSARY === */
		async fetchGlossary(){
			try {
				this.glossary = await API.fetchGlossary()
			}
			catch (error) {
				console.error(error)
				this.$toast.success('Не удалось загрузить глоссарий')
				window.onerror && window.onerror(error)
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
				this.$toast.success('Не удалось сохранить термин')
				window.onerror && window.onerror(error)
			}
		},
		async deleteTerm(deleteTerm){
			if (!confirm('Вы уверены что хотите удалить термин?')) return
			const index = this.glossary.findIndex(term => term.id === deleteTerm.id)
			if(~index) this.glossary.splice(index, 1)
			if(deleteTerm.id > 0) await API.deleteGlossaryTerm(deleteTerm.id)
			this.$toast.success('Термин удален')
		},
		async fetchGlossaryAccess(){
			try {
				this.glossaryEditAccess = await API.fetchGlossaryAccess()
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
			}
		},
		async updateGlossaryAccess(){
			try {
				await API.updateGlossaryAccess(this.glossaryEditAccess)
				this.isGlossaryAccessDialog = false
				this.$toast.success('Изменения сохранены')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось сохранить изменения')
				window.onerror && window.onerror(error)
			}
		},
		/* === GLOSSARY === */
	},
};
</script>

<style lang="scss">
.KBPageV2{
	display: flex;
	align-items: stretch;

	height: 100vh;

	&-nav{
		width: 290px;
		flex: 0 0 290px;
	}
	&-main{
		flex: 1;
		display: flex;
		flex-flow: column nowrap;

		position: relative;
	}
	&-toolbar{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;

		min-height: 35px;
		padding: 5px 15px;
		border-bottom: 1px solid #dfdfdf;

		background-color: #f8f8f8;
	}
	&-body{
		flex: 1;
		overflow-x: hidden;
		overflow-y: auto;
	}
}
</style>
