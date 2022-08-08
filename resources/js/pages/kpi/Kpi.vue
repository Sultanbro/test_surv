<template>
<div class="kpi p-3">
    <div class="d-flex mb-2 mt-2 jcfe">
        <button class="btn btn-primary rounded" @click="addKpi">Добавить</button>
    </div>
    


    <!-- table -->

    <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <td>
                    <i class="fa fa-cogs" @click="adjustFields"></i>
                </td>

                <td v-for="(field, i) in show_fields" :key="i">
                    {{ field.name }}
                </td>

                <td>Действия</td>

            </tr>

        </thead>

        <tbody>

            <template v-for="(item, i) in items">
                <tr class="jt-row" :key="i">
                    <td class="jt-cell" @click="item.expanded = !item.expanded">{{ i + 1 }}</td>
                    <td class="jt-cell" v-for="(field, f) in show_fields" :key="f">

                        <div v-if="field.key == 'stats'">
                            <i class="fa fa-chart-bar btn btn-primary rounded" @click="showKpiStats(i)"></i>
                        </div>

                        <div v-else>
                            {{ item[field.key] }}
                        </div>

                    </td>
                    <td class="jt-cell">
                        <i class="fa fa-edit mr-1 btn btn-primary rounded" @click="editKpi"></i>
                        <i class="fa fa-delete btn btn-primary rounded" @click="deleteKpi"></i>
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
                                            <td class="jt-cell">{{ j + 1 }}</td>
                                            <td class="jt-cell">asdasd</td>
                                            <td class="jt-cell">dsfsdr</td>
                                            <td class="jt-cell">rtyr</td>
                                            <td class="jt-cell">ghjg</td>
                                            <td class="jt-cell">;lk;k</td>
                                            <td class="jt-cell">bnm</td>
                                            <td class="jt-cell">haopi</td>
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
                {name: 'IT отдел', elements: [{}, {}], expanded: false},
                {name: 'Али Акпанов', elements: [{}, {}], expanded: false},
                {name: 'Руслан Ташметов', elements: [], expanded: false},
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
                type: 'superselect'
            });

            fields.push({
                name: 'Выполнение KPI от 80-99%',
                key: 'completed_80',
                visible: true,
                type: 'number'
            });

            fields.push({
                name: 'Выполнение KPI от 100%',
                key: 'completed_100',
                visible: true,
                type: 'number'
            });

            fields.push({
                name: 'Нижний порог отсечения премии, %',
                key: 'lower_limit',
                visible: true,
                type: 'number'
            });

            fields.push({
                name: 'Верхний порог отсечения премии, %',
                key: 'upper_limit',
                visible: true,
                type: 'number'
            });
            
            fields.push({
                name: 'Статистика',
                key: 'stats',
                visible: true,
                type: 'number'
            });

            fields.push({
                name: 'Дата создания',
                key: 'created_at',
                visible: true,
                type: 'date'
            });

            fields.push({
                name: 'Дата изменения',
                key: 'updated_at',
                visible: true,
                type: 'date'
            });

            fields.push({
                name: 'Постановщик',
                key: 'created',
                visible: true,
                type: 'text'
            });

            fields.push({
                name: 'Изменил',
                key: 'updated',
                visible: true,
                type: 'text'
            });

            this.show_fields = fields;
        },

        addKpi() {
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