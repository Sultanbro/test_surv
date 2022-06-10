<template>

    <div class="check-page mt-2">
        <div class="col-md-12 p-0 row">
            <div class="col-md-2">
                <a id="showCheckSideBar" @click="addNewCheckModalShow()"  class="btn btn-success" style="color: white">Создать чек лист</a>
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
                            <a href="#"   @click="editCheck(arrCheckList.id,arrCheckList.item_type)">
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
                            <a href="/timetracking/quality-control">
                                <i class="pl-4 far fa-address-card fa-2x"></i>
                            </a>

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

        <b-modal id="bv-modal-2" hide-footer>
        <template #modal-title>
        Редактировать
        </template>
        <div class="d-block">
        <input value="asdasd" class="form-control"/>

        <input value="2" class="form-control mt-2"/>
        </div>
        <div class="d-flex mt-2">
        <b-button variant="primary mr-2" class="" @click.prevent="$bvModal.hide('bv-modal-2')">Сохранить</b-button>
        <b-button variant="danger" class="" @click.prevent="$bvModal.hide('bv-modal-2')">Отменить</b-button>
        </div>
        </b-modal>

        <sidebar
                title="Создать чек лист"
                :open="showCheckSideBar"
                @close="showCheckSideBar = false"
                width="65%"
        >






            <div class="col-md-12 p-0">

              <div class="col-12 p-0">

                <div class="row">
                  <div class="col-md-3">
                    <p>Для группы чек лист</p>
                  </div>
                  <div class="col-md-4 p-0">

                    <div style="position: relative;border: 1px solid #dcdcdc">
                      <div class="gen-role-class" style="position: absolute" >
                        <a class="role_1" @click="selectedRoles('1')" >

                          <i class="fas fa-chalkboard-teacher role_icon_false">  </i>
                        </a>
                        <a class="role_2" @click="selectedRoles('2')">

                          <i class="fas fa-chalkboard-teacher role_icon_false"></i>
                        </a>
                        <a class="role_3" @click="selectedRoles('3')" >

                          <i class="fas fa-chalkboard-teacher role_icon_false" ></i>
                        </a>
                      </div>

                      <div class="popupShowSelected">
                        <div v-if="selectedRole.role_1" >
                          <p class="list-role"  v-for="item in  groups_arr">

                            <a @click="addDivBlock(item.name,item.code,'1')"  v-bind:class="{ active: item.checked }" class="btn btn-block" style="display: flex">
                              <i class="fas fa-arrow-alt-circle-right  style-icons" ></i>
                              <span  style="margin-top: 5px; margin-left:15px;">{{ item.name}}</span>
                              <i v-if="item.checked" class="icon-checked fas fa-solid fa-angle-down"></i>
                            </a>

                          </p>
                        </div>
                        <div v-if="selectedRole.role_2">
                          <p class="list-role"  v-for="item in  positions_arr">
                            <a @click="addDivBlock(item.name,item.code,'2')" v-bind:class="{ active: item.checked }" class="btn btn-block" style="display: flex">
                              <i class="fas fa-arrow-alt-circle-right style-icons" ></i>
                              <span  style="margin-top: 5px; margin-left:15px;">{{ item.name}}</span>
                              <i v-if="item.checked" class="icon-checked fas fa-solid fa-angle-down"></i>
                            </a>
                          </p>
                        </div>
                        <div v-if="selectedRole.role_3">
                          <p class="list-role"  v-for="item in   allusers_arr">
                            <a @click="addDivBlock(item.name,item.code,'3')" v-bind:class="{ active: item.checked }" class="btn btn-block" style="display: flex">
                              <i class="fas fa-arrow-alt-circle-right  style-icons" ></i>
                              <span style="margin-top: 5px; margin-left:15px;">{{ item.last_name}} {{ item.name}}</span>
                              <i v-if="item.checked" class="icon-checked fas fa-solid fa-angle-down"></i>
                            </a>
                          </p>
                        </div>
                      </div>
                    </div>



                    <div id="selected-block-array"  class="selected-block-array" >
                      <a v-if="placeholderSelect" style="color: #abb1b8;" >Добавить Отделы/Сотрудники</a>
                        <div class="addElement"  v-for="(item,i) in allValueArray"   >
                          <a class="elementHoverList">
                            <span> {{ item.text }} </span>
                            <div class="ui-tag-selector-tag-remove"  @click="deleteDesk(i,item.code,item.type)">
                              <span class="ui-tag-selector-remove-icon  ">x</span>
                            </div>
                          </a>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
<!--                <selected-modal-checkList :groups=groups-->
<!--                                          :allusers="allusers"-->
<!--                                          :positions="positions"-->
<!--                                          :someProps="parent" @updateParent="onUpdateSalary"-->
<!--                ></selected-modal-checkList>-->
                <div class="row mt-5 pb-3" style="border-bottom: 1px solid #dee2e6">
                    <div class="col-md-3">
                        <p>Колво показов</p>
                    </div>
                    <div class="col-md-4 p-0" >
                        <input v-model="countView"   placeholder="Максимум 10 " type="number" class="form-control btn-block">
                    </div>
                </div>
                <div class="row mt-4 pl-3">
                    <div class="col-md-12 pr-0 mt-2" v-for="(item, index) in arrCheckInput">

                      <div class="row">
                          <div class="col-md-5">
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
                valueFindGr:[],
                arrCheckInput:[],
                arrCheckLists:[],
                inputSearch:'',
                countView:'1',
                errors: {
                    message:'',
                    msg:'',
                    text:[],
                    counterror:[],
                    show:false,
                    save:false,
                    class:'btn btn-success',
                },
                showCheckSideBar:false,
                addButton:false,
                editButton:false,
                placeholderSelect:true,
                check_id:null,
                flag_type:true,
                allValueArray:[],
                groups_arr:[],
                allusers_arr:[],
                positions_arr:[],
                selectedRole:{
                  role_1:true,
                  role_2:false,
                  role_3:false,
                },



            }
        },
        created(){


            this.addCheckList()
            this.viewCheckList()
        },
        mounted() {
          this.positions_arr = this.positions;
          if (Object.keys(JSON.parse(this.groups)).length > 0) {
            this.groups_arr = JSON.parse(this.groups);
            const arrayFailedGr = Object.entries(this.groups_arr).map((arr) => ({
              code: arr[0],
              name: arr[1],
              checked:false,
              type:1,
            }));

            this.groups_arr = arrayFailedGr
          }
          if (Object.keys(this.positions_arr).length > 0) {
            // this.groups_arr = JSON.parse(this.positions_arr);

            const arrayFailedGr = Object.entries(this.positions_arr).map((arr) => ({
              code: arr[0],
              name: arr[1],
              checked:false,
              type:2,
            }));
            this.positions_arr = arrayFailedGr
          }
          if (this.allusers) {
            for (let i = 0; i < this.allusers.length; i++) {
              if (this.allusers[i]['name'].length > 1) {
                this.allusers_arr[i] = {
                  name: this.allusers[i]['name'] + '  ' + this.allusers[i]['last_name'],
                  code: this.allusers[i]['id'],
                  checked:false,
                  type:3,
                }
              }
            }
          }
        },
        methods:{
            // onUpdateSalary(someData) {
            //     this.valueGroups = someData['valueGroups'];
            //     this.valuePositions = someData['valuePositions'];
            //     this.valueUsers = someData['valueUsers'];
            //     // Выполняем необходимые действия с `someData`
            // },

            addNewCheckModalShow(){
                this.showCheckSideBar = true
                this.addButton = true
                this.editButton = false
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


                this.validateInput(arrCheckInput,this.countView)

                // console.log(arrCheckInput,'arr',this.check_id,this.valueGroups,this.countView,'www');


                if (this.errors.save){
                    axios.post('/timetracking/settings/edit/check/save/', {
                        check_id:this.check_id,
                        allValueArray:this.allValueArray,
                        countView:this.countView,
                        arrCheckInput:arrCheckInput,
                        valueFindGr:this.valueFindGr
                    }).then(response => {
                        if (response.data.type == 1){
                            this.$message.error('Ошибка');
                            this.errors.show = true;
                            this.errors.message = response.data.item;
                        }else {
                            this.$message.success('Успешно изменен');
                            this.errors.show = false;
                            this.showCheckSideBar = false;
                            this.viewCheckList()
                        }


                    })
                }


            },
            editCheck(check_id,type){


                this.addButton = false
                this.editButton = true
                this.showCheckSideBar = true
                this.check_id = check_id
                this.errors.show = false

                axios.post('/timetracking/settings/edit/check', {
                    check_id:check_id,
                    type:type,
                }).then(response => {

                    // console.log(type,'type');
                    // console.log(check_id,'imasheev');
                    // console.log(response,'imasheevsdf');




                    this.editValueThis = response.data;
                    this.valueFindGr = response.data.item_id;
                    this.countView = response.data.count_view;
                    this.arrCheckInput = JSON.parse(response.data['active_check_text'])

                    this.editValueThis.view = true
                    this.editValueThis.arr= response.data





                    // if (response.data.item_type == 1){
                    //     this.valueGroups = [{name:response.data.title,code:response.data.item_id}];
                    //     this.click_show.gr = true;
                    //     this.click_show.ps = false
                    //     this.valuePositions = null;
                    // }else if (response.data.item_type == 2){
                    //     this.valuePositions = [{name:response.data.title,code:response.data.item_id}];
                    //     this.click_show.ps = true;
                    //     this.click_show.gr = false;
                    //     this.valueGroups = null;
                    // }else if (response.data.item_type == 3){
                    //
                    //
                    //     this.click_show.us = true
                    // };

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
                this.validateInput(this.arrCheckInput,this.countView)


                if (this.errors.save){
                    axios.post('/timetracking/settings/add/check', {
                        allValueArray:this.allValueArray,
                        countView:this.countView,
                        arrCheckInput:this.arrCheckInput,

                    }).then(response => {

                        if (response.data.success == false){
                            this.errors.show = false;
                            this.errors.msg = null;
                            // this.showCheckSideBar = false;
                            for (let i = 0;i < this.allValueArray.length;i++){
                               if (this.allValueArray[i]['type'] == response.data.exists[0]['item_type'] && this.allValueArray[i]['code'] == response.data.exists[0]['item_id']){


                                 if (response.data.exists[0]['item_type'] == 1){
                                   this.errors.msg = 'Данная Группа ' +this.allValueArray[i]['text']+ ' Ранне Добавлено  ';
                                   this.$message.error(this.errors.msg);
                                 }else if(response.data.exists[0]['item_type'] == 2){
                                   this.errors.msg = 'Данная ' +this.allValueArray[i]['text']+ ' Должность Ранне Добавлено ';
                                   this.$message.error(this.errors.msg);
                                 }else if (response.data.exists[0]['item_type'] == 3){
                                   this.errors.msg = 'Данный Пользователь ' +this.allValueArray[i]['text']+ ' Ранне Добавлено';
                                   this.$message.error(this.errors.msg);
                                 }
                               }
                            }
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
            validateInput(array_check_input,count_view){
                this.countView = count_view,
                    this.arrCheckInput = array_check_input,
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
                }


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

            },
            closeAlert() {
                this.errors.show = false;
            },
            selectedRoles(type){
              if (type == 1){
                this.selectedRole.role_1 = true
                this.selectedRole.role_2 = false
                this.selectedRole.role_3 = false
              }else if (type == 2){
                this.selectedRole.role_1 = false
                this.selectedRole.role_2 = true
                this.selectedRole.role_3 = false
              }else if (type == 3){
                this.selectedRole.role_1 = false
                this.selectedRole.role_2 = false
                this.selectedRole.role_3 = true
              }

            },
            addDivBlock(item,id,type){
              this.flag_type = true;
              this.placeholderSelect = false;

              if (this.allValueArray.length > 0){
                for (let i = 0; i < this.allValueArray.length;i ++){
                  if (this.allValueArray[i]['type'] == type && this.allValueArray[i]['code'] == id){
                    this.$message.error('Группа ранее добавлено');
                    this.flag_type = false;
                    this.allValueArray[i]['exists'] = true
                  }else if (this.allValueArray[i]['type'] == type && this.allValueArray[i]['code'] == id){
                    this.$message.error('Должность ранее добавлено');
                    this.flag_type = false;
                    this.allValueArray[i]['exists'] = true
                  }else if (this.allValueArray[i]['type'] == type && this.allValueArray[i]['code'] == id){
                    this.$message.error('Пользователь ранее добавлено');
                    this.flag_type = false;
                    this.allValueArray[i]['exists'] = true
                  }
                }
              }



              if (this.flag_type == true){
                this.allValueArray.push({
                  text: item,
                  code:id,
                  type:type,
                  exists:false,
                });

                if (type == 1){
                  for (var i = 0; i < this.groups_arr.length;i++){
                    if (this.groups_arr[i]['code'] == id){
                      this.groups_arr[i]['checked'] = true
                    }
                  }
                }else if(type == 2){
                  for (var i = 0; i < this.positions_arr.length;i++){
                    if (this.positions_arr[i]['code'] == id){
                      this.positions_arr[i]['checked'] = true
                    }
                  }

                }else if(type == 3){
                  for (var i = 0; i < this.allusers_arr.length;i++){
                    if (this.allusers_arr[i]['code'] == id){
                      this.allusers_arr[i]['checked'] = true
                    }
                  }
                }




              }
            },
            deleteDesk(id,code,type){
              this.allValueArray.splice(id,1)

              if (this.allValueArray.length == 0){
                this.placeholderSelect = true;
              }

              for (var i = 0; i < this.groups_arr.length;i++){
                if (this.groups_arr[i]['type'] == type && this.groups_arr[i]['code'] == code){
                  this.groups_arr[i]['checked'] = false
                }
              }

              for (var i = 0; i < this.positions_arr.length;i++){
                if (this.positions_arr[i]['type'] == type && this.positions_arr[i]['code'] == code){
                  this.positions_arr[i]['checked'] = false
                }
              }

              for (var i = 0; i < this.allusers_arr.length;i++){
                if (this.allusers_arr[i]['type'] == type && this.allusers_arr[i]['code'] == code){
                  this.allusers_arr[i]['checked'] = false
                }
              }





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


    .icon-checked{
      position: absolute;
      right: 30px;
      top: 5px;
      font-size: 25px;
      color:#67dfef;
    }
    .ui-tag-selector-remove-icon{
      color: #acbfc5;
      position: absolute;
      font-size: 17px;
      right: -35px;
      top: -35px;
    }

    /*.ui-tag-selector-tag-remove a:hover{*/
    /*  color: red;*/
    /*}*/
    .ui-tag-selector-tag-remove{
      cursor: pointer;
      position: relative;

    }
    .addElement a:hover{
      background-color: #dafbff;
    }



    .addElement{
      display: -ms-inline-flexbox;
      display: inline-flex;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      z-index: 2;
      -webkit-transition: 50ms;
      -o-transition: 50ms;
      transition: 50ms;
      max-width: 200px;
      margin: 5px;
    }


    .elementHoverList{
      color: white;
      padding: 7px;
      background-color: #67dfef;

    }
    .elementHoverList span {
      margin-right: 30px;

    }

    .selected-block-array{
      border: 2px solid #dee2e6;
      padding: 10px;
      max-height: 175px;
      overflow-x: hidden;
    }



    .style-icons > span{
      margin-top: 5px;
      margin-left: 15px;
    }

    .style-icons{
      /* background-color: #e4ebed; */
      /* align-items: center; */
      /* text-align: center; */
      /* margin-left: 0px; */
      /* border-radius: 100%; */
      /* padding-right: 8px; */
      /* padding-left: 10px; */
      /* padding-bottom: 5px; */
      padding-top: 5px;
      /* padding-top: 0px; */
      font-size: 22px;
      color: #abb1b8;
    }


    .list-role{
      margin: 0px;
      border: 1px solid white;
      position: relative;
    }

    .list-role a:hover{
      background-color: #e0f6fe;
      border: 1px solid white;
    }

    .active{
      background-color: #e0f6fe;
      border: 1px solid white;
    }

    .popupShowSelected{
      max-height:  250px;
      min-height: 250px;
      overflow-x: hidden;
    }

    .role_1{
      padding: 17px 20px 15px 20px;
      background-color: #e4ebed;
      margin-left: -66px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      position: absolute;
      cursor: pointer;

      /*background-color: #2fc6f6;*/

      /*padding: 9px 110px 15px 20px;*/
      /*background-color: #2fc6f6;*/
      /*margin-left: -150px;*/
      /*border-top-left-radius: 30%;*/
      /*border-bottom-left-radius: 30%;*/
      /*position: absolute;*/
      /*cursor: pointer;*/

    }

    .role_2{
      padding: 16px 20px 17px 20px;
      background-color: #e4ebed;
      margin-left: -66px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      top: 77px;
      position: relative;
      cursor: pointer;

    }

    .role_3{
      padding: 20px 20px 15px 20px;
      background-color: #e4ebed;
      margin-left: -68px;
      border-top-left-radius: 100%;
      border-bottom-left-radius: 100%;
      top: 140px;
      position: relative;
      cursor: pointer;

    }

    .role_icon_false{
      font-size: 20px;
      color: #abb1b8;
      font-weight: bold;
    }

    .role_icon_true{
      font-size: 20px;
      color: #ffffff;
      font-weight: bold;
    }

    .gen-role-class a:hover{
      /*padding: 10px 110px 12px 20px;*/
      background-color: #2fc6f6;

      /*border-top-left-radius: 30%;*/
      /*border-bottom-left-radius: 30%;*/
      /*position: absolute;*/
      /*cursor: pointer;*/

    }

    .gen-role-class a:hover i{
      font-size: 20px;
      color: #ffffff;
      font-weight: bold;
    }

</style>