<template>
<div class="stats px-3 py-1">
    <!-- top line -->
    <div class="d-flex my-4 jcsb aifs">
        
         <div class="d-flex aic mr-2">
            <div class="d-flex aic mr-2">
                <span>Показывать:</span>
                <input type="number" min="1" max="100" v-model="pageSize" class="form-control ml-2 input-sm" />
            </div>
            <super-filter
                ref="child"
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
        :searchText="searchText"
        :date="date"
        v-if="s_type_main == 1"
    />

    <t-stats-bonus
        :groups="bonus_groups"
        :group_names="groups"
        :month="month"
        v-if="s_type_main == 2"
        :key="bonus_groups"
    />

    <t-stats-quartal
        :users="quartal_users"
        :groups="quartal_groups"
        :key="quartal_users"
        :searchText="searchText"
        v-if="s_type_main == 3"
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
import {formatDate} from "./kpis.js";

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
        },
    },

    data() {
        return {
            searchText: new URL(location.href).searchParams.get('target') ? new URL(location.href).searchParams.get('target') : '',
            s_type_main: 1,
            month: new Date().getMonth(),
            active: 1,
            paginationKey: 1,
            pageSize: 20,
            items: [],
            all_items: [],
            page_items: [],
            groups: {},
            date: null,
            activities: [],
            bonus_items: [],
            bonus_groups: [],
            quartal_users: [],
            quartal_groups: []
        }
    },

    created() {
       this.fetchData([])
       this.page_items = this.items.slice(0, this.pageSize);
    },
    mounted() {
        this.$watch(
          "$refs.child.searchText",
          (new_value, old_value) => (this.searchText = new_value)
        );
    },
    methods: {
        
        onChangePage(page_items) {
            this.page_items = page_items;
        },
 
        fetchData(filters) {
            console.log(filters.data_from);
            let loader = this.$loading.show();
            this.s_type_main = filters.data_from ? filters.data_from.s_type : 1;
            this.month = filters.data_from ? filters.data_from.month : new Date().getMonth();

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

                    this.date = filters.data_from != undefined 
                        ? new Date(filters.data_from.year, filters.data_from.month, 1).toISOString().substr(0, 10)
                        : new Date().toISOString().substr(0, 10);
                        
                    loader.hide()
                }).catch(error => {
                    loader.hide()
                    alert(error)
                });
            }else if(this.s_type_main == 2){
                axios.get('/statistics/bonuses').then(response => {
                    console.log(response.data);
                    this.bonus_groups = response.data;
                    /*this.bonus_groups = response.data.groups;
                    this.bonus_groups = this.bonus_groups.map(res=> ({...res, expanded: false}));
                    for(let i = 0; i < this.bonus_groups.length; i++){
                        this.bonus_groups[i].users = this.bonus_groups[i].users.map(res=> ({...res, expanded: false, totals: {
                                quantity: 0,
                                sum:0,
                                amount:0
                            }
                        }));
                    }*/
                    loader.hide();
                }).catch(error => {
                    loader.hide();
                    alert(error);
                });
            }else if(this.s_type_main == 3){
                axios.get('/statistics/quartal-premiums').then(response => {
                    
                    //this.quartal_items = response.data;
                    this.quartal_users = response.data[0].map(res=> ({...res, expanded: false}));
                    this.quartal_groups = response.data[1].map(res=> ({...res, expanded: false}));
                    console.log(this.quartal_groups);
                    loader.hide();
                }).catch(error => {
                    loader.hide();
                    alert(error);
                });
            }else{
                loader.hide();
                alert('error!');
            }          
        },

    } 
}
</script>
