<template>
	<div>
		<!-- User Interface controls -->
		<b-row>
			<!-- <b-col lg="4" class="my-1">
            <b-form-group class="mb-0">
                <b-input-group size="sm">
                    <b-form-input id="filter-input" v-model="filter" type="search" placeholder=""></b-form-input>

                    <b-input-group-append>
                        <b-button :disabled="!filter" @click="filter = ''">Очистить</b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-form-group>
        </b-col>

        <b-col lg="2" md="6" class="my-1">
            <b-form-group class="mb-0">
                <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select>
            </b-form-group>
        </b-col>


        <b-col lg="4" class="my-1 d-flex justify-content-end">

            <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0 mr-2"></b-pagination>
        </b-col> -->

			<b-col
				lg="2"
				class="my-1"
			>
				<div class="d-flex">
					<div>
						<button
							class="btn btn-success btn-sm mr-2"
							@click="toggleAbbBookWindow"
						>
							Добавить книгу
						</button>
					</div>
				</div>
			</b-col>
			<b-col
				md="12"
				class="my-2"
			>
				<div
					v-if="addBookWindow"
					class="mb-3"
				>
					<p><strong>Новая книга</strong></p>
					<b-form-input
						v-model="book.title"
						placeholder="Название книги"
						class="mb-2"
					/>
					<b-form-input
						v-model="book.author"
						placeholder="Автор книги"
						class="mb-2"
					/>
					<b-form-input
						v-model="book.link"
						placeholder="Ссылка"
						class="mb-2"
					/>
					<button
						class="btn btn-success btn-sm"
						@click="addBook"
					>
						Сохранить
					</button>
				</div>
			</b-col>
		</b-row>

		<!-- Main table element -->
		<b-table
			:items="items"
			:fields="fields"
			:current-page="currentPage"
			:per-page="perPage"
			:filter="filter"
			striped
			:filter-included-fields="filterOn"
			:sort-by.sync="sortBy"
			:sort-desc.sync="sortDesc"
			:sort-direction="sortDirection"
			stacked="md"
			show-empty
			small
			@filtered="onFiltered"
		>
			<template #cell(title)="row">
				<a
					:href="row.item.link"
					download=""
					target="_blank"
				>{{ row.value }}</a>
			</template>

			<template #cell(groups)="row">
				<b-badge
					v-for="group_id in row.value"
					:key="group_id"
					variant="primary"
					class="mr-1"
				>
					{{ groups[group_id] }}
				</b-badge>
			</template>

			<template #cell(actions)="row">
				<div class="d-flex s-flex">
					<div
						size="sm"
						class="edit_button mr-2"
						@click="clickRow(row)"
					>
						<div v-if="row.detailsShowing">
							<i class="fa fa-times" />
						</div>
						<div v-else>
							<i class="fa fa-pencil" />
						</div>
					</div>
					<div
						size="sm"
						class="delete_button"
						@click="deleteBook(row.item.id)"
					>
						<i class="fa fa-trash" />
					</div>
				</div>
			</template>

			<template #row-details>
				<b-card>
					<b-form-input
						v-model="ebook.title"
						placeholder="Название книги"
						class="mb-2"
					/>
					<b-form-input
						v-model="ebook.author"
						placeholder="Автор книги"
						class="mb-2"
					/>
					<b-form-input
						v-model="ebook.link"
						placeholder="Ссылка"
						class="mb-2"
					/>
					<button
						class="btn btn-success btn-sm"
						@click="editBook"
					>
						Сохранить
					</button>
				</b-card>
			</template>
		</b-table>
	</div>
</template>

<script>
/* eslint-disable vue/no-side-effects-in-computed-properties */
export default {
	name: 'BooksComponent',
	props: {
		selectedGroup: Number,
	},
	data() {
		return {
			items: [],
			groups: [],
			addBookWindow: false,
			lastClickedRow: null,
			book: {
				title: '',
				author: '',
				link: ''
			},
			ebook: {
				id: null,
				title: '',
				author: '',
				link: ''
			},
			fields: [
				{
					key: 'id',
					label: 'id'
				},
				{
					key: 'title',
					label: 'Название',
					sortable: true
				},
				{
					key: 'author',
					label: 'Автор',
					sortable: true
				},
				{
					key: 'groups',
					label: 'Группы',
					sortable: true
				},
				{
					key: 'actions',
					label: '',
				},
			],
			totalRows: 1,
			currentPage: 1,
			perPage: 100,
			pageOptions: [10, 20, 40, 50, {
				value: 100,
				text: 'Показать 100'
			}],
			sortBy: '',
			sortDesc: false,
			sortDirection: 'asc',
			filter: null,
			filterOn: ['title']
		}
	},
	computed: {
		sortOptions() {
			return this.fields
				.filter(f => f.sortable)
				.map(f => {
					return {
						text: f.label,
						value: f.key
					}
				})
		},
		itemProvider() {
			this.filtered = this.items.filter((el) => {
				return el.title.indexOf(this.filter.title) > -1 && el.groups.includes(this.selectedGroup);
			})

			this.totalRows = this.filtered.length
			return this.filtered
		},
	},
	watch: {
		selectedGroup: function () {
			this.getBooks()
		},
		// filter: {
		//     handler: function () {
		//         this.$refs.table.refresh()
		//     },
		//     deep: true
		// }
	},
	created() {
		this.getBooks()
	},
	mounted() {
		this.totalRows = this.items.length
	},
	methods: {
		toggleAbbBookWindow() {
			this.addBookWindow = !this.addBookWindow
		},
		clickRow(row) {
			if (this.lastClickedRow != null) {
				if (this.lastClickedRow.index == row.index) {
					this.lastClickedRow.toggleDetails();
					this.lastClickedRow = null
				} else {
					this.lastClickedRow.toggleDetails();
					this.lastClickedRow = row;
					row.toggleDetails();
				}
			} else {
				row.toggleDetails();
				this.lastClickedRow = row;
			}

			this.ebook.id = row.item.id;
			this.ebook.title = row.item.title;
			this.ebook.link = row.item.link;
			this.ebook.author = row.item.author;
		},
		onFiltered(filteredItems) {
			this.totalRows = filteredItems.length
			this.currentPage = 1
		},
		messageoff() {
			setTimeout(() => {
				this.message = null
			}, 3000)
		},
		addBook() {
			this.axios.post('/bp_books/book/add', {
				title: this.book.title,
				author: this.book.author,
				group_id: this.selectedGroup,
				link: this.book.link,
			})
				.then(() => {
					this.book.title = ''
					this.book.author = ''
					this.book.link = ''
					this.addBookWindow = false
					// this.lastClickedRow.toggleDetails();
					// this.lastClickedRow = null
					this.$toast.success('Успешно сохранено');
					this.messageoff()
					this.getBooks();

				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		},
		editBook() {
			this.axios.post('/bp_books/book/edit', {
				id: this.ebook.id,
				title: this.ebook.title,
				author: this.ebook.author,
				link: this.ebook.link,
			})
				.then(response => {
					this.ebook.id = ''
					this.ebook.title = ''
					this.ebook.link = ''
					this.ebook.author = ''
					this.lastClickedRow.toggleDetails();
					this.lastClickedRow = null
					if (response.data.code == 1) {
						this.$toast.success(response.data.message);
					} else {
						this.$toast.error(response.data.message);
					}

					this.getBooks()
					this.messageoff()
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		},
		deleteBook(id) {
			this.axios.post('/bp_books/book/delete', {
				id: id,
			})
				.then(() => {
					this.$toast.success('Книга удалена')
					this.getBooks()
					this.messageoff()
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		},
		getBooks() {
			this.axios.post('/bp_books/books', {
				group_id: this.selectedGroup
			})
				.then(response => {
					this.items = response.data.books
					this.groups = response.data.groups
					this.totalRows = this.items.length

					this.groupFilter()
				})
		},
		groupFilter() {
			if(this.selectedGroup != 0) {
				this.items = this.items.filter((el) => {

					let includes = false

					el.groups.forEach((item) => {
						if(item == this.selectedGroup) includes = true
					})

					return includes;
				})
			}

		}
	}
}
</script>

<style scoped>
.b-table-sticky-header>.table,
.table-responsive>.table,
[class*=table-responsive-]>.table {
    border: 1px solid #dee2e6;
}

select,
input,
button {
    outline: 0 !important;
}

.input-group .custom-select:not(:last-child),
.input-group .form-control:not(:last-child),
button.btn,
.custom-select-sm,
.input-group>.input-group-append>.btn,
.input-group>.input-group-append>.input-group-text,
.input-group>.input-group-prepend:first-child>.btn:not(:first-child),
.input-group>.input-group-prepend:first-child>.input-group-text:not(:first-child),
.input-group>.input-group-prepend:not(:first-child)>.btn,
.pagination-sm .page-item:first-child .page-link .input-group>.input-group-prepend:not(:first-child)>.input-group-text {
    border-radius: 0;
}
i {
  cursor: pointer;
}
.edit_button:hover {
  color: #40a9ff;
}
.delete_button:hover {
    color: #ff4040;
}
table {
        border: 1px solid #dee2e6;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(145,145,145,.05);
}
.table td:first-child,
.table th:first-child {
    width: 4px;
    font-weight: 700;
}
.s-flex {
        justify-content: flex-end;
    margin-right: 15px;
}


</style>
