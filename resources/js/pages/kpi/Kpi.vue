<template>
<div class="kpi px-3 py-1">

    <!-- top line -->
    <div class="d-flex mb-2 mt-2 jcsb aifs">
        
         <div class="d-flex aic mr-2">
            <div class="d-flex aic mr-2">
                <span>Показывать:</span>
                <input type="number" min="1" max="100" v-model="pageSize" class="form-control ml-2 input-sm" />
            </div>
            <input 
                class="searcher mr-2 input-sm"
                v-model="searchText"
                type="text"
                placeholder="Поиск по совпадениям..."
                @keyup="onSearch"
            >
            <span class="ml-2"> 
                Найдено: {{ items.length }}
            </span>
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
                    <td  v-for="(field, f) in fields" :key="f" :class="field.class"> 

                        <div v-if="field.key == 'target'" >
                            <superselect
                                v-if="item.target == null || item.id == 0"
                                class="w-full" 
                                :values="item.target == null ? [] : [item.target]" 
                                :single="true"
                                @choose="(target) => item.target = target"
                                @remove="() => item.target = null"
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

                        <div v-else-if="field.key == 'created_by' && item.creator != null">
                            {{ item.creator.last_name + ' ' + item.creator.name }}
                        </div>

                        <div v-else-if="field.key == 'updated_by' && item.updater != null">
                            {{ item.updater.last_name + ' ' + item.updater.name }}
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
                                    :expanded="item.expanded"
                                    :activities="activities"
                                    :groups="groups"
                                    :completed_80="item.completed_80"
                                    :completed_100="item.completed_100"
                                    :lower_limit="item.lower_limit"
                                    :upper_limit="item.upper_limit"
                                    :editable="true"
                                    :kpi_page="true"
                                    :key="item.items"
                                />
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

         <div class="col-md-4 mb-2" v-for="(field, f) in all_fields">
            <b-form-checkbox
                v-model="show_fields[field.key]"
                :value="true"
                :unchecked-value="false"
            >
                {{ field.name }}
            </b-form-checkbox>
        </div>

      </div>  
    </b-modal>

</div>
</template>

<script>
import {kpi_fields, newKpi} from "./kpis.js";
import {findModel, groupBy} from "./helpers.js";

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
            all_fields: kpi_fields,
            fields: [],
            uri: 'kpi',
            groups: [],
            searchText: '',
            modalAdjustVisibleFields: false,
            page_items: [],
            pageSize: 100,
            paginationKey: 1,
            items: [],
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
        this.fetchKPI()
    
        this.setDefaultShowFields()
        this.prepareFields(); 
        this.addStatusToItems(); 
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

            axios.post(this.uri + '/' + 'get', {
                filters: filter 
            }).then(response => {
                
                this.items = response.data.kpis;
                this.all_items = response.data.kpis;
                this.activities = response.data.activities;
                this.groups = response.data.groups;

                this.page_items = this.items.slice(0, this.pageSize);

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        setDefaultShowFields() {
            let obj = {}; // Какие поля показывать
            kpi_fields.forEach(field => obj[field.key] = true); 

            if(localStorage.kpi_show_fields) {
                this.show_fields = JSON.parse(localStorage.getItem('kpi_show_fields'));
                if(this.show_fields == null) this.show_fields = obj
            } else {
                this.show_fields = obj
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
            let visible_fields = [],
                show_fields = this.show_fields;
            
            kpi_fields.forEach((field, i) => {
                if(this.show_fields[field.key] != undefined
                    && this.show_fields[field.key]
                ) {
                    visible_fields.push(field)
                }
            });

            this.fields = visible_fields;
        },

        addKpi() {
            this.items.unshift(newKpi());
            this.$toast.info('Добавлен KPI');
        },

        validateMsg(item) {
            let msg = '';

            if(item.target == null)    msg = 'Выберите Кому назначить'
            
            let share = 0;

            console.log(item);

            if(item.items != undefined) {

                item.items.every((el, i) => {

                    share += Math.abs(el.share);

                    if(el.name.length <= 1) {
                        msg = 'Заполните название активности #' + (i+1);
                        return false;
                    }

                    if(el.activity_id == 0 || el.activity_id == undefined) {
                        msg = 'Выберите показатель #' + (i+1);
                        return false;
                    }

                   
                    if(Number(el.plan) <= 0) {
                        msg = 'План должен быть больше 0 #' + (i+1);
                        return false;
                    }


                    return true;
                });
            }
            
            
            if(share > 100) {
                msg = 'Доля активностей должна быть не более 100%';
            }
            
            return msg;
        },

        saveKpi(i) {

            let item = this.items[i]
            let method = this.items[i].id == 0 ? 'save' : 'update';

            /**
             * validate item
             */
            let not_validated_msg = this.validateMsg(item);
            if(not_validated_msg != '') {
                this.$toast.error(not_validated_msg)
                return;
            }

            
            let loader = this.$loading.show();

            let fields = {
                id: item.id,
                targetable_id: item.target.id,
                targetable_type: findModel(item.target.type),
                completed_80: item.completed_80,
                completed_100: item.completed_100,
                upper_limit: item.upper_limit,
                lower_limit: item.lower_limit,
                items: item.items 
            };
 
            let req = this.items[i].id == 0 
                ? axios.post(this.uri + '/' + method, fields)
                : axios.put(this.uri + '/' + method, fields);

            req.then(response => {
                
                item.id = response.data.id;
                item.items.forEach((el, index) => {
                    el.id = response.data.items[index]
                });

                this.removeDeletedItems(item.items)

                this.$toast.info('KPI Сохранен');
                loader.hide()
            }).catch(error => {
                let m = error;
                if(error.message == 'Request failed with status code 409') {
                    m = 'Выберите другую цель "Кому". Этому объекту уже назначен KPI';
                }
                
                loader.hide()
                alert(m)
            });


        },  

        removeDeletedItems(items) {
            let indexes = [];
            let counter = 0;
            items.forEach((el, index) => {
                if(el.deleted != undefined && el.deleted) {
                    indexes.push(index)
                }
            });
            console.log(indexes)
            indexes.forEach(index => {
                items.splice(index-counter, 1);
                counter++;
            });

        },

        deleteKpi(i) {
         
            let item = this.items[i]
            let a = this.all_items.findIndex(el => el.id == item.id);

            if(!confirm('Вы уверены?')) {
                return;
            }

            if(item.id == 0) {
                if(a != -1) this.all_items.splice(a, 1);
                 this.onSearch();
                this.$toast.info('KPI Удален!');
                return;
            }

            let loader = this.$loading.show();
            axios.delete(this.uri + '/delete/' + item.id).then(response => {

             
                if(a != -1) this.all_items.splice(a, 1);
                this.onSearch();

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

        onSearch() { 
            let text = this.searchText;
     
            if(this.searchText == '') {
               this.items = this.all_items;
            } else {
                this.items = this.all_items.filter((el, index) => {
                    let has = false;

                    if (
                        el.target != null
                        && el.target.name.toLowerCase().indexOf(text.toLowerCase()) > -1
                    ) {
                        has = true;
                    }

                    if (
                        el.title.toLowerCase().indexOf(text.toLowerCase()) > -1
                    ) {
                        has = true;
                    }

                    if (
                        el.creator != null
                        && (
                            el.creator.name.toLowerCase().indexOf(text.toLowerCase()) > -1
                            || el.creator.last_name.toLowerCase().indexOf(text.toLowerCase()) > -1
                        )
                    ) {
                        has = true;
                    }

                    if (
                        el.updater != null
                        && (
                            el.updater.name.toLowerCase().indexOf(text.toLowerCase()) > -1
                            || el.updater.last_name.toLowerCase().indexOf(text.toLowerCase()) > -1
                        )
                    ) {
                        has = true;
                    }

                    return has; 
                }); 
            }

            this.page_items = this.items.slice(0, this.pageSize);
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