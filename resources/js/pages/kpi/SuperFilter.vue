<template>
<div class="super-filter p-3">
    <input 
        class="searcher"
        v-model="searchText"
        type="text"
        placeholder="Поиск по совпадениям..."
        @click="show = true"
        @keyup="$emit('search-text-changed', this.searchText)"
    >

    <div class="block" v-if="show" @click="show = false">
        haha
    </div>


</div>
</template>

<script>
export default {
    name: "KPI", 
    props: {
      url: {
        required: true,
        type: String,
      }  
    },
    data() {
        return {
            show: false,
            searchText: '',
        }
    }, 

    created() {
       
    },
    methods: {

        fetchKPI() {
            let loader = this.$loading.show();

            axios.get('/kpi/get').then(response => {
                
                this.items = repsonse.data.items;
                this.activities = repsonse.data.activities;
                this.groups = repsonse.data.groups;

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },
        
        clear() {
            this.searchText = ''
        }
     
    } 
}
</script>