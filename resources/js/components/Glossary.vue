<template>
<div class="glossary">

    <!-- buttons -->
    <div class="buttons d-flex mb-3">
        <input type="text" class="search">
        <button class="btn" v-if="mode == 'edit'">
            Добавить
        </button>
    </div>
    
    <!-- words -->
    <div class="block" v-for="(word, i) in words" :key="i">
        <div class="word">
            <input type="text" v-model="word.word" :disabled="mode == 'edit'">
        </div>
        <div class="definition">
            <textarea v-model="word.definition" :disabled="mode == 'edit'"></textarea>
        </div>
        <div class="action d-flex" v-if="mode == 'edit'">
            <button class="btn btn-sm mr-3" @click="deleteWord(i)">
                <i class="fa fa-trash"></i>
            </button>
            <button class="btn btn-sm" @click="save(i)">
                <i class="fa fa-save"></i>
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
            words: []
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
                this.words = response.data;
            }).catch(error => {
                console.error(error)
            })
        },

        deleteWord(i) {
            this.words.splice(i, 1);
        }
    }
}
</script>