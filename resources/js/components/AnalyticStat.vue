<template>
<div class="">
 
    <div class="table-header">
        <input type="text" class="cell-coords" v-model="coords">
        <input type="text" class="cell-type" v-model="cell_type">
        <input type="text" class="cell-show-value" v-model="cell_show_value">
        <input type="text" class="cell-value" v-model="cell_value">
        <input type="text" class="cell-comment" v-model="cell_comment">
    </div>

    <div class="d-flex">
        <div class="relative   w551" id="wow-table">
            <!-- table -->
            <table class="as-table left-side" >
                <tr>
                    <td class="ruler-cells t-cell text-center">
                        <div class="in-cell inner-div " @click="editMode()">
                            <i class="fa fa-cogs"></i>
                        </div>
                    </td>
                    <td v-for="(letter, index) in letter_cells.slice(1, 4)" :key="index"
                        class="ruler-cells t-cell text-center">
                        <div class="in-cell inner-div d-flex" v-if="index == 0">
                            <span class="two-letter">A</span>
                            <span class="two-letter">B</span>
                        </div>
                        <div class="in-cell inner-div" v-else>
                            {{ letter }}
                        </div>
                    </td>
                </tr>

                <tr v-for="(item, i_index) in items" :key="i_index">

                        <td class="t-cell rownumber ruler-cells">
                            <div class="in-cell inner-div text-center">
                                <span v-if="editTableMode && i_index > 3" @click="deleteRow(i_index)">
                                    <i class="fa fa-trash pointer"></i>
                                </span>
                                <span v-if="editTableMode && i_index > 2" @click="add_row(i_index)">
                                    <i class="fa fa-plus-square pointer"></i>
                                </span>
                                <span>{{ i_index + 1 }}</span>
                            </div>
                        </td>

                        <template v-for="(field,f_index) in fields.slice(0, 4)" >
                            <td class="t-cell font-bold"  :key="f_index" @click="focus(i_index, f_index)" v-if="field.key != 'plan'" :class="item[field.key].class">
                                
                                <template v-if="field.key == 'name' && [1,2,3].includes(i_index)">
                                    <div class="d-flex justify-content-between">
                                        <div class="inner-div halfy" @click="focusName(i_index, f_index, 1)"
                                            :class="{
                                                'focused': focused_item === i_index && focused_field === f_index && focused_subfield == 1,
                                                'context': item[field.key].context,
                                                'disabled': item[field.key].editable == 0
                                            }"
                                            @contextmenu.prevent.stop="openContextMenu(item[field.key], i_index, f_index)">
                                            

                                            
                                            <div class="disabled"></div>

                                            <div class="contextor" v-if="item[field.key].context">
                                                  
                                                 
                                                    <ul class="types">
                                                        <li  @click="add_formula_1_31(item[field.key])">
                                                            Формула с 1 по 31
                                                        </li>
                                                    </ul>
                                                </div>

                                            <input type="text" class="in-cell" v-if="focused_item === i_index && focused_field === f_index && focused_subfield == 1" 
                                                v-model="item['name'].value" @change="change_stat(i_index, 'name')"/>

                                            <input v-else type="text" class="in-cell" :value="item['name'].show_value" />

                                    
                                            <div class="bottom-angle" v-if="focused_item === i_index && focused_field === f_index && focused_subfield == 1"  >
                                                <div class="angler"></div>
                                            </div>

                                            <div class="top-angle"  :class="item[field.key].type">

                                            </div>
                                        </div>
                                        <div class="inner-div halfy" @click="focusName(i_index, f_index, 2)"
                                            :class="{
                                                'focused': focused_item === i_index && focused_field === f_index && focused_subfield == 2,
                                                'context': item['plan'].context,
                                                'disabled': item['plan'].editable == 0
                                            }"
                                            @contextmenu.prevent.stop="openContextMenu(item['plan'], i_index, f_index)">
                                            
                                            <div class="disabled"></div>
                                    
                                            <input type="text" class="in-cell" v-if="focused_item === i_index && focused_field === f_index && focused_subfield == 2" 
                                                v-model="item['plan'].value" @change="change_stat(i_index, 'plan')"/>
                                            <input v-else type="text" class="in-cell" :value="item['plan'].show_value + (i_index == 2 ? '%' : '')" />
                                    
                                            <div class="bottom-angle" v-if="focused_item === i_index && focused_field === f_index && focused_subfield == 2"  >
                                                <div class="angler"></div>
                                            </div>

                                            <div class="top-angle"  :class="item[field.key].type">

                                            </div>
                                        </div>
                                    </div>
                                    
                                </template>
                                
                                <template v-else>
                                    <div class="inner-div"
                                        :class="{
                                            'focused': focused_item === i_index && focused_field === f_index,
                                            'context': item[field.key].context,
                                            'disabled': item[field.key].editable == 0
                                        }"
                                        @contextmenu.prevent.stop="openContextMenu(item[field.key], i_index, f_index)">
                                        
                                        <div class="disabled"></div>
                                        <div class="contextor" v-if="item[field.key].context">
                                            <div class="fonter d-flex justify-content-between" v-if="activeuserid == 5">
                                                <div @click="add_class(item[field.key], 'font-bold')">Ж</div>
                                                <div @click="add_class(item[field.key], 'font-italic')">К</div>
                                                <div @click="add_class(item[field.key], 'text-left')">Л</div>
                                                <div @click="add_class(item[field.key], 'text-center')">Ц</div>
                                                <div @click="add_class(item[field.key], 'text-right')">П</div>
                                            </div>
                                            <div class="color-choser d-flex justify-content-between">
                                                <div class="bg-red"  @click="add_class(item[field.key], 'bg-red')"></div>
                                                <div class="bg-yellow"  @click="add_class(item[field.key], 'bg-yellow')"></div>
                                                <div class="bg-green"  @click="add_class(item[field.key], 'bg-green')"></div>
                                                <div class="bg-blue"  @click="add_class(item[field.key], 'bg-blue')"></div>
                                                <div class="bg-violet"  @click="add_class(item[field.key], 'bg-violet')"></div>
                                            </div>
                                            <ul class="types">
                                    
                                                    <li  @click="change_type('initial', i_index, field.key)" v-if="activeuserid == 5 || ['sum', 'avg'].includes(field.key)">
                                                        Обычный
                                                    </li>
                                                    <li  @click="change_type('formula', i_index, field.key)" v-if="activeuserid == 5">
                                                        Формула
                                                    </li>
                                                    <li  @click="change_type('time', i_index, field.key)" v-if="['name'].includes(field.key)">
                                                        Часы из табеля
                                                    </li>
                                                    <li  @click="change_type('stat', i_index, field.key)" v-if="['name'].includes(field.key)">
                                                        Показатели
                                                    </li>
                                                    <li  @click="change_type('avg', i_index, field.key)" v-if="['avg'].includes(field.key)">
                                                        Среднее за месяц
                                                    </li>
                                                    <li  @click="change_type('sum', i_index, field.key)" v-if="['sum'].includes(field.key)">
                                                        Сумма за месяц
                                                    </li>
                                                    <li  @click="selectDepend(item[field.key])" v-if="['name'].includes(field.key) && item[field.key].depend_id === null">
                                                        Зависимость от ряда
                                                    </li>
                                                    <li  @click="removeDependency(item[field.key])" v-else-if="['name'].includes(field.key)">
                                                        Убрать зависимость
                                                    </li>
                                                    <li  @click="add_formula_1_31(item[field.key])" v-if="['name'].includes(field.key)">
                                                        Формула с 1 по 31
                                                    </li>
                                                    <li  @click="add_inhouse(item[field.key])" v-if="['name'].includes(field.key)">
                                                        Отсутствие минут inhouse
                                                    </li>
                                                    <li  @click="add_remote(item[field.key])" v-if="['name'].includes(field.key)">
                                                        Отсутствие минут remote
                                                    </li>
                                            
                                            </ul>
                                        </div>


                                        <input type="text" class="in-cell" v-if="focused_item === i_index && focused_field === f_index" 
                                                v-model="item[field.key].value" @change="change_stat(i_index, field.key)"/>
                                            <input v-else type="text" class="in-cell" :value="item[field.key].show_value + (i_index == 2 && field.key == 'sum' ? '%' : '')" />
                                            
                                    
                                
                                        <div class="bottom-angle" v-if="focused_item === i_index && focused_field === f_index"  >
                                            <div class="angler"></div>
                                        </div>

                                        <div class="top-angle"  :class="item[field.key].type">

                                        </div>
                                    </div>
                                </template>
                                
                            </td>
                        </template>
                </tr>


            
            </table>
            

            
        </div>
    
        <div class="table-responsive">
            <!-- table 2 -->
            <table class="as-table mr150">
                <tr>
                    
                    <td v-for="(letter, index) in letter_cells.slice(4, letter_cells.length)" :key="index"
                        class="ruler-cells t-cell text-center">
                        <div class="in-cell inner-div">
                            {{ letter }}
                        </div>
                    </td>
                </tr>

                <tr v-for="(item, i_index) in items" :key="i_index">


                        <template v-for="(field,f_index) in fields">
                            <td class="t-cell font-bold"  :key="f_index" @click="focus(i_index, f_index)" :class="item[field.key].class" v-if="f_index > 3">
                                <div class="inner-div"
                                    :class="{
                                        'focused': focused_item === i_index && focused_field === f_index,
                                        'context': item[field.key].context,
                                        'disabled': item[field.key].editable == 0 || ['stat', 'time'].includes(item[field.key].type),
                                        'less': item[field.key].depend_id !== null && items[depender[item[field.key].depend_id]] !== undefined && Number(items[depender[item[field.key].depend_id]][field.key].show_value) > Number(item[field.key].show_value),
                                        'more': item[field.key].depend_id !== null && items[depender[item[field.key].depend_id]] !== undefined && Number(items[depender[item[field.key].depend_id]][field.key].show_value) < Number(item[field.key].show_value),
                                    }" 
                                    @contextmenu.prevent.stop="openContextMenu(item[field.key], i_index, f_index)">
                                    
                                    <div class="disabled"></div>
                                    <div class="contextor" v-if="item[field.key].context">
                                        <ul class="types">
                                            <li>
                                                <div class="d-flex decimals">
                                                    <p>Дробные</p>
                                                    <input v-model="item[field.key].decimals"  type="number" @change="setDecimals"/>
                                                </div>
                                            </li>
                                            <li  @click="change_type('initial', i_index, field.key)">
                                                Обычный
                                            </li>
                                            <li  @click="change_type('formula', i_index, field.key)">
                                                Формула
                                            </li>
                                            <li  @click="add_formula_1_31(item[field.key])">
                                                Формула с 1 по 31
                                            </li>
                                        </ul>
                                    </div>

                                    <input type="text" class="in-cell" v-if="focused_item === i_index && focused_field === f_index" 
                                        v-model="item[field.key].value" @change="change_stat(i_index, field.key)"/>
            
                                    <input v-else-if="i_index != 0" type="text" class="in-cell" :value="(Number(item[field.key].show_value) != 0 ? Number(item[field.key].show_value).toFixed(item[field.key].decimals) + item[field.key].sign : '')" />
                                    <input v-else type="text" class="in-cell" :value="item[field.key].show_value" />

                                    <div class="bottom-angle" v-if="focused_item === i_index && focused_field === f_index">
                                        <div class="angler"></div>
                                    </div>

                                    <div class="top-angle"  :class="item[field.key].type">

                                    </div>
                                </div>
                            </td>
                        </template>
                        
                
                </tr>


            
            </table>
        </div>
    </div>


    

     <!-- Modal Create activity -->
    <a-modal v-model="showDependy"  title="Зависимость значений от ряда" @ok="save_depend()" :width="400" class="modalle">
    
         <div class="row">
             <div class="col-12">
                 <b v-if="itemy !== undefined && itemy !== null"> {{ itemy.value }}</b>
             </div>
            <div class="col-5 mt-1">
                <p class="">Ряд</p>
            </div>
            <div class="col-7">
                <select v-model="depend_id" class="form-control form-control-sm">
                    <option :value="dep.row_id"  v-for="(dep, key) in dependencies" :key="key">{{ dep.index }} {{ dep.name }}</option>
                </select>
            </div>
        </div>

    </a-modal>

     <!-- Modal Create activity -->
    <a-modal v-model="showVariants"  title="Формула на 31 дней" @ok="save_cell_activity()" :width="650" class="modalle">
    
         <div class="row">
            <div class="col-5">
                <p class="">Активность</p>
            </div>
            <div class="col-7">
                <select v-model="activity_id" class="form-control form-control-sm">
                    <option :value="activity.id"  v-for="(activity, key) in activities" :key="key">{{ activity.name }}</option>
                </select>
            </div>
        </div>

    </a-modal>

    <!-- Modal showFormula1_31 -->
    <a-modal v-model="showFormula1_31"  title="" @ok="save_formula_1_31()" :width="650" class="modalle">
    
        <div class="row">
            <div class="col-12">
                <p>Пишите ряд для выбора в фигурных скобках, 5 ряд это - {5}</p>
                <p>Пример формулы: {5} * 12 / 1000</p>
                <p>Станет        : E5  * 12 / 1000</p>
            </div>
            <div class="col-12 mb-3">
                <input type="text" class="form-control form-control-sm" v-model="formula_1_31">
            </div>
            <div class="col-4">
                Количество цифр после запятой
            </div>
            <div class="col-8">
                <input type="text" class="form-control form-control-sm" v-model="formula_1_31_decimals">
            </div>
        </div>

    </a-modal>

    <!-- comment for remote / inhouse add minutes -->
    <a-modal v-model="showCommentWindow"  title="" @ok="saveComment()" :width="650" class="modalle">
    
        <div class="row">
            <div class="col-12">
                <p>По какой причине добавляются минуты?</p>
            </div>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" v-model="comment">
            </div>
        </div>

    </a-modal>

    

    
</div>
</template>

<script>
const Parser = require('expr-eval').Parser;
export default {
  components: {},
    name: "AnalyticStat", 
    props: ['monthInfo', 'activeuserid', 'table', 'group_id', 'fields', 'activities'],
    data() {
        return {
            active: '1',
            focused_item: null,
            focused_field: null,
            focused_subfield: null,
            editTableMode: false,
            showVariants: false, // activites
            showDependy: false, // 
            coords: null,
            activity_id: null,
            depend_id: null,
            dependencies: [],
            cell_value: null,
            cell_type: null,
            cell_show_value: null,
            cell_comment: null,
            showFormula1_31: false,
            showCommentWindow: false, // for remote/ inhouse add hours
            comment: '', // for remote/ inhouse add hours
            comment_i: '', // for remote/ inhouse add hours
            comment_f: '', // for remote/ inhouse add hours
            depender: [],
            formula_1_31: '',
            formula_1_31_decimals: 0,
            cell_types: {
                initial: 'Обычный',
                formula: 'Формула',
                time: 'Часы из табеля',
                stat: 'Показатели',
                avg: 'Среднее',
                sum: 'Сумма дней',
                salary: 'Начисления',
                remote: 'Отсутствие remote',
                inhouse: 'Отсутствие inhouse',
            },
            items: [
                {
                    'plan': {
                        value: 'testplan',
                        show_value: 'x',
                        context: false,
                        type: 'initial',
                    },
                },
            ],
            itemy: null,
            letter_cells: [],
        }
    },

    created() {
        this.items = this.table
        this.form()
        
        this.calcGlobal()
        this.setDependencies();
       
        //this.fields = this.columns 
    },
    
    mounted () {
        document.addEventListener("keyup", this.nextItem);
        // this.listener()
    },

    methods: {
        setDependencies() {
            let arr = [];

            this.items.forEach((item, index) => {

                if(![0,1,2,3].includes(index)) {
                    arr.push({
                        'row_id': item['name']['row_id'],
                        'name': index + 1,
                        'index': item['name']['value'],
                    });
                }

                
            });
            this.dependencies  = arr


            let depender = {};
            this.items.forEach((item, index) => {
                
                depender[item['name']['row_id']] = index;
                
            });

            
            this.depender = depender

        },

        editMode() {
            console.log('hey what happened');
            this.editTableMode = !this.editTableMode
        },

        listener() {
            var ignoreClickOnMeElement = document.getElementById('wow-table');

            var self = this;
            document.addEventListener('click', function(event) {
                var isClickInsideElement = ignoreClickOnMeElement.contains(event.target);
                if (!isClickInsideElement) {
                    self.hideContextMenu();
                }
            });
        },

        calc(combinations) {
            let expression = this.getExpression(combinations);

            console.log(expression)

            return expression !== null && expression.length > 0 ? Parser.evaluate(expression) : 0
        },

        add_class(item, clasxs) {
            let c = item.class

            if(clasxs == 'text-center' && c !== null) {
                item.class = item.class.replace('text-left', "");
                item.class = item.class.replace('text-right', "");
            }

            if(clasxs == 'text-left' && c !== null) {
                item.class = item.class.replace('text-center', "");
                item.class = item.class.replace('text-right', "");
            }

            if(clasxs == 'text-right' && c !== null) {
                item.class = item.class.replace('text-left', "");
                item.class = item.class.replace('text-center', "");
            }

            if(clasxs == 'bg-red' && c !== null) {
                item.class = item.class.replace('bg-yellow', "");
                item.class = item.class.replace('bg-green', "");
                item.class = item.class.replace('bg-blue', "");
                item.class = item.class.replace('bg-violet', "");
            }

            if(clasxs == 'bg-yellow' && c !== null) {
                item.class = item.class.replace('bg-red', "");
                item.class = item.class.replace('bg-green', "");
                item.class = item.class.replace('bg-blue', "");
                item.class = item.class.replace('bg-violet', "");
            }

            if(clasxs == 'bg-green' && c !== null) {
                item.class = item.class.replace('bg-yellow', "");
                item.class = item.class.replace('bg-red', "");
                item.class = item.class.replace('bg-blue', "");
                item.class = item.class.replace('bg-violet', "");
            }

            if(clasxs == 'bg-blue' && c !== null) {
                item.class = item.class.replace('bg-yellow', "");
                item.class = item.class.replace('bg-green', "");
                item.class = item.class.replace('bg-red', "");
                item.class = item.class.replace('bg-violet', "");
            }

            if(clasxs == 'bg-violet' && c !== null) {
                item.class = item.class.replace('bg-yellow', "");
                item.class = item.class.replace('bg-green', "");
                item.class = item.class.replace('bg-blue', "");
                item.class = item.class.replace('bg-red', "");
            }

            if(c !== null && c.includes(clasxs)) {
                item.class = item.class.replace(clasxs, "");
            } else {
                item.class = item.class + ' ' + clasxs;
            }
            
            if(item.type == 'formula') {
                item.show_value = item.value;
                let combinations = this.combinator(item.value);
                item.formula  = this.getExpression(combinations, 'db');
                item.show_value = this.calc(combinations);
            }

            this.editQueryItem(item);
        }, 

        calcGlobal() {
            let items = this.formula_searcher()

            items.forEach(it => {
                let combinations = this.combinator(it.value);
                it.formula  = this.getExpression(combinations, 'db');
                it.show_value = Number(Number(this.calc(combinations).toFixed(it.decimals)));
            });
        },
       
        getExpression(combinations, type = 'local') {
            let expression = '';

            if(type == 'local') { // для vue
                combinations.forEach(item => {

                    
                    if(item.type == 'formula') {
                        let inner_combinations = this.combinator(item.value);
                        let inner_calc = 0;
                        let inner_item = this.searcher(item.text);
                        inner_calc = this.calc(inner_combinations);
                        inner_item.show_value = inner_calc;
                        expression += inner_calc;
                    } else if(item.type == 'action'){
                        expression += item.text;
                    } else {
                        item.value = Number(item.value);
                        expression += !isNaN(item.value) ?  item.value : 0;
                    }
                });
            }

            if(type == 'db') { // для хранения формулы в базе
                combinations.forEach(item => {
                    if(item.type == 'value') expression += item.value
                    if(item.type == 'cell') expression += item.code
                    if(item.type == 'formula') expression += item.code
                    if(item.type == 'action') expression += item.text
                });
            }

            return expression;
        },

        add_row(i_index) {

            
            let loader = this.$loading.show();
            axios.post("/timetracking/analytics/add-row", {
                group_id: this.group_id,
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                after_row_id: this.items[i_index]['name']['row_id'],
            })
            .then((response) => {
                this.$message.success('Добавлено');

             
                this.items.splice(i_index + 1, 0, response.data);
                //this.setDependencies();
                loader.hide()
            }).catch(error => {
                this.$message.error('Не получилось');
                console.log(error)
                loader.hide()
            });
 
        },
        

        deleteRow(index) {
           let e = confirm("Вы уверены?");
           let loader = this.$loading.show();
            
           if(e) {
               axios.post("/timetracking/analytics/delete-row", {
                    group_id: this.group_id,
                    date: this.$moment(
                        `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                        "MMMM YYYY" 
                    ).format("YYYY-MM-DD"),
                    item: this.items[index]
                })
                .then((response) => {
                    this.$message.success('Удалено');
                    // Delete item from items
                    this.items.splice(index, 1);
                    this.setDependencies();
                    loader.hide()
                }).catch(error => {
                    this.$message.error('Не получилось');
                    console.log(error)
                    loader.hide()
                });
           }
        },

        save_depend() {
        
            axios.post("/timetracking/analytics/add-depend", {
                id: this.itemy['row_id'],
                depend_id: this.depend_id
            })
            .then((response) => {
                this.$message.success('Обновите, чтобы подтянуть данные!');
                
                this.showDependy = false;
                this.depend_id = null;
                this.itemy = null
            }).catch(error => {
                this.$message.error('Не получилось');
                console.log(error)
            });
           
        },

        removeDependency(item) {
            axios.post("/timetracking/analytics/dependency/remove", {
                id: item.row_id,
            })
            .then((response) => {
                this.$message.success('Обновите, чтобы подтянуть данные!');
            }).catch(error => {
                this.$message.error('Не получилось');
                console.log(error)
            });
           
        },

        focus(i,f) {

            if([1,2,3].includes(i) && f == 0) return ""

                console.log(i,f)
                console.log(this.focused_item,this.focused_field )
            if(!(this.focused_item == i && this.focused_field == f)) {
                console.log('hide');
                 this.hideContextMenu();
            }
           
            // indexes
            this.focused_item = i
            this.focused_field = f

            // cell value
            let item = this.items[i][this.fields[f].key];

            this.cell_value = item.value
            this.cell_comment = item.comment
            this.cell_type = this.cell_types[item.type]
            this.cell_show_value = item.show_value
            this.coords = item.cell

        },

        focusName(i,f, s) {

            console.log(i,f, s)
            this.hideContextMenu();
            // indexes
            this.focused_item = i
            this.focused_field = f

            // cell value
            // console.log(this.items[i])
            // console.log(this.fields[f])

            this.focused_subfield = s;

            let field = s == 2 ? 'plan' : 'name';

            this.cell_value = this.items[i][field].value
            this.cell_comment = this.items[i][field].comment
           //console.log(this.items[i][this.fields[f].key])
            this.cell_type = this.cell_types[this.items[i][field].type]

          //  console.log(this.items[i][field])
            this.coords = this.items[i][field].cell
        },

        hideContextMenu() {
            this.items.forEach((item, index) => {
                Object.values(item).forEach((value) => {
                   // console.log(value)
                    value.context = false
                });
            })
        },

        openContextMenu(item, i_index, f_index) {
            if(![5,18,157,84,14009].includes(Number(this.activeuserid))) {
                return "";
            }
            this.focus(i_index, f_index);
            this.hideContextMenu();

            //if(item.editable == 1) {
                item.context = true;
            //}
            
        },

        editQuery(i_index, f_index) {
            let item = this.items[i_index][f_index];

            axios.post("/timetracking/analytics/edit-stat", {
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                column_id: item.column_id,
                row_id: item.row_id,
                value: item.value, 
                show_value: item.show_value,
                type: item.type,
                group_id: this.group_id,
                class: item.class,
                formula: item.formula,
                comment: this.comment,
            })
            .then((response) => {
                this.showCommentWindow = false;
                this.comment_i = '';
                this.comment_f = '';
                this.$message.success('Сохранено');
            }).catch(error => {
                this.$message.error('Не сохранено');
                console.log(error)
            });
        },

        

        save_cell_time() {
             let loader = this.$loading.show();
             let item = this.item
            axios.post('/timetracking/analytics/save-cell-time', {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                year: this.monthInfo.currentYear,
                column_id: item.column_id,
                row_id: item.row_id,
                group_id: this.group_id,
                class: item.class,
            }).then(response => {
                this.$message.success('Обновите чтобы подтянуть данные!')
               
                this.item = null;
                loader.hide()
            }).catch(error => {
                loader.hide()
                this.$message.error('Ошибка!')
                alert(error)
            });
        },

        
        save_cell_sum() {
             let loader = this.$loading.show();
             let item = this.item
            axios.post('/timetracking/analytics/save-cell-sum', {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                year: this.monthInfo.currentYear,
                column_id: item.column_id,
                row_id: item.row_id,
                group_id: this.group_id,
                class: item.class,
            }).then(response => {
                this.$message.success('Сумма подтянута!')
               

                this.item.value = response.data
                this.item.show_value = response.data
                this.item = null;
                loader.hide()
            }).catch(error => {
                loader.hide()
                this.$message.error('Ошибка!')
                alert(error)
            });
        },

        save_cell_avg() {
             let loader = this.$loading.show();
             let item = this.item
            axios.post('/timetracking/analytics/save-cell-avg', {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                year: this.monthInfo.currentYear,
                column_id: item.column_id,
                row_id: item.row_id,
                group_id: this.group_id,
                class: item.class,
            }).then(response => {
                this.$message.success('Среднее за месяц подтянута!')
               
                this.item.value = response.data
                this.item.show_value = response.data
                this.item = null;
                loader.hide()
            }).catch(error => {
                loader.hide()
                this.$message.error('Ошибка!')
                alert(error)
            });
        },


        save_cell_activity() { 
             let loader = this.$loading.show();
             let item = this.item
            axios.post('/timetracking/analytics/save-cell-activity', {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
                year: this.monthInfo.currentYear,
                column_id: item.column_id,
                row_id: item.row_id,
                value: item.value, 
                show_value: item.show_value,
                group_id: this.group_id,
                type: item.type,
                class: item.class,
                formula: item.formula,
                activity_id: this.activity_id
            }).then(response => {
                this.$message.success('Обновите чтобы подтянуть данные!')
               

                this.item = null;

                this.showVariants = false
                loader.hide()
            }).catch(error => {
                loader.hide()
                this.$message.error('Ошибка!')
                alert(error)
            });
        },
        
        editQueryItem(item) {
            console.log(item)
            axios.post("/timetracking/analytics/edit-stat", {
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                column_id: item.column_id,
                row_id: item.row_id,
                value: item.value,
                show_value: item.show_value,
                type: item.type,
                class: item.class,
                formula: item.formula,
                group_id: this.group_id,
            })
            .then((response) => {
                this.$message.success('Сохранено');
            }).catch(error => {
                this.$message.error('Не сохранено');
                console.log(error)
            });
        },

        change_type(type, i_index, f_index) {
            let item = this.items[i_index][f_index];
            item.type = type

            if(item.type == 'initial') {
                item.show_value = item.value;

                this.calcGlobal()
                   this.editQuery(i_index, f_index);

            }

            if(item.type == 'formula') {
                item.show_value = item.value;
                
                let combinations = this.combinator(item.value);

                item.formula  = this.getExpression(combinations, 'db');

                item.show_value = this.calc(combinations);

                this.calcGlobal()

                this.editQuery(i_index, f_index);
            }

            if(item.type == 'time'){
                this.item = item;
                this.save_cell_time();
            }

            if(item.type == 'sum'){
                this.item = item;
                this.save_cell_sum();
            }

            if(item.type == 'avg'){
                this.item = item;
                this.save_cell_avg();
            }

            if(item.type == 'stat'){
                this.item = item;
                this.showVariants = true;
            }

           

         
        },

        selectDepend(item) {
            this.itemy = item
            this.showDependy = true;
        },

        add_formula_1_31(item) {
            this.itemy = item
            this.showFormula1_31 = true;
        },

        add_inhouse(item) {
            this.itemy = item;
            axios.post("/timetracking/analytics/add-remote-inhouse", {
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                row_id: this.itemy.row_id,
                type: 'inhouse'
            })
            .then((response) => {
                this.$message.success('Обновите для сохранения');
                this.itemy = null;
            }).catch(error => {
                this.$message.error('Не сохранено');
                console.log(error)
            });
        },

        add_remote(item) {
            this.itemy = item;
            axios.post("/timetracking/analytics/add-remote-inhouse", {
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                row_id: this.itemy.row_id,
                type: 'remote'
            })
            .then((response) => {
                this.$message.success('Обновите для сохранения');
                this.itemy = null;
            }).catch(error => {
                this.$message.error('Не сохранено');
                console.log(error)
            });
        },

        setDecimals() {

            let item = this.items[this.focused_item][this.focused_field];
            this.itemy = item;
         

            axios.post("/timetracking/analytics/set-decimals", {
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                row_id: this.itemy.row_id,
                column_id: this.itemy.column_id,
                decimals: this.itemy.decimals
            })
            .then((response) => {
                this.$message.success('Сохранено!');
                this.hideContextMenu();
                this.itemy = null;
            }).catch(error => {
                this.$message.error('Не сохранено');
                console.log(error)
            });
        },

        save_formula_1_31() {
            let rows = [];

            let text =  this.formula_1_31

            this.items.forEach((item, index) => {
                index++;
                text = text.replaceAll("{" + index + "}", "{" + item['name']['row_id'] + "}");
            });

            console.log(text);

            axios.post("/timetracking/analytics/add-formula-1-31", {
                date: this.$moment(
                    `${this.monthInfo.currentMonth} ${this.monthInfo.currentYear}`,
                    "MMMM YYYY"
                ).format("YYYY-MM-DD"),
                formula: text,
                row_id: this.itemy.row_id,
                decimals: this.formula_1_31_decimals
            })
            .then((response) => {
                this.$message.success('Обновите для сохранения');
                this.showFormula1_31 = false
                this.formula_1_31 = '';
                this.formula_1_31_decimals = 9;
                this.itemy = null;
            }).catch(error => {
                this.$message.error('Не сохранено');
                console.log(error)
            });
        },

        change_stat(i_index, f_index) {
            let item = this.items[i_index][f_index];

            if(item.type == 'initial') {
                item.show_value = item.value;

                this.calcGlobal();
            }

            if(item.type == 'formula') {
                item.show_value = item.value;
                
                let combinations = this.combinator(item.value);

                item.formula  = this.getExpression(combinations, 'db');

                item.show_value = this.calc(combinations);

                this.calcGlobal()
            }

            if(item.type == 'remote' || item.type == 'inhouse') {
                item.show_value = item.value;
                
                this.showCommentWindow = true;
                this.comment_i = i_index;
                this.comment_f = f_index;

                return '';
            }

            this.editQuery(i_index, f_index);
            
        },

        saveComment() { // for remote/inhouse add hours
            if(this.comment.length > 5) {
                this.editQuery(this.comment_i, this.comment_f);
                this.items[this.comment_i][this.comment_f].comment = this.comment;
                this.showCommentWindow = false;
            } else {
                this.$message.info('Пожалуйста, напишите подробнее');
            }
        },

        form() {
          //  console.log(this.fields)
            this.set_letters(this.fields.length);
           // this.set_fields();
        },

        nextItem () { // move by arrows
        return '';
            if (event.keyCode == 38) { // up
                if(this.focused_item !== 0) {
                    this.focused_item--
                }
                console.log('up')
            } else if (event.keyCode == 40) { // down
                if(this.focused_item !== this.items.length - 1) {
                    this.focused_item++
                }
                console.log('down')
            } else if (event.keyCode == 37) { // left
                console.log('left')
                if(this.focused_field !== 0) {
                    this.focused_field--
                }
            } else if (event.keyCode == 39) { // right
                console.log('right')
                if(this.focused_field !== this.fields.length - 1) {
                    this.focused_field++
                }
            }
            this.focus(this.focused_item, this.focused_field);
        },

        letters() {
            return ['A','B','C', 'D','E','F','G','H','I','J','K','L','M','N','O', 'P','Q','R','S','T','U','V','W','X','Y','Z'];
        },

        set_letters(q) {
            let letters = this.letters();

            this.letter_cells.push('A');

            let fl_pos = 0;
            let sl_pos = -1;
            for(let i = 0;i<q - 1;i++) {
                fl_pos = (i + 1) % letters.length;
                if(fl_pos == 0) sl_pos++;

                if(sl_pos >= 0) {
                    this.letter_cells.push(letters[sl_pos] + letters[fl_pos]);
                } else {
                    this.letter_cells.push(letters[fl_pos]);
                }

            } 
            
        },

        handleClick (event, item) { // for context menu
            this.$refs.vueSimpleContextMenu.showMenu(event, item)
        },

        optionClicked (event) { // for context menu
            window.alert(JSON.stringify(event))
        },

        // get array of expression combinations
        combinator(text) {
            if(text === null) return [];
            //let text = "-12+B4+AA10*12.7*AE31/aR7";
            var positions = [];

            text = text.toUpperCase();
            let combinations =  text.match(/A?[A-Z][1-9]?[0-9]/gi);
            let floats =  text.match(/(\*|\/|\+|\-|\s|\(|^){1}[0-9]+\.?[0-9]?/gi); // цифры 

            if(combinations === null) combinations = [];
            if(floats === null) floats = [];
            // find multipliers
            var multiply = [];
            for(var i=0; i<text.length;i++) {
                if (text[i] === "*") {
                    multiply.push(i);
                    positions.push({
                        text: '*',
                        index: i,
                        type: 'action'
                    });
                }
            }

            // find additions
            var addition = [];
            for(var i=0; i<text.length;i++) {
                if (text[i] === "+") {
                    addition.push(i);
                    positions.push({
                        text: '+',
                        index: i,
                        type: 'action'
                    });
                }
            }

            // find substract
            var substract = [];
            for(var i=0; i<text.length;i++) {
                if (text[i] === "-" && i != 0) {
                    substract.push(i);
                    positions.push({
                        text: '-',
                        index: i,
                        type: 'action'
                    });
                }
            }

            // find dividers
            var divider = [];
            for(var i=0; i<text.length;i++) {
                if (text[i] === "/") {
                    divider.push(i);
                    positions.push({
                        text: '/',
                        index: i,
                        type: 'action'
                    });
                }
            }

            // find parentheses
            var parentheses = [];
            for(var i=0; i<text.length;i++) {
                if (text[i] === "(" || text[i] === ")") {
                    parentheses.push(i);
                    positions.push({
                        text: text[i],
                        index: i,
                        type: 'action'
                    });
                }
            }
            
            // cells
            let last_pos = -1;
            for(let i = 0;i<combinations.length;i++) {
                // TODO find value of field
                last_pos++;
                let s = this.searcher(combinations[i]);
             
                last_pos = text.indexOf(combinations[i], last_pos),
                positions.push({
                    text: combinations[i],
                    index: last_pos,
                    type: s !== undefined && s.type == 'formula' ? 'formula' : 'cell',
                    value: s !== undefined ?  s.value : 0,
                    code: s !== undefined ?  '[' + s.column_id + ':'+ s.row_id + ']' : 0,
                });
            }

            // just numbers in expression
            for(let i = 0;i<floats.length;i++) {
                let f_index = text.indexOf(floats[i]);
                let f_text = floats[i];
                if(['*','/','+', '-', '('].includes(floats[i][0])) {
                    f_index++;
                    f_text = f_text.substr(1,f_text.length);
                }
                
                positions.push({
                    text: f_text,
                    index: f_index,
                    type: 'value',
                    value: Number(f_text)
                });
            }

            // sort array
            positions.sort(function(a, b) {
                if (a.index < b.index) return -1;
                if (a.index > b.index) return 1;
                return 0;
            });
           
           return positions;
        },

        searcher(cell){

            let res;
            for (var i=0; i < this.items.length; i++) {
                //console.log(this.items[i])
                Object.values(this.items[i]).forEach(item => {
                    if(item.cell == cell) {
                        res = item;
                    }
                });
            }
            return res;
        },

        formula_searcher(){

            let items = [];
            for (var i=0; i < this.items.length; i++) {
                //console.log(this.items[i])
                Object.values(this.items[i]).forEach(item => {
                    if(item.type == 'formula') {
                        items.push(item);
                    }
                });
            }
            return items;
        }
    } 
}
</script>

<style>

</style> 
