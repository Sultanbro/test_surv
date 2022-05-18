<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Доступы</h1>
    <div
      class="section d-flex aic jcsb my-2"
      v-for="(course, c_index) in items"
      :key="course.id"
      @click="selectCourse(c_index)"
    >
      <p class="mb-0">{{ course.name }}</p>

      <div class="d-flex">
        <i
        class="fa fa-cogs"
        v-if="course.id != 0"
        @click.stop="editAccess(c_index)"
      ></i>
      <i
        class="fa fa-trash ml-2"
        v-if="course.id != 0"
        @click.stop="deleteCourse(c_index)"
      ></i>
      </div>
    </div>

   
  </div>


  <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
    <div class="hat">
      <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a href="#">Доступы</a>
          <template v-if="activeCourse">
            <i class="fa fa-chevron-right"></i>
            <a href="#" >{{ activeCourse.name }}</a>
          </template>
          <!---->
        </div>
        <div class="control-btns"></div>
      </div>
    </div>
    <div class="content mt-3">




      <div class="col-12 columnContent " >
        <div class="row">
          <div class="col-md-2 columnInnerLeft">
            Name
          </div>
          <div class="col-md-5" >
            <input class="form-control btn-block" placeholder="Editor"/>
          </div>
        </div>
      </div>

      <div class="col-12 columnContent " >
        <div class="row">
          <div class="col-md-2 columnInnerLeft">
            Slug
          </div>
          <div class="col-md-5" >
            <input class="form-control btn-block" placeholder="Editor"/>
          </div>
        </div>
      </div>

      <div class="col-12 columnContent " >
        <div class="row">
          <div class="col-md-2 columnInnerLeft">
            Permissions
          </div>
          <div class="col-md-5" >

            <div>
              <button  class="btn btn-light font-bold">Select all</button>
              <button  class="btn btn-light font-bold">Do not select any</button>
            </div>


          </div>
          <div class="col-md-12 p-0 mt-4">

            <div class="col-md-2 offset-2">
              <p class="font-weight-bold list-checkbox">Product</p>
              <ul>
                <li>
                   <input id="view_own_products" type="checkbox"/>
                   <label for="view_own_products" class="label-input" >View own products</label>
                </li>
                <li>
                  <input id="view_products" type="checkbox"/>
                  <label for="view_products" class="label-input" >View products</label>
                </li>
                <li>
                  <input id="manage_own_products" type="checkbox"/>
                  <label for="manage_own_products" class="label-input">Manage own products</label>
                </li>
                <li>
                  <input id="manage_products"  type="checkbox"/>
                  <label for="manage_products" class="label-input">Manage products</label>
                </li>
                <li>
                  <input id="force_delete_products"  type="checkbox"/>
                  <label for="force_delete_products" class="label-input">Force Delete products</label>
                </li>
                <li>
                  <input id="restore_products"  type="checkbox"/>
                  <label for="restore_products" class="label-input">Restore products</label>
                </li>
              </ul>
            </div>

            <div class="col-md-2 p-0">
              <p class="font-weight-bold list-checkbox">Category</p>
              <ul>
                <li>
                  <input id="view_own_categories" type="checkbox"/>
                  <label for="view_own_categories" class="label-input" >View own categories</label>
                </li>
                <li>
                  <input id="view_categories" type="checkbox"/>
                  <label for="view_categories" class="label-input" >View categories</label>
                </li>
                <li>
                  <input id="manage_own_categories" type="checkbox"/>
                  <label for="manage_own_categories" class="label-input">Manage own categories</label>
                </li>
                <li>
                  <input id="manage_categories"  type="checkbox"/>
                  <label for="manage_categories" class="label-input">Manage categories</label>
                </li>
                <li>
                  <input id="force_delete_categories"  type="checkbox"/>
                  <label for="force_delete_categories" class="label-input">Force Delete categories</label>
                </li>
                <li>
                  <input id="restore_categories"  type="checkbox"/>
                  <label for="restore_categories" class="label-input">Restore categories</label>
                </li>
              </ul>
            </div>

            <div class="col-md-2 p-0">
              <p class="font-weight-bold list-checkbox">Order</p>
              <ul>
                <li>
                  <input id="view_own_orders" type="checkbox"/>
                  <label for="view_own_orders" class="label-input" >View own orders</label>
                </li>
                <li>
                  <input id="view_orders" type="checkbox"/>
                  <label for="view_orders" class="label-input" >View orders</label>
                </li>
                <li>
                  <input id="manage_own_orders" type="checkbox"/>
                  <label for="manage_own_orders" class="label-input">Manage own orders</label>
                </li>
                <li>
                  <input id="manage_orders"  type="checkbox"/>
                  <label for="manage_orders" class="label-input">Manage orders</label>
                </li>
                <li>
                  <input id="force_delete_orders"  type="checkbox"/>
                  <label for="force_delete_orders" class="label-input">Force Delete orders</label>
                </li>
                <li>
                  <input id="restore_orders"  type="checkbox"/>
                  <label for="restore_orders" class="label-input">Restore orders</label>
                </li>
              </ul>
            </div>

            <div class="col-md-2">
              <p class="font-weight-bold list-checkbox">User</p>
              <ul>
                <li>
                  <input id="view_users" type="checkbox"/>
                  <label for="view_users" class="label-input" >View users</label>
                </li>
                <li>
                  <input id="create_users" type="checkbox"/>
                  <label for="create_users" class="label-input" >Create users</label>
                </li>
                <li>
                  <input id="edit_users" type="checkbox"/>
                  <label for="edit_users" class="label-input">Edit user</label>
                </li>
                <li>
                  <input id="delete_users"  type="checkbox"/>
                  <label for="delete_users" class="label-input">Delete users</label>
                </li>

              </ul>
            </div>

            <div class="col-md-2 pl-0">
              <p class="font-weight-bold list-checkbox">Role</p>
              <ul>
                <li>
                  <input id="view_roles" type="checkbox"/>
                  <label for="view_roles" class="label-input" >View roles</label>
                </li>
                <li>
                  <input id="create_roles" type="checkbox"/>
                  <label for="create_roles" class="label-input" >Create roles</label>
                </li>
                <li>
                  <input id="edit_roles" type="checkbox"/>
                  <label for="edit_roles" class="label-input">Editroles</label>
                </li>
                <li>
                  <input id="delete_roles"  type="checkbox"/>
                  <label for="delete_roles" class="label-input">Delete roles</label>
                </li>

              </ul>
            </div>
          </div>
        </div>
      </div>




      <div v-if="activeCourse" class="p-3">
        <course :id="activeCourse.id" /> 
      </div>



    </div>
  </div>

 

</div>
</template>
<style>

  .label-input{
    cursor: pointer;
    padding-left:5px;
  }
  .list-checkbox ul li{
    list-style: none;
    float: left;
  }
  .columnContent{
    padding-top: 25px;
    padding-bottom: 25px;
    border-bottom: 1px solid #ced4da;

  }

  .columnInnerLeft{
    margin-top: 10px;
  }
</style>
<script>
export default {
  name: "Permissions",
  data() {
    return {
      test: 'dsa',
      items: [],
      activeCourse: null,
    };
  },
  created() {
    //this.fetchData();
  },
  mounted() {},
  methods: {
    selectCourse(i) {
      this.activeCourse = this.courses[i];
    },

    editAccess(i) {
      alert('Видимость и назначение курса группам');
    },

    createCourse() {
      if (this.modals.add_course.name.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios
        .post("/admin/courses/create", {
          name: this.modals.add_course.name,
        })
        .then((response) => {
          this.modals.add_course.show = false;
          this.modals.add_course.name = "";

          this.courses.push({
            id: response.data.id,
            name: response.data.name,
            items: [],
          });

          this.$message.success("Курс успешно создан!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    fetchData() {
      let loader = this.$loading.show();

      axios
        .get("/permissions/get", {})
        .then((response) => {

          console.log(response.data);
          //this.items = response.data.courses;
         
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },
  },
};
</script>
