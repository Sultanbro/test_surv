<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Настройка кабинета</h1>

    <div class="settingCabinet">
      <ul class="p-0">
        <li><a style="color: black" v-if="user.is_admin === 1"  @click="userRoles = true , userProfile = false "  v-bind:class="{ active: userRoles }" >Административные настройки</a></li>
        <li class="position-relative">
          <a style="color: black"  @click="userProfile = true , userRoles = false"  v-bind:class="{ active: userProfile }" >Настройка собственного профиля</a>

          <ul v-if="userProfile" class="position-absolute " style="right: 0px;top: 20px">

            <li >
              <a @click="payment_data = false , basic_data = true"   v-bind:class="{ active: basic_data }"  >Основные данные</a>
            </li>
            <li>
              <a @click="payment_data = true , basic_data = false"   v-bind:class="{ active: payment_data }" >Платежные данные</a>
            </li>


          </ul>
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
          <a v-if="basic_data" href="#">Настройка профиля</a>
          <a v-else href="#">Настройка платежные данный</a>
        </div>
        <div class="control-btns"></div>
      </div>



    <div class="content mt-3 py-3" v-if="basic_data">

      <div class="contacts-info col-md-6 none-block mt-10" id="profile_d" >
        <label class="my-label-6 img_url_md" for="upload_image" style="cursor:pointer;border: 1px solid #f8f8f8;background-color: unset" >
          <div style="border: 2px solid #ced4da;padding: 5px">
            <img style="width: 200px;height: 200px"
                 class="image-card__image"
                 :src="img" :alt="img">
            <form @submit="formSubmit" enctype="multipart/form-data">
              <input id="upload_image" hidden type="file" class="form-control" v-on:change="onChange">
              <button class="btn btn-primary btn-block">Сохранить</button>
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
        <div v-if="user.is_admin === 1" class="form-group row">
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
            <input class="form-control" type="date" name="birthday" id="birthday" required>
          </div>
        </div>
        <div class="col-4 p-0">
          <div class="form-group row">
            <button style="color: white" @click.prevent="userSaveData()" class="btn btn-success ml-3 btn-block" type="button">Сохранить</button>
          </div>
        </div>
      </div>

    </div>


    <div class="content mt-3 py-3" v-if="payment_data">



      <div class="col-12 row payment-profile" v-for="(payment,index) in payments">

        <div class="col-2">
          <input v-model="payment.bank" class="form-control" placeholder="Банк">
        </div>

        <div class="col-2">
          <input v-model="payment.country" class="form-control" placeholder="Страна">
        </div>

        <div class="col-2">
          <input v-model="payment.name_cart" class="form-control" placeholder="Имя на карте">
        </div>

        <div class="col-2">
          <input v-model="payment.phone" class="form-control" placeholder="Телефон">
        </div>

        <div class="col-2">
          <input v-model="payment.number_cart" class="form-control" placeholder="Номер карты">
        </div>


        <div class="col-2">
          <button v-if="payment.delete" class="btn btn-danger btn-sm card-delete rounded ml-5 mt-1" @click="removePaymentCart(index)">
            <span class="fa fa-trash"></span>
          </button>


          <button v-else class="btn btn-primary btn-sm card-delete rounded ml-5 mt-1" >
            <span class="fa fa-trash"></span>
          </button>

        </div>

      </div>


      <div class="col-8 row mt-5">


        <div class="col-3">
            <button   @click="addPayment(index)" style="color: white" class="btn btn-phone btn-primary  btn-block">
              Добавить карту
            </button>
        </div>

        <div class="col-3">
            <button style="color: white"  class="btn btn-success  btn-block btn-block" type="button">Сохранить</button>
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
      admins: [],
      activeCourse: null,
      userRoles:false,
      userProfile:false,
      img:'',
      success: '',
      password:'',

      basic_data:true,
      payment_data:false,
      payments:[
        {
        bank: '',
        country: '',
        name_cart: '',
        phone: '',
        number_cart: '',
        delete:false,
        },


      ]

    };
  },
  mounted() {

  },
  created() {
    this.fetchData();
    this.user = JSON.parse(this.auth_role)

    console.log(this.payments,'09999')
  },
  methods: {
    onChange(e) {
      this.file = e.target.files[0];
    },
    formSubmit(e) {

      e.preventDefault();
      let existingObj = this;
      const config = {
        headers: {
          'content-type': 'multipart/form-data'
        }
      }

      //
      //
      // console.log(this.img,'999')
      // this.img = null;
      // console.log(this.img,'888')
      let data = new FormData();
      data.append('file', this.file);
      axios.post('/profile/upload/edit/profile', data, config)

          .then(function (res) {

            console.log(res,'0777')

            // existingObj.success = res.data.success;
            existingObj.img = 'public/users_img/'+res.data.file_name;
            existingObj.$message.success('Успешно Удалено');

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
        country:'',
        name_cart:'',
        phone:'',
        number_cart:'',
        delete:true,
      });


    },

    removePaymentCart(index){

      // console.log(this.valueGroups ,'07777')

      if (index != 0){
        this.payments.splice(index, 1)
      }else {
        alert('К сожалению последний позиция не удаляется');
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

          console.log(this.user,'user_cabinet')

          if (this.user.img_url != null){
            this.img = '/users_img/'+response.data.user.img_url;
          }else{
            this.img = '/users_img/noavatar.png';
          }



        })
        .catch((error) => {
          alert(error);
        });

    },

    userSaveData() {



      axios.post('/timetracking/update/save/', {
        query:this.user,
        password:this.password,
      }).then(response => {


        if (response.data.success){

          this.$message.success('Успешно Сохранено')
        }

      })

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
          alert(error);
        });
    },



  },

};

</script>

<style>
.contacts-info{
  margin-top:30px;
}
.active{
  color: blue;
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
