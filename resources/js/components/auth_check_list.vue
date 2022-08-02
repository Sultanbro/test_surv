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
                   <div class="col-12 p-0">

                     <div class="col-md-12 pr-0 mt-2" v-for="(item, index) in auth_check">
                       <span class="font-weight-bold">{{item.title}}</span>
                       <div class="col-10 p-0 mt-2" v-for="(val,ind) in item.check_input">
                          <div class="mr-5">
                            <b-form-checkbox v-model="val.checked" size="sm" >
                              <span style="cursor: pointer">{{val.text}}</span>
                            </b-form-checkbox>
                          </div>

                          <div style="position: absolute;right: 0px;top: 0px">
                           <input type="url" style="width: 150%" v-model="val.https" class="form-control form-control-sm" placeholder="url">
                         </div>
                       </div>

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

            auth_check_list:{},
            open_check:{
              default:0
            },
        },
        data() {
            return {
                showAuthUserCheck: false,
                count:0,
                auth_check:[],
                sendChecklist :false,
                currentTime: 0,
                notification_time: null,
                times: null,
            };
        },

        created() {
            this.getNotificationTime();
            this.viewCheck()
              if (this.open_check == 1){
                this.showAuthUserCheck = true;
              }
        },
        mounted: function () {
          window.setInterval(() => {
            this.currentTime = new Date()
          }, 1000)
        }, 
        watch: {
            async currentTime(newValue, oldValue) {
                if(newValue.getTime() > this.notification_time.getTime()){
                    this.toggle();
                    this.getNotificationTime(this.times)
                }
            }
        },
        methods: {
            getNotificationTime(times){
                var hours = 9 / times;
                var date = new Date();
                var now = this.currentTime;
                for(let i = 0; i < times; i++){
                    date.setHours((9 + (i * hours)), 0, 0, 0);
                    if(date > now){
                        this.notification_time = date;
                        break;
                    }
                }
            },
            isValidUrl(url){
                var a  = document.createElement('a');
                a.href = url;
                return (a.host && a.host != window.location.host);
            },
            toggle() {
                this.showAuthUserCheck = !this.showAuthUserCheck
                // document.getElementById('list-example').classList.toggle("sticky");
            },


            saveCheck(){
                this.validate(this.auth_check)

                if (this.sendChecklist){

                    console.log(this.auth_check,'this.auth_check')
                    axios.post('/timetracking/settings/auth/check/user/send', {
                      auth_check:this.auth_check
                    }).then(response => {

                      console.log(response,'response')
                      this.showAuthUserCheck= false
                      this.$toast.success('Успешно выполнено');

                      ;

                    })

                }else {
                    this.$toast.error('заполнить поля выбранный чек листов');
                }
            },

            validate(auth_check){


              this.auth_check.forEach(el => {
                el['check_input'].forEach(ch =>{


                  if (ch['checked'] == true){
                    this.sendChecklist = false;
                    //console.log(this.isValidUrl(ch['https']));
                    if (this.isValidUrl(ch['https']) && ch['checked']){
                      this.sendChecklist = true;
                        this.$toast.success('Чек лист сохранен!');
                    }else{
                        this.$toast.error('Ссылка не является действительной!');
                    }
                  }
                })
              });




            },

            viewCheck(){



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

                    this.times = response.data[0];
                    this.getNotificationTime(this.times);

                })
            },



        },
    };
</script>

<style lang="scss">

</style>