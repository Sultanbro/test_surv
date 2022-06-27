<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Настройка кабинета</h1>

    <div class="settingCabinet">
      <ul class="p-0">
        <li>
          <a style="color: black" v-if="auth_role == '1'" @click="userRoles = true">Административные настройки</a>
          <a href="/timetracking/edit-person?id=1">  Настройка собственного профиля</a>
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
</div>
</template>
<script>


export default {
  name: "Cabinet",
  props: {
    auth_role:'',
    auth_id:Number,

  },
  data() {
    return {
      test: 'dsa',
      items: [],
      users: [],
      admins: [],
      activeCourse: null,
      userRoles:false,
    };
  },
  created() {
    this.fetchData();

    console.log(this.auth_role,'authRole_id')
    console.log(this.auth_id,'id')

  },
  mounted() {

    // this.userRoles = this.authRole;
    // this.userRoles = JSON.parse(this.authRole);

    // console.log(this.userRoles,'mounted')
    // console.log(this.authRole,'mounted')
  },
  methods: {
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
