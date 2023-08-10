<template>
	<div>
		<div class="book_settings">
			<div class="row mb-2">
				<div class="col-lg-3">
					<b-form-select
						:options="selectGroups"
						size="md"
						class="group-select col-lg-6 d-flex"
						@change="groupselect"
					>
						<template #first>
							<b-form-select-option
								:value="null"
								disabled
							>
								Выберите отдел из списка
							</b-form-select-option>
						</template>
					</b-form-select>
				</div>
				<div class="col-lg-9 d-flex align-items-start">
					<div class="add-grade">
						<input
							v-model="new_position"
							type="text"
							class="form-control"
						>
						<button
							class="btn btn-success"
							@click="addGroup"
						>
							Добавить список книг
						</button>
					</div>
					<div
						v-if="group_id"
						class="listgroup"
					>
						<button
							class="btn btn-danger"
							@click="deleteGroup"
						>
							Удалить список
						</button>
					</div>
				</div>
			</div>

			<div class="mb-4">
				<div>
					<Books
						ref="books"
						:selected-group="group_id"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Books from '../components/books/books.vue'

export default {
	name: 'BookGroups',
	components: {
		Books,
	},
	data() {
		return {
			groups: [],
			message: null,
			selectGroups: [],
			new_position: '',
			options: [],
			value: [],
			group_id: 0
		}
	},
	created() {
		this.getGroups()
	},
	methods: {
		addGroup() {
			this.axios.post('/bp_books/groups/add', {
				name: this.new_position,
			}).then(response => {
				if (response.data.message) {
					this.$toast.info(response.data.message);
				} else {
					this.$toast.info('Список добавлен');
				}

				this.getGroups()
				this.new_position = ''
			}).catch(error => {
				console.error(error.response)
			})
		},
		deleteGroup() {
			if (confirm('Вы уверены что хотите удалить отдел?')) {
				this.axios.post('/bp_books/groups/delete', {
					id: this.group_id,
				}).then(() => {
					this.$toast.info('Список удален');
					this.group_id = 0
					this.getGroups()
				})
			}
		},
		groupselect(value) {
			this.axios.post('/bp_books/groups', {
				group_id: value,
			})
				.then(response => {
					if (response.data) this.group_id = response.data.group_id
				})
		},
		messageoff() {
			setTimeout(() => {
				this.message = null
			}, 3000)
		},
		getGroups() {
			this.axios.get('/bp_books/groups', {}).then(response => {
				this.groups = response.data.groups
				this.options = response.data.books
				this.selectGroups = []
				this.groups.forEach((item) => {
					this.selectGroups.push({
						'value': item.id,
						'text': item.name,
					})
				})
			}).catch(error => {
				console.error(error.response)
			})
		},
		refreshPositionsComponent() {
			this.$refs.positions.getPositions();
		},
		refreshBooksComponent() {
			this.$refs.books.getBooks();
		},
	}
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scoped>
.form-control {
    border-radius: 0;
}

.listgroup {
    display: flex;
    flex-wrap: wrap;
}

.groupitem {
    margin-right: 10px;
    margin-bottom: 8px;
    padding-right: 34px;
    position: relative;
}
.groupitem .fa {
      position: absolute;
    top: -1px;
    padding: 11px;
}
.add-grade {
    display: flex;
    max-width: 500px;
    margin-right: 20px;
    margin-bottom: 8px;
}

.dialerlist {
    display: flex;
    align-items: center;
    margin: 15px 0;
    margin-top: 0;
    max-width: 100%;
}

.dialerlist .fl {
    flex: 1;
    display: flex;
    align-items: center;
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

.b-flex {
    display: flex;
}

.b-2 {
    width: 50%;
}

.b-60 {
    width: 60%;
}
.b-50 {
    width: 50%;
}
.b-40 {
    width: 40%;
}

.book_settings .multiselect__tag {
    display: block !important;
}
.profitem.btn,
.groupitem.btn {
    cursor: pointer;
    text-overflow: inherit;
    overflow-wrap: break-word;
    white-space: initial;
    text-align: left;
}
h4.cp {
    cursor: pointer;
}
.group-select {
    border-radius: 0;
    max-width: 100%;
}


</style>
