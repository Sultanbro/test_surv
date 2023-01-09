<template>
<div class="d-flex courses">

  <div class="lp">
    <h1 class="page-title">Курсы</h1>


    <Draggable
      class="sss"
      tag="div"
      :handle="'.fa-bars'"
      :list="courses"
      :group="{ name: 'g1' }"
      @end="saveOrder"
      :id="0"
    >
      <div
        class="section d-flex  my-2"
        :class="{'active':activeCourse != null && activeCourse.id == course.id}"
        v-for="(course, c_index) in courses"
        :key="course.id"
        :id="course.id"
        @click="selectCourse(c_index)"
      >
        <i class="fa fa-bars mr-2 mt-1 pointer"></i>

        <div class="d-flex aic jcsb w-full">
           <p class="mb-0">{{ course.name }}</p>
            <div class="d-flex">

            <i
              class="fa fa-trash ml-2"
              v-if="course.id != 0"
              @click.stop="deleteCourse(c_index)"
            ></i>
           </div>
        </div>

      </div>
    </Draggable>

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
        <Course :id="activeCourse.id" />
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
import Draggable from 'vuedraggable'
import Course from '@/pages/Course.vue'

export default {
  name: 'Courses',
  components: {
    Draggable,
    Course,
  },
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
    this.$emit('init')
    this.fetchData();
  },
  mounted() {},
  methods: {
    saveOrder(event) {

      axios.post('/courses/save-order', {
        id: event.item.id,
        order: event.newIndex, // oldIndex
      })
      .then(response => {
          this.$toast.success('Очередь сохранена');
      })
    },
    selectCourse(i) {
      this.activeCourse = this.courses[i];
      window.history.replaceState({ id: "100" }, "Курсы", "/courses?id=" + this.activeCourse.id);
    },

		editAccess(i) {
			alert('Видимость и назначение курса отделам');
		},

		createCourse() {
			if (this.modals.add_course.name.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			axios
				.post('/admin/courses/create', {
					name: this.modals.add_course.name,
				})
				.then((response) => {
					this.modals.add_course.show = false;
					this.modals.add_course.name = '';

					this.courses.push({
						id: response.data.id,
						name: response.data.name,
						items: [],
					});

					this.activeCourse = this.courses[this.courses.length - 1]


					this.$toast.success('Курс успешно создан!');
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
					.post('/admin/courses/delete', {
						id: this.courses[i].id
					})
					.then((response) => {
						this.$toast.success('Курс успешно удален!');
						this.courses.splice(i,1)
						this.activeCourse = null;
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
				.get('/admin/courses/get', {})
				.then((response) => {
					this.courses = response.data.courses;

           const urlParams = new URLSearchParams(window.location.search);
          let course_id = urlParams.get('id');

          if(course_id != null) {
            let i = this.courses.findIndex(el => el.id == course_id)
            if(i != -1) this.activeCourse = this.courses[i]
          } else if (this.courses.length > 0) {
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
