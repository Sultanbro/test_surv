<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Настройка кабинета</h1>

    <div class="settingCabinet">
      <ul class="p-0">
        <li>
<!--          v-if="auth_role == '1'"-->
          <a style="color: black" v-if="userArray.is_admin === 1"  @click="userRoles = true , userProfile = false ">Административные настройки</a>

          <a style="color: black"  @click="userProfile = true , userRoles = false">Настройка собственного профиля</a>
        </li>
      </ul>
    </div>



  </div>
  <div class="rp" style="flex: 1 1 0%;" v-if="userRoles">
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

    <div class="content mt-3 py-3">



      <div class="">


        <div class="form-group row">
          <div class="col-sm-4">

            <input hidden type="file" name="image" id="upload_image" accept="image/*" />
            <input hidden type="file" name="photo" id="photo" >

            <input id="user_id_img" value="1" hidden>
            <input name="file_name_img" value="empty" id="file_name_img" hidden>

            <label class="my-label-6 img_url_md" for="upload_image" style="cursor:pointer;" >
              <img src="https://cp.callibro.org/files/img/8.png" alt="img">


              <div class="mt-2 font-weight-bold font-sm text-center " style="width:100%">
                {{userArray.name}}
              </div>

              <div class="mt-0 mb-3 font-sm text-center " style="width:100%">
                {{userArray.email}}
              </div>
            </label>




          </div>



        </div>

      </div>

      <div class="contacts-info col-md-6 none-block mt-10" id="profile_d" >

        <div class="form-group row">
          <label for="firstName"
                 class="col-sm-4 col-form-label font-weight-bold">Имя <span class="red">*</span></label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="name" id="firstName" required
                   placeholder="Имя сотрудника" v-model="userArray.name"
            >
          </div>
        </div>

        <div class="form-group row">
          <label for="lastName"
                 class="col-sm-4 col-form-label font-weight-bold">Фамилия <span class="red">*</span></label>
          <div class="col-sm-8">
            <input class="form-control" type="text" name="last_name" id="lastName" required
                   placeholder="Фамилия сотрудника" v-model="userArray.last_name"
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



      </div>

    </div>
  </div>
</div>





</template>
<script>

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
      admins: [],
      activeCourse: null,
      userRoles:false,
      userProfile:false,
      userArray:[]
    };
  },
  created() {
    this.fetchData();
    this.authRole();

this.userProfile = true





  },
  mounted() {

    // this.userRoles = this.authRole;
    // this.userRoles = JSON.parse(this.authRole);

    // console.log(this.userRoles,'mounted')
    // console.log(this.authRole,'mounted')
  },
  methods: {

    authRole(){
      this.userArray = JSON.parse(this.auth_role)

      console.log(this.userArray,'0999');

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


<!--<script>-->

<!--$(document).ready(function(){-->

<!--  // $image_crop = $('#image_demo').croppie({-->
<!--  //   enableExif: true,-->
<!--  //   viewport: {-->
<!--  //     width:200,-->
<!--  //     height:200,-->
<!--  //     type:'square' //circle-->
<!--  //   },-->
<!--  //   boundary:{-->
<!--  //     width:300,-->
<!--  //     height:300-->
<!--  //   }-->
<!--  // });-->


<!--  $('#upload_image').on('change', function(){-->

<!--    var reader = new FileReader();-->
<!--    reader.onload = function (event) {-->
<!--      $image_crop.croppie('bind', {-->
<!--        url: event.target.result-->
<!--      }).then(function(){-->


<!--        console.log('jQuery bind complete');-->
<!--      });-->
<!--    }-->



<!--    reader.readAsDataURL(this.files[0]);-->

<!--    $('#uploadimageModal').modal('show');-->


<!--  });-->


<!--});-->

<!--function crop_image(){-->
<!--  $image_crop.croppie('result', {-->
<!--    type: 'canvas',-->
<!--    size: 'viewport'-->
<!--  }).then(function(response){-->
<!--    var user_id = $("#user_id_img").val();-->
<!--    var file_name = $("#file_name_img").val();-->
<!--    var origin_file = $("#upload_image").val();-->


<!--    var k = getFileKis;-->

<!--    //-->
<!--    // console.log(k,'0789')-->
<!--    //-->
<!--    console.log(response,'789465312')-->

<!--    $.ajax({-->
<!--      type:'POST',-->
<!--      url: "/profile/upload/edit/",-->
<!--      data:{-->
<!--        "image": response,-->
<!--        'user_id':user_id,-->
<!--        'file_name':file_name,-->
<!--        // file:getFileKis-->
<!--      },-->
<!--      // cache: false,-->
<!--      // contentType: 'json',-->
<!--      // processData: false,-->
<!--      success: (data) => {-->

<!--        console.log(data,'imasheev kis')-->

<!--        $('#uploadimageModal').modal('hide');-->

<!--        $(".img_url_md").html(data['src'])-->

<!--        $(".img_url_sm").html(data['src'])-->

<!--        $("#file_name_img").attr('value',data['filename'])-->


<!--      },-->
<!--      error: function(data){-->
<!--        console.log(data);-->
<!--      }-->
<!--    });-->
<!--  })-->
<!--}-->
<!--</script>-->