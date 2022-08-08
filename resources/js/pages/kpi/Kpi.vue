<template>
<div class="kpi p-3">
    <div class="d-flex mb-2 mt-2 jcfe">
        <button class="btn btn-primary rounded" @click="addKpi">Добавить</button>
    </div>
    


    <!-- table -->

    <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <th>
                    <i class="fa fa-cogs" @click="adjustFields"></i>
                </th>

                <th v-for="(field, i) in show_fields" :key="i" :class="field.class">
                    {{ field.name }}
                </th>

                <th>Действия</th>

            </tr>

        </thead>

        <tbody>

            <template v-for="(item, i) in items">
                <tr :key="i">
                    <td  @click="item.expanded = !item.expanded">{{ i + 1 }}</td>
                    <td  v-for="(field, f) in show_fields" :key="f">

                        <div v-if="field.key == 'stats'" :class="field.class">
                            <i class="fa fa-chart-bar btn btn-primary p-1" @click="showKpiStats(i)"></i>
                        </div>

                        <div v-else :class="field.class">
                            {{ item[field.key] }}
                        </div>

                    </td>
                    <td >
                        <i class="fa fa-edit mr-1 btn btn-primary p-1" @click="editKpi"></i>
                        <i class="fa fa-trash btn btn-primary p-1" @click="deleteKpi"></i>
                    </td>
                </tr>

                <template v-if="item.elements !== undefined && item.elements.length > 0">
                    <tr class="collapsable" :class="{'active': item.expanded}" :key="i + 'a'">
                        <td :colspan="show_fields.length + 2">
                            <div class="table__wrapper">
                                <table class="table table-inner">
                                    <thead>
                                        <tr>
                                            <td>12312</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                            <td>123123</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="jt-row j-hidden" :class="{'j-hidden': !item.expanded}" v-for="(element, j) in item.elements" :key="i + '-' + j">
                                            <td >{{ j + 1 }}</td>
                                            <td >asdasd</td>
                                            <td >dsfsdr</td>
                                            <td >rtyr</td>
                                            <td >ghjg</td>
                                            <td >;lk;k</td>
                                            <td >bnm</td>
                                            <td >haopi</td>
                                        </tr>
                                    </tbody>
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
export default {
    name: "KPI", 
    props: {
        
    },
    data() {
        return {
            active: 1,
            show_fields: [],
            items: [
                {
                    target: 'IT отдел',
                    completed_80: 10000,
                    completed_100: 20000,
                    lower_limit: 80,
                    upper_limit: 100,
                    stats: 1,
                    created_at: new Date(),
                    updated_at: new Date(),
                    created: 'Ходжа Абулхаир',
                    updated: 'Али Акпанов',
                    elements: [{}, {}], 
                    expanded: false
                },
                {
                    target: 'Али Акпанов',
                    completed_80: 10000,
                    completed_100: 30000,
                    lower_limit: 80,
                    upper_limit: 100,
                    stats: 2,
                    created_at: new Date(),
                    updated_at: new Date(),
                    created: 'Ходжа Абулхаир',
                    updated: 'Али Акпанов',
                    elements: [{}, {}], 
                    expanded: false
                },
            ]
        }
    }, 

    created() {
       // this.fetchData()
       this.prepareFields(); 
       this.addStatusToItems(); 
    },
    methods: {

        fetchData() {
            let loader = this.$loading.show();

            axios.post('/kpi/' + this.page, {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
            }).then(response => {
              
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },
        
        adjustFields() {
            this.$toast.info('Настроить поля');
        },

        addStatusToItems() {
            this.items.forEach(el => el.on_edit = false);
        },

        prepareFields() {
            let fields = [];
            
            fields.push({
                name: 'Кому',
                key: 'target',
                visible: true,
                type: 'superselect',
                class: 'text-left'
            });

            fields.push({
                name: 'Выполнение KPI от 80-99%',
                key: 'completed_80',
                visible: true,
                type: 'number',
                class: 'text-center'
            });

            fields.push({
                name: 'Выполнение KPI от 100%',
                key: 'completed_100',
                visible: true,
                type: 'number',
                class: 'text-center'
            });

            fields.push({
                name: 'Нижний порог отсечения премии, %',
                key: 'lower_limit',
                visible: true,
                type: 'number',
                class: 'text-center'
            });

            fields.push({
                name: 'Верхний порог отсечения премии, %',
                key: 'upper_limit',
                visible: true,
                type: 'number',
                class: 'text-center'
            });
            
            fields.push({
                name: 'Статистика',
                key: 'stats',
                visible: true,
                type: 'number',
                class: 'text-center'
            });

            fields.push({
                name: 'Дата создания',
                key: 'created_at',
                visible: true,
                type: 'date',
                class: 'text-center'
            });

            fields.push({
                name: 'Дата изменения',
                key: 'updated_at',
                visible: true,
                type: 'date',
                class: 'text-center'
            });

            fields.push({
                name: 'Постановщик',
                key: 'created',
                visible: true,
                type: 'text',
                class: 'text-center'
            });

            fields.push({
                name: 'Изменил',
                key: 'updated',
                visible: true,
                type: 'text',
                class: 'text-center'
            });

            this.show_fields = fields;
        },

        addKpi() {
            this.items.unshift({
                target: 'Test target',
                completed_80: 10000,
                completed_100: 30000,
                lower_limit: 80,
                upper_limit: 100,
                stats: 3,
                created_at: new Date(),
                updated_at: new Date(),
                created: 'Али Акпанов',
                updated: 'Али Акпанов',
                elements: [{}, {}], 
                expanded: false
            });

            this.$toast.info('Добавить KPI');
        },

        editKpi() {
            this.$toast.info('Редактировать KPI');
        }, 

        deleteKpi() {
            this.$toast.info('Удалить KPI');
        },

        showStat() {
            this.$toast.info('Показать статистику');
        }
 
    } 
}
</script>