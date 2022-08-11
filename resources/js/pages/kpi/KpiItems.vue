<template>
<div class="kpi-item">

    <table class="table table-inner">
        <thead>
            <tr>
                <th></th>
                <th>Наименование активности</th>
                <th>Вид плана</th>
                <th>Показатели</th>
                <th>Ед. изм.</th>
                <th>Целевое значение на месяц</th>
                <th>Удельный вес, %</th>
                <th>Сумма премии при выполнении плана, KZT</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <tr class="jt-row j-hidden" :class="{'j-hidden': !expanded}" v-for="(item, i) in items" :key="i">
                <td class="first-column"></td>
                <td>
                    <input type="text" class="form-control" v-model="item.name" />
                </td>
                <td class="text-center">
                    <select v-model="item.method" class="form-control">
                        <option v-for="key in Object.keys(methods)" :key="key"
                            :value="key">
                            {{ methods[key] }}
                        </option>
                    </select>
                </td>
                <td class="text-center">
                    <div class="d-flex">
                        <select 
                            v-model="item.source"
                            class="form-control"
                        >
                            <option v-for="key in Object.keys(sources)" :key="key"
                                :value="key">
                                {{ sources[key] }}
                            </option>
                        </select>

                        <select 
                            v-if="item.source == 1"
                            v-model="item.group_id"
                            class="form-control"
                        >
                            <option value="0" selected>-</option>
                            <option v-for="(group, id) in groups" :value="id" :key="id">{{ group }}</option>
                        </select>

                        <select 
                            v-model="item.activity_id"
                            class="form-control"
                        >
                            <option value="0" selected>-</option>
                            <option v-for="activity in grouped_activities[item.source][item.group_id]" :value="activity.id" :key="item.source + ' ' + activity.id">{{ activity.name }}</option>
                        </select>
                    </div>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control" v-model="item.unit" />
                </td>
                <td class="text-center">
                    <input type="number" class="form-control" v-model="item.plan" min="0" />
                </td>
                <td class="text-center">
                    <input type="number" class="form-control" v-model="item.share" min="0"  max="100"/>
                </td>
                <td class="text-center">
                    <input type="number" class="form-control" v-model="item.sum" min="0" />
                </td>
                <td>
                    <i class="fa fa-trash btn btn-primary p-1 mx-2" @click="deleteItem(i)"></i>
                </td>
            </tr>

            <tr>
                <td></td>
                <td colspan="8" class="plus-item" @click="addItem">
                    <div class="px-2 py-1">
                        <i class="fa fa-plus mr-2"></i> <b>Добавить активность</b>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
      
</div>
</template>

<script>
import { componentsPlugin } from 'bootstrap-vue';

export default {
    name: "KpiItems", 
    props: {
        expanded: {
            default: false,
        },
        items: {
            default: []
        },
        activities: {
            default: {}
        },
        groups: {
            default: {}
        }
    },
    data() {
        return {
            active: 1,
            methods: [],
            sources: [],
            grouped_activities: {},
        }
    }, 

    created() {
        this.fillSelectOptions()
        this.fillItems('with_sources_and_group_id');
    },

    methods: {

      
        deleteItem() {
            this.$toast.info('Удалить KPI');
        },


        addItem(i) {
            this.items.push({name:"Показатель"});
        },

        fillSelectOptions() {
            this.setMethods()
            this.setSources()

            let grouped = this.groupBy(this.activities)
            
            let a = {};
            Object(grouped).keys.forEach(id => {
                if(id == 1) {
                    a[id] = this.groupBy(grouped[id])
                } else {
                    a[id][0] = grouped[id];
                }
            })

            console.log(grouped)
        },

        setMethods() {
            this.methods = {
                1: 'сумма минут',
                2: 'среднее значение',
                3: 'сумма, не более',
                4: 'среднее, не более',
                5: 'сумма, не менее',
            };
        },

        setSources() {
            this.sources = {
                0: 'без источника',
                1: 'из показателей отдела',
                2: 'из битрикса',
                3: 'из амосрм',
            }
        },

        groupBy(xs, key) {
            return xs.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {});
        },

        defineSourcesAndGroups(t) {
            this.items.forEach(el => {
                el.source = 0;
                el.group_id = 0;

                if(el.activity_id != 0) {
                    let i = this.activities.findIndex(a => a.id == el.activity_id);
                    if(i != -1) {
                        el.source = this.activities[i].source
                        if(el.source == 1) el.group_id = this.activities[i].group_id
                    }
                }
            });
        }
 
    } 
}
</script>