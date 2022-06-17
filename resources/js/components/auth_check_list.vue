<template>
    <div>
        <div @click="toggle()"   class="btn-rm">
            <a class="text-white rounded" >
                <span class="far fa-address-card text-white "></span>
            </a>
        </div>







        <sidebar
                title="Индивидуальный чек лист"
                :open="showAuthUserCheck"
                @close="toggle()"
                width="70%"
        >

        <div class="col-md-12 p-0">



                <div class="row">
                    <div class="col-md-12 pr-0 mt-2" v-for="(item, index) in auth_check">
                        <span class="font-weight-bold">{{item.title}}</span>
                        <div class="col-12 p-0 mt-2" v-for="(val,ind) in item.check_input">
                           <b-form-checkbox v-model="val.checked" size="sm" >
                             <span style="cursor: pointer">{{val.text}}</span>
                           </b-form-checkbox>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="col-md-6 p-0">
                            <button @click.prevent="saveCheck"   title="Сохранить" class="btn btn-primary">
                                Выполнить
                            </button>
                        </div>
                    </div>
                </div>


            </div>




        </sidebar>

    </div>
</template>

<script>
    export default {
        name: "auth-check-list",
        props: {
            // user_id:'',
            auth_check_list:{}
        },
        data() {
            return {
                showAuthUserCheck: false,
                count:0,
                auth_check:[]
            };
        },

        created() {
            this.viewCheck()
        },
        methods: {
            toggle() {
                this.showAuthUserCheck = !this.showAuthUserCheck
                // document.getElementById('list-example').classList.toggle("sticky");
            },


            saveCheck(){



                axios.post('/timetracking/settings/auth/check/user/send', {
                    auth_check:this.auth_check
                }).then(response => {

                    this.showAuthUserCheck= false
                    this.$message.success('Успешно выполнено');

                   ;

                })

            },

            viewCheck(){

              console.log(this.auth_check_list,'1999')

                axios.post('/timetracking/settings/auth/check/user', {
                    auth_check:this.auth_check_list

                }).then(response => {


                    if (response.data.checklist.length > 0) {

                      for (let i = 0;i < response.data.checklist.length;i++){
                        if (response.data['checklist'][i].length > 0){

                          if (response.data['checklist'][i][0]['flag']){
                            this.auth_check.push({
                              title: response.data['checklist'][i][0]['title'],
                              id: response.data['checklist'][i][0]['check_id'],
                              gr_id: response.data['checklist'][i][0]['item_id'],
                              type: response.data['checklist'][i][0]['item_type'],
                              check_input:JSON.parse(response.data['checklist'][i][0]['checked'])
                            });
                          }else{
                            this.auth_check.push({
                              title: response.data['checklist'][i][0]['title'],
                              id: response.data['checklist'][i][0]['id'],
                              gr_id: response.data['checklist'][i][0]['item_id'],
                              type: response.data['checklist'][i][0]['item_type'],
                             check_input:JSON.parse(response.data['checklist'][i][0]['active_check_text'])
                            });
                          }


                        }
                      }
                    }



                })
            },



        },
    };
</script>

<style lang="scss">

</style>