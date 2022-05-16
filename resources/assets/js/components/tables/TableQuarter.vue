<template>
    <div class="quarter-page">
        <p class="call-norm">{{ group }} </p>

<<<<<<< HEAD
=======
<!--        <button @click="sayHello">clic 1</button>-->
<!--        <button v-on:mouseover="sayHelloO">clic 2</button>-->

        <span class="label label-default" v-tooltip:bottom="tooltipText" />




<!--        <div v-show="active">Show</div>-->
<!--        <div @mouseover="mouseOver">Hover over me!</div>-->
>>>>>>> 968c676b9667baf337d1d469e6293d81fd9b6141

        <div class="d-flex">

            <table class="table table-bordered table-sm">
                <tr>

                    <th class="mark">
                        <label for="quarter_1" v-b-popover.hover.top.html="'Количество сотрудников на данный момент'" title="Первый Квартал" >Первый Квартал</label>

                        <input   type="checkbox" id="quarter_1" class="position-absolute ml-3" style="margin-top:-5px">

                    </th>
                    <th class="mark">
                        <label for="quarter_2" v-b-popover.hover.top.html="'Количество сотрудников на данный момент'" title="Второй квартал">Второй квартал</label>
                        <input type="checkbox" id="quarter_2" class="position-absolute ml-3" style="margin-top:-5px">
                    </th>
                </tr>
                <tr class="quarter-inputs">
                    <th>
                        <input type="number" class="form-control" placeholder="Сумма">
                        <input type="text" class="mt-1 form-control" placeholder="Текстовый поля">
                    </th>
                    <th>
                        <input type="number" class="form-control" placeholder="Сумма">
                        <input type="text" class="mt-1 form-control" placeholder="Текстовый поля">
                    </th>

                </tr>
                <tr>
                    <th class="mark">
                        <label for="quarter_3" v-b-popover.hover.top.html="'Количество сотрудников на данный момент'" title="Третий Квартал">Третий Квартал</label>
                        <input type="checkbox" id="quarter_3" class="position-absolute ml-3" style="margin-top:-5px">
                    </th>
                    <th class="mark">
                        <label for="quarter_4" v-b-popover.hover.top.html="'Количество сотрудников на данный момент'" title="Четвертый квартал">Четвертый квартал</label>
                        <input type="checkbox" id="quarter_4" class="position-absolute ml-3" style="margin-top:-5px">
                    </th>
                </tr>
                <tr>
                    <th><input type="number" class="form-control" placeholder="Сумма">
                        <input type="text" class="mt-1 form-control" placeholder="Текстовый поля">
                    </th>
                    <th>
                        <input type="number" class="form-control" placeholder="Сумма">
                        <input type="text" class="mt-1 form-control" placeholder="Текстовый поля">
                    </th>
                </tr>

            </table>
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

        data() {


                return{
                    name: "imashev",
                    active: false,

                }
        },
        methods:{
            sayHello(){
                console.log('123')
            },
            sayHelloO(){
                this.active = false;
                console.log('010203')
            }
        },

        computed: {
            tooltipText: function() {
                // put your logic here to change the tooltip text
                return 'This is a computed tooltip'
            }
        }
    }


</script>

<style lang="scss" scoped>


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
