<template>
<div class="indicators p-3">

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
                @apply="fetch"
                @search-text-changed="onSearch"
            >
            </super-filter>
            <div class="ml-2"> 
                Найдено: {{ items.length }}
            </div>
        </div>

        <button class="btn rounded btn-outline-success" @click="addItem">
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
                    <td  @click="item.expanded = !item.expanded" class="pointer">
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ i + 1 }}</span>
                        </div>
                    </td>
                    <td  v-for="(field, f) in fields" :key="f">

                        
                        <div v-if="non_editable_fields.includes(field.key)" :class="field.class">
                           {{ item[field.key] }}
                        </div>

                        <div v-else-if="field.key == 'source' && item.source != undefined" :class="field.class">
                           <div class="d-flex">
                                <select 
                                    v-model="item.source"
                                    class="form-control small"
                                >
                                    <option v-for="key in Object.keys(sources)" :key="key"
                                        :value="key">
                                        {{ sources[key] }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div v-else-if="field.key == 'group_id' && item.group_id != undefined" :class="field.class">
                           <div class="d-flex">
                                <select 
                                    v-if="item.source == 1"
                                    v-model="item.group_id"
                                    class="form-control small"
                                >
                                    <option value="0" selected>-</option>
                                    <option v-for="(group, id) in groups" :value="id" :key="id">{{ group }}</option>
                                </select>
                            </div>
                        </div>

                        <div v-else :class="field.class">
                            <input type="text" class="form-control" v-model="item[field.key]" @change="validate(item[field.key], field.key)" /> 
                        </div>

                    </td>
                    <td >
                        <i class="fa fa-save ml-2 mr-1 btn btn-success p-1" @click="saveItem(i)"></i>
                        <i class="fa fa-trash btn btn-danger p-1" @click="deleteItem(i)"></i>
                    </td>
                </tr>
              
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
        title="Настройка списка Показатели"
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
       
       
        </div>
      </div>  
    </b-modal>

</div>
</template>

<script>
export default {
    name: "QuartalPremiums", 
    props: {
        
    },
    watch: {
        show_fields: {
            handler: function (val) {
                localStorage.indicators_show_fields = JSON.stringify(val);
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
            sources: {
                0: 'без источника',
                1: 'из показателей отдела',
                2: 'из битрикса',
                3: 'из амосрм',
            },
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
            ]
        }
    }, 

    created() {
        this.fetch()
    
        this.setDefaultShowFields()
        this.prepareFields(); 

    },

    methods: {

        onChangePage(page_items) {
            this.page_items = page_items;
        },

        fetch(filter = null) {
            let loader = this.$loading.show();

            axios.post('/indicators/get', {
                filters: filter 
            }).then(response => {
                
                this.items = response.data.items;

                this.items.forEach(el => el.expanded = false);
                this.page_items = this.items.slice(0, this.pageSize);

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        setDefaultShowFields() {
        
            if(localStorage.indicators_show_fields) {
                this.show_fields = JSON.parse(localStorage.getItem('indicators_show_fields'));
            } else {
                this.show_fields = { // Какие поля показывать
                    name: true,
                    group_id: true,
                    daily_plan: true,
                    unit: true,
                    share: true,
                    method: true,
                    view: true,
                    source: true,
                    editable: true,
                    order: true,
                    weekdays: true,
                    created_by: true,
                    updated_by: true,
                    created_at: true,
                    updated_at: true,
                }
            }

        },

        adjustFields() {
            this.modalAdjustVisibleFields = true;
        },

        prepareFields() {
            let fields = [];
       
                   
            if(this.show_fields['name']) {
                fields.push({
                    name: 'Название',
                    key: 'name',
                    visible: true,
                    type: 'text',
                    class: 'text-left w-230 '
                });
            }
            
            if(this.show_fields['group_id']) {
                fields.push({
                    name: 'Группа',
                    key: 'group_id',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }

            if(this.show_fields['daily_plan']) {
                fields.push({
                    name: 'План',
                    key: 'daily_plan',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['unit']) {
                fields.push({
                    name: 'План',
                    key: 'Ед.изм.',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }
            if(this.show_fields['share']) {
                fields.push({
                    name: 'Доля',
                    key: 'share',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['method']) {
                fields.push({
                    name: 'Метод',
                    key: 'method',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
             if(this.show_fields['view']) {
                fields.push({
                    name: 'Вид',
                    key: 'view',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
             if(this.show_fields['source']) {
                fields.push({
                    name: 'Источник',
                    key: 'source',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
      
            if(this.show_fields['editable']) {
                fields.push({
                    name: 'Редактируемый',
                    key: 'editable',
                    visible: true,
                    type: 'date',
                    class: 'text-center'
                });
            }
            if(this.show_fields['order']) {
                fields.push({
                    name: 'Порядок',
                    key: 'order',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }

            if(this.show_fields['weekdays']) {
                fields.push({
                    name: 'Рабочие дни',
                    key: 'weekdays',
                    visible: true,
                    type: 'text',
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

            this.fields = fields;
        },

        addItem() {
            this.items.unshift({
                id: 0,
                name: null,
                group_id: '',
                daily_plan: 0,
                share: 0,
                method: 0,
                view: 1,
                source: null,
                editable: null,
                order: '',
                weekdays: '',
                created_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                updated_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                expanded: false
            });

            this.$toast.info('Добавить показатель');
        },

        saveItem(i) {
            let loader = this.$loading.show();
            let item = this.items[i]
            let method = this.items[i].id == 0 ? 'save' : 'update';

            if(item.target == null) {
                this.$toast.error('Выберите Кому назначить показатель!');
                return;
            }
            
            let fields = {...item};
 
            let req = this.items[i].id == 0 
                ? axios.post('/indicators/' + method, fields)
                : axios.put('/indicators/' + method, fields);

            req.then(response => {
                
                let indicator = response.data.indicator;
                
                item.id = indicator.id;

                this.$toast.info('Квартальная премия Сохранена!');
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        }, 

        deleteItem(i) {
            let loader = this.$loading.show();
            let item = this.items[i]

            if(!confirm('Вы уверены?')) {
                return;
            }

            if(item.id == 0) {
                this.items.splice(i) // maybe will be error cause of page_items
                this.$toast.info('Индикатор Удален!');
                return;
            }

            axios.delete('/indicators/delete', {
                id: item.id
            }).then(response => {

                this.items.splice(i) // maybe will be error cause of page_items

                this.$toast.info('Индикатор Удален!');
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