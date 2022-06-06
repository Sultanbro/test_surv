<template>
<div class="p-3 permissions">

<h4 class="title">Настройка доступов</h4>

<!-- Главная страница -->
<section v-if="!showRoles && role == null">
  <div class="d-flex mb-3">
      <button class="btn btn-info btn-sm mr-2" @click="showRolesPage">Роли</button>
      <button class="btn btn-default btn-sm" @click="addRole">Добавить роль</button>
  </div>
  
  <div class="list">
    <div class="item d-flex">
      <div class="person"><b>Пользователь</b></div>
      <div class="role">Роль</div>
      <div class="groups">Группы</div>
      <div class="actions"></div>
    </div>
    <div class="item d-flex" v-for="(item, i) in items" :key="i">
      <div class="person">
        <v-select :options="users" label="name" v-model="item.user" class="noscrollbar"></v-select>
      </div>
      <div class="role">
        <v-select :options="roles" label="name" v-model="item.role" class="noscrollbar"></v-select>
      </div>
      <div class="groups">
        <v-select :options="groups" label="name" v-model="item.groups" class="noscrollbar" multiple></v-select>
      </div>
      <div class="actions d-flex">
         <button class="btn btn-success btn-sm mr-2" @click="saveItem(i)">Сохранить</button>
        <button class="btn btn-danger btn-sm" @click="deleteItem(i)">Удалить</button>
      </div>
    </div>
    <div class=" d-flex mt-3">
      <button class="btn btn-primary btn-sm" @click="addItem">Добавить пользователя</button>
    </div>
  </div>
</section>



<!-- Edit роль -->
<section v-if="role && !showRoles">
  <div class="d-flex mb-3">
    <button class="btn btn-primary btn-sm mr-2" @click="back">Назад</button>
      <button class="btn btn-info btn-sm" @click="showRolesPage">Роли</button>
  </div>


  <input type="text" v-model="role.name" class="role-title mb-3" />


  <div class="pages">
    <div class="item d-flex" v-for="(page, i) in pages" :key="i">
      <div class="name mr-3">{{page.name}}</div>
      <div class="check d-flex">
          <label class="mr-3 pointer">
            <input class="pointer" v-model="permissions[page.key + '-view']"  type="checkbox" />
            <i class="fa fa-eye"></i>
          </label>
          <label class="mr-3 pointer">
            <input class="pointer" v-model="permissions[page.key + '-edit']"  type="checkbox" />
            <i class="fa fa-edit"></i>
          </label>
      </div>
    </div>
  </div>

  <div class="mt-5">
     <button class="btn btn-success btn-sm" @click="updateRole">Сохранить</button>
  </div>
</section>



<!-- Показать все роли -->
<section v-if="showRoles && role == null">
  <div class="d-flex mb-3">
    <button class="btn btn-primary btn-sm mr-2" @click="back">Назад</button>
      <button class="btn btn-info btn-sm" @click="addRole">Добавить роль</button>
  </div>

  

  <div class="roles">
    <div class="item" v-for="(item, i) in roles" :key="i">
      <div class="name" @click="editRole(i)">{{ item.name }}</div> 
    </div>
  </div>
</section>









               

</div>
</template>
<script>
export default {
  name: "Permissions",
  data() {
    return {
      role: null,
      users: [], // all select
      groups: [], // all select
      items: [],
      roles: [],
      pages: [],
      permissions: [],
      showRoles: false,
    };
  },
  created() {
    this.fetchData();
  },
  mounted() {},
  methods: {

    fetchData() {
      let loader = this.$loading.show();

      axios
        .get("/permissions/get", {})
        .then((response) => {

          this.users = response.data.users;
          this.roles = response.data.roles;
          this.groups = response.data.groups;
          this.pages = response.data.pages;

          this.items = [ 
              {
                user: {
                  id: 5,
                  name: 'ALi'
                },
                role: {
                  id: 1,
                  name: 'writerro'
                },
                groups: [
                  {
                    id: 42,
                    name: 'Kaspi'
                  }
                ]
              }
            ];


          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    addItem() {
      this.items.push(
        {
          user: null,
          role: null,
          groups: []
        }
      );
    },

    editRole(i) {
      this.showEditRole = true;

      let role = this.roles[i];
      this.permissions  = role.permissions
      this.role = role;
      this.showRoles = false;
    }, 

    showRolesPage() {
      this.role = null;
      this.showRoles = true;
    },

    addRole() {
      this.role = {
        name: 'Test',
        id: null,
        permissions: {}
      }
    },

    updateRole() {
      let loader = this.$loading.show();
       axios
        .post( '/permissions/update-role', {
          role: this.role
        })
        .then((response) => {

          let index = this.roles.findIndex(x => x.id == null);
          if(index != -1) this.roles[index].id = response.data.id;
          loader.hide();
           this.$message.success('Роль сохранена!');
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    deleteItem(i) {

      let loader = this.$loading.show();
       axios
        .post( '/permissions/delete-user', {
          user: this.items[i].user
        })
        .then((response) => {
          this.items.splice(i,1)
          loader.hide();
           this.$message.success('Пользователь удален!');
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });

    },

    saveItem(i) {
      let item = this.items[i]
      if(item.user == null) return null;
      if(item.role == null) return null;

      let loader = this.$loading.show();
       axios
        .post( '/permissions/update-user', {
          user: item
        })
        .then((response) => {
          loader.hide();
           this.$message.success('Пользователь сохранен!');
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    back() {
      this.showRoles = false;
      this.role = null;
    }
  },
};
</script>
