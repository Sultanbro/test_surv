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
					class="d-flex aic gap-1"
				>
					<div
						class="btn btn-grey btn-block mb-3"
						@click="openGlossary"
					>
						<span>Глоссарий</span>
					</div>

					<div
						v-if="isOwner && mode === 'edit'"
						class="btn btn-grey mb-3 px-3"
						@click="isGlossaryAccessDialog = true"
					>
						<i class="fa fa-cog" />
					</div>
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
						:access="glossaryEditAccess"
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
				:can_edit="!!(activeBook.access == 2 || isAdmin || canEditBook)"
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
				@page-add="fetchData"
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
			no-enforce-focus
		>
			<div v-if="update_book != null">
				<input
					v-model="update_book.title"
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

import Draggable from 'vuedraggable'
import GlossaryComponent from '../components/Glossary.vue'
const Booklist = () => import(/* webpackChunkName: "Booklist" */ '@/pages/booklist') // база знаний разде
import SimpleSidebar from '@/components/ui/SimpleSidebar' // сайдбар table
// import SuperSelect from '@/components/SuperSelect' // with User ProfileGroup and Position
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

export default {
	name: 'KBPage',
	components: {
		Draggable,
		GlossaryComponent,
		Booklist,
		SimpleSidebar,
		// SuperSelect,
		JobtronOverlay,
		AccessSelect,
		AccessSelectFormControl,
	},
	props: {
		auth_user_id: {
			type:Number,
			default: 0
		},
		can_edit: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return {
			mode: 'read',
			books: [],
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

			showEdit: false,
			show_page_id: 0,
			section_name: '',
			update_book: null,

			search: {
				input: '',
				items: [],
				timeout: null,
			},

			show_glossary: false,
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
		}
	},
	watch: {
		auth_user_id(){
			this.init()
		}
	},

	created() {
		if(!this.users.length) this.loadCompany()
		if(this.auth_user_id) this.init()
	},

	methods: {
		...mapActions(['loadCompany']),
		searchCheck() {
			if (this.search.input.length === 0) this.clearSearch()
		},
		clearSearch() {
			clearTimeout(this.search.timeout)
			this.search = {
				input: '',
				items: [],
				timeout: null,
			}
		},
		init(){
			this.fetchData()
			this.fetchGlossary()
			this.fetchGlossaryAccess()

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
				this.$toast.error('Не удалось получить список разделов')
				window.onerror && window.onerror(error)
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
				this.$toast.error('Не удалось получить настройки')
				window.onerror && window.onerror(error)
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
				this.$toast.error('Не удалось сохранить настройки')
				window.onerror && window.onerror(error)
			}
		},

		async selectSection(book, page_id = 0) {
			try {
				this.fetchAccess(book)
				const data = await API.fetchKBBook(book.id)

				if(data.error) return this.$toast.info('Раздел не найден')

				// change URL
				const urlParams = new URLSearchParams(window.location.search)
				const b = urlParams.get('b')
				let uri = '/kb?s=' + book.id
				if(this.search.input) uri += '&hl=' + this.search.input
				if(b || page_id) uri += '&b=' + (b || page_id)
				window.history.replaceState({}, 'База знаний', uri)

				this.trees = data.trees
				this.activeBook = data.book
				this.show_page_id = page_id
				this.showSearch = false
				this.clearSearch()
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить раздел')
				window.onerror && window.onerror(error)
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
				this.$toast.error('Не удалось удалить раздел')
				window.onerror && window.onerror(error)
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
				this.$toast.error('Не удалось восстановить раздел')
				window.onerror && window.onerror(error)
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

		searchInput() {
			clearTimeout(this.search.timeout)
			this.search.timeout = setTimeout(this.runSearch, 500)
		},

		async runSearch(){
			if(this.search.input.length <= 2) return null
			try {
				const data = await API.searchKBBook({
					text: this.search.input,
					id: null
				})
				this.search.items = data.items
				this.emphasizeTexts()
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Поиск не удался')
				window.onerror && window.onerror(error)
			}
		},

		emphasizeTexts() {
			this.search.items.forEach(item => {
				item.text = item.text.replace(new RegExp(this.search.input,'gi'), '<b>' + this.search.input +  '</b>')
			})
		},

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
			this.clearAccess()
			this.showEdit = true
			this.update_book = book
			this.fetchAccess(book)
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
				this.$toast.error('Не создать раздел')
				window.onerror && window.onerror(error)
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
				this.$toast.error('Не удалось получить архивные разделы')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},

		async updateSection(silent) {
			if (this.update_book.title.length <= 2) return this.$toast.error('Слишком короткое название!')
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
					id: this.update_book.id,
					title: this.update_book.title,
					who_can_read: this.whoCanReadActual,
					who_can_edit: this.whoCanEditActual,
					who_can_read_pairs: pairs,
					who_can_edit_pairs: editPairs,
				})

				this.showEdit = false
				const index = this.books.findIndex(b => b.id == this.update_book.id)

				if(index != -1) this.books[index].title = this.update_book.title

				this.update_book = null
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
				this.$toast.error('Не удалось сохранить порядок')
				window.onerror && window.onerror(error)
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
				this.$toast.error('Не удалось созранить изменения')
				window.onerror && window.onerror(error)
			}
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
