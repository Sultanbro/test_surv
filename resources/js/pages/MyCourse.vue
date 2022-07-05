<template>
<div class="d-flex mycourse">

  <!-- левый сайдбар -->
  <div class="lp">
    <h1 class="page-title">Мои курсы</h1> 

    <!-- список курсов -->
    <div v-if="activeCourse == null">
         <div class="section d-flex aic jcsb my-2"
          v-for="(course, c_index) in courses"
          :key="course.id"
          @click="getCourse(course.course_id)"
        >
          <p class="mb-0">{{ course.course.name }}</p>
        </div>
    </div>

    <!-- выбранный курс -->
    <div v-else>
       <div class="py-3">
          <div class="course-item" v-for="(item, c_index) in items"
            :key="item.id"
            :class="{'active': item.active}"
            @click="selectCourseItem(c_index)"
          >
            <div class="title">
                {{ item.title }}
            </div>
          </div>
      </div>
    </div>

  </div>

  <!-- правая часть -->
  <div class="rp" style="flex: 1 1 0%; padding-bottom: 0px;">
    <div class="hat">
      <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a @click="back">Мои курсы</a>
          <template v-if="activeCourse">
            <i class="fa fa-chevron-right"></i>
            <a href="#" >{{ activeCourse.course.name }}</a>
          </template>
        </div>
        <div class="control-btns">
            <div v-if="activeStep">
              <button class="btn btn-primary py-0" @click="nextStep"> Перейти к следующему</button>
            </div>
        </div>
      </div>
      <div><!----></div>
    </div>
    <div class="content mt-3">
      <div v-if="activeCourse" class="p">
       
        <!-- поле курса -->


          <div class="">

           


            <div class="mmmm-block">
              <div v-if="activeCourseItem">


                  <div v-if="activeCourseItem.item_model == 'App\\Models\\Books\\Book'">
                    <page-upbooks-read :book_id="activeCourseItem.item_id" mode="read"  />
                  </div>

                  <div class="p-3" v-if="activeCourseItem.item_model == 'App\\Models\\Videos\\Video'">
                      <page-playlist-edit 
                          :id="activeCourseItem.item_id"
                          mode="read" />
                  </div>



                  <div v-if="activeCourseItem.item_model == 'App\\KnowBase'" class="p">
                      <!-- <div class="text-container" v-html="activeStep.text"></div> -->

                       <booklist 
                        ref="booklist"
                        :trees="trees" 
                        :parent_name="activeCourseItem.title" 
                        :parent_id="activeCourseItem.item_id"
                        :show_page_id="0" 
                        mode="read"
                        :course_page="true"
                        :auth_user_id="0" /> 

                  </div>

                 
                  <!-- <div class="py-3">
                    <questions
                      :questions="activeStep.questions"
                      :id="activeStep.id"
                      :type="activeStep.type"
                      @passed="setStepStatus"
                      mode="read"
                      />

                      
                  </div>   -->
                  
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
      activeCourseItem: null,
      activeCourse: null,
      activeStep: null,
      trees: []
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
    setStart() {
      if(this.items.length > 0 && this.items[0].steps.length > 0) {
        this.activeCourseItem = this.items[0];
        this.activeStep = this.items[0].steps[0];
      }
    },
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
     this.activeCourseItem = this.items[i];
      if(this.activeCourseItem.model_type == 'App\\KnowBase') {
        this.selectKnowbaseSection();
      }
  },
    

    selectKnowbaseSection(book, page_id = 0) {
      axios
        .post("kb/tree", {
          id: id,
        })
        .then((response) => {
          if(response.data.error) {
            this.$message.info('Раздел не найден');
          }
          this.trees = response.data.trees;
        })
        .catch((error) => {
          alert(error);
        });
    },

    setStepStatus() {
      let index = this.activeCourseItem.steps.findIndex(s => s.id == this.activeStep.id);
      this.activeCourseItem.steps[index].status = 1;
    },

    selectStep(step, item) {
      this.activeCourseItem = item;
      this.activeStep = step;
    },

 
    nextStep() {
      let index = this.activeCourseItem.steps.findIndex(s => s.id == this.activeStep.id);
      
      this.$message.info(index);
      this.$message.info(this.activeCourseItem.steps[index]);
      if(this.activeCourseItem.steps[index].status != 1) {
        this.$message.info('Ответьте на вопросы правильно!');
        return ;
      }
   

      if(this.activeCourseItem.steps.length - 1  == index) {
         
        let c_index = this.items.findIndex(s => s.id == this.activeCourseItem.id);
        if(this.items.length - 1 == c_index) {
          this.$message.info('Конец курса!');
          return;
        } else {
          this.activeCourseItem = this.items[c_index + 1 ];
          this.activeStep = this.activeCourseItem.steps[0];
        }

      } else {
        this.activeCourseItem.steps[index + 1];
      }

     
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
          this.setStart();
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
