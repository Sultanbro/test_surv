<template>
<div class="t-stats">

    <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <th class="first-column"></th>

                <th class="w">KPI</th>
                
                <th class="px-2" v-if="editable">Средний %</th>
                <th class="px-2" v-if="!editable">Нижний порог отсчета</th>
                <th class="px-2" v-if="!editable">Верхний порог отсчета</th>
                <th class="px-2" v-if="!editable">При выполнении на 80-99%</th>
                <th class="px-2" v-if="!editable">При выполнении на 100%</th>

                <th v-if="editable"></th>

            </tr>

        </thead>
 
        <tbody>

            <template v-for="(wrap_item, w) in items">
                
                <tr class="main-row">

                    <td  @click="wrap_item.expanded = !wrap_item.expanded" class="pointer py-1">
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
                    <td class="px-2 py-1" v-if="editable">{{ wrap_item.avg }}%</td>
                    <td class="px-2 py-1" v-if="!editable">{{ wrap_item.lower_limit }}%</td>
                    <td class="px-2 py-1" v-if="!editable">{{ wrap_item.upper_limit }}%</td>
                    <td class="px-2 py-1" v-if="!editable">{{ wrap_item.completed_80 }}</td>
                    <td class="px-2 py-1" v-if="!editable">{{ wrap_item.completed_100 }}</td>
                    <td v-if="editable"></td>
                        
                </tr>

                <template v-if="wrap_item.users != undefined && wrap_item.users.length > 0">
                    <tr class="collapsable" :class="{'active': wrap_item.expanded || !editable }" :key="w + 'a'">
                        <td :colspan="editable ? 3 : 6">
                            <div class="table__wrapper">
                            <table class="child-table">
                                <template v-for="(user, i) in wrap_item.users">
                                    <tr :key="i" class="child-row" v-if="editable">
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
                                                        :completed_80="wrap_item.completed_80"
                                                        :completed_100="wrap_item.completed_100"
                                                        :lower_limit="wrap_item.lower_limit"
                                                        :upper_limit="wrap_item.upper_limit"
                                                        :editable="editable"
                                                        :kpi_page="false"
                                                        @recalced="countAvg"
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
      
</div>
</template>

<script> 
import {kpi_fields} from "./kpis.js";

export default {
    name: "StatsTable", 
    props: {
        items: {
            default: [],
        },
        activities: {
            default: [],
        },
        groups: {
            default: [],
        },
        editable: {
            default: false
        } 
    },

    watch: {
        show_fields: {
            handler: function (val) {
                localStorage.kpi_show_fields = JSON.stringify(val);
                this.prepareFields();
            },
            deep: true
        },
    },

    data() {
        return {
            show_fields: [],
            all_fields: kpi_fields,
            fields: [],
            non_editable_fields: [
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ]
        }
    },

    created() {
       this.prepareFields()
    },

    methods: {
        
        expand(w) {
            this.items[w].expanded = !this.items[w].expanded
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

        countAvg() {
            this.items.forEach(kpi => {
                kpi.avg = 0;
                // let avg = 0;
                // let count = 0;
                // let sum = 0;

                // kpi.users.forEach(user => {
                //     user.items.forEach(item => {
                //         sum += Number(item.percent)
                //         count++
                //     });
                // });

                // console.log('avg', sum , count)

                // kpi.avg = count > 0 ? Number(Number(sum / count * 100).toFixed(2)) : 0;
            });
        }

    } 
}
</script>


<style scoped lang="scss">
.child-table {width:100%}
.profile-salary-info {
    .table td, .table th, .table thead th {
        vertical-align: middle;
        min-width: 42px;
        font-size: 13px;
        padding: 5px 12px;
        text-align: center;
    }

    .j-table .table-inner {
        background: #e9eef3;
    }
}
</style>