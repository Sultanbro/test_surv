<template>
<div class="d-flex mycourse">

  <div class="lp">
    <h1 class="page-title">Мои курсы</h1>

    <div v-if="activeCourse == null">
      <!-- список курсов -->
         <div
          class="section d-flex aic jcsb my-2"
          v-for="(course, c_index) in courses"
          :key="course.id"
          @click="getCourse(course.course_id)"
        >
          <p class="mb-0">{{ course.course.name }}</p>
        </div>


    </div>





    <div v-else>
      <!-- выбран курс -->
      <div class="btn btn-grey mb-3 mt-3" @click="back">
        <i class="fa fa-arrow-left"></i>
        <span>Вернуться к моим курсам</span> 
      </div>
      <div>
        <p>{{ activeCourse.course.name }}</p>
        <img class="course-img w-full" :src="activeCourse.course.img" />

      </div>

        <div>
        Пройдено: 0%
         <progress value="0" max="100"></progress>
      </div>

      <div class="mt-3 " v-html="activeCourse.course.text"></div>
    </div>
      
   

   
  </div>


  <div class="rp" style="flex: 1 1 0%; padding-bottom: 0px;">
    <div class="hat">
      <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a @click="back">Мои курсы</a>
          <template v-if="activeCourse">
            <i class="fa fa-chevron-right"></i>
            <a href="#" >{{ activeCourse.course.name }}</a>
          </template>
          <!---->
        </div>
        <div class="control-btns"></div>
      </div>
      <div><!----></div>
    </div>
    <div class="content mt-3">
      <div v-if="activeCourse" class="p">
       
        <!-- поле курса -->


          <div class="">

            <div class="col-md-3 fixed-height py-3 ">



                <div v-for="(item, c_index) in items"
                        :key="item.id" class="course-item" :class="{
                          'active': item.active
                        }"
                        @click="selectCourseItem(c_index)"
                        >

                      <div class="title">
                          {{ item.title }}
                      </div>

                      <div v-if="item.status == 2">
                          <div
                            class="section d-flex aic jcsb my-2"
                            @click="selectStep(step)"
                            v-for="(step, i) in item.steps"
                          >
                            <p class="mb-0">{{ step.title }}</p>
                        </div>
                      </div>

                    
                    </div>
              </div>



            <div class="col-md-9 fixed-height py-3 ">
              <div v-if="activeStep">


                  <div v-if="activeStep.type == 'book'">
                    EEEEEE NOOOOOOOOOO
                  </div>



                  <div v-if="activeStep.type == 'video'">
                      <div id="video" class="mb-3 w65"></div>
                  </div>



                  <div v-if="activeStep.type == 'kb'">
                      <div class="text-container" v-html="activeStep.text"></div>
                  </div>

                 

                  <questions
                    :questions="activeStep.questions"
                    :id="activeStep.id"
                    :type="activeStep.type"
                    mode="read"
                  />
                  
                  <button class="btn btn-primary mt-3" @click="nextStep"> Перейти к следующему</button>
              </div>
            </div>


            </div>
          </div>
 



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
      test: "dsa",
      items: [],
      courses: [],
      activeCourse: null,
      activeStep: null,
    };
  },
  created() {
    this.fetchData();

    // бывор группы
    const urlParams = new URLSearchParams(window.location.search);
    let id = urlParams.get("id");
    if (id) {
      this.getCourse(id);
    }
  },
  mounted() {},
  methods: {
    selectCourse(i) {
      this.items = [];
      this.activeCourse = this.courses[i];
    },

    fetchData() {
      let loader = this.$loading.show();

      axios
        .get("/my-courses/get", {})
        .then((response) => {
          this.courses = response.data.courses;
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

  selectCourseItem(i) {
    this.activeCourse
  },

    selectStep(step) {
      this.activeStep = step;

        if(this.activeStep.type == 'video') {
          this.player = new Playerjs({
            id: "video",
            poster: "",
            file: this.player.links,
          });
        }
        
      

    },

    nextStep() {
      alert('next step')
    },
    getCourse(id) {
      let loader = this.$loading.show();

      axios
        .get("/my-courses/get/" + id)
        .then((response) => {
          this.items = response.data.items;
          this.activeCourse = response.data.course;

          // change URL
          const urlParams = new URLSearchParams(window.location.search);
          let b = urlParams.get("id");
          let uri = "/my-courses?id=" + id;
          window.history.replaceState({}, "Мои курсы", uri);

          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    back() {
      this.activeCourse = null;
      window.history.replaceState({ id: "100" }, "Мои курсы", "/my-courses");
    },
  },
};
</script>
