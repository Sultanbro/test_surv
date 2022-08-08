<template>
<div class="kpi p-3">
    <div class="d-flex mb-2 mt-2 jcfe">
        <button class="btn btn-primary rounded" @click="addKpi">Добавить</button>
    </div>
    
    <!-- table -->
    <div class="j-table">

        <div class="jt-heading">
            <div class="jt-row">

                <div class="jt-head">
                    <i class="fa fa-cogs" @click="adjustFields"></i>
                </div>

                <div class="jt-head" v-for="(field, i) in show_fields" :key="i">
                    {{ field.name }}
                </div>

                <div class="jt-head">Действия</div>
            </div>
        </div>

        <div class="jt-body">
            
            <template v-for="(item, i) in items">
                <div class="jt-row" :key="i">
                    <div class="jt-cell" @click="item.expanded = !item.expanded">{{ i + 1 }}</div>
                    <div class="jt-cell" v-for="(field, f) in show_fields" :key="f">

                        <div v-if="field.key == 'stats'">
                            <i class="fa fa-chart-bar btn btn-primary rounded" @click="showKpiStats(i)"></i>
                        </div>

                        <div v-else>
                            {{ item[field.key] }}
                        </div>

                    </div>
                    <div class="jt-cell">
                        <i class="fa fa-edit mr-1 btn btn-primary rounded" @click="editKpi"></i>
                        <i class="fa fa-delete btn btn-primary rounded" @click="deleteKpi"></i>
                    </div>
                </div>

                <template v-if="item.elements.length > 0">
                
                    <div class="jt-row j-hidden" :class="{'j-hidden': !item.expanded}" v-for="(element, j) in item.elements" :key="i + '-' + j">
                        <div class="jt-cell">{{ j + 1 }}</div>
                        <div class="jt-cell">asdasd</div>
                        <div class="jt-cell">dsfsdr</div>
                        <div class="jt-cell">rtyr</div>
                        <div class="jt-cell">ghjg</div>
                        <div class="jt-cell">;lk;k</div>
                        <div class="jt-cell">bnm</div>
                        <div class="jt-cell">haopi</div>
                    </div>
                </template>
               


            </template>

         

        </div>  
    </div>


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
                {name: 'IT отдел', elements: [], expanded: false},
                {name: 'Али Акпанов', elements: [], expanded: false},
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