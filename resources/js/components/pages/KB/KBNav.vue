<template>
	<aside class="KBNav">
		<div class="KBNav-wrapper">
			<div
				v-if="!currentBook"
				class="KBNav-glossary"
			>
				<button
					class="KBNav-button"
					@click="$emit('glossary-open')"
				>
					Глоссарий
				</button>

				<button
					v-if="isOwner && mode === 'edit'"
					class="KBNav__button-settings"
					@click="$emit('glossary-settings')"
				>
					<SettingsIcon />
				</button>
			</div>

			<div class="KBNav-search">
				<SearchIcon class="fa fa-search" />
				<input
					v-model="search.input"
					type="text"
					placeholder="Быстрый поиск"
					class="form-input"
					@input="searchInput"
				>
			</div>

			<!-- Back buttons -->
			<div
				v-if="archived.show || currentBook"
				class="KBNav-back"
			>
				<div
					v-if="archived.show"
					class="btn btn-grey btn-block mb-3 mt-4"
					@click="archived.show = false"
				>
					<i class="fa fa-arrow-left" />
					<span>Выйти из архива</span>
				</div>
				<button
					v-if="currentBook"
					class="KBNav-button"
					style="margin-top: 5%"
					@click="onBack"
				>
					<div class="KBNav-button-content">
						<BackChapterIcon />
						Вернуться к разделам
					</div>
				</button>
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
					/>
				</div>

				<template v-else-if="favorites.length">
					<div class="KBNav-favorites">
						<div
							v-for="(favorite, index) in favorites"
							:key="favorite.id"
							class="KBNav-favorite"
						>
							<p
								v-if="favorite.isFavorite"
								@click="$emit('favorite', favorite, index)"
							>
								<FavoriteIcon />
							</p>
							<p @click="$emit('search', favorite, '')">
								{{ favorite.title }}
							</p>
						</div>
						<hr>
					</div>
				</template>
			</div>
			<div
				v-if="search.input.length"
				class="KBNav-searchItems"
			>
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
					<!-- eslint-disable-next-line -->
						<div class="KBNav-searchText" v-html="item.text" />
				</div>
			</div>
			<div v-else-if="!archived.show">
				<KBNavItems
					v-if="currentBook"
					:key="'p' + listsKey"
					:input="search.input"
					class="KBNav-items"
					:items="books"
					:opened="true"
					:mode="mode"
					:parent="currentBook"
					:active="activeBook ? activeBook.id : null"
					@update-input="updateInput"
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
					class="KBNav-items"
					:items="books"
					:opened="true"
					:mode="mode"
					:parent="null"
					:active="null"
					:sections-mode="true"
					@update-input="updateInput"
					@show-page="$emit('book', $event)"
					@page-order="$emit('page-order', $event)"
					@add-page="$emit('add-page', $event)"
					@add-book="$emit('create', $event)"
					@remove-book="archiveBook($event)"
					@settings="$emit('settings', $event)"
				/>
			</div>
			<div
				v-else
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
		</div>

		<div v-if="mode === 'edit' && !currentBook">
			<div
				v-if="!archived.show"
				class="KBNav__footer"
			>
				<button
					class="KBNav__footer-button"
					title="Добавить базу знаний"
					@click="$emit('create', $event)"  
				>
					<AddIcon />
					Добавить
				</button>
				<button
					title="Архив"
					class="KBNav__footer-archive"
					@click="getArchivedBooks"
				>
					<ArchiveIcon />
				</button>
			</div>
		</div>
		<div v-if="mode === 'edit' && currentBook">
			<div
				v-if="!archived.show"
				class="KBNav-footer-buttons"
			>
				<button
					class="KBNav-button"
					@click="$emit('add-page', currentBook)"
				>
					<div class="KBNav-footer-button">
						<AddIconSilver />
						Страница
					</div>
				</button>
				<button
					v-if="$laravel.is_admin"
					class="KBNav-button"
					@click="$emit('create', currentBook)"
				>
					<div class="KBNav-footer-button">
						<AddIconSilver />
						База
						<InfoIcon />
					</div>
					<!-- <img
						v-b-popover.hover.right="
							'Вы можете создать дополнительную базу знаний с отдельными доступами'
						"
						src="/images/dist/profit-info.svg"
						class="img-info ml-3"
						alt="info icon"
						width="18"
					/> -->
				</button>
			</div>
		</div>
	</aside>
</template>

<script>
import { fetchSettings, updateSettings } from '@/stores/api.js';
import * as KBAPI from '@/stores/api/kb.js';
import { mapState } from 'pinia';
import { usePortalStore } from '@/stores/Portal';

import SearchIcon from '../../../../assets/icons/SearchIcon.vue';
import SettingsIcon from '../../../../assets/icons/SettingsIcon.vue';
import AddIcon from '../../../../assets/icons/AddIcon.vue';
import ArchiveIcon from '../../../../assets/icons/ArchiveIcon.vue';
import BackChapterIcon from '../../../../assets/icons/BackChapterIcon.vue';
import AddIconSilver from '../../../../assets/icons/AddIconSilver.vue';
import InfoIcon from '../../../../assets/icons/InfoIcon.vue';
import FavoriteIcon from '../../../../assets/icons/FavoriteIcon.vue';

import KBNavItems from './KBNavItems.vue';

const API = {
	fetchSettings,
	updateSettings,
	...KBAPI,
};

export default {
	name: 'KBNav',
	components: {
		SearchIcon,
		SettingsIcon,
		KBNavItems,
		AddIcon,
		ArchiveIcon,
		BackChapterIcon,
		AddIconSilver,
		InfoIcon,
		FavoriteIcon,
	},
	props: {
		mode: {
			type: String,
			default: 'read',
		},
		activeBook: {
			type: Object,
			default: null,
		},
		books: {
			type: Array,
			default: () => [],
		},
		favorites: {
			type: Array,
			default: () => [],
		},
		currentBook: {
			type: Object,
			default: null,
		},
	},
	data() {
		return {
			search: {
				input: '',
				items: [],
				timepout: null,
				loading: false,
			},
			allItems: [],
			archived: {
				show: false,
				items: [],
			},
			listsKey: 1,
		};
	},
	computed: {
		...mapState(usePortalStore, ['isOwner', 'isAdmin']),
		pagesMap() {
			const map = {};
			this.getPages(map, this.pages);
			return map;
		},
	},
	watch: {
		pages() {
			this.updateKeys();
		},
		books() {
			this.updateKeys();
		},
	},
	mounted() {
	},
	methods: {
		unFavorite(favorite) {
			return (favorite.isFavorite = false);
		},
		updateKeys() {
			++this.listsKey;
		},
		onBack() {
			this.$emit('back');
			this.clearSearch();
		},
		getPages(map, pages) {
			pages.map((page) => {
				map[page.id] = page;
				if (page.children && page.children.length)
					this.getPages(map, page.children);
			});
		},

		// === SEARCH ===
		searchInput() {
			clearTimeout(this.search.timeout);
			this.search.timeout = setTimeout(() => {
				this.runSearch().then(() => {
					this.$forceUpdate();
				});
			}, 500);
		},
		updateInput() {
			this.search.input = '';
		},
		searchCheck() {
			if (this.search.input.length === 0) this.clearSearch();
		},
		clearSearch() {
			clearTimeout(this.search.timeout);
			this.search = {
				input: '',
				items: [],
				timeout: null,
				loading: false,
			};
		},
		async runSearch() {
			try {
				if (!this.search.input.length) this.$emit('back')
        
				const data = await API.searchKBBook({
					text: this.search.input,
					id: this.currentBook?.id || null,
				});
				this.search.items = data.items.map((item) => {
					const regExp = new RegExp(this.search.input, 'gi');
					item.text = item.text.replace(
						regExp,
						'<span class="KBNav-searchTerm">' + this.search.input + '</span>'
					);
					return item;
				});
				this.$forceUpdate();
			} catch (error) {
				console.error(error);
				this.$toast.error('Поиск не удался');
				window.onerror && window.onerror(error);
			}
		},

		// === ARCHIVE ===
		async archiveRestore(book) {
			if (!confirm('Вы уверены что хотите восстановить раздел?')) return;
			try {
				await API.restoreKBBook(book.id);
				const index = this.archived.items.findIndex(
					(archived) => archived.id === book.id
				);
				this.archived.items.splice(index, 1);
				this.$emit('unarchive', book);

				const { tree } = await API.fetchKBBooksV2();

				// eslint-disable-next-line vue/no-mutating-props
				this.books = await tree

				this.$toast.success('Раздел восстановлен');
			} catch (error) {
				console.error(error);
				this.$toast.error('Не удалось восстановить раздел');
				window.onerror && window.onerror(error);
			}
		},
		async archiveBook(book) {
			if (!confirm('Вы уверены что хотите архивировать раздел?')) return;
			try {
				await API.deleteKBBook(book.id);
				this.$emit('archive', book);
				this.$toast.success('Раздел удален');
			} catch (error) {
				console.error(error);
				this.$toast.error('Не удалось удалить раздел');
				window.onerror && window.onerror(error);
			}
		},
		async getArchivedBooks() {
			const loader = this.$loading.show();

			try {
				const books = await API.fetchKBArchived();
				this.archived.items = books;
				this.archived.show = true;
			} catch (error) {
				console.error(error);
				this.$toast.error('Не удалось получить архивные разделы');
				window.onerror && window.onerror(error);
			}
			loader.hide();
		},

		// === ORDER ===
		async saveBookOrder(event) {
			/* eslint-disable camelcase */
			try {
				await API.updateKBOrder({
					id: event.item.getAttribute('data-id'),
					order: event.newIndex,
					parent_id: null,
				});
				this.$toast.success('Очередь сохранена');
			} catch (error) {
				console.error(error);
				this.$toast.error('Не удалось сохранить очередь');
				window.onerror && window.onerror(error);
			}
			/* eslint-enable camelcase */
		},
		// === ORDER ===
	},
};
</script>

<style lang="scss">
%button-content {
	display: flex;
	justify-content: center;
	align-items: center;
}

.KBNav {
	display: flex;
	flex-flow: column nowrap;
	justify-content: space-between;

	height: 100vh;
	padding: 0.4% 1%;
	border-right: 1px solid #dfdfdf;

	background-color: #ffffff;

	&-items {
		margin-top: 10px;
	}
	&-search {
		background-color: #f7fafc;
		padding: 5%;
		border-radius: 8px;
		margin-top: 6%;
		width: 102%;
		position: relative;
		.fa-search {
			position: absolute;
			top: 12px;
			left: 18px;
			color: #bdcadf;
		}
		.form-input {
			font-size: 14px;
			padding: 0px 35px;
			background-color: #f7fafc;
		}
		.search-clear {
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
	&-glossary {
		display: flex;
		align-items: center;
		gap: 4px;
		.fa-cog {
			color: #333;
		}
	}

	&-button {
		width: 100%;
		height: 35px;
		// padding: 8.5px 45px;
		color: #8da0c1;
		font-size: 14px;
		border: 1px solid #8da0c1;
		border-radius: 8px;
		background-color: #ffffff;
		.KBNav-button-content {
			@extend %button-content;
			gap: 13px;
		}
		.KBNav-footer-button {
			@extend %button-content;
			gap: 3px;
		}
	}

	&__button-settings {
		padding: 4px;
		border-radius: 6.79px;
		border: 1px solid #8da0c1;
		background-color: #ffffff;
	}

	&-searchResults,
	&-archive,
	&-items {
		flex: 1;
		overflow-y: auto;
	}

	&-searchItems {
		display: flex;
		flex-flow: column nowrap;
		gap: 10px;
	}
	&-searchItem {
		padding: 3px 5px 10px;
		border-bottom: 1px solid #ddd;
		font-size: 14px;
		cursor: pointer;
		&:hover {
			background-color: #f2f2f2;
		}
	}
	&-searchBook {
		color: #1272aa;
	}
	&-searchTitle {
		font-size: 16px;
		// font-weight: 700;
		// color: #666;
    cursor: pointer;
    margin-top: 4px;
	}
	&-searchText {
		margin-top: 5px;
		font-size: 12px;
		color: #999;
	}
	&-searchTerm {
		background-color: #ff0;
		color: #333;
	}

	&-currentTitle {
		display: block;
		margin: 5px 0 10px;

		position: relative;

		font-size: 16px;
		font-weight: 700;
		&:hover {
			.KBNav {
				&-itemActions {
					font-size: 13px;
					display: flex;
				}
			}
		}
	}

	&__footer {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 9px;
		.KBNav__footer-button {
			width: 100%;
			background-color: #ebf3fb;
			color: #156ae8;
			padding: 2%;
			border-radius: 8px;
			display: flex;
			align-items: center;
			font-weight: 500;
			justify-content: center;
			gap: 4px;
		}
		.KBNav__footer-archive {
			border: 1px solid #8da0c1;
			padding: 7px;
			background-color: #ffffff;
			border-radius: 8px;
		}
	}

	&-footer-buttons {
		display: flex;
		gap: 8px;
	}

	&-item {
		padding: 8px 0;

		position: relative;

		font-size: 13px;
		font-weight: 400;
		white-space: nowrap;
		text-overflow: ellipsis;

		overflow: hidden;
		cursor: pointer;
		&:hover {
			background-color: #f1f1f1;
			.KBNav {
				&-itemActions {
					display: flex;
				}
			}
		}
	}
	&-mover {
		color: #1db332 !important;
		cursor: move;
	}
	&-itemText {
		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-itemActions {
		display: none;

		position: absolute;
		top: 50%;
		right: 0;

		text-align: center;
		transform: translateY(-50%);
	}
	&-itemAction {
		width: 27px;
		padding: 5px;
		margin-left: 5px;

		color: #333;
		background: #ddd;
		border-radius: 4px;
		cursor: pointer;

		&:hover {
			color: #007bff;
		}
	}
	// &-favorites{}
	&-favorite {
		padding: 4px 0;

		position: relative;

		font-size: 14px;
		font-weight: 400;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		display: flex;
		gap: 5px;
		cursor: pointer;
		&:hover {
			background-color: #f1f1f1;
		}
	}
	.chapter {
		.mb-0 {
			width: auto;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}
}
</style>
