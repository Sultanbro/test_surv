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
            />
            <span class="ml-2"> 
                Найдено: {{ items.length }}
            </span>
        </div>

    </div>
    
    <!-- table -->

    <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <th class="first-column"></th>

                <th class="w-full">KPI</th>
                
                <th class="px-2">Средний %</th>

                <th></th>

            </tr>

        </thead>

        <tbody>

            <template v-for="(wrap_item, w) in page_items">
                
                <tr class="main-row">

                    <td  @click="expand(w)" class="pointer py-1">
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="wrap_item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ w + 1 }}</span>
                        </div>
                    </td>
                    <td class="px-2 py-1">
                        <span v-if="wrap_item.target != null">{{ wrap_item.target.name }}</span>
                        <span v-else>---</span>
                    </td>
                    <td class="px-2 py-1">{{ wrap_item.avg }}%</td>
                    <td></td>
                        
                </tr>

                <template v-if="wrap_item.users != undefined && wrap_item.users.length > 0">
                    <tr class="collapsable" :class="{'active': wrap_item.expanded}" :key="w + 'a'">
                        <td :colspan="fields.length + 2">
                            <div class="table__wrapper">
                            <table class="child-table">
                                <template v-for="(user, i) in wrap_item.users">
                                    <tr :key="i" class="child-row">
                                        <td  @click="user.expanded = !user.expanded" class="pointer px-2">
                                            <i class="fa fa-minus mt-1 little-expander" v-if="user.expanded"></i>
                                            <i class="fa fa-plus mt-1 little-expander" v-else></i>
                                            <span class="ml-2 bg-transparent">{{ i + 1 }}</span>
                                        </td>
                                        <td class="px-2 py-1">{{ user.name }}</td>
                                        
                                        <template v-if="user.items !== undefined"">
                                            <td class="px-2" v-for="kpi_item in user.items">{{ kpi_item.name }} <b>{{ kpi_item.percent}}%</b></td>
                                        </template>
                                        
                                    </tr>

                                    <template v-if="user.items !== undefined">
                                        <tr class="collapsable" :class="{'active': user.expanded}" :key="i + 'a'">
                                            <td :colspan="fields.length + 2">
                                                <div class="table__wrapper__second">
                                                    <kpi-items
                                                        :kpi_id="user.id"
                                                        :items="user.items" 
                                                        :expanded="user.expanded"
                                                        :activities="activities"
                                                        :groups="groups"
                                                        :completed_80="user.completed_80"
                                                        :completed_100="user.completed_100"
                                                        :lower_limit="user.lower_limit"
                                                        :upper_limit="user.upper_limit"
                                                        :editable="false"
                                                    />
                                                </div>
                                            </td>
                                        </tr>                
                                    </template>
                                
                                </template>
                            </table>
                            </div>
                        </td>
                    </tr>
                </template>

            </template>
          
        </tbody>
     </table>
      

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
import {kpi_fields} from "./kpis.js";
import {findModel} from "./helpers.js";

export default {
    name: "Stats", 
    props: {

    },
    data() {
        return {
            active: 1,
            paginationKey: 1,
            
            items: [],
            itsems: [
                {
                    id: 1,
                    targetable_type: '',
                    targetable_id: 1,
                    target: {},
                    name: 'test line',
                    completed_80: 20000,
                    completed_100: 30000,
                    lower_limit: 80,
                    upper_limit: 100,
                    avg: 45,
                    expanded: false,
                    kpi_items: [
                        {
                            id: 13,
                            name: 'Кол=во минут',
                            percent: 0 
                        },
                        {
                            id: 14,
                            name: 'ОКК',
                            percent: 0 
                        },
                    ],
                    users: [
                        {
                            expanded: false,
                            user_id: 123,
                            type: 'user',
                            name: 'Али Акпанов',
                            kpi_items: [
                                {
                                    id: 13,
                                    method: 1,
                                    name: 'Кол=во минут',
                                    activity_id: 0,
                                    plan: 450,
                                    share: 50,
                                    fact: 355,
                                    percent: 0,
                                    sum: 0
                                },
                                {
                                    id: 14,
                                    method: 2,
                                    name: 'ОКК',
                                    activity_id: 0,
                                    plan: 80,
                                    share: 50,
                                    fact: 81,
                                    percent: 0,
                                    sum: 0
                                },
                            ]
                        }
                    ]
                }
            ],

            groups: {
                42: 'kaspi',
                26: 'IT отдел'
            },
            page_items: [],
            pageSize: 10,
            active: 1,
            show_fields: [],
            all_fields: kpi_fields,
            fields: [],
            groups: [],
            modalAdjustVisibleFields: false,
            all_items: [],
            activities: [],
            non_editable_fields: [
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ]
        }
    },

    created() {
       this.fetchData()
       this.prepareFields()
       this.page_items = this.items.slice(0, this.pageSize);
    },
    methods: {
        
        expand(i) {
            this.page_items[i].expanded = !this.page_items[i].expanded
        },

        expandUser(w, i) {
            this.page_items[i].users[w].expanded = !this.page_items[i].users[w].expanded
        },

        onChangePage(page_items) {
            this.page_items = page_items;
        },

        prepareFields() {
            let visible_fields = [],
                show_fields = this.show_fields;
            
            kpi_fields.forEach((field, i) => {
                if(this.show_fields[field.key] != undefined
                    && this.show_fields[field.key]
                ) {
                    visible_fields.push(field)
                }
            });

            this.fields = kpi_fields;
        }, 

        fetchData() {
            let loader = this.$loading.show();

            axios.post('/statistics/kpi', {
                filters: [] 
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

    } 
}
</script>
