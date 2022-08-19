<template>
<div class="quartal-premiums p-3">

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

                        <div v-else-if="non_editable_fields.includes(field.key)" :class="field.class">
                           {{ item[field.key] }}
                        </div>

                        <div v-else-if="field.key == 'activity_id' && item.source != undefined" :class="field.class">
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

                                <select 
                                    v-if="item.source == 1"
                                    v-model="item.group_id"
                                    class="form-control small"
                                >
                                    <option value="0" selected>-</option>
                                    <option v-for="(group, id) in groups" :value="id" :key="id">{{ group }}</option>
                                </select>

                                <select 
                                    v-model="item.activity_id"
                                    class="form-control small"
                                >
                                    <option value="0" selected>-</option>
                                    <option v-for="activity in grouped_activities(item.source, item.group_id)" :value="activity.id"  >{{ activity.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div v-else-if="field.key == 'unit'" :class="field.class">
                            <select 
                                v-model="item.unit"
                                class="form-control"
                            >
                                <option value="0" selected>-</option>
                                <option v-for="key in Object.keys(units)" :value="key">{{ units[key] }}</option>
                            </select>
                        </div>

                        <div v-else-if="field.key == 'daypart'" :class="field.class">
                            <select 
                                v-model="item.unit"
                                class="form-control"
                            >
                                <option value="0" selected>-</option>
                                <option v-for="key in Object.keys(dayparts)" :value="key">{{ dayparts[key] }}</option>
                            </select>
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
        title="Настройка списка Квартальные премии"
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
    name: "QuartalPremiums", 
    props: {
        
    },
    watch: {
        show_fields: {
            handler: function (val) {
                localStorage.quartal_premium_show_fields = JSON.stringify(val);
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
        this.fetch()
    
        this.setDefaultShowFields()
        this.prepareFields(); 
        this.addStatusToItems(); 

    },

    methods: {

        onChangePage(page_items) {
            this.page_items = page_items;
        },

        fetch(filter = null) {
            let loader = this.$loading.show();

            axios.post('/quartal-premiums/get', {
                filters: filter 
            }).then(response => {
                
                this.items = response.data.items;
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
            let obj = { // Какие поля показывать
                    target: true,
                    title: true,
                    sum: true,
                    activity_id: true,
                    from: true,
                    to: true,
                    plan: true,
                    text: true,
                    created_by: true,
                    updated_by: true,
                    created_at: true,
                    updated_at: true,
                };

            if(localStorage.quartal_premium_show_fields) {
                this.show_fields = JSON.parse(localStorage.getItem('quartal_premium_show_fields'));
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
            
            if(this.show_fields['title']) {
                fields.push({
                    name: 'Название',
                    key: 'title',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }

            if(this.show_fields['activity_id']) {
                fields.push({
                    name: 'Показатели',
                    key: 'activity_id',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['unit']) {
                fields.push({
                    name: 'План',
                    key: 'plan',
                    visible: true,
                    type: 'text',
                    class: 'text-center'
                });
            }
            if(this.show_fields['quantity']) {
                fields.push({
                    name: 'Кол-во',
                    key: 'from',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
            if(this.show_fields['daypart']) {
                fields.push({
                    name: 'Период',
                    key: 'to',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
             if(this.show_fields['text']) {
                fields.push({
                    name: 'Текст',
                    key: 'text',
                    visible: true,
                    type: 'number',
                    class: 'text-center'
                });
            }
             if(this.show_fields['sum']) {
                fields.push({
                    name: 'Вознаграждение',
                    key: 'sum',
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

        addItem() {
            this.items.unshift({
                id: 0,
                target: null,
                title: '',
                sum: 0,
                source: 0,
                group_id: 0,
                activity_id: 1,
                from: null,
                to: null,
                text: '',
                created_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                updated_at: new Date().toISOString().substr(0, 19).replace('T',' '),
                created_by: 'Али Акпанов',
                updated_by: 'Али Акпанов',
                expanded: false
            });

            this.$toast.info('Добавить Бонус');
        },

        saveItem(i) {
            let loader = this.$loading.show();
            let item = this.items[i]
            let method = this.items[i].id == 0 ? 'save' : 'update';

            if(item.target == null) {
                this.$toast.error('Выберите Кому назначить бонус!');
                return;
            }
            
            let fields = {...item};
 
            let req = this.items[i].id == 0 
                ? axios.post('/quartal-premiums/' + method, fields)
                : axios.put('/quartal-premiums/' + method, fields);

            req.then(response => {
                
                let bonus = response.data.bonus;
                
                item.id = bonus.id;

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
                this.$toast.info('Квартальная Удалена!');
                return;
            }

            axios.delete('/quartal-premiums/delete', {
                id: item.id
            }).then(response => {

                this.items.splice(i) // maybe will be error cause of page_items

                this.$toast.info('Квартальная Удалена!');
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