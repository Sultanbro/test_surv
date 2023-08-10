<template>
	<div class="glossary">
		<!-- buttons -->
		<div class="buttons d-flex mb-3">
			<input
				v-model="search_text"
				type="text"
				class="search form-control form-control-sm"
			>
			<button
				v-if="mode == 'edit'"
				class="btn"
				@click="add"
			>
				Добавить
			</button>
		</div>

		<!-- words -->
		<div
			v-for="(word, i) in filteredWords"
			:key="i"
			class="block"
		>
			<div class="word">
				<input
					v-model="word.word"
					type="text"
					:disabled="mode == 'read'"
					class="form-control"
				>
			</div>
			<div class="definition">
				<textarea
					v-model="word.definition"
					:disabled="mode == 'read'"
					class="form-control"
				/>
			</div>
			<div
				v-if="mode == 'edit'"
				class="action d-flex"
			>
				<button
					class="btn btn-sm"
					@click="save(i)"
				>
					<i class="fa fa-save" />
				</button>
				<button
					class="btn btn-sm ml-2"
					@click="deleteWord(i)"
				>
					<i class="fa fa-trash" />
				</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'GlossaryComponent',
	props: ['mode'],
	data(){
		return {
			words: [],
			search_text: ''
		}
	},

	computed: {
		filteredWords() {
			return this.words.filter(el => el.word.toLowerCase().indexOf(this.search_text.toLowerCase()) > -1);
		}
	},

	created(){
		this.fetch()
	},

	methods: {
		fetch() {
			this.axios.get('/glossary/get', {})
				.then(response => {
					this.words = response.data;
				}).catch(error => {
					console.error(error)
				})
		},

		save(i) {
			this.axios.post('/glossary/save', {word: this.words[i]})
				.then(response => {
					this.words[i].id = response.data;
					this.$toast.success('Определение сохранено');
				}).catch(error => {
					console.error(error)
				})
		},

		add() {
			this.search_text = '';
			this.words.unshift({
				id: 0,
				word: '',
				definition: ''
			});
		},

		deleteWord(i) {
			if(this.words[i].id == 0) this.words.splice(i, 1);

			this.axios.post('/glossary/delete', {id: this.words[i].id})
				.then(() => {
					this.words.splice(i, 1);
					this.$toast.success('Определение удалено');
				}).catch(error => {
					console.error(error)
				})
		},
	}
}
</script>