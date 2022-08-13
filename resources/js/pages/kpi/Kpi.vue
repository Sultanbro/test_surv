<template>
<div class="kpi p-3">

    <!-- top line -->
    <div class="d-flex mb-2 mt-2 jcfe">
        <div class="d-flex">
           
            <super-filter 
                :ref="'filter'"
                :url="'kpi/get'" 
                @search-text-changed="onSearch"
            >
            </super-filter>
        </div>
        <button class="btn btn-primary rounded" @click="addKpi">Добавить</button>
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

            <template v-for="(item, i) in items">
                <tr :key="i">
                    <td  @click="item.expanded = !item.expanded" class="pointer">
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ i + 1 }}</span>
                        </div>
                    </td>
                    <td  v-for="(field, f) in fields" :key="f">

                        <div v-if="field.key == 'target'" :class="field.class">
                            <superselect
                                class="w-full" 
                                :values="item.target == null ? [] : [item.target]" 
                                :single="true"
                                :ask_before_delete="'Вы уверены, что хотите поменять Кому назначен KPI?'"
                                :one_choice="true"
                                :key="i" /> 
                        </div>

                        <div v-else-if="field.key == 'stats'" :class="field.class">
                            <i class="fa fa-chart-bar btn btn-primary p-1" @click="showKpiStats(i)"></i>
                        </div>

                        <div v-else-if="non_editable_fields.includes(field.key)" :class="field.class">
                           {{ item[field.key] }}
                        </div>

                        <div v-else :class="field.class">
                            <input type="text" class="form-control" v-model="item[field.key]" /> 
                        </div>

                    </td>
                    <td >
                        <i class="fa fa-save ml-2 mr-1 btn btn-primary p-1" @click="saveKpi(i)"></i>
                        <i class="fa fa-trash btn btn-danger p-1" @click="deleteKpi(i)"></i>
                    </td>
                </tr>

                <template v-if="item.elements !== undefined && item.elements.length > 0">
                    <tr class="collapsable" :class="{'active': item.expanded}" :key="i + 'a'">
                        <td :colspan="fields.length + 2">
                            <div class="table__wrapper">
                                <kpi-items
                                    :items="item.elements"
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
            items: [
                {
                    id: 1,
                    target: {
                        name: 'IT отдел',
                        id: 26,
                        type: 2
                    },
                    completed_80: 10000,
                    completed_100: 20000,
                    lower_limit: 80,
                    upper_limit: 100,
                    created_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                    updated_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                    created_by: 'Ходжа Абулхаир',
                    updated_by: 'Али Акпанов',
                    elements: [], 
                    expanded: false
                },
                {
                    target: {
                        name: 'Али Акпанов',
                        id: 5,
                        type: 1
                    },
                    completed_80: 10000,
                    completed_100: 30000,
                    lower_limit: 80,
                    upper_limit: 100,
                    stats: 2,
                    created_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                    updated_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                    created_by: 'Ходжа Абулхаир',
                    updated_by: 'Али Акпанов',
                    elements: [], 
                    expanded: false
                },
            ],
            activities: [
               {
                    id: 1,
                    name: 'TEst 1',
                    source: 1,
                    group_id: 42
                },
                {
                    id: 2,
                    name: 'Test 2',
                    source: 1,
                    group_id: 42
                },
                {
                    id: 3,
                    name: 'Grouper 1',
                    source: 0
                },
                {
                    id: 4,
                    name: 'Groups 2',
                    source: 0
                },
            ],
            non_editable_fields: [
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ]
        }
    }, 

    created() {
       // this.fetchKPI()
       // this.fetchActivities()
    
        this.setDefaultShowFields()
        this.prepareFields(); 
        this.addStatusToItems(); 

        this.items.forEach(el => {
        let a = Math.floor(Math.random() * 3) + 1

        for(let i=1;i<=a;i++) {
            el.elements.push({
                name: 'Активность',
                activity_id: 0,
                unit: '%',
                method: 1,
                source: 0,
                plan: '90%',
                share: '40%',
                sum: '10000',
                fact: 100,
                source: 1,
                group_id: 0
            });
        }

        this.groups = {
            23: 'Адм сотрудники',
            26: 'It отдел',
            42: 'Каспи',
        };
     
       })
    },
    methods: {

        fetchKPI() {
            let loader = this.$loading.show();

            axios.get('/kpi/get').then(response => {
                
                this.items = repsonse.data.items;
                this.activities = repsonse.data.activities;
                this.groups = repsonse.data.groups;

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
            this.items.forEach(el => el.on_edit = false);
        },

        prepareFields() {
            let fields = [];
            
            if(this.show_fields['target']) {
                fields.push({
                    name: 'Кому',
                    key: 'target',
                    visible: true,
                    type: 'superselect',
                    class: 'text-left'
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
                elements: [{}, {}], 
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

            axios.post('/kpi/' + method, {
                kpi: item
            }).then(response => {
                
                let kpi = response.data.kpi;
                
                item.id = kpi.id;
                item.elements.forEach((el, index) => {
                    el.id = kpi.elements[index]
                });

                this.$toast.info('KPI Сохранен!');
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        }, 

        deleteKpi() {
            this.$toast.info('Удалить KPI');
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
        }
     },
 
    } 
}
</script>