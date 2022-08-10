<template>
<div class="kpi-item p-3">

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
                <td></td>
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
                            v-model="item.activity_id"
                            class="form-control"
                        >
                            <option value="0" selected>-</option>
                            <option v-for="activity in activities[item.source]" :value="activity.id" :key="item.source + ' ' + activity.id">{{ activity.name }}</option>
                        </select>
                    </div>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control" v-model="item.unit" />
                </td>
                <td class="text-center">
                    <input type="text" class="form-control" v-model="item.plan" />
                </td>
                <td class="text-center">
                    <input type="text" class="form-control" v-model="item.share" />
                </td>
                <td class="text-center">
                    <input type="text" class="form-control" v-model="item.sum" />
                </td>
                <td>
                    <i class="fa fa-trash btn btn-primary p-1" @click="deleteItem(i)"></i>
                </td>
            </tr>

            <tr>
                <td></td>
                <td colspan="8" class="plus-item" @click="addItem">
                    <div>
                        <i class="fa fa-plus mr-2"></i> <b>Добавить активность</b>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
      
</div>
</template>

<script>
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
    },
    data() {
        return {
            active: 1,
            methods: [],
            sources: [],
        }
    }, 

    created() {
        this.fillSelectOptions()
    },

    methods: {

      
        deleteItem() {
            this.$toast.info('Удалить KPI');
        },


        addItem(i) {
            this.items[i].elements.push({name:"Показатель"});
        },

        fillSelectOptions() {
            this.setMethods()
            this.setSources()
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
 
    } 
}
</script>