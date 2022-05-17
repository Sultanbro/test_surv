<template>
<div class="d-flex">

  <div class="lp">
    <h1 class="page-title">Курсы</h1>

    <div
      class="section d-flex aic jcsb my-2"
      v-for="(course, c_index) in courses"
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

    <button class="btn-add" @click="modals.add_course.show = true">
      Добавить курс
    </button>
  </div>


  <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
    <div class="hat">
      <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a href="#">Курсы</a>
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

    <b-modal
      v-model="modals.add_course.show"
      title="Новый курс"
      size="md"
      class="modalle"
      hide-footer
    >
      <input
        type="text"
        v-model="modals.add_course.name"
        placeholder="Название курса..."
        class="form-control mb-2"
      />
      <button class="btn btn-primary rounded m-auto" @click="createCourse">
        <span>Сохранить</span>
      </button>
    </b-modal>

</div>
</template>

<script>
export default {
  name: "Courses",
  data() {
    return {
      test: 'dsa',
      courses: [],
      activeCourse: null,
      modals: {
        add_course: {
          show: false,
          name: ''
        }
      },
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

    deleteCourse(i) {
       if (confirm("Вы уверены удалить курс?")) {
        
        let loader = this.$loading.show();

        axios
          .post("/admin/courses/delete", {
            id: this.courses[i].id
          })
          .then((response) => {
            this.$message.success("Курс успешно удален!");
            this.courses.splice(i,1)
            loader.hide();
          })
          .catch((error) => {
            loader.hide();
            alert(error);
          });
        }
    },
    fetchData() {
      let loader = this.$loading.show();

      axios
        .get("/admin/courses/get", {})
        .then((response) => {
          this.courses = response.data.courses;
          if (this.courses.length > 0) {
            this.activeCourse = this.courses[0];
          }
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
