<template>
<div class="p-3 permissions">

<h4 class="title">Настройка доступов</h4>

<!-- Главная страница -->
<section v-if="role == null">
  <div class="d-flex mb-3">
    <div class="list">
      <div class="item d-flex contrast">
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

          <button class="btn btn-default btn-sm" @click="deleteItem(i)">
            <i class="fa fa-times" />
          </button>
        </div>
      </div>
      <div class=" d-flex mt-3">
        <button class="btn btn-default btn-sm" @click="addItem">Добавить</button>
      </div>
      <div class=" d-flex mt-3">
        <button class="btn btn-success btn-sm" @click="saveItems">Сохранить</button>
      </div>
      
    </div>

    <!-- Показать все роли -->
    <div class="roles-list">
      <div class="roles">
        <div class="contrast role-title"><b>Список ролей</b></div>
        <div class="item d-flex" v-for="(item, i) in roles" :key="i">
          <div class="name" @click="editRole(i)">{{ item.name }}</div> 
          <div class="actions">
            <i class="far fa-edit" @click="editRole(i)" />
            <i class="fa fa-times" @click="deleteRole(i)"/>
          </div>
        </div>
      </div>
      <button class="btn btn-default btn-sm mt-3" @click="addRole">Добавить роль</button>
    </div>
  </div>
</section>







<!-- Edit роль -->
<section v-if="role && !showRoles">
  <div class="d-flex mb-3">
    <button class="btn btn-primary btn-sm mr-2" @click="back">Назад</button>
  </div>

  <input type="text" v-model="role.name" class="role-title form-control mb-3" />

  <div class="pages">
    <div class="item d-flex contrast">
      <div class="name mr-3">Страница</div>
      <div class="check d-flex">Просмотр</div>
      <div class="check d-flex">Редактирование</div>
    </div>
    <div class="item d-flex" v-for="(page, i) in pages" :key="i">
      <div class="name mr-3">{{page.name}}</div>
      <div class="check d-flex">
          <label class="mb-0 pointer">
            <input class="pointer" v-model="role.perms[page.key + '_view']"  type="checkbox"  />
          </label>
      </div>
        <div class="check d-flex">
          <label class="mb-0 pointer">
            <input class="pointer" v-model="role.perms[page.key + '_edit']"  type="checkbox"  />
          </label>
      </div>
    </div>
  </div>

  <div class="mt-3">
     <button class="btn btn-success btn-sm" @click="updateRole">Сохранить</button>
  </div>
</section>












               

</div>
</template>
<script>
export default {
  name: "Permissions",
  watch: {
    items: {
      immediate: true, 
      handler (val, oldVal) {
        console.log(val)
      }
    }
  },
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
          this.items = response.data.items;

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
        perms: {}
      }
    },

    updateRole() {
      let loader = this.$loading.show();
      
      this.permissions = [];
      Object.keys(this.role.perms).forEach((el, index) => {
        this.permissions.push(el)
      });



      axios
        .post('/permissions/update-role', {
          role: this.role,  
          permissions: this.permissions
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

      if(!confirm('Вы точно хотите удалить доступ пользователю?')) {
         return false; 
       }

      if(this.items[i].user.id == null) {
        this.items.splice(i,1)
        return false; 
      }

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

    saveItems() {
      
      
      if(item.user == null) return null;
      if(item.role == null) return null;

      let loader = this.$loading.show();
       axios
        .post( '/permissions/update-user', {
          items: this.items,
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
    },

    deleteRole(i) {

       if(!confirm('Вы уверены удалить роль?')) {
         return false; 
       }

      if(this.roles[i].id == null) {
        this.roles.splice(i,1);
        return false; 
      }

      let loader = this.$loading.show();
       axios
        .post( '/permissions/delete-role', {
          role: this.roles[i]
        })
        .then((response) => {
          loader.hide();
           this.$message.success('Роль удалена!');
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    }
  },
};
</script>
