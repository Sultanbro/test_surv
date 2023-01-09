<template>
<div class="glossary">

    <!-- buttons -->
    <div class="buttons d-flex mb-3">
        <input type="text" class="search form-control form-control-sm" v-model="search_text">
        <button class="btn" v-if="mode == 'edit'" @click="add">
            Добавить
        </button>
    </div>
    
    <!-- words -->
    <div class="block" v-for="(word, i) in filteredWords" :key="i">
        <div class="word">
            <input type="text" v-model="word.word" :disabled="mode == 'read'" class="form-control">
        </div>
        <div class="definition">
            <textarea v-model="word.definition" :disabled="mode == 'read'" class="form-control"></textarea>
        </div>
        <div class="action d-flex" v-if="mode == 'edit'">
            <button class="btn btn-sm" @click="save(i)">
                <i class="fa fa-save"></i>
            </button>
            <button class="btn btn-sm ml-2" @click="deleteWord(i)">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>    

</div>
</template>

<script>
export default {
	name: 'Glossary',
	props: ['mode'],
	data(){
		return {
			words: [],
			search_text: ''
		}
	},

	created(){
		this.fetch()
	},
    
	computed: {
		filteredWords() {
			return this.words.filter(el => el.word.toLowerCase().indexOf(this.search_text.toLowerCase()) > -1);
		}
	},

	methods: {
		fetch() {
			axios.get('/glossary/get', {})
				.then(response => {
					this.words = response.data;
				}).catch(error => {
					console.error(error)
				})
		},

		save(i) {
			axios.post('/glossary/save', {word: this.words[i]})
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

			axios.post('/glossary/delete', {id: this.words[i].id})
				.then(response => {
					this.words.splice(i, 1);
					this.$toast.success('Определение удалено');
				}).catch(error => {
					console.error(error)
				})
		},
	}
}
</script>