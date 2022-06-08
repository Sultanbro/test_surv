<template>
    <div class="col-12 p-0">

        <div class="row">
            <div class="col-md-3">
                <p>Для группы чек лист</p>
            </div>
            <div class="col-md-4 p-0">

                    <div v-if="showModal"  style="position: relative;border: 1px solid #dcdcdc">
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
                                <p class="list-role-1"  v-for="item in  groups_arr">
                                    <a @click="addDivBlock(item.name,item.code,'1')" class="btn btn-block" style="display: flex">
                                        <i class="fas fa-map-signs style-icons" ></i>
                                        <span  style="margin-top: 5px; margin-left:15px;">{{ item.name}}</span>
                                    </a>
                                </p>
                            </div>
                            <div v-if="selectedRole.role_2">
                                <p class="list-role-1"  v-for="item in  positions_arr">
                                    <a @click="addDivBlock(item.name,item.code,'2')" class="btn btn-block" style="display: flex">
                                        <i class="fas fa-map-signs style-icons" ></i>
                                        <span style="margin-top: 5px; margin-left:15px;">{{ item.name}}</span>
                                    </a>
                                </p>
                            </div>
                            <div v-if="selectedRole.role_3">
                                <p class="list-role-1"  v-for="item in   allusers_arr">
                                    <a @click="addDivBlock(item.name,item.code,'3')" class="btn btn-block" style="display: flex">
                                        <i class="fas fa-map-signs style-icons" ></i>
                                        <span style="margin-top: 5px; margin-left:15px;">{{ item.last_name}} {{ item.name}}</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>



                    <div id="selected-block-array"  class="selected-block-array" >
                       <a style="color: #abb1b8;" id="placholder-select">Добавить Отделы/Сотрудники</a>
                        {{ this.templateKKK }}
                    </div>
                     <button class="btn btn-success btn btn-block" style="margin-bottom: 20px; margin-left: 0px;color: white;" type="button" @click="doSomething">Добавить</button>





            </div>
        </div>
    </div>
</template>

<script>
    // import Multiselect from 'vue-multiselect'
    // Vue.component('multiselect', Multiselect)


    export default {
        name: "test",
        props: {
            groups:{},
            allusers:{},
            positions:{},
            editValueThis:{}
        },
        data() {
            return {
                flag_type:{
                    gr:true,
                    ps:true,
                    us:true,
                },
                valueGroups:[],
                valuePositions:[],
                valueUsers:[],
                groups_arr:[],
                allusers_arr:[],
                positions_arr:[],
                showModal: true,
                selectedRole:{
                    role_1:true,
                    role_2:false,
                    role_3:false,
                },
                templateKKK:'',

            };
        },
        mounted() {

          console.log(this.editValueThis.view,'dd')
          console.log(this.editValueThis,'dddd')






            this.positions_arr = this.positions;
            if (Object.keys(JSON.parse(this.groups)).length > 0) {
                this.groups_arr = JSON.parse(this.groups);
                const arrayFailedGr = Object.entries(this.groups_arr).map((arr) => ({
                    code: arr[1],
                    name: arr[0],
                }));

                this.groups_arr = arrayFailedGr
            }
            if (Object.keys(this.positions_arr).length > 0) {
                // this.groups_arr = JSON.parse(this.positions_arr);

                const arrayFailedGr = Object.entries(this.positions_arr).map((arr) => ({
                    code: arr[0],
                    name: arr[1],
                }));
                this.positions_arr = arrayFailedGr
            }
            if (this.allusers) {
                for (let i = 0; i < this.allusers.length; i++) {
                    if (this.allusers[i]['name'].length > 1) {
                        this.allusers_arr[i] = {
                            name: this.allusers[i]['name'] + '  ' + this.allusers[i]['last_name'],
                            code: this.allusers[i]['id']
                        }
                    }
                }
            }
        },
        methods: {

            doSomething() {

              if (this.editValueThis.view == true){
                console.log(this.editValueThis.view,'kis')
                this.addDivBlock(this.editValueThis.title,this.editValueThis.id,this.editValueThis.item_type);
              }


                this.$message.success('Успешно Сохранено')
                this.$emit('updateParent', {
                    valueGroups: this.valueGroups ,
                    valuePositions:this.valuePositions,
                    valueUsers:this.valueUsers,
                })
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
            toggle() {
                this.showModal = !this.showModal
            },
            addDivBlock(item,id,type){
                $("#placholder-select").empty();

                if (type == 1){
                    if (this.valueGroups.length > 0){
                        this.flag_type.gr = true;
                        for (let i = 0; i < this.valueGroups.length;i ++){
                            if (this.valueGroups[i]['code'] == id){
                                alert('Уже Добавлено');
                                this.flag_type.gr = false;
                            }
                        }
                    }
                    if (this.flag_type.gr == true){
                          this.valueGroups.push({
                              text: item,
                              code:id,
                          });
                      }
                }
                if (type == 2){
                    if (this.valuePositions.length > 0){
                        this.flag_type.ps = true;
                        for (let i = 0; i < this.valuePositions.length;i ++){
                            if (this.valuePositions[i]['code'] == id){
                                alert('Уже Добавлено');
                                this.flag_type.ps = false;
                            }
                        }
                    }
                    if (this.flag_type.ps == true){
                        this.valuePositions.push({
                            text: item,
                            code:id,
                        });
                    }
                }
                if (type == 3){
                    if (this.valueUsers.length > 0){
                        this.flag_type.us = true;
                        for (let i = 0; i < this.valueUsers.length;i ++){
                            if (this.valueUsers[i]['code'] == id){
                                alert('Уже Добавлено');
                                this.flag_type.us = false;
                            }
                        }
                    }
                    if (this.flag_type.us == true){
                        this.valueUsers.push({
                            text: item,
                            code:id,
                        });
                    }
                }

                if (this.flag_type.us && this.flag_type.ps && this.flag_type.gr){

                  // this.templateKKK = '<button @click="deleteDesk(1)" >Счётчик кликов</button>'
                     var span = '<a id="id-'+id+'" style="background-color: #67dfef;padding: 7px;color: white;margin: 7px"  onclick="deleteDesk(1)"  >'+item+ '</a>';
                    $("#selected-block-array").append(span);
                }


            },

            deleteDesk(id){

                console.log(id,'asdasd')

                $("#id-"+id).remove();
            },

        },

    }





</script>

<style scoped>

  .selected-block-array{
      border: 2px solid #dee2e6;
      min-height: 70px;
      padding: 10px
  }

 .selected-block-array a:hover{

     background-color: indianred;
 }

 .style-icons > span{
   margin-top: 5px;
   margin-left: 15px;
 }

 .style-icons{
     background-color: #e4ebed;
     align-items: center;
     text-align: center;
     margin-left: 0px;
     border-radius: 100%;
     padding-right: 8px;
     padding-left: 10px;
     padding-bottom: 5px;
     padding-top: 5px;
     /* padding-top: 0px; */
     font-size: 20px;
     color: #abb1b8;

 }


 .list-role-1{
     margin: 0px;
     border: 1px solid white;
 }

 .list-role-1 a:hover{
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

 /*.spanAction{*/
     /*position: absolute;*/
     /*font-weight: bold;*/
     /*font-size: 20px;*/
     /*!*padding-left: 30px;*!*/
     /*color: white;*/
     /*!*top: 10px;*!*/
 /*}*/




</style>