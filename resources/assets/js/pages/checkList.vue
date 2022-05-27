<template>

    <div class="check-page mt-2">
        <div class="col-md-12 p-0">
            <div class="col-md-2">
                <a id="showCheckSideBar" @click="addNewCheckModalShow()"  class="btn btn-success" style="color: white">Создать чек лист aaa</a>
            </div>

            <div class="col-md-3">
                <input v-model="inputSearch"  type="text"  class="form-control" placeholder="Поиск"/>
            </div>


        </div>

        <div class="col-md-12 mt-4">

            <i class="bi bi-reception-4"></i>

            <div class="view-table">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Сотрудники/отдел</th>
                        <th scope="col">Кол-показов
                            <i class="fa fa-info-circle" style="cursor: pointer"
                               v-b-popover.hover.right.html="'Сколько раз будет уведомление Чек лист автоматически будет всплывать в кабинете сотрудника'"
                               title="Работают">
                            </i>
                        </th>
                        <th scope="col"  >Постановщик</th>
                        <th scope="col" >Статитика</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr v-for="arrCheckList of arrCheckLists">
                        <td >
                            <a href="#"   @click="editCheck(arrCheckList.id)">
                                {{arrCheckList.title}}
                            </a>
                        </td>
                        <td >
                            {{arrCheckList.count_view}}
                        </td>
                        <td>
                            {{arrCheckList.auth_last_name}}         {{arrCheckList.auth_name}}
                        </td>
                        <td class="position-relative">
                            <i class="pl-4 far fa-address-card fa-2x"></i>
                            <!--<i class="pl-4 far fa-signal fa-2x"></i>-->

                            <a class="position-absolute" @click="arrCheckDelete(arrCheckList.id)" style="right: 0">
                                <i class="fa fa-trash fa-2x" aria-hidden="true"></i>
                            </a>
                        </td>

                    </tr>


                    </tbody>
                </table>
            </div>

        </div>

        <!--<b-modal id="bv-modal-2" hide-footer>-->
        <!--<template #modal-title>-->
        <!--Редактировать-->
        <!--</template>-->
        <!--<div class="d-block">-->
        <!--<input value="asdasd" class="form-control"/>-->

        <!--<input value="2" class="form-control mt-2"/>-->
        <!--</div>-->
        <!--<div class="d-flex mt-2">-->
        <!--<b-button variant="primary mr-2" class="" @click.prevent="$bvModal.hide('bv-modal-2')">Сохранить</b-button>-->
        <!--<b-button variant="danger" class="" @click.prevent="$bvModal.hide('bv-modal-2')">Отменить</b-button>-->
        <!--</div>-->
        <!--</b-modal>-->

        <sidebar
                title="Создать чек лист"
                :open="showCheckSideBar"
                @close="showCheckSideBar = false"
                width="60%"
        >

            <div class="col-md-12 p-0">
                <div class="row">
                    <div class="col-md-3">
                        <p>Для группы чек лист</p>
                    </div>
                    <div class="col-md-4 p-0" >

                        <label for="gr-id"  :open="click_show.gr" @close="click_show.gr = false" >
                            <p style="margin-left: 30px;cursor: pointer">Группы</p>
                        </label>

                        <div class="position-absolute" style="top: 0px">
                            <b-form-checkbox  v-model="click_show.gr"  id="gr-id"></b-form-checkbox>
                        </div>
                        <!--<textarea class="form-control btn-block"></textarea>-->
                    </div>
                </div>
                <div class="row" v-if="click_show.gr">
                    <div class="col-md-11" style="margin-top: -10px;margin-bottom: 10px;">
                        <div>
                            <!--<label class="typo__label">Tagging</label>-->
                            <multiselect v-model="valueGroups" tag-placeholder="Add this as new tag" placeholder="Выбрать группы"
                                         label="name" track-by="code" :options="groups_arr" :multiple="true" :taggable="true">
                            </multiselect>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <p>Для должностных чек лист</p>
                    </div>
                    <div class="col-md-4 p-0" >

                        <label for="ps-id"  :open="click_show.ps" @close="click_show.ps = false">
                            <p style="margin-left: 30px;cursor: pointer">Должности</p>
                        </label>

                        <div class="position-absolute" style="top: 0px">
                            <b-form-checkbox  v-model="click_show.ps"  id="ps-id"></b-form-checkbox>
                        </div>
                        <!--<textarea class="form-control btn-block"></textarea>-->
                    </div>
                </div>
                <div class="row" v-if="click_show.ps">
                    <div class="col-md-11" style="margin-top: -10px;margin-bottom: 10px;">
                        <div>
                            <!--<label class="typo__label">Tagging</label>-->
                            <multiselect v-model="valuePositions" tag-placeholder="Add this as new tag" placeholder="Выбрать должности"
                                         label="name" track-by="code" :options="positions_arr" :multiple="true" :taggable="true">
                            </multiselect>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <p>Для сотрудников чек лист</p>
                    </div>
                    <div class="col-md-4 p-0" >

                        <label for="us-id"  :open="click_show.us" @close="click_show.us = false">
                            <p style="margin-left: 30px;cursor: pointer">Сотрудники</p>
                        </label>

                        <div class="position-absolute" style="top: 0px">
                            <b-form-checkbox  v-model="click_show.us"  id="us-id"></b-form-checkbox>
                        </div>
                        <!--<textarea class="form-control btn-block"></textarea>-->
                    </div>
                </div>

                <div class="row" v-if="click_show.us">
                    <div class="col-md-11" style="margin-top: -10px;margin-bottom: 10px;">
                        <div>
                            <!--<label class="typo__label">Tagging</label>-->
                            <multiselect  tag-placeholder="Add this as new tag" placeholder="Выбрать сотрудников"
                                          label="name" track-by="code" :options="groups_arr" :multiple="true" :taggable="true">
                            </multiselect>

                        </div>
                    </div>
                </div>



                <div class="row mt-3 pb-3" style="border-bottom: 1px solid #dee2e6">
                    <div class="col-md-3">
                        <p>Колво показов</p>
                    </div>
                    <div class="col-md-4 p-0" >
                        <input v-model="countView"   placeholder="Максимум 10 " type="number" class="form-control btn-block">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 pr-0 mt-2" v-for="(item, index) in arrCheckInput">

                        <div class="col-md-7">
                            <div class="position-absolute" style="margin-left: -15px;top: 2px">
                                <b-form-checkbox v-model="item.checked"  ></b-form-checkbox>
                            </div>
                            <input v-model="item.text"  type="text" placeholder="Впишите активность чек листа" class="form-control btn-block ml-2">
                        </div>
                        <div class="col-md-3 p-0 mr-3 ml-1">
                            <input v-model="item.https"  type="text" placeholder="https:" class="form-control btn-block ">
                        </div>

                        <button v-if="index == '0'"  @click="deleteCheckList(index)"
                                type="button"  title="Удалить чек-лист"
                                class="btn btn-secondary btn-sm">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>


                        <button v-else  @click="deleteCheckList(index)"
                                type="button"  title="Удалить чек-лист"
                                class="btn btn-primary btn-sm">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="col-md-12 mt-3">


                        <div v-if="errors.show" class="alert mb-3 alert-danger p-2" >
                          <span v-if="this.errors.message">
                              {{ this.errors.message}}
                          </span>
                            <span v-if="this.errors.countViewError">
                              {{ this.errors.countViewError}}
                          </span>

                            <button type="button" class="close mb-3" @click="closeAlert()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>


                        <!--<div v-if="countViewTextError.length">-->
                        <!--<p style="color: #c75f5f">{{ this.countViewTextError }}</p>-->
                        <!--</div>-->

                        <div class="col-md-6 p-0">

                            <button type="button" @click="addCheckList()" title="Добавить новый пункт чек листа" class="btn btn-success">
                                Добавить пункт чек листа
                            </button>

                            <button v-if="addButton" type="button" @click="saveCheckList()" title="Сохранить" class="btn btn-primary">
                                Cохранить
                            </button>

                            <button v-if="editButton" type="button" @click="saveEditCheckList(arrCheckInput)" title="Сохранить" class="btn btn-primary">
                                Изменить Сохранение
                            </button>
                        </div>

                        <div class="col-md-6 p-0">

                        </div>
                    </div>
                </div>


            </div>

        </sidebar>
    </div>
</template>


<script>
    import Multiselect from 'vue-multiselect'
    export default {
        name: "TableQuarter",

        props: {
            groups:{},
            allusers:{},
            positions:{}
        },
        components: {
            Multiselect
        },
        data() {
            return{
                valueGroups:[],
                valuePositions:[],
                groups_arr:[],
                allusers_arr:[],
                positions_arr:[],
                arrCheckInput:[],
                arrCheckLists:[],
                inputSearch:'',
                countView:'1',
                errors: {
                    message:'',
                    text:[],
                    counterror:[],
                    show:false,
                    save:false,
                    save_checkbox:false,
                    class:'btn btn-success',
                },
                active: false,
                showCheckSideBar:false,
                addButton:false,
                editButton:false,
                check_id:null,
                click_show:{
                    gr:false,
                    ps:false,
                    us:false
                }

            }
        },
        // computed:{
        //     todosByTitle() {
        //         console.log(this.inputSearch);
        //         // return this.todos.filter(item => item.title.indexOf(this.search) !== -1)
        //     },
        // },
        created(){
            this.addCheckList()
            this.viewCheckList()
        },
        mounted() {

            // this.allusers_arr = JSON.parse(this.allusers);
            this.positions_arr = this.positions;





            if (Object.keys(JSON.parse(this.groups)).length > 0){
                this.groups_arr = JSON.parse(this.groups);
                const arrayFailedGr = Object.entries(this.groups_arr).map((arr) => ({
                    code: arr[0],
                    name: arr[1],
                }));
                this.groups_arr = arrayFailedGr
            }

            if (Object.keys(this.positions_arr).length > 0){

                const arrayFailedPs = Object.entries(this.positions_arr).map((arr) => ({
                    code: arr[0],
                    name: arr[1],
                }));
                this.positions_arr = arrayFailedPs
            }







            // console.log(this.positions_arr,'pos')
        },
        methods:{
            addNewCheckModalShow(){
                this.showCheckSideBar = true
                this.addButton = true
                this.editButton = false
                this.click_show.gr = false
                this.click_show.ps = false
                this.click_show.us = false
                this.valueGroups = []
                this.arrCheckInput=
                    [
                        {   checked: true,
                            text: '',
                            https:''
                        },

                    ]


                // this.addCheckList()
                // this.viewCheckList()

            },
            saveEditCheckList(arrCheckInput,){



                this.validateInput(arrCheckInput,this.countView,this.valueGroups)



                if (this.errors.save){
                    axios.post('/timetracking/settings/edit/check/save/', {
                        check_id:this.check_id,
                        valueGroups:this.valueGroups,
                        countView:this.countView,
                        arrCheckInput:arrCheckInput,
                    }).then(response => {



                        this.$message.success('Успешно изменен');
                        this.errors.show = false;
                        this.showCheckSideBar = false;
                        this.viewCheckList()
                    })
                }


            },
            editCheck(check_id){
                this.addButton = false
                this.editButton = true
                this.showCheckSideBar = true
                this.check_id = check_id
                this.errors.show = false


                axios.post('/timetracking/settings/edit/check', {
                    check_id:check_id
                }).then(response => {



                    this.countView = response.data.count_view
                    this.arrCheckInput = JSON.parse(response.data['active_check_text'])
                    this.valueGroups = JSON.parse(response.data['role_check'])

                    if (this.valueGroups.length > 0){
                        this.click_show.gr = true
                    }


                })



            },
            arrCheckDelete(id){

                let confirmDelte = confirm("Вы действительно хотите безвозвратно удалить ?");

                if (confirmDelte){
                    axios.post('/timetracking/settings/delete/check', {
                        delete_id:id
                    }).then(response => {
                        this.viewCheckList()
                        this.$message.success('Успешно Удалено');
                    })
                }


            },
            viewCheckList(){
                axios.get('/timetracking/settings/list/check', {
                }).then(response => {
                    this.arrCheckLists = response.data
                })
            },
            saveCheckList(){
                this.saveButton = true
                this.errors.save_checkbox = false
                this.validateInput(this.arrCheckInput,this.countView,this.valueGroups)


                if (this.click_show.gr == true || this.click_show.ps == true || this.click_show.us == true){
                    this.errors.save_checkbox = true;
                    if (this.click_show.gr == true && this.valueGroups.length  == 0){
                        this.errors.save_checkbox = false;
                        this.errors.show = true;
                        this.errors.message = "Выбрать Группы"
                    }
                    if (this.click_show.ps == true && this.valuePositions.length  == 0){
                        this.errors.save_checkbox = false;
                        this.errors.show = true;
                        this.errors.message = "Выбрать Должности"
                    }
                }else {
                    this.errors.save_checkbox = false;
                    this.errors.show = true;
                    this.errors.message = "Выбрать для кого чек лист"
                }



                if (this.errors.save &&  this.errors.save_checkbox){
                    axios.post('/timetracking/settings/add/check', {
                        valueGroups:this.valueGroups,
                        countView:this.countView,
                        arrCheckInput:this.arrCheckInput,
                    }).then(response => {

                        if (response.data.success == false){

                            this.$message.error('Уже существует вы можете от отредактировать aa');
                            this.errors.show = false;
                            this.showCheckSideBar = false;
                        }else {


                            this.$message.success('Успешно Добавлено');
                            this.errors.show = false;
                            this.showCheckSideBar = false;
                            this.viewCheckList()
                        }

                    })
                }


            },
            addCheckList() {
                // this.buttonClass = 'primary',
                this.arrCheckInput.push({
                    checked: true,
                    text: "",
                    https: '',
                });
            },
            deleteCheckList(index){

                // console.log(this.valueGroups ,'07777')

                if (index != 0){
                    this.arrCheckInput.splice(index, 1)
                }else {
                    alert('К сожалению последний позиция не удаляется');
                }

            },

            validateInput(array_check_input,count_view,value_groups){
                // console.log(array_check_input,count_view,value_groups,'fuckk')
                this.countView = count_view,
                    this.valueGroups = value_groups,
                    this.arrCheckInput = array_check_input
                this.errors.save = false

                for (let i = 0; i < this.arrCheckInput.length;i++){
                    // if (this.arrCheckInput[i]['checked'] === true){

                    if (this.arrCheckInput[i]['text'] != null){
                        if (this.arrCheckInput[i]['text'].length < 1){
                            this.errors.text.push('errorText');
                        }
                    }else {
                        this.errors.text.push('errorText');
                    }

                    // }else{
                    //     this.errors.counterror.push('errorCount');
                    //  }
                }
                // if (this.errors.counterror.length != this.arrCheckInput.length){

                if (this.errors.text.length == 0 && this.countView < 11 && this.countView != 0){
                    this.errors.save = true
                }else{
                    this.errors.message = 'заполните текст'
                    this.errors.show = true

                    if (this.countView == 0){
                        if (this.errors.text.length == 0){
                            this.errors.message = 'Колво показов минимум 1 '
                        }else {
                            this.errors.message =  'заполните текст и Колво показов минимум 1'
                        }
                    }else if (this.countView > 11) {
                        if (this.errors.text.length == 0) {
                            this.errors.message = 'Колво показов максимум 10'
                        }else {
                            this.errors.message = 'заполните текст и Колво показов максимум 10'
                        }
                    }
                    this.errors.text = []
                    this.errors.counterror = []
                }
                // }else {
                //     this.errors.counterror = []
                //     this.errors.show = true
                //     this.errors.message = 'Выбрать хоть один Чек лист'
                // }
            },
            closeAlert() {
                this.errors.show = false;
            },
        },

    }


</script>

<style lang="scss" scoped>

    .check-page {
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