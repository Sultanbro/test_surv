<template>


    <div class="quarter-page">
        <p class="call-norm">{{ group }} </p>

        <div class="d-flex">



            <table class="table table-bordered table-sm mark">



                <thead style="background: aliceblue;color: #0077e0;">
                    <tr>
                        <th style="width: 25%">Период</th>
                        <th>Сумма</th>
                        <th>Коментарии</th>
                    </tr>
                </thead>

                <tr v-for="item in arr"  >


                    <th >
                        <!--<span style="cursor: pointer" v-b-popover.hover.right.html="'Количество сотрудников на данный момент'" >-->
                            <!--<label v-for="item.quarter">-->
                            <span  v-if="item.quarter == 1"> Первый </span>
                            <span  v-if="item.quarter == 2"> Второй </span>
                            <span  v-if="item.quarter == 3"> Третий </span>
                            <span  v-if="item.quarter == 4"> Четвертый </span>
                            Квартал
                        <!--</span>-->
                            <!--</label>-->
                            <input v-model="item.checked" style="position: absolute;margin-top:-6px;margin-left: 10px;cursor: pointer" type="checkbox" />
                            <p style="font-size: 14px" v-if="item.quarter == 1">Период с 01.01.{{item.year}} до 31.03.{{item.year}}</p>
                            <p style="font-size: 14px" v-if="item.quarter == 2">Период с 01.04.{{item.year}} до 30.06.{{item.year}}</p>
                            <p style="font-size: 14px" v-if="item.quarter == 3">Период с 01.07.{{item.year}} до 30.09.{{item.year}}</p>
                            <p style="font-size: 14px" v-if="item.quarter == 4">Период с 01.10.{{item.year}} до 31.12.{{item.year}}</p>

                    </th>
                    <th>
                        <div v-if="item.checked" >
                            <input  v-model="item.sum" type="number" class="form-control mb-1" value="0"/>
                        </div>
                    </th>
                    <th>
                        <div v-if="item.checked">
                            <textarea  v-model="item.text" class="form-control" placeholder="Коментарии"></textarea>
                        </div>
                    </th>
                </tr>
            </table>



        </div>

        <div v-for="item in items">
            <p>{{ item.quarter }} квартал</p>
            <input type="checkbox" v-model="item.checked">
            <div v-if="item.checked">
                <input type="number" v-model="item.sum">
                <textarea v-model="item.text"></textarea>
            </div>
        </div>


        <!--selectedQuarter-->
        <div class="col-12 p-0 row">
            <div class="col-6 pr-0">
                <div v-if="errors.length">
                        <p style="color: #c75f5f" v-for="error in errors">{{ error }}</p>
                </div>
                <a style="color: white;text-align: center;border-radius: unset"
                   id="selectedQuarter" @click="selectedQuarter"
                   class=" btn-block btn btn-success p-0"  >Активировать</a>
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
<<<<<<< HEAD
            user_id: {
=======
            oklad: {
>>>>>>> ca9bd50832cb224fad949023595cef684498cb41
                default: 0,
            },
            type: {
                default: 'common',
            },
        },
        data() {
            return{
<<<<<<< HEAD
                    arr:{
                        sum:[],
                        text:[],
                        quartal:[],
                    },
                    errors: [],
                    active: false,

                }
=======
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
            this.axios();

            this.emptyArr();
>>>>>>> ca9bd50832cb224fad949023595cef684498cb41
        },
        created(){
            // this.arr = this.selector();
            this.getQuartalBonuses();
        },
        methods:{
<<<<<<< HEAD
            getQuartalBonuses(){
                axios
                    .post('/timetracking/quarter/get/quarter/', {
                    user_id:this.user_id
=======

            emptyArr() {

                let arr =[];

                for(let i=1;i<=4;i++) {
                    arr.push({
                        text: '',
                        checked: false,
                        sum: 0,
                        quarter: i
                    });
                }

                this.items = arr;
            },

            selectedQuarter() {
                axios.post('/timetracking/quarter/store', {
                    items:this.items,
                    user_id: this.user_id,
                }).then(response => {
                    this.items = response.data.items

                    console.log(response,'kii');

>>>>>>> ca9bd50832cb224fad949023595cef684498cb41
                })
                .then(response => {
                    this.arr = response.data[0];
                });

            },

            selectedQuarter() {
                this.errors = [];

                console.log(this.errors,'imashev kairat');

                for (let i = 1;i <=4;i++){

                    if (this.arr[i]['checked']){
                        // console.log(this.arr[i]['text'],'pro');
                        if (!this.arr[i]['text'].length > 0) {


                            // this.errors.push('Заполните коментарии');


                            if (i == 1){
                                this.errors[i] = 'Заполните Коментарии Первого Квартала';
                            }else if (i == 2){
                                this.errors[i] = 'Заполните Коментарии Второго Квартала';
                            }else if (i == 3){
                                this.errors[i] = 'Заполните Коментарии Третьего Квартала';
                            }else if (i == 4){
                                this.errors[i] = 'Заполните Коментарии Четвертого Квартала';
                            }

                        }
                    }
                }

                if (this.errors.length === 0){
                    axios.post('/timetracking/quarter/store', {
                        arr:this.arr,
                        user_id:this.user_id,
                    }).then(response => {


                        if (response.data.success == 1){
                            alert('Успешно изменено');
                            console.log('1995');
                        }
                    })
                }
            },

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
