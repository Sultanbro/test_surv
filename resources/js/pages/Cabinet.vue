<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Настройка кабинета</h1>

    <div class="settingCabinet">
      <ul class="p-0">


        <li><a  v-if="user.is_admin === 1" style="color: black" @click="userRoles = true , userProfile = false "  v-bind:class="{ profile_active: userRoles }" >Административные настройки</a></li>
        <li class="position-relative">
          <a style="color: black"  @click="userProfile = true , userRoles = false"  v-bind:class="{ profile_active: userProfile }" >Настройка собственного профиля</a>
        </li>


      </ul>
    </div>



  </div>
  <div  v-if="userRoles" class="rp" style="flex: 1 1 0%;"   >
    <div class="hat">
      <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a href="#">Настройка кабинета</a>

        </div>
        <div class="control-btns"></div>
      </div>
    </div>
    <div class="content mt-3 py-3">


        <div class="p-3">
          <div class="form-group">
            Субдомен
            <input class="form-control mt-1" id="view_own_orders" type="text"/>
          </div>

          <div class="form-group">
            Часовой пояс
            <input class="form-control mt-1" id="view_own_orders" type="text"/>
          </div>


          <div class="form-group">
            Администраторы

            <multiselect
                v-model="admins"
                :options="users"
                :multiple="true"
                :close-on-select="false"
                :clear-on-select="true"
                :preserve-search="true"
                placeholder="Выберите"
                label="email"
                track-by="email"
                :taggable="true"
                @tag="addTag"
              >
              </multiselect>
          </div>


          <div class="mt-3">
            <button class="btn btn-success" @click="save">Сохранить</button>
          </div>





      </div>
    </div>
  </div>

  <div  v-if="userProfile" class="col-12 p-0">
     <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a  href="#">Настройка профиля</a>
        </div>
        <div class="control-btns"></div>
      </div>
     <div class="content mt-3 py-3" >
      <div class="contacts-info col-md-6 none-block mt-10" id="profile_d" >
        <label class="my-label-6 img_url_md" for="upload_image" style="cursor:pointer;border: 1px solid #f8f8f8;background-color: unset" >
          <div style="border: 2px solid #ced4da;padding: 5px">
            <img style="width: 200px;height: 200px"
                 class="image-card__image"
                 :src="img" :alt="img">
            <form enctype="multipart/form-data">
              <input id="upload_image" hidden type="file" class="form-control" v-on:change.prevent="onChange">
            </form>
          </div>
        </label>
        <div class="form-group row">
          <label for="firstName"
                 class="col-sm-4 col-form-label font-weight-bold">Имя <span class="red">*</span></label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="name" id="firstName" required
                   placeholder="Имя сотрудника" v-model="user.name"
            >
          </div>
        </div>
        <div class="form-group row">
          <label for="lastName"
                 class="col-sm-4 col-form-label font-weight-bold">Фамилия <span class="red">*</span></label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="last_name" id="lastName" required
                   placeholder="Фамилия сотрудника" v-model="user.last_name"
            >
          </div>
        </div>

        <div  v-if="user.is_admin === 1" class="form-group row">
          <label for="email" class="col-sm-4 col-form-label font-weight-bold">Новый пароль </label>
          <div class="col-sm-8">
            <input v-model="password" minlength="5" class="form-control" type="password" name="new_pwd" id="new_pwd"
                   placeholder="********"
                   >
          </div>
        </div>

        <div class="form-group row">
          <label for="lastName"
                 class="col-sm-4 col-form-label font-weight-bold">День рождения <span class="red">*</span></label>
          <div class="col-sm-8">
            <input   v-model="birthday" class="form-control" type="date" name="birthday" id="birthday" required>
          </div>
        </div>

      </div>
    </div>
     <div class="content mt-3 py-3">
      <div class="col-12 row payment-profile" v-for="(payment,index) in payments">

        <div class="col-2">
          <input v-model="payment.bank" class="form-control" placeholder="Банк">
        </div>

        <div class="col-2">
          <input v-model="payment.country" class="form-control" placeholder="Страна">
        </div>

        <div class="col-2">
          <input v-model="payment.cardholder" class="form-control" placeholder="Имя на карте">
        </div>

        <div class="col-2">
          <input type="number" v-model="payment.phone" class="form-control" placeholder="Телефон">
        </div>

        <div class="col-2">
          <input type="number"  v-model="payment.number" class="form-control card-number" placeholder="Номер карты">
        </div>


        <div class="col-2 position-relative">

          <button v-if="payment.id"  style="position: absolute;left: 0px" class=" btn btn-danger btn-sm card-delete rounded mt-1" @click="removePaymentCart(index,payment.id)">
            <span class="fa fa-trash"></span>
          </button>

          <button v-else style="position: absolute;left: 0px" class=" btn btn-primary btn-sm card-delete rounded mt-1" @click="removePaymentCart(index,'dev')" >
            <span class="fa fa-trash"></span>
          </button>

        </div>

      </div>
      <div class="col-12" v-if="cardValidatre.error">
        <div class="alert alert-danger">
          <span>Заполните все поля</span>
        </div>
      </div>
      <div class="col-8 row mt-3">

        <div class="col-3">
            <button   @click="addPayment()" style="color: white" class="btn btn-phone btn-primary  btn-block">
              Добавить карту
            </button>
        </div>

        <div class="col-3" >
            <button @click.prevent="editProfileUser()" style="color: white"  class="btn btn-success  btn-block btn-block" type="button">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</div>





</template>
<script>
// import { Cropper } from 'vue-advanced-cropper'
// import 'vue-advanced-cropper/dist/style.css';


export default {
  name: "Cabinet",
  props: {
    auth_role:{},
  },
  data() {
    return {
      test: 'dsa',
      items: [],
      users: [],
      user:[],
      user_card:[],
      admins: [],
      activeCourse: null,
      userRoles:false,
      userProfile:false,
      img:'',
      success: '',
      password:'',
      birthday:'',
      cardValidatre:{
          error:false,
          type:false,
      },
      payments:[
        {
          bank:'',
          cardholder:'',
          country:'',
          number:'',
          phone:'',
        },
      ],


    };
  },
  mounted() {

  },
  created() {
    this.fetchData();
    this.user = JSON.parse(this.auth_role)

    this.format_date(this.user.birthday)

  },
  methods: {

    format_date(value){

      console.log(value,'iks');

      if (value) {
          return  this.birthday = moment(String(value)).format('YYYY-MM-DD')
      }


    },
    onChange(e) {
      this.file = e.target.files[0];

      this.formSubmit()
    },
    formSubmit() {

      // e.preventDefault();
      let existingObj = this;
      const config = {
        headers: {
          'content-type': 'multipart/form-data'
        }
      }
      let data = new FormData();
      data.append('file', this.file);
      axios.post('/profile/upload/edit/profile', data, config)

          .then(function (res) {



            // existingObj.success = res.data.success;
            existingObj.img = '/users_img/'+res.data.file_name;
            existingObj.$message.success('Успешно Удалено');


            $("#img_url_sm").attr('src',existingObj.img)

          })
          .catch(function (err) {
            existingObj.output = err;
          });



    },
    change({ coordinates, canvas }) {

      this.canvas = canvas,

      console.log(coordinates, canvas);
    },
    addPayment(){



      this.payments.push({
        bank:'',
        cardholder:'',
        country:'',
        number:'',
        phone:'',
      });


    },
    removePaymentCart(index,type_id){



      let confirmDelte = confirm("Вы действительно хотите безвозвратно удалить ?");
      if (confirmDelte){

        this.payments.splice(index, 1)
        this.$message.success('Успешно Удалено')

        if (type_id != 'dev'){
          axios.post("/profile/remove/card/",{
            card_id:type_id,
          }).then((response) => {
           }).catch((error) => {
             alert(error);
           });

        }

      }
    },
    editProfileUser(){
      this.cardValidatre.type = false;
      this.cardValidatre.error = false;


      this.payments.forEach(el => {
        this.cardValidatre.type = false;
        if (el['bank'] != null && el['cardholder'] != null && el['country'] != null && el['number'] != null && el['phone'] != null){
          if (el['bank'].length > 2 && el['cardholder'].length > 2 && el['country'].length > 2 && el['number'].length > 2 && el['phone'].length > 2){
            this.cardValidatre.type = true;
          }

        }

      });

      if (this.cardValidatre.type){
        axios.post('/profile/edit/user/cart/', {
          cards:this.payments,
          query:this.user,
          password:this.password,
          birthday:this.birthday,
        }).then(response => {
          if (response.data.success){
            this.$message.success('Успешно Сохранено')
          }
        })
      }else{
        this.cardValidatre.error = true;
      }
    },
    addTag(newTag) {
      const tag = {
        email: newTag,
        id: newTag,
      };
      this.users.push(tag);
    },
    fetchData() {


      axios
        .get("/cabinet/get")
        .then((response) => {

          this.admins = response.data.admins;
          this.users = response.data.users;
          this.user = response.data.user;

          if (response.data.user_payment != null && response.data.user_payment != undefined){
              if (response.data.user_payment.length > 0){
                this.payments = response.data.user_payment;
              }else{
                this.payments = []

              }
          }

          if (this.user.img_url != null && this.user.img_url != undefined){
            this.img = '/users_img/'+response.data.user.img_url;
          }else{
            this.img = '/users_img/noavatar.png';
          }






        })
        .catch((error) => {

          alert(error);


        });

    },
    save() {
      axios
        .post("/cabinet/save", {
          admins: this.admins,
        })
        .then((response) => {
          this.$message.success('Сохранено')
        })
        .catch((error) => {
          alert(error,'6565');
        });
    },
  },

};

</script>

<style>
.contacts-info{
  margin-top:30px;
}
.profile_active{
  font-weight: bold;
}

.payment-profile{
  margin-top: 30px;
  margin-bottom: 10px;
}

.addPayment{
  padding: 15px;
  margin-top:-15px;
}
</style>
