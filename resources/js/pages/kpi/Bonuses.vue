<template>
<div class="bonuses px-3 py-1">

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

        <button class="btn rounded btn-outline-success" @click="addItem">
            <i class="fa fa-plus mr-2"></i>
            <span>Добавить</span>
        </button>
    </div>
    
    <!-- table NEW -->
    <table class="table b-table table-bordered table-sm table-responsive mb-3">
        <tr>
            <th class="b-table-sticky-column text-center px-1">
                <i class="fa fa-cogs" @click="adjustFields"></i>
            </th>
            <th 
                v-for="(field, i) in fields"
                :class="[
                     field.class,
                    {'b-table-sticky-column l-2' : field.key == 'target'
                }]"
            >
                {{ field.name }}
            </th>
            <th></th>
        </tr>
        <tr>
            
        </tr>

        <template v-for="(item, i) in page_items">
            <tr>
                <td class="b-table-sticky-column text-left">
                    <input class="ml-2" type="checkbox" v-model="item.selected" />
                    <span class="ml-2">{{ i + 1 }}</span>
                </td>
                <td v-for="(field, f) in fields"  :class="[
                     field.class,
                    {'b-table-sticky-column l-2' : field.key == 'target'
                }]">
                    <div v-if="field.key == 'target'">
                        <superselect
                            v-if="item.target == null"
                            class="w-full" 
                            :values="[]" 
                            :single="true"
                            @choose="(target) => item.target = target"
                        /> 
                        <div v-else class="d-flex aic">
                            <i class="fa fa-user ml-2 color-user" v-if="item.target.type == 1"></i> 
                            <i class="fa fa-users ml-2 color-group" v-if="item.target.type == 2"></i> 
                            <i class="fa fa-briefcase ml-2 color-position" v-if="item.target.type == 3"></i> 
                            <span class="ml-2">{{ item.target.name }}</span>
                        </div>
                    </div>
                         
                    <div v-else-if="field.key == 'created_by' && item.creator != null">
                        {{ item.creator.last_name + ' ' + item.creator.name }}
                    </div>

                    <div v-else-if="field.key == 'updated_by' && item.updater != null">
                        {{ item.updater.last_name + ' ' + item.updater.name }}
                    </div>

                    <div v-else-if="non_editable_fields.includes(field.key)">
                        {{ item[field.key] }}
                    </div>

                    

                    <div v-else-if="field.key == 'activity_id' && item.source != undefined">
                        <div class="d-flex">
                            <select 
                                v-model="item.source"
                                class="form-control small"
                                @change="++source_key"
                            >
                                <option v-for="key in Object.keys(sources)"
                                    :value="key">
                                    {{ sources[key] }}
                                </option>
                            </select>

                            <select 
                                v-if="Number(item.source) == 1"
                                v-model="item.group_id"
                                class="form-control small"
                                :key="'c' + source_key"
                            >
                                <option value="0" selected>-</option>
                                <option v-for="(group, id) in groups" :value="id">{{ group }}</option>
                            </select>

                            <div v-else></div>        

                            <select 
                                v-model="item.activity_id"
                                class="form-control small"
                                :key="'d' + source_key"
                            >
                                <option value="0" selected>-</option>
                                <option v-for="activity in grouped_activities(item.source, item.group_id)" :value="activity.id"  >{{ activity.name }}</option>
                            </select>
                        </div>
                    </div>
                        
                    <div v-else-if="field.key == 'unit'">
                        <select 
                            v-model="item.unit"
                            class="form-control"
                        >
                            <option value="0" selected>-</option>
                            <option v-for="key in Object.keys(units)" :value="key">{{ units[key] }}</option>
                        </select>
                    </div>

                    <div v-else-if="field.key == 'daypart'">
                        <select 
                            v-model="item.daypart"
                            class="form-control"
                        >
                            <option v-for="key in Object.keys(dayparts)" :value="key">{{ dayparts[key] }}</option>
                        </select>
                    </div>

                    <div v-else>
                        <input
                            :type="field.type"
                            class="form-control"
                            v-model="item[field.key]"
                            @change="validate(item[field.key], field.key)"
                        /> 
                    </div>
                </td>
                <td>
                    <i
                        class="fa fa-save btn btn-success p-1 ml-1"
                        @click="saveItemFromTable(i)"
                    />
                    <i
                        class="fa fa-edit btn btn-primary p-1"
                        @click="openSidebar(i)"
                    />
                    <i
                        class="fa fa-trash btn btn-danger p-1"
                        @click="deleteItem(i)"
                    />
                </td>
            </tr>
        </template>
    </table>

    <!-- pagination -->
    <jw-pagination
        class=""
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
        title="Настройка списка"
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


    <sidebar
        title="Настроить бонус"
        v-if="activeItem != null"
        :open="showSidebar"
        @close="closeSidebar"
        width="40%"
    >   
        <div class="row m-0">
            <div class="mb-3" v-for="(field, f) in all_fields" :class="field.alter_class">
                        
                        <div class="mb-2 mt-2 field">{{ field.name }}</div>

                        <div v-if="field.key == 'target'" class="mr-5">
                            <superselect
                                v-if="activeItem.id == 0"
                                class="w-full" 
                                :values="activeItem.target == null ? [] : [activeItem.target]" 
                                :single="true"
                                @choose="(target) => activeItem.target = target" /> 
                            <div v-else class="d-flex aic">
                                <i class="fa fa-user ml-2 color-user" v-if="activeItem.target.type == 1"></i> 
                                <i class="fa fa-users ml-2 color-group" v-if="activeItem.target.type == 2"></i> 
                                <i class="fa fa-briefcase ml-2 color-position" v-if="activeItem.target.type == 3"></i> 
                                <span class="ml-2">{{ activeItem.target.name }}</span>
                            </div>
                        </div>
                         
                        <div v-else-if="field.key == 'created_by' && activeItem.creator != null">
                            {{ activeItem.creator.last_name + ' ' + activeItem.creator.name }}
                        </div>

                        <div v-else-if="field.key == 'updated_by' && activeItem.updater != null">
                            {{ activeItem.updater.last_name + ' ' + activeItem.updater.name }}
                        </div>

                        <div v-else-if="non_editable_fields.includes(field.key)">
                            {{ activeItem[field.key] }}
                        </div>

                      

                        <div v-else-if="field.key == 'activity_id' && activeItem.source != undefined">
                            <div class="d-flex">
                                <select 
                                    v-model="activeItem.source"
                                    class="form-control small mr-2"
                                    @change="++source_key"
                                >
                                    <option v-for="key in Object.keys(sources)"
                                        :value="key">
                                        {{ sources[key] }}
                                    </option>
                                </select>

                                <select 
                                    v-if="Number(activeItem.source) == 1"
                                    v-model="activeItem.group_id"
                                    class="form-control small mr-2"
                                    :key="'a' + source_key"
                                >
                                    <option value="0" selected>-</option>
                                    <option v-for="(group, id) in groups" :value="id">{{ group }}</option>
                                </select>      

                                <select 
                                    v-model="activeItem.activity_id"
                                    class="form-control small"
                                    :key="'b' + source_key"
                                >
                                    <option value="0" selected>-</option>
                                    <option v-for="activity in grouped_activities(activeItem.source, activeItem.group_id)" :value="activity.id">{{ activity.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div v-else-if="field.key == 'unit'">
                            <select 
                                v-model="activeItem.unit"
                                class="form-control"
                            >
                                <option value="0" selected>-</option>
                                <option v-for="key in Object.keys(units)" :value="key">{{ units[key] }}</option>
                            </select>
                        </div>

                        <div v-else-if="field.key == 'daypart'">
                            <select 
                                v-model="activeItem.daypart"
                                class="form-control"
                            >
                                <option v-for="key in Object.keys(dayparts)" :value="key">{{ dayparts[key] }}</option>
                            </select>
                        </div>

                        <div v-else-if="field.key == 'text'">
                            <textarea v-model="activeItem[field.key]" class="form-control"></textarea>
                        </div>

                        <div v-else>
                            <input :type="field.type" class="form-control" v-model="activeItem[field.key]" @change="validate(activeItem[field.key], field.key)" /> 
                        </div>

            </div>
            <div>
                <button
                    class="d-flex aic  btn btn-success ml-3" 
                    @click="saveItem"
                >
                    <i 
                        class="fa fa-save"
                    />
                    <span class="ml-2">Сохранить</span>
                </button>
            </div>
        </div>
        
    </sidebar>
</div>
</template>

<script>
import {fields, newBonus} from "./bonuses.js";
import {findModel} from "./helpers.js";

export default {
    name: "Bonuses", 
    props: {
        
    },
    watch: {
        show_fields: {
            handler: function (val) {
                localStorage.bonus_show_fields = JSON.stringify(val);
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
            activeItem: null,
            uri: 'bonus',
            showSidebar: false,
            show_fields: [],
            fields: [],
            all_fields: fields,
            groups: [],
            searchText: '',
            modalAdjustVisibleFields: false,
            page_items: [],
            pageSize: 10,
            paginationKey: 1,
            items: [], // after filter changes
            all_items: [],
            activities: [],
            source_key: 1,
            dayparts: {
                0: 'Полный день',
                1: 'До обеда',
                2: 'После обеда',
            },
            units: {
                one: 'За каждую единицу',
                all: 'За все',
                first: 'Первый кто достигнет',
            },
            sources: {
                0: 'без источника',
                1: 'из показателей отдела',
                2: 'из битрикса',
                3: 'из амосрм',
            },
            non_editable_fields: [
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ]
        }
    }, 

    created() {
        this.setDefaultShowFields()
        this.prepareFields(); 
        this.addStatusToItems(); 
    },

    mounted() {
        this.fetch()
    },

    methods: {

        onChangePage(page_items) {
            this.page_items = page_items;
        },

        fetch(filter = null) {
            let loader = this.$loading.show();

            axios.post( this.uri + '/get', {
                filters: filter 
            }).then(response => {
                
                this.all_items = response.data.bonuses
                this.items = response.data.bonuses;
                this.activities = response.data.activities;
                this.groups = response.data.groups;

                this.defineSourcesAndGroups('t');

                this.items.forEach(el => el.expanded = false);
                this.page_items = this.items.slice(0, this.pageSize);

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        openSidebar(i) {
            this.activeItem = this.page_items[i]     
            this.showSidebar = true
        },

        closeSidebar() {
            this.showSidebar = false
            this.activeItem = null;
        },
        
        setDefaultShowFields() {

            let obj = {}; // Какие поля показывать
            fields.forEach(field => obj[field.key] = true); 

            if(localStorage.bonus_show_fields) {
                this.show_fields = JSON.parse(localStorage.getItem('bonus_show_fields'));
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
                el.on_edit = false
                el.source = 0
                el.group_id = 0
                el.selected = false
            });
        },

        prepareFields() {
            let visible_fields = [],
                show_fields = this.show_fields;
            
            fields.forEach((field, i) => {
                if(this.show_fields[field.key] != undefined
                    && this.show_fields[field.key]
                ) {
                    visible_fields.push(field)
                }
            });

            this.fields = visible_fields;
        },

        addItem() {
            this.activeItem = newBonus()
            this.showSidebar = true
        },

        validateMsg(item) {
            let msg = '';

            if(item.target == null)    msg = 'Выберите Кому назначить'
            if(item.title.length <= 1) msg = 'Заполните название'
            
            // activity id
            let a;
            if(item.source == 1) {
                a = this.activities.findIndex(el => el.source == item.source && el.group_id == item.group_id && el.id == item.activity_id);
            } else {
                a = this.activities.findIndex(el => el.source == item.source && el.id == item.activity_id);
            }
            
            if(item.activity_id == 0 || item.activity_id == undefined || a == -1) {
                msg = 'Выберите показатель';
            } 

            // another
            if(item.quantity <= 0)     msg = 'Кол-во должно быть больше нуля'
            if(item.sum <= 0)          msg = 'Вознаграждение должно быть больше нуля'
            
            return msg;
        },

        save(item) {
            
            /**
             * validate item
             */
            let not_validated_msg = this.validateMsg(item);
            if(not_validated_msg != '') {
                this.$toast.error(not_validated_msg)
                return;
            }
            
            /**
             * prepare fields
             */
            let loader = this.$loading.show();
            let method = item.id == 0 ? 'save' : 'update';

            let fields = {
                targetable_id: item.target.id,
                targetable_type: findModel(item.target.type),
                ...item
            };
 
            let req = item.id == 0 
                ? axios.post(this.uri + '/' + method, fields)
                : axios.put(this.uri + '/' + method, fields);

            /**
             * request
             */
            req.then(response => {
    
                if(method == 'save') {
                    let bonus = response.data.bonus;
                    item.id = bonus.id;
                   // this.items.unshift(item);
                    this.all_items.unshift(item);
                    this.showSidebar = false
                }

                this.$toast.info('Сохранено');
                loader.hide()
            }).catch(error => {
                let m = error;
                if(error.message == 'Request failed with status code 409') {
                    m = 'Выберите другую цель "Кому"';
                }
                
                loader.hide()
                alert(m)
            });
        },

        deletee(id, i) {
            let loader = this.$loading.show();
            axios.delete(this.uri + '/delete/' + id).then(response => {
                this.deleteEvery(id, i)
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        deleteEvery(id, i) {
            
            let a = this.all_items.findIndex(el => el.id == id)
            if(a != -1) this.all_items.splice(a, 1);

            this.onSearch();

            this.$toast.info('Удалено');
        },

        saveItem() {
            this.save(this.activeItem)
        }, 

        saveItemFromTable(i) {
            this.save(this.page_items[i])
        },

        deleteItem(i) {
          
            let item = this.page_items[i]

            if(!confirm('Вы уверены?')) {
                return;
            }

            if(item.id == 0) {
                this.deleteEvery(item.id, i);
                return;
            }

            this.deletee(item.id, i);
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
            value = Math.abs(Number(value));
            if(isNaN(value) || isFinite(value)) {
                value = 0;
            }

            if(['lower_limit', 'upper_limit'].includes(field) && value > 100) {
                value = 100;
            }
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
        },

        grouped_activities(source, group_id) {
            if(source == 1) {
                return this.activities.filter(el => el.source == source && el.group_id == group_id);
            } else {
                group_id = 0
                return this.activities.filter(el => el.source == source);
            }
        }
    },
 
}
</script>