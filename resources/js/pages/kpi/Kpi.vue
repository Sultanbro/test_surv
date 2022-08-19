<template>
<div class="kpi p-3">

    <!-- top line -->
    <div class="d-flex mb-2 mt-2 jcsb aifs">
        
        <div class="d-flex mr-2">
            <div class="d-flex aifs mr-2">
                <span>Показывать:</span>
                <input type="number" min="1" max="100" v-model="pageSize" class="form-control ml-2" />
            </div>
            <super-filter 
                :ref="'filter'"
                :groups="groups"
                @apply="fetchKPI"
                @search-text-changed="onSearch"
            >
            </super-filter>
            <div class="ml-2"> 
                Найдено: {{ items.length }}
            </div>
        </div>

        <button class="btn rounded btn-outline-success" @click="addKpi">
            <i class="fa fa-plus mr-2"></i>
            <span>Добавить</span>
        </button>
    </div>
    
    <!-- table -->

    <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <th class="first-column">
                    <i class="fa fa-cogs" @click="adjustFields"></i>
                </th>

                <th v-for="(field, i) in fields" :key="i" :class="field.class">
                    {{ field.name }}
                </th>

                <th>Действия</th>

            </tr>

        </thead>

        <tbody>

            <template v-for="(item, i) in page_items">
                <tr :key="i">
                    <td  @click="expand(i)" class="pointer">
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ i + 1 }}</span>
                        </div>
                    </td>
                    <td  v-for="(field, f) in fields" :key="f">

                        <div v-if="field.key == 'target'" :class="field.class">
                            <superselect
                                v-if="item.target == null"
                                class="w-full" 
                                :values="[]" 
                                :single="true"
                                @choose="(target) => item.target = target"
                                :key="i" /> 
                            <div v-else class="d-flex aic">
                                <i class="fa fa-user ml-2" v-if="item.target.type == 1"></i> 
                                <i class="fa fa-users ml-2" v-if="item.target.type == 2"></i> 
                                <i class="fa fa-briefcase ml-2" v-if="item.target.type == 3"></i> 
                                <span class="ml-2">{{ item.target.name }}</span>
                                
                            </div>
                        </div>

                        <div v-else-if="field.key == 'stats'" :class="field.class">
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

                <template v-if="item.items !== undefined && item.items.length > 0">
                    <tr class="collapsable" :class="{'active': item.expanded}" :key="i + 'a'">
                        <td :colspan="fields.length + 2">
                            <div class="table__wrapper">
                                <kpi-items
                                    :items="item.items"
                                    :expanded="item.expanded"
                                    :activities="activities"
                                    :groups="groups"
                                    :completed_80="item.completed_80"
                                    :completed_100="item.completed_100"
                                    :lower_limit="item.lower_limit"
                                    :upper_limit="item.upper_limit"
                                >
                                </kpi-items>
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


    <!-- modal Adjust Visible fields -->
    <b-modal 
        v-model="modalAdjustVisibleFields"
        title="Настройка списка «KPI»"
        @ok="modalAdjustVisibleFields = !modalAdjustVisibleFields"
        ok-text="Закрыть"
        size="lg">
     
      <div class="row">

        <div class="col-md-4 mb-2">
           <b-form-checkbox
              v-model="show_fields.updated_at"
              :value="true"
              :unchecked-value="false"
              >
              Дата изменения
          </b-form-checkbox>
        
        </div> 
        <div class="col-md-4 mb-2">
           <b-form-checkbox
              v-model="show_fields.created_by"
              :value="true"
              :unchecked-value="false"
              >
              Постановщик
          </b-form-checkbox>
        
          
        </div>  

        <div class="col-md-4 mb-2">
          <b-form-checkbox
              v-model="show_fields.updated_by"
              :value="true"
              :unchecked-value="false"
              >
              Изменил
          </b-form-checkbox>
       
        </div>
      </div>  
    </b-modal>

</div>
</template>

<script>
export default {
    name: "KPI", 
    props: {
        
    },
    watch: {
        show_fields: {
            handler: function (val) {
                localStorage.kpi_show_fields = JSON.stringify(val);
                this.prepareFields();
            },
            deep: true
        },
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
            active: 1,
            show_fields: [],
            fields: [],
            groups: [],
            searchText: '',
            modalAdjustVisibleFields: false,
            page_items: [],
            pageSize: 10,
            paginationKey: 1,
            items: [],
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
        this.fetchKPI()
        // this.fetchActivities()
    
        this.setDefaultShowFields()
        this.prepareFields(); 
        this.addStatusToItems(); 

    //     this.items.forEach(el => {
    //     let a = Math.floor(Math.random() * 3) + 1

    //     for(let i=1;i<=a;i++) {
    //         el.items.push({
    //             name: 'Активность',
    //             activity_id: 0,
    //             unit: '%',
    //             method: 1,
    //             source: 0,
    //             plan: '90%',
    //             share: '40%',
    //             sum: '10000',
    //             fact: 100,
    //             source: 1,
    //             group_id: 0
    //         });
    //     }

    //     this.groups = {
    //         23: 'Адм сотрудники',
    //         26: 'It отдел',
    //         42: 'Каспи',
    //     };
     
    //    })
    },
    methods: {
        
        expand(i) {
            this.page_items[i].expanded = !this.page_items[i].expanded
        },

        onChangePage(page_items) {
            this.page_items = page_items;
        },

        fetchKPI(filter = null) {
            let loader = this.$loading.show();

            axios.post('/kpi/get', {
                filters: filter 
            }).then(response => {
                
                this.items = response.data.kpis;
                this.activities = response.data.activities;
                this.groups = response.data.groups;

                this.items.forEach(el => el.expanded = false);
                this.page_items = this.items.slice(0, this.pageSize);

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        setDefaultShowFields() {
        
            if(localStorage.kpi_show_fields) {
                this.show_fields = JSON.parse(localStorage.getItem('kpi_show_fields'));
            } else {
                this.show_fields = { // Какие поля показывать
                    target: true,
                    completed_80: true,
                    completed_100: true,
                    lower_limit: true,
                    upper_limit: true,
                    stats: true,
                    created_at: true,
                    updated_at: true,
                    created_by: true,
                    updated_by: true,
                };
            }

        },

        adjustFields() {
            this.modalAdjustVisibleFields = true;
        },

        addStatusToItems() {
            this.items.forEach(el => {

                el.items.forEach(a => {
                    a.source = 0
                    a.group_id = 0
                });

                el.on_edit = false
            
            });
        },

        prepareFields() {
            let fields = [];
            
            if(this.show_fields['target']) {
                fields.push({
                    name: 'Кому',
                    key: 'target',
                    visible: true,
                    type: 'superselect',
                    class: 'text-left w-230 '
                });
            }
            
            if(this.show_fields['completed_80']) {
                fields.push({
                    name: 'Выполнение KPI от 80-99%',
                    key: 'completed_80',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }

            if(this.show_fields['completed_100']) {
                fields.push({
                    name: 'Выполнение KPI от 100%',
                    key: 'completed_100',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['lower_limit']) {
                fields.push({
                    name: 'Нижний порог отсечения премии, %',
                    key: 'lower_limit',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['upper_limit']) {
                fields.push({
                    name: 'Верхний порог отсечения премии, %',
                    key: 'upper_limit',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['stats']) {
                fields.push({
                    name: 'Статистика',
                    key: 'stats',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['created_at']) {
                fields.push({
                    name: 'Дата создания',
                    key: 'created_at',
                    visible: true,
                    type: 'date',
                    class: 'text-center'
                });
            }

            if(this.show_fields['updated_at']) {
                fields.push({
                    name: 'Дата изменения',
                    key: 'updated_at',
                    visible: true,
                    type: 'date',
                    class: 'text-center'
                });
            }
            if(this.show_fields['created_by']) {
                fields.push({
                    name: 'Постановщик',
                    key: 'created_by',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }

            if(this.show_fields['updated_by']) {
                fields.push({
                    name: 'Изменил',
                    key: 'updated_by',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }

            this.fields = fields;
        },

        addKpi() {
            this.items.unshift({
                id: 0,
                target: null,
                completed_80: 10000,
                completed_100: 30000,
                lower_limit: 80,
                upper_limit: 100,
                stats: 3,
                created_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                updated_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                created_by: 'Али Акпанов',
                updated_by: 'Али Акпанов',
                items: [{
                    sum: 0,
                    method: 1,
                    name: 'Активность',
                    activity_id: 0,
                    plan: 0,
                    share: 0
                }], 
                expanded: false
            });

            this.$toast.info('Добавить KPI');
        },

        saveKpi(i) {
            let loader = this.$loading.show();
            let item = this.items[i]
            let method = this.items[i].id == 0 ? 'save' : 'update';

            if(item.target == null) {
                this.$toast.error('Выберите Кому назначить KPI!');
                return;
            }
            
            let fields = {
                id: item.id,
                targetable_id: item.target.id,
                targetable_type: item.target.type,
                completed_80: item.completed_80,
                completed_100: item.completed_100,
                upper_limit: item.upper_limit,
                lower_limit: item.lower_limit,
                items: item.items 
            };
 
            let req = this.items[i].id == 0 
                ? axios.post('/kpi/' + method, fields)
                : axios.put('/kpi/' + method, fields);

            req.then(response => {
                
                let kpi = response.data.kpi;
                
                item.id = kpi.id;
                item.items.forEach((el, index) => {
                    el.id = kpi.items[index].id
                });

                this.$toast.info('KPI Сохранен!');
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        }, 

        deleteKpi(i) {
            let loader = this.$loading.show();
            let item = this.items[i]

            if(!confirm('Вы уверены?')) {
                return;
            }

            if(item.id == 0) {
                this.items.splice(i) // maybe will be error cause of page_items
                this.$toast.info('KPI Удален!');
                return;
            }

            axios.delete('/kpi/delete', {
                id: item.id
            }).then(response => {

                this.items.splice(i) // maybe will be error cause of page_items

                this.$toast.info('KPI Удален!');
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        showStat() {
            this.$toast.info('Показать статистику');
        },

        onSearch(text) { 
            this.searchText = text;
            if(this.searchText == '') {
                //this.filtered_items = this.items; 
            } else {
                // this.filtered_items = this.items.filter((el, index) => {
                // let has = false;
                // el.targets.forEach(target => {
                //     if(target.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1) has = true;
                // });

                // el.groups.forEach(target => {
                //     if(target.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1) has = true;
                // });

                // el.roles.forEach(target => {
                //     if(target.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1) has = true;
                // });

                // return has; 
                //}); 
            }
        },

        validate(value, field) {
            value = abs(Number(value));
            if(isNaN(value) || isFinite(value)) {
                value = 0;
            }

            if(['lower_limit', 'upper_limit'].includes(field) && value > 100) {
                value = 100;
            }
        }
    },
 
}
</script>