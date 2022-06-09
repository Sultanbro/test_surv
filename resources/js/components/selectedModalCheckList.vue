<template>
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
                        <a style="color: #abb1b8;" id="placholder-select">Добавить Отделы/Сотрудники</a>

                      <div class="addElement"  v-for="(item,i) in allValueArray"  >
                          <a  class="elementHoverList">
                               <span> {{ item.text }} </span>
                              <div class="ui-tag-selector-tag-remove"  @click="deleteDesk(i,item.code)">
                                <span class="ui-tag-selector-remove-icon  ">x</span>
                              </div>
                          </a>

                      </div>

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

            };
        },
        mounted() {
            this.positions_arr = this.positions;
            if (Object.keys(JSON.parse(this.groups)).length > 0) {
                this.groups_arr = JSON.parse(this.groups);
                const arrayFailedGr = Object.entries(this.groups_arr).map((arr) => ({
                    code: arr[1],
                    name: arr[0],
                    checked:false,
                }));

                this.groups_arr = arrayFailedGr
            }
            if (Object.keys(this.positions_arr).length > 0) {
                // this.groups_arr = JSON.parse(this.positions_arr);

                const arrayFailedGr = Object.entries(this.positions_arr).map((arr) => ({
                    code: arr[0],
                    name: arr[1],
                    checked:false,
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
                        }
                    }
                }
            }
        },
        methods: {

            doSomething() {

              event.default()
              console.log(this.allValueArray,'07771995')


                this.$message.success('Успешно Сохранено')
                this.$emit('updateParent', {
                     allValueArray: this.allValueArray
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
            addDivBlock(item,id,type){
                $("#placholder-select").empty();

                    // if (this.allValueArray.length > 0){
                    //     for (let i = 0; i < this.allValueArray.length;i ++){
                    //         if (this.allValueArray[i]['code'] == id){
                    //             alert('Уже Добавлено');
                    //             this.flag_type = false;
                    //         }
                    //     }
                    // }


                    if (this.flag_type == true){
                          this.allValueArray.push({
                              text: item,
                              code:id,
                              type:type
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
            deleteDesk(id,code){
                this.allValueArray.splice(id,1)
                for (var i = 0; i < this.groups_arr.length;i++){
                  if (this.groups_arr[i]['code'] == code){
                    this.groups_arr[i]['checked'] = false
                  }
                }

            },
            // toggle() {
            //   this.showModal = !this.showModal
            // },

        },

    }





</script>

<style scoped>
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

 /*.spanAction{*/
     /*position: absolute;*/
     /*font-weight: bold;*/
     /*font-size: 20px;*/
     /*!*padding-left: 30px;*!*/
     /*color: white;*/
     /*!*top: 10px;*!*/
 /*}*/




</style>