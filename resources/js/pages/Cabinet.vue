<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Настройка кабинета</h1>

    <div class="settingCabinet">
      <ul class="p-0">
        <li>
          <a style="color: black"   @click="userRoles = true , userProfile = false ">Административные настройки</a>

          <a style="color: black"  @click="userProfile = true , userRoles = false">Настройка собственного профиля</a>


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
          <a href="#">Настройка профиля</a>
        </div>
        <div class="control-btns"></div>
      </div>

      <div class="col-5" style="max-height: 200px">
        <cropper
            class="cropper"
            :src="img"
            :stencil-props="{
      aspectRatio: 10/12
      }"
            @change="change"
        ></cropper>


      </div>

    <div class="col-5">


    </div>

    <div class="col-12 w-25">

      <div ref="dropzone" class="p-5 bg-dark">
          Upload
      </div>

    </div>

    <div class="content mt-3 py-3">



      <div class="contacts-info col-md-6 none-block mt-10" id="profile_d" >

        <div class="form-group row">
          <label for="firstName"
                 class="col-sm-4 col-form-label font-weight-bold">Имя <span class="red">*</span></label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="name" id="firstName" required
                   placeholder="Имя сотрудника"
            >
          </div>
        </div>

        <div class="form-group row">
          <label for="lastName"
                 class="col-sm-4 col-form-label font-weight-bold">Фамилия <span class="red">*</span></label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="last_name" id="lastName" required
                   placeholder="Фамилия сотрудника"
            >
          </div>
        </div>

        <div class="form-group row">
          <label for="email" class="col-sm-4 col-form-label font-weight-bold">Новый пароль </label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="new_pwd" id="new_pwd"
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

        <div class="form-group row">

          <button class="btn btn-primary ml-3" type="button">Сохранить</button>
        </div>



      </div>

    </div>
  </div>
</div>





</template>
<script>
// import { Cropper } from 'vue-advanced-cropper'
// import 'vue-advanced-cropper/dist/style.css';
import Dropzone from 'dropzone'

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
      img: '',
      image:'',
      dropzone:null
    };
  },
  mounted() {
    this.dropzone = new Dropzone(this.refs.dropzone,{
      url:'get'
    })
  },
  created() {
    this.fetchData();

    this.user = JSON.parse(this.auth_role)
  //
  //   console.log(this.user);



  },
  methods: {

    change({ coordinates, canvas }) {

      this.canvas = canvas,

      console.log(coordinates, canvas);
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
        })
        .catch((error) => {
          alert(error);
        });

    },


     save() {
      axios
        .post("/cabinet/save", {
          admins: this.admins
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

