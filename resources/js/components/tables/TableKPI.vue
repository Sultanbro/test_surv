<template>
<div class="kpi-page kpipage">
    <p class="call-norm">{{ group }} </p>
    <div class="d-flex">
        <table class="table table-bordered mr-3 table-sm">
            <!-- <tr v-if="!is_admin">
                <td class="left bold mark">Оклад (в т.ч. налоги)</td>
                <td>
                    <span v-if="is_admin">Индивидуально</span>
                    <span v-else>{{ oklad }}</span>
                </td>
            </tr> -->
            <tr>
                <td class="left bold mark">Выполнение KPI от 80-99%</td>
                <td>
                    <input type="number" v-if="is_admin" class="form-control number_input" v-model="kpi_80_99" /><span v-else>{{ kpi_80_99 }}</span>
                </td>
            </tr>
            <tr>
                <td class="left bold mark">Выполнение KPI на 100%</td>
                <td>
                    <input type="number" v-if="is_admin" class="form-control number_input" v-model="kpi_100" /><span v-else>{{ kpi_100 }}</span>
                </td>
            </tr>
            <!-- <tr v-if="!is_admin">
                <td class="left bold mark">Итого при выполнении плана</td>
                <td>
                    <span v-if="is_admin">Оклад + Премиальные</span>
                    <span v-else>{{ itogo }}</span>
                </td>
            </tr> -->
        </table>
        <table class="table table-bordered table-sm">
            <tr>
                <th class="mark">
                    Нижний порог<br />
                    отсечения премии, %
                </th>
                <th class="mark">
                    Верхний порог<br />
                    отсечения премии, %
                </th>
            </tr>
            <tr>
                <td>
                    <div class="d-flex justify-content-center">
                        <b-input type="number" min="0" max="100" v-if="is_admin" class="form-control number_input mr-2" v-model="nijn_porok" />
                        <span v-else>{{ nijn_porok }}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-center">
                        <b-input type="number" min="0" max="100" v-if="is_admin" class="form-control number_input mr-2" v-model="verh_porok" />
                        <span v-else>{{ verh_porok }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <p class="call-norm">Активности KPI</p>
    <table class="table table-bordered table-sm">
        <tr>
            <th class="mark" v-if="is_admin"></th>
            <th class="left mark">Наименование активности</th>
            <th class="mark w-92" v-if="is_admin" >
                Вид плана
            </th>
            <th class="mark" v-if="type == 'individual'">
                Группа
            </th>
            <th class="mark" v-if="is_admin">
                Показатели
            </th>
            <th class="mark" v-if="is_admin">
                Ед.изм.
            </th>
            <th class="mark">
                Целевое значение<br />
                на месяц
            </th>
            <th class="mark"  v-if="!is_admin" >
                Выполнено
            </th>
            <th class="mark mw">Удельный вес, %</th>
            <th class="mark">
                Сумма премии при<br />
                выполнении плана, тг
            </th>
            <th class="mark mw">% <br />  выполнения</th>
            <th class="mark">
                Сумма премии за<br />
                % выполнения
            </th>
        </tr>
       
        <t-kpi-indicator v-for="kpi_indicator in kpi_indicators" :key="kpi_indicator.id" 
                    :kpi_indicator="kpi_indicator" 
                    :is_admin="is_admin"
                    :nijn_porok="nijn_porok"
                    :verh_porok="verh_porok"
                    :kpi_80_99="kpi_80_99"
                    :kpi_100="kpi_100"
                    :workdays="workdays"
                    :activities="activities"
                    :groups="groups"
                    :type="type">
                    </t-kpi-indicator>
        
        
        
        
        <tr>
            <td class="td-transparent" :colspan="_colspan"></td>
            <td>{{ total }}</td>
        </tr>
    </table>
    <p class="error">{{ error }}</p>
    <p v-if="showAfterEdit">Не забудьте нажать на кнопку "Сохранить", чтобы сохранить изменения и удаления</p>
    
    <button class="btn btn-success" v-if="is_admin" @click.prevent="saveKPIBefore()">Сохранить</button>
    <button class="btn btn-primary" v-if="is_admin" @click.prevent="addkpi_indicator">Добавить</button>
    <button class="btn btn-danger" v-if="is_admin && showDeleteButton" @click.prevent="beforeDeletekpi_indicators">Удалить</button>

    
    
    <p class="text-red mt-2" v-if="!is_admin">
        * сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
    </p>



    <b-modal id="bv-modal" hide-footer>
        <template #modal-title>
            Подтвердите удаление
        </template>
        <div class="d-block ">
            <p>Вы уверены, что хотите удалить выбранные активности? Возможно в таблицы Аналитики группы уже внесены данные.</p>
        </div>
        <div class="d-flex">
            <b-button class="mt-3 mr-1"  variant="danger" block @click.prevent="deletekpi_indicators">Удалить</b-button>
            <b-button variant="primary" class="mt-3 ml-1" block @click.prevent="$bvModal.hide('bv-modal')">Отмена</b-button>
        </div>
    </b-modal>

    <b-modal id="bv-modal-2" hide-footer>
        <template #modal-title>
            Успешно
        </template>
        <div class="d-block ">
            <p>Ваши изменения успешно сохранены!</p>
        </div>
        <div class="d-flex">
            <b-button variant="primary" class="" @click.prevent="$bvModal.hide('bv-modal-2')">Принято</b-button>
        </div>
    </b-modal>


</div>
</template>

<script>
export default {
    name: "TableKPI",
    props: {
        group: {
            default: null, 
        },
        activeuserid: {
            default: 0,
        },
        is_admin: {
            default: false,
        },
        oklad: {
            default: 0,
        },
        group_id: {
            default: 0,
        },
        type: {
            default: 'common',
        },
    },
    watch: {
        kpi_indicators: {
            handler: function(newValue) {
                this.showDeleteButton = false;
                this.kpi_indicators.forEach((kpi_indicator, key) => {
                    if(kpi_indicator.checked) this.showDeleteButton = true;
                })
            },
            deep: true
        },
    },
    computed: {
        itogo() {
            return parseFloat(this.oklad) + parseFloat(this.total);
        },
        total() {
            let _total = 0;

            this.kpi_indicators.forEach((kpi_indicator, key) => {
                if(!kpi_indicator.deleted) _total += parseFloat(kpi_indicator.result);
            })
            return _total;
        },
    },
    data() {
        return {
            kpi_80_99: 0,
            kpi_100: 0,
            kpi_indicators: [],
            activities: [],
            nijn_porok: 80,
            verh_porok: 100,
            _colspan: 5,
            groups: [],
            showDeleteButton: false,
            showAfterEdit: false,
            workdays: 26,
            error: '',
        };
    },
    created() {
        if(this.type == 'individual') {
            this.fetchIndividual();
        } else {
            this.fetch();
        }
        
        this._colspan = this.is_admin ? 9 : 6
        
       
    },
    methods: {
        fetch() {
            let loader = this.$loading.show();
            
            axios
                .post("/timetracking/kpi_get", {
                    group_id: this.group_id,
                    is_admin: this.is_admin,
                    activeuserid: this.activeuserid
                })
                .then((response) => {
                    this.kpi_indicators = response.data.kpi_indicators
                    this.kpi_80_99 = response.data.kpi.kpi_80_99 ? Number(response.data.kpi.kpi_80_99) : 0;
                    this.kpi_100 = response.data.kpi.kpi_100 ? Number(response.data.kpi.kpi_100) : 0;
                    this.nijn_porok = response.data.kpi.nijn_porok ? response.data.kpi.nijn_porok : 0;
                    this.verh_porok = response.data.kpi.verh_porok ? response.data.kpi.verh_porok : 0;
                    this.activities = response.data.activities;
                    
                    this.workdays = response.data.workdays
                   
                    loader.hide();
                });
        },

        saveKPIBefore() {
            if(this.type == 'individual') {
                this.saveKPIIndividual();
            } else {
                this.saveKPI();
            }
        },

        fetchIndividual() {
            let loader = this.$loading.show();


            axios
                .post("/timetracking/kpi_get_individual", {
                    group_id: this.group_id,
                    is_admin: this.is_admin,
                    activeuserid: this.activeuserid
                })
                .then((response) => {
                    this.kpi_indicators = response.data.kpi_indicators
                    this.kpi_80_99 = response.data.kpi.kpi_80_99 ? Number(response.data.kpi.kpi_80_99) : 0;
                    this.kpi_100 = response.data.kpi.kpi_100 ? Number(response.data.kpi.kpi_100) : 0;
                    this.nijn_porok = response.data.kpi.nijn_porok ? response.data.kpi.nijn_porok : 0;
                    this.verh_porok = response.data.kpi.verh_porok ? response.data.kpi.verh_porok : 0;
                    this.activities = response.data.activities;
                    
                    this.workdays = response.data.workdays
                    this.groups = response.data.groups
                   
                    loader.hide();
                });
        },

        getBusinessDateCount(month, year, workdays) {
  
            month = month - 1; 
            let next_month = (month + 1) == 12 ? 0 : month + 1; 
            let next_year = (month + 1) == 12 ? year + 1 : year; 

            var start = new Date(year, month, 1);
            var end = new Date(next_year, next_month, 1);

            let days = (end - start) / 86400000;

            let business_days = 0,
                weekends = workdays == 5 ? [0,6] : [0];

            for(let i = 1; i <= days; i++) {
                let d = new Date(year, month, i).getDay(); 
                if(!weekends.includes(d)) business_days++;
            }
            
            return business_days;
        },


        saveKPI() { 
            this.error = ''
            this.showAfterEdit = false
            let loader = this.$loading.show()

            let err = this.error

            this.kpi_indicators.forEach((kpi_indicator, key) => {
                if(kpi_indicator.name == '' && !kpi_indicator.deleted) err = 'Пожалуйста, задайте "Название активности"!' // название kpi indicatora
            })
            
            if(err != '') {
                this.error = err;
                return loader.hide();
            }
            


            axios.post("/timetracking/kpi_save", {
                    group_id: this.group_id,
                    kpi_indicators: this.kpi_indicators,
                    kpi_80_99: this.kpi_80_99,
                    kpi_100: this.kpi_100,
                    nijn_porok: this.nijn_porok,
                    verh_porok: this.verh_porok,
                })
                .then((response) => {
                    this.$bvModal.show('bv-modal-2')
                    this.fetch();
                    loader.hide()
                });
        },
        
        saveKPIIndividual() { 
            this.error = ''
            this.showAfterEdit = false
            let loader = this.$loading.show()

            let err = this.error

            this.kpi_indicators.forEach((kpi_indicator, key) => {
                if(kpi_indicator.name == '' && !kpi_indicator.deleted) err = 'Пожалуйста, задайте "Название активности"!' // название kpi indicatora
            })
            
            if(err != '') {
                this.error = err;
                return loader.hide();
            }



            axios.post("/timetracking/kpi_save_individual", {
                    user_id: this.group_id,
                    kpi_indicators: this.kpi_indicators,
                    kpi_80_99: this.kpi_80_99,
                    kpi_100: this.kpi_100,
                    nijn_porok: this.nijn_porok,
                    verh_porok: this.verh_porok,
                })
                .then((response) => {
                    this.$bvModal.show('bv-modal-2')
                    this.fetchIndividual();
                    loader.hide()
                });
        },

        addkpi_indicator() {
            this.kpi_indicators.push({
                daily_plan: 0,
                id: 0,
                group_id: this.group_id,
                activity_id: 0,
                completed: 100,
                result: 0,
                cell: null,
                deleted: false,
                name: "",
                plan_unit: "minutes",
                unit: "",
                ud_ves: 0,
            });
        },

        beforeDeletekpi_indicators() {
            this.kpi_indicators.forEach((kpi_indicator, key) => {
                if(kpi_indicator.checked) this.$bvModal.show('bv-modal')
            })
        },
        deletekpi_indicators() {
            this.kpi_indicators.forEach((kpi_indicator, key) => {
                this.error = ''
                this.showAfterEdit = true
                if(kpi_indicator.checked) kpi_indicator.deleted = true
                this.$bvModal.hide('bv-modal')
            })
            
        }
    },
};
</script>

<style lang="scss" scoped>
.kpi-page.kpipage {
    .number_input {
        width: 100px;
        display: inline-block;
        text-align: center;

        &.form-control {
            padding-left: 23px;
        }
    }
    .form-control {
        padding: 2px 7px;
        font-size: 14px;
        border: 0;
    }
    .table-bordered {
        th {
            font-weight: 600;
        }

        td,
        th {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;

            &.left {
                text-align: left;
            }

            &.bold {
                font-weight: 600;
            }

            &.mark {
                background: aliceblue;
                color: #0077e0;
            }
        }
    }

    .form-control { 
        padding: 2px 7px;
        font-size: 14px;
        border: 0;
    }
    .error {color:red}
    .call-norm {
        font-size: 18px;
        font-weight: 700;
        padding: 15px 0;
        color: #333;
        margin-bottom: 0;
    }

    .td-transparent {
        border-bottom-color: transparent !important;
        border-left-color: transparent !important;
    }
    .w-92 {width: 92px;}
    .mw {width: 1px;}
}

</style>
