<template>
	<aside class="KBNav">
		<div
			class="KBNav-search"
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
			v-if="!currentBook"
			class="KBNav-glossary"
		>
			<div
				class="btn btn-grey btn-block mb-3"
				@click="$emit('glossary-open')"
			>
				<span>Глоссарий</span>
			</div>

			<div
				v-if="isOwner && mode === 'edit'"
				class="btn btn-grey mb-3 px-3"
				@click="$emit('glossary-settings')"
			>
				<i class="fa fa-cog" />
			</div>
		</div>

		<!-- Back buttons -->
		<div
			v-if="archived.show || currentBook"
			class="KBNav-back"
		>
			<div
				v-if="archived.show"
				class="btn btn-grey btn-block mb-3"
				@click="archived.show = false"
			>
				<i class="fa fa-arrow-left" />
				<span>Выйти из архива</span>
			</div>
			<div
				v-if="currentBook"
				class="btn btn-grey btn-block mb-3"
				@click="onBack"
			>
				<i class="fa fa-arrow-left" />
				<span>Вернуться к разделам</span>
			</div>
		</div>

		<!-- Search -->
		<div
			v-if="search.input.length"
			class="KBNav-searchResults"
		>
			<div
				v-if="search.input.length < 3"
				class="text-muted"
			>
				Введите минимум 3 символа
			</div>
			<div
				v-else-if="search.loading"
				class="text-muted"
			>
				Загрузка...
			</div>
			<div
				v-else-if="!search.items.length"
				class="text-muted"
			>
				Ничего не найдено
			</div>
			<div class="KBNav-searchItems">
				<div
					v-for="item in search.items"
					:key="item.id"
					class="KBNav-searchItem"
					@click="$emit('search', item, search.input)"
				>
					<p
						v-if="item.book"
						class="KBNav-searchBook"
					>
						{{ item.book.title }}
					</p>
					<p class="KBNav-searchTitle">
						{{ item.title }}
					</p>
					<!-- eslint-disable vue/no-v-html -->
					<div
						class="KBNav-searchText"
						v-html="item.text"
					/>
					<!-- eslint-enable vue/no-v-html -->
				</div>
			</div>
		</div>

		<!-- Archive -->
		<div
			v-else-if="archived.show"
			class="KBNav-archive"
		>
			<div
				v-for="(book, bIndex) in archived.items"
				:key="bIndex"
				class="KBNav-item"
				:title="book.title"
			>
				<p class="KBNav-itemText">
					{{ book.title }}
				</p>
				<div class="KBNav-itemActions">
					<i
						class="KBNav-itemAction fa fa-trash-restore mr-1"
						@click.stop="archiveRestore(book)"
					/>
				</div>
			</div>
		</div>

		<!-- Nav items -->
		<div
			v-else
			class="KBNav-items"
		>
			<div
				v-if="currentBook"
				class="KBNav-currentTitle"
			>
				{{ currentBook.title }}
				<div
					v-if="mode == 'edit' && currentBook.canEdit"
					class="KBNav-itemActions"
				>
					<i
						class="KBNav-itemAction fa fa-plus mr-1"
						@click="$emit('add-page', currentBook)"
					/>
				</div>
			</div>
			<template v-else-if="favorites.length">
				<div class="KBNav-favorites">
					<div
						v-for="item in favorites"
						:key="item.id"
						class="KBNav-favorite"
						@click="$emit('search', item, '')"
					>
						<i class="fas fa-heart" />
						{{ item.title }}
					</div>
					<hr>
				</div>
			</template>
			<KBNavItems
				v-if="currentBook"
				:key="'p' + listsKey"
				:items="books"
				:opened="true"
				:mode="mode"
				:parent="currentBook"
				:active="activeBook ? activeBook.id : null"
				@show-page="$emit('page', $event)"
				@add-page="$emit('add-page', $event)"
				@page-order="$emit('page-order', $event)"
				@add-book="$emit('create', $event)"
				@remove-book="archiveBook($event)"
				@settings="$emit('settings', $event)"
			/>
			<KBNavItems
				v-else-if="books.length"
				:key="'b' + listsKey"
				:items="books"
				:opened="true"
				:mode="mode"
				:parent="null"
				:active="null"
				:sections-mode="true"
				@show-page="$emit('book', $event)"
				@page-order="$emit('page-order', $event)"
				@add-book="$emit('create', $event)"
				@remove-book="archiveBook($event)"
				@settings="$emit('settings', $event)"
			/>
		</div>

		<div v-if="mode === 'edit' && !currentBook">
			<div
				v-if="!archived.show"
				class="d-flex jscb mt-3"
			>
				<div
					class="btn btn-grey w-full mr-1"
					@click="$emit('create')"
				>
					<i class="fa fa-plus" />
					<span>Добавить</span>
				</div>
				<div
					class="btn btn-grey"
					title="Архив"
					@click="getArchivedBooks"
				>
					<i class="fa fa-box" />
				</div>
			</div>
		</div>
		<div v-if="mode === 'edit' && currentBook">
			<div
				v-if="!archived.show"
				class="d-flex jscb mt-3 gap-1"
			>
				<div
					class="btn btn-grey w-full mr-1"
					@click="$emit('add-page', currentBook)"
				>
					<i class="fa fa-plus" />
					<span>Страница</span>
				</div>
				<div
					v-if="$laravel.is_admin"
					class="btn btn-grey w-full mr-1"
					@click="$emit('create', currentBook)"
				>
					<i class="fa fa-plus" />
					<span>База</span>
					<img
						v-b-popover.hover.right="'Вы можете создать дополнительную базу знаний с отдельными доступами'"
						src="/images/dist/profit-info.svg"
						class="img-info ml-3"
						alt="info icon"
						width="18"
					>
				</div>
			</div>
		</div>
	</aside>
</template>

<script>
import {
	fetchSettings,
	updateSettings,
} from '@/stores/api.js'
import * as KBAPI from '@/stores/api/kb.js'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'

import KBNavItems from './KBNavItems.vue'

const API = {
	fetchSettings,
	updateSettings,
	...KBAPI
}

export default {
	name: 'KBNav',
	components: {
		KBNavItems,
	},
	props: {
		mode: {
			type: String,
			default: 'read',
		},
		activeBook: {
			type: Object,
			default: null
		},
		books: {
			type: Array,
			default: () => []
		},
		favorites: {
			type: Array,
			default: () => []
		},
		currentBook: {
			type: Object,
			default: null
		},
	},
	data(){
		return {
			search: {
				input: '',
				items: [],
				timepout: null,
				loading: false,
			},
			archived: {
				show: false,
				items: [],
			},
			listsKey: 1,
		}
	},
	computed: {
		...mapState(usePortalStore, ['isOwner', 'isAdmin']),
		pagesMap(){
			const map = {}
			this.getPages(map, this.pages)
			return map
		},
	},
	watch: {
		pages(){
			this.updateKeys()
		},
		books(){
			this.updateKeys()
		},
	},
	mounted(){},
	methods: {
		updateKeys(){
			++this.listsKey
		},
		onBack(){
			this.$emit('back')
			this.clearSearch()
		},
		getPages(map, pages){
			pages.map(page => {
				map[page.id] = page
				if(page.children && page.children.length) this.getPages(map, page.children)
			})
		},

		// === SEARCH ===
		searchInput() {
			clearTimeout(this.search.timeout)
			this.search.timeout = setTimeout(this.runSearch, 500)
		},
		searchCheck() {
			if (this.search.input.length === 0) this.clearSearch()
		},
		clearSearch() {
			clearTimeout(this.search.timeout)
			this.search = {
				input: '',
				items: [],
				timeout: null,
				loading: false,
			}
		},
		async runSearch(){
			if(this.search.input.length <= 2) return null
			try {
				const data = await API.searchKBBook({
					text: this.search.input,
					id: this.currentBook?.id || null
				})
				this.search.items = data.items.map(item => {
					const regExp = new RegExp(this.search.input,'gi')
					item.text = item.text.replace(regExp, '<span class="KBNav-searchTerm">' + this.search.input +  '</span>')
					return item
				})
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Поиск не удался')
				window.onerror && window.onerror(error)
			}
		},
		// === SEARCH ===

		// === ARCHIVE ===
		async archiveRestore(book){
			if (!confirm('Вы уверены что хотите восстановить раздел?')) return
			try {
				await API.restoreKBBook(book.id)
				const index = this.archived.items.findIndex(archived => archived.id === book.id)
				this.archived.items.splice(index, 1)
				this.$emit('unarchive', book)
				this.$toast.success('Раздел восстановлен')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось восстановить раздел')
				window.onerror && window.onerror(error)
			}
		},
		async archiveBook(book){
			if (!confirm('Вы уверены что хотите архивировать раздел?')) return
			try {
				await API.deleteKBBook(book.id)
				this.$emit('archive', book)
				this.$toast.success('Раздел удален')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось удалить раздел')
				window.onerror && window.onerror(error)
			}
		},
		async getArchivedBooks(){
			const loader = this.$loading.show()

			try {
				const books = await API.fetchKBArchived()
				this.archived.items = books
				this.archived.show = true
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось получить архивные разделы')
				window.onerror && window.onerror(error)
			}
			loader.hide()
		},
		// === ARCHIVE ===

		// === ORDER ===
		async saveBookOrder(event){
			/* eslint-disable camelcase */
			try {
				await API.updateKBOrder({
					id: event.item.getAttribute('data-id'),
					order: event.newIndex,
					parent_id: null
				})
				this.$toast.success('Очередь сохранена')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не удалось сохранить очередь')
				window.onerror && window.onerror(error)
			}
			/* eslint-enable camelcase */
		},
		// === ORDER ===
	},
}
</script>

<style lang="scss">
$KBNav-padding: 15px;
.KBNav{
	display: flex;
	flex-flow: column nowrap;
	gap: 10px;

	height: 100vh;
	padding: $KBNav-padding;
	border-right: 1px solid #dfdfdf;

	background-color: #f8f8f8;
	&-search{
		position: relative;
		.fa-search{
			position: absolute;
			top: 10px;
			left: 10px;
			color: #bdcadf;
		}
		.form-control{
			padding-left: 35px !important;
		}
		.search-clear{
			position: absolute;
			top: 8px;
			right: 12px;

			font-size: 16px;
			font-style: normal;
			line-height: 1;
			color: red;
			cursor: pointer;
		}
	}
	&-glossary{
		display: flex;
		align-items: center;
		gap: .25rem;
		.fa-cog{
			color: #333;
		}
	}

	&-searchResults,
	&-archive,
	&-items{
		flex: 1;
		padding: 0 $KBNav-padding;
		margin: 0 -$KBNav-padding;
		overflow-y: auto;
	}

	&-searchItems{
		display: flex;
		flex-flow: column nowrap;
		gap: 10px;
	}
	&-searchItem{
		padding: 3px 5px 10px;
		border-bottom: 1px solid #ddd;
		font-size: 14px;
		cursor: pointer;
		&:hover{
			background-color: #f2f2f2;
		}
	}
	&-searchBook{
		color: #1272aa;
	}
	&-searchTitle{
		font-size: 16px;
		font-weight: 700;
		color: #666;
	}
	&-searchText{
		margin-top: 5px;
		font-size: 12px;
		color: #999;
	}
	&-searchTerm{
		background-color: #ff0;
		color: #333;
	}

	&-currentTitle{
		display: block;
		margin: 5px 0 10px;

		position: relative;

		font-size: 16px;
		font-weight: 700;
		&:hover{
			.KBNav{
				&-itemActions{
					font-size: 13px;
					display: flex;
				}
			}
		}
	}

	&-item{
		padding: 8px 0;

		position: relative;

		font-size: 13px;
		font-weight: 400;
		white-space: nowrap;
		text-overflow: ellipsis;

		overflow: hidden;
		cursor: pointer;
		&:hover{
			background-color: #f1f1f1;
			.KBNav{
				&-itemActions{
					display: flex;
				}
			}
		}
	}
	&-mover{
		color: #1db332 !important;
		cursor: move;
	}
	&-itemText{
		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-itemActions{
		display: none;

		position: absolute;
		top: 50%;
		right: 0;

		text-align: center;
		transform: translateY(-50%);
	}
	&-itemAction{
		width: 27px;
		padding: 5px;
		margin-left: 5px;

		color: #333;
		background: #ddd;
		border-radius: 4px;
		cursor: pointer;

		&:hover{
			color: #007bff;
		}
	}
	// &-favorites{}
	&-favorite{
		padding: 8px 0;

		position: relative;

		font-size: 13px;
		font-weight: 400;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;

		cursor: pointer;
		&:hover{
			background-color: #f1f1f1;
		}
	}
	.chapter{
		.mb-0{
			width: auto;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}
}
</style>
