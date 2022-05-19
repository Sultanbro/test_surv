<template>
<div class="d-flex">

  <div class="lp">
    <h1 class="page-title">Мои курсы</h1>

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
          <a href="#">Мои курсы</a>
          <template v-if="activeCourse">
            <i class="fa fa-chevron-right"></i>
            <a href="#" >{{ activeCourse.name }}</a>
          </template>
          <!---->
        </div>
        <div class="control-btns"></div>
      </div>
      <div><!----></div>
    </div>
    <div class="content mt-3">
      <div v-if="activeCourse" class="p-3">
        <course :id="activeCourse.id" /> 
      </div>
    </div>
  </div>

 

</div>
</template>

<script>
export default {
  name: "MyCourse",
  data() {
    return {
      test: 'dsa',
      items: [],
      activeCourse: null,
    };
  },
  created() {
    this.fetchData();
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
        .get("/my-courses/get", {})
        .then((response) => {

          console.log(response.data);
          this.items = response.data.items;
         
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
