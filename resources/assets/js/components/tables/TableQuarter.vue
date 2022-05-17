<template>
    <div class="quarter-page">
        <p class="call-norm">{{ group }} </p>
        <div class="d-flex">
             <table class="table table-bordered table-sm">
                    <tr>

                        <th class="mark">
                            <label @click="checkedDisabled('1')" for="quarter_1" v-b-popover.hover.top.html="'При активации профиль сотрудника появится условия получение квартального бонуса '"  >Первый Квартал</label>
                            <input  @click="checkedDisabled('1')" v-model="checked_quartal" value="1" type="radio" name="checked_quarter" id="quarter_1" class="position-absolute ml-3" style="margin-top:-5px">
                        </th>
                        <th class="mark">
                            <label @click="checkedDisabled('2')" for="quarter_2" v-b-popover.hover.top.html="'При активации профиль сотрудника появится условия получение квартального бонус'" title="Второй квартал">Второй квартал</label>
                            <input @click="checkedDisabled('2')" v-model="checked_quartal" value="2" type="radio" name="checked_quarter" id="quarter_2" class="position-absolute ml-3" style="margin-top:-5px">
                        </th>
                    </tr>
                    <tr class="quarter-inputs">
                        <th>
                            <input disabled v-model.trim='form.sum_1'   id="input-sum-1" type="number"  value="0"  class="form-control" placeholder="Сумма">
                            <textarea  disabled v-model.trim='form.text_1' id="input-text-1"  class="mt-1 form-control" placeholder="Текстовый поля"></textarea>
                        </th>
                        <th>
                            <input disabled v-model.trim='form.sum_2'  id="input-sum-2"  type="number" class="form-control" placeholder="Сумма">
                            <textarea  disabled v-model.trim='form.text_2' id="input-text-2"  class="mt-1 form-control" placeholder="Текстовый поля"></textarea>
                        </th>

                    </tr>
                    <tr>
                        <th class="mark">
                            <label @click="checkedDisabled('3')" for="quarter_3" v-b-popover.hover.top.html="'При активации профиль сотрудника появится условия получение квартального бонус'" >Третий Квартал</label>
                            <input @click="checkedDisabled('3')" v-model="checked_quartal" value="3" type="radio" name="checked_quarter" id="quarter_3" class="position-absolute ml-3" style="margin-top:-5px">
                        </th>
                        <th class="mark">
                            <label @click="checkedDisabled('4')" for="quarter_4" v-b-popover.hover.top.html="'При активации профиль сотрудника появится условия получение квартального бонус'" >Четвертый квартал</label>
                            <input @click="checkedDisabled('4')" v-model="checked_quartal" value="4" type="radio" name="checked_quarter" id="quarter_4" class="position-absolute ml-3" style="margin-top:-5px">
                        </th>
                    </tr>
                    <tr>
                        <th><input disabled  v-model.trim='form.sum_3' id="input-sum-3"  type="number" class="form-control" placeholder="Сумма">
                            <textarea  disabled v-model.trim='form.text_3' id="input-text-3" class="mt-1 form-control" placeholder="Текстовый поля"></textarea>                        </th>
                        <th>
                            <input disabled v-model.trim='form.sum_4'  id="input-sum-4"  type="number" class="form-control" placeholder="Сумма">
                            <textarea  disabled v-model.trim='form.text_4' id="input-text-4"  class="mt-1 form-control" placeholder="Текстовый поля"></textarea>

                        </th>
                    </tr>
                </table>
        </div>

        <div v-for="item in items">

            <input type="checkbox" v-model="item.checked">
            <input type="number" v-model="item.sum">
            <textarea v-model="item.text"></textarea>

        </div>


        <!--selectedQuarter-->
        <div class="col-12 p-0 row">
            <div class="col-6 pr-0">
                <a style="color: white;text-align: center;border-radius: unset"
                   id="selectedQuarter" @click="selectedQuarter"
                   class=" btn-block btn btn-success p-0"  >Активировать</a>
            </div>
            <div class="col-6 pr-0">
                <a style="color: white;text-align: center;border-radius: unset"
                   id="selectedQuarterReset"
                   class=" btn-block btn btn-danger p-0"  >Обнулить</a>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: "TableQuarter",
        props: {
            group: {
                default: 'Ежегодный  квартальный календарь',
            },
            activeuserid: {
                default: 0,
            },
            user_id: {
                default: 0,
            },
            is_admin: {
                default: false,
            },
            oklad: {
                default: 0,
            },
            type: {
                default: 'common',
            },
        },
        data() {
            return{
                active: false,
                checked_quartal:false,
                form:{
                    sum_1:'',
                    sum_2:'',
                    sum_3:'',
                    sum_4:'',
                    text_1:'',
                    text_2:'',
                    text_3:'',
                    text_4:'',
                },
                items: []
            }
        },
        created() {
            this.selector();
        },
        methods:{

            selector() {

                let arr =[];

                for(let i=1;i<=4;i++) {
                    arr.push({
                        text: '',
                        checked: false,
                        sum: 0,
                        quarter: $i
                    });
                }

                this.items = arr;
            },
            
            selectedQuarter() {
                axios.post('/timetracking/quarter/store', {
                    items:this.items,
                    user_id: this.user_id,
                }).then(response => {
                    console.log(response,'kii');

                })
                    .catch(error => {
                        console.log(error.response)
                    });

                //     .then(response => {
                //     this.$message.success('Сохранено!');
                // })
                //     .catch(error => {
                //     this.$message.error('Ошибка');
                // })

            },

            checkedDisabled(par_id){

                console.log(this.checked_quartal);
                console.log('imashev');


                for (let i = 1;i <= 4;i++){
                    document.getElementById('input-sum-'+i).setAttribute('disabled','disabled');
                    document.getElementById('input-text-'+i).setAttribute('disabled','disabled');
                }
                document.getElementById('input-sum-'+par_id).removeAttribute('disabled');
                document.getElementById('input-text-'+par_id).removeAttribute('disabled');

                document.getElementById('selectedQuarter').classList.remove('selectedQuarter');

            }
        },


    }


</script>

<style lang="scss" scoped>


    .selectedQuarter{
        display:none;
    }
    .mark label{
        cursor: pointer;
    }

    .quarter-page {
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
            border: 1px solid #dee2e6;
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
