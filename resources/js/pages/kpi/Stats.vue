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

                <th class="w-full">Сотрудник</th>
               
                <th></th>

            </tr>

        </thead>

        <tbody>

            

           
            <template v-for="(wrap_item, w) in page_items">
                
                <tr class="main-row">

                    <td  @click="expand(w)" class="pointer">
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="wrap_item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ w + 1 }}</span>
                        </div>
                    </td>
                    <td>{{ wrap_item.name }}</td>
                    <td></td>
                        
                </tr>

                <template v-if="wrap_item.kpis != undefined && wrap_item.kpis.length > 0">
                    <tr class="collapsable" :class="{'active': wrap_item.expanded}" :key="w + 'a'">
                        <td :colspan="fields.length + 2">
                            <div class="table__wrapper">
                            <table>
                                <template v-for="(item, i) in wrap_item.kpis">
                                    <tr :key="i">
                                        <td  @click="expand(i)" class="pointer">
                                            <span class="ml-2">{{ i + 1 }}</span>
                                        </td>
                                        <td  v-for="(field, f) in fields" :key="f" :class="field.class"> 

                                         

                                            <div v-if="field.key == 'stats'" :class="field.class">
                                                <i class="fa fa-chart-bar btn btn-primary p-1" @click="showKpiStats(i)"></i>
                                            </div>

                                            <div v-else-if="non_editable_fields.includes(field.key)" :class="field.class">
                                            {{ item[field.key] }}
                                            </div>

                                            <div v-else :class="field.class">
                                                <input type="text" class="form-control" v-model="item[field.key]" @change="validate(item[field.key], field.key)" /> 
                                            </div>

                                        </td>
                                        <td >
                                            <i class="fa fa-save ml-2 mr-1 btn btn-success p-1" @click="saveKpi(i)"></i>
                                            <i class="fa fa-trash btn btn-danger p-1" @click="deleteKpi(i)"></i>
                                        </td>
                                    </tr>

                                    <template v-if="item.items !== undefined">
                                        <tr class="collapsable" :class="{'active': item.expanded}" :key="i + 'a'">
                                            <td :colspan="fields.length + 2">
                                                <div class="table__wrapper">
                                                    <kpi-items
                                                        :kpi_id="item.id"
                                                        :items="item.items" 
                                                        :expanded="true"
                                                        :activities="activities"
                                                        :groups="groups"
                                                        :completed_80="item.completed_80"
                                                        :completed_100="item.completed_100"
                                                        :lower_limit="item.lower_limit"
                                                        :upper_limit="item.upper_limit"
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

            items: [
                {
                    expanded: false,
                    user_id: 123,
                    type: 'user',
                    name: 'Али Акпанов',
                    kpis: [
                        {
                            id: 2,
                            completed_80: 20000,
                            completed_100: 30000,
                            lower_limit: 80,
                            upper_limit: 100,
                            kpi_items: [
                                {
                                    id: 13,
                                    method: 1,
                                    name: 'Кол=во минут',
                                    activity_id: 0,
                                    plan: 450,
                                    share: 100,
                                    fact: 355, // факт с user_stat
                                }
                            ]
                        },
                        {
                            id: 2,
                            completed_80: 20000,
                            completed_100: 30000,
                            lower_limit: 80,
                            upper_limit: 100,
                            kpi_items: []
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
       // this.fetchData()
       this.prepareFields()
       this.page_items = this.items.slice(0, this.pageSize);
    },
    methods: {
        
        expand(i) {
            this.page_items[i].expanded = !this.page_items[i].expanded
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
    } 
}
</script>
