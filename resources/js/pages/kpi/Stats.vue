<template>
<div class="stats px-3 py-1">
    <!-- top line -->
    <div class="d-flex mb-2 mt-2 jcsb aifs">
        
         <div class="d-flex aic mr-2">
            <div class="d-flex aic mr-2">
                <span>Показывать:</span>
                <input type="number" min="1" max="100" v-model="pageSize" class="form-control ml-2 input-sm" />
            </div>
            <super-filter
                :groups="groups"
                @search-text-changed="onSearch"
                @apply="fetchData"
            />
            <span class="ml-2"> 
                Найдено: {{ items.length }}
            </span>
        </div>

    </div>
    
    <!-- table -->
    <t-stats 
        :activities="activities"
        :groups="groups"
        :items="page_items"
        :editable="true"
    />

    <!-- pagination -->
    <jw-pagination
        class="mt-3"
        :key="paginationKey"
        :items="items"
        :labels="{
            first: '<<',
            last: '>>',
            previous: '<',
            next: '>'
        }"
        @changePage="onChangePage"
        :pageSize="+pageSize"
    ></jw-pagination>

</div>
</template>

<script> 
export default {
    name: "Stats", 
    props: {

    },
    
    watch: {
        pageSize: {
            handler: function(val) {
                if(val < 1) {
                    val = 1;
                    return;
                }
                
                if(val > 100) {
                    val = 100;
                    return;
                }

                this.paginationKey++;
            }
        }
    },

    data() {
        return {
            active: 1,
            paginationKey: 1,
            pageSize: 10,
            items: [],
            all_items: [],
            page_items: [],
            groups: {},
            activities: [],
        }
    },

    created() {
       this.fetchData([])
       this.page_items = this.items.slice(0, this.pageSize);
    },

    methods: {
        
        onChangePage(page_items) {
            this.page_items = page_items;
        },
 
        fetchData(filters) {
            let loader = this.$loading.show();

            axios.post('/statistics/kpi', {
                filters: filters 
            }).then(response => {
                
                // items
                this.items = response.data.items;
                this.activities = response.data.activities;
                this.groups = response.data.groups;

                // paginate
                this.page_items = this.items.slice(0, this.pageSize);

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        onSearch(asd) {
            console.log(asd)
        },
    } 
}
</script>
