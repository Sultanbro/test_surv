<template>
	<div>
		<multiselect
			v-model="value"
			:options="books"
			:multiple="true"
			:close-on-select="false"
			:clear-on-select="false"
			:preserve-search="true"
			placeholder="Выберите"
			label="name"
			track-by="name"
			:taggable="true"
			@tag="addTag"
			@remove="removeGroup"
			@select="selectGroup"
		/>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */
/* eslint-disable vue/no-mutating-props */

export default {
	name: 'ProfileBooks',
	props: {
		user_id: {
			type: Number,
			default: null
		},
		books: {
			type: Array,
			default: null
		},
		in_books: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			message: null,
			value: [],
		}
	},
	created() {
		this.value = this.in_books;
	},
	mounted() {
		//this.getPositions();
	},
	methods: {
		addTag(newTag) {
			const tag = {
				name: newTag,
				id: newTag
			}
			this.books.push(tag)
			this.value.push(tag)


		},
		messageoff() {
			setTimeout(() => {
				this.message = null
			}, 3000)
		},
		selectGroup(selectedOption) {

			this.axios.post('/timetracking/edit-person/book', {
				user_id: this.user_id,
				book_id: selectedOption.id,
				action: 'add',
			})
				.then(() => {
					this.$toast.info('Сотрудник получил книгу "' + selectedOption.name + '"');
					this.messageoff()
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		},
		removeGroup(selectedOption) {
			this.axios.post('/timetracking/edit-person/book', {
				user_id: this.user_id,
				book_id: selectedOption.id,
				action: 'delete'
			})
				.then(() => {
					this.$toast.info('Сотрудник лишился книги "' + selectedOption.name + '"');
					this.messageoff()
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		}
	}
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scoped></style>
