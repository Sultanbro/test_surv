<template>
<div class="p-3 permissions">

<h4 class="title">Настройка доступов<span v-if="role != null">: Роли</span></h4>

<!-- Главная страница -->
<section>
  <div class="d-flex mb-3">
    <div class="list" v-if="role == null">
      <div class="item d-flex contrast">
        <div class="person"></div>
        <div class="role">Роль</div>
        <div class="groups">Отделы  
              <i class="fa fa-info-circle ml-2" 
                v-b-popover.hover.right.html="'Выберите только те отделы, которые будет видеть сотрудник(-и)'" 
                title="Доступ к отделам">
            </i>
        </div>
        <div class="actions"></div>
      </div>
      
      <permission-item 
        v-for="(item, i) in items"
        class="item d-flex" 
        :key="i" 
        @deleteItem="deleteItem(i)"
        :item="item"
        :groups="groups"
        :users="users"
        :roles="roles"
      />

      <div class=" d-flex mt-3">
        <button class="btn btn-default btn-sm" @click="addItem">Добавить</button>
      </div>
      <div class=" d-flex mt-3">
        <button class="btn btn-success btn-sm" @click="saveItems">Сохранить</button>
      </div>
      
      <div class="mt-3">
        <superselect :values="values" />
      </div>
    </div>

    <!-- Edit роль -->
    <div v-if="role" class="edit-role">
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
    </div>

    <!-- Edit роль -->
    <div v-if="role" class="edit-role">
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
      values: [],
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
          user_id: null,
          groups_all: false,
          user: {
            id: null,
            name: ''
          },
          role: {
            id: null,
            name: ''
          },
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
      Object.keys(this.role.perms).forEach((key, index) => {
        if(this.role.perms[key]) this.permissions.push(key)
      });



      axios
        .post('/permissions/update-role', {
          role: this.role,  
          permissions: this.permissions
        })
        .then((response) => {
          if(this.role.id == null) {
             this.roles.push({
               id: response.data.id,
               name: response.data.name,
               perms: this.role.perms,
               permissions: []
             });
          }

          this.role = null;
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
      
      let loader = this.$loading.show();
       axios
        .post( '/permissions/update-user', {
          items: this.items,
        })
        .then((response) => {
          loader.hide();
           this.$message.success('Пользователи сохранены!');
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
           this.roles.splice(i,1);
           this.$message.success('Роль удалена!');
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

  
  },
};
</script>
