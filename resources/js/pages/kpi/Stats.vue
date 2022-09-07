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
                @apply="fetchData"
            />
            <span class="ml-2" v-if="items"> 
                Найдено: {{ items.length }}
            </span>
            <span class="ml-2" v-else> 
                Найдено: 0
            </span>
        </div>

    </div>
    
    <!-- table -->
    <t-stats 
        :activities="activities"
        :groups="groups"
        :items="page_items"
        :editable="true"
        v-if="s_type_main == 1"
    />

    <t-stats-bonus
        :items="page_items"
        :groups="bonus_groups"
        :group_names="groups"
        v-if="s_type_main == 2"
        :key="bonus_groups"
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
        v-if="s_type_main == 1"
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
            s_type_main: 1,
            active: 1,
            paginationKey: 1,
            pageSize: 10,
            items: [],
            all_items: [],
            page_items: [],
            groups: {},
            activities: [],
            bonus_items: [],
            bonus_groups: []
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
            this.s_type_main = filters.data_from ? filters.data_from.s_type : 1;
            if(this.s_type_main == 1){
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
            }else if(this.s_type_main == 2){
                axios.get('/statistics/bonuses').then(response => {
                    this.bonus_groups = response.data.groups;
                    this.bonus_groups = this.bonus_groups.map(res=> ({...res, expanded: false}));
                    for(let i = 0; i < this.bonus_groups.length; i++){
                        this.bonus_groups[i].users = this.bonus_groups[i].users.map(res=> ({...res, expanded: false, totals: {
                                quantity: 0,
                                sum:0,
                                amount:0
                            }
                        }));
                    }
                    loader.hide();
                }).catch(error => {
                    loader.hide();
                    alert(error);
                });
            }        
        },

    } 
}
</script>
