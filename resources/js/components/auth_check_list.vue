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
                       <span class="font-weight-bold">{{index}}</span>
                       <div class="col-10 p-0 mt-2" v-for="(val,ind) in item">
                          <div class="mr-5">
                            <b-form-checkbox v-model="val.checkedtasks[0].checked" size="sm" >
                              <span style="cursor: pointer">{{val.task}}</span>
                            </b-form-checkbox>
                          </div>

                          <div style="position: absolute;right: 0px;top: 0px">
                           <input type="url" style="width: 150%" v-model="val.checkedtasks[0].url" @input="event => val.checkedtasks[0].url >= 0 ? linkIsSet = true : linkIsSet = false" class="form-control form-control-sm" placeholder="url">
                         </div>
                       </div>

                     </div>
                   </div>

                    <div class="col-md-12 mt-3">
                        <div class="col-md-6 p-0">
                            <button @click.prevent="saveChecklist"   title="Сохранить" class="btn btn-primary" :disabled="linkIsSet">
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

            auth_check_list: Array,
            open_check:{
              default:0
            },
        },
        data() {
            return {
                linkIsSet: true,
                checked_tasks:[],
                showAuthUserCheck: false,
                count:0,
                auth_check:[],
                sendChecklist :false,
                currentTime: 0,
                notification_time: null,
                times: 1,
                show_counts:[],
            };
        },

        created() {
            this.getTasks();
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
                    //console.log(newValue.getHours()+':'+newValue.getMinutes()+' -- ');
                
                if(this.notification_time != null){
                    if(newValue.getHours() == this.notification_time.getHours() && newValue.getMinutes() == this.notification_time.getMinutes()){
                        this.toggle();
                        this.getNotificationTime(this.show_counts)
                    }
                }
 

            }
        },
        computed:{
            isChecked(){
                    return false;
            }
        },
        methods: {

            getTasks(){
                var ids = [];
                this.auth_check_list.forEach(val => {
                    ids.push(val.id);
                })
                axios.post('/checklist/tasks',{
                    checklist_id:ids
                }).then(response => {
                    this.auth_check = response.data[0];
                    this.show_counts = response.data[1];

                    this.getNotificationTime(this.show_counts);
                })
            },
            getNotificationTime(times){
                var maxtimes = 1;
                times.forEach(val => {
                    if(maxtimes < val){
                        maxtimes = val;
                    }
                });
                var hours = 9 / maxtimes;
                var date = new Date();
                var now = new Date();
                for(let i = 0; i < maxtimes; i++){
                    date.setHours((12 + (i * hours)), 0, 0, 0);
                    if(date >= now){
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

            saveChecklist(){
                
                axios.post('/checklist/save', {
                      auth_check:this.auth_check
                }).then(response => {
                  if(response.data == 1){
                      this.showAuthUserCheck= false
                      this.$toast.success('Успешно выполнено');
                  }else if(response.data == 2){
                      this.$toast.success('Поставьте галочку!');
                  }else{
                    this.$toast.success('Укажите правильную ссылку!');
                  }

                });   
            
            },
            saveCheck(){
                this.validate(this.auth_check)

                if (this.sendChecklist){

                    axios.post('/timetracking/settings/auth/check/user/send', {
                      auth_check:this.auth_check
                    }).then(response => {

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
                el['checkedtasks'].forEach(ch =>{


                  if (ch['checked'] == true){
                    this.sendChecklist = false;
                    //console.log(this.isValidUrl(ch['https']));
                    if (this.isValidUrl(ch['url']) && ch['checked']){
                      this.sendChecklist = true;
                        this.$toast.success('Чек лист сохранен!');
                    }else{
                        this.$toast.error('Ссылка не является действительной!');
                    }
                  }
                })
              });




            },





        },
    };
</script>

<style lang="scss">

</style>