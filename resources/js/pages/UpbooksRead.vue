<template>
  <div class="upbook-read-page">

    <!-- left side bar -->
    <div class="controls">
      <div @click="$emit('back')" class="btn w-full btn-success" v-if="showBackBtn">
        <i class="fa fa-arrow-left"></i>
        Назад
      </div>

      <div class="d-flex first-block">

        <!-- left -->
        <div v-if="activeBook != null">
          <img :src="activeBook.img == '' ? '/images/book_cover.jpg' : activeBook.img" 
            class="w-full pr-3" />
          <div v-if="isLoading">
            <p class="text-center mt-3">
              <b>Загружается...</b>
            </p>
          </div>
        </div>

        <!-- right -->
        <div>
           <div>
            <p class="text-center">
              <b>{{ page }} / {{ pageCount }}</b>
            </p>
          </div>

          <div class="d-flex justify-content-center"> 
            <button class="btn rounded mr-2" @click="prevPage">
              <i class="fa fa-chevron-left"></i>
            </button>
            <button class="btn rounded" @click="nextPage">
              <i class="fa fa-chevron-right"></i>
            </button>
          </div>

          <div class="d-flex justify-content-center mt-2">
            <button class="btn rounded mr-1 p-2" @click="zoomIn">
              <i class="fa fa-search-plus"></i>
            </button>
            <button class="btn rounded mr-1 p-2" @click="zoom = 0">
              <i class="fa fa-bars"></i>
            </button> 
            <button class="btn rounded p-2" @click="zoomOut">
              <i class="fa fa-search-minus"></i> 
            </button>
          </div>  
        </div>
      </div>  
     
      <!-- Page numbers -->
      <div class="chapters mt-3">
        <div class="item font-bold mb-2">
          <p class="mb-0">Вопросы на странице:</p>
        </div>

        <div class="item d-flex" v-for="test in tests" :class="{
            'pass': test.pass || test.item_model !== null,
            'active': page == test.page
          }">
          <div class="mr-2">
            <i class="fa fa-arrow-right pointer" v-if="page == test.page || active_page == test.page"></i>
            <i class="fa fa-check pointer" v-else-if="test.item_model !== null"></i>
            <i class="fa fa-lock pointer" v-else></i>
          </div>
          <p class="mb-0" @click="moveTo(test.page, test.pass)">
            Стр. {{ test.page }} : {{ test.questions.length }} вопрос (-ов)
          </p>
        </div>
      </div>

    </div>

    <!-- PDF viewer -->
    <div class="pdf"
      :class="{
        'show': activeTest == null,
        w600: zoom == 600,
        w700: zoom == 700,
        w800: zoom == 800,
        w900: zoom == 900,
        w1000: zoom == 1000,
        w1100: zoom == 1100,
        w1200: zoom == 1200,
        w1300: zoom == 1300,
        w1400: zoom == 1400,
        w1500: zoom == 1500,
        w1600: zoom == 1600,
        full: zoom == 0,
      }"
    >

      <vue-pdf-embed 
        v-if="activeBook !== null"
        :source="activeBook.link"
        ref="pdfRef"
        :page="page"
        class="plugin"
        @rendered="handleDocumentRender"
      />
    </div>

    <!-- Test viewer -->
    <div class="test" v-if="activeTest !== null">
      <questions
        :questions="activeTest.questions"
        :pass="activeTest.pass"
        :id="0"
        :key="test_key"
        type="book"
        :mode="mode" 
        @continueRead="nextPage"
        @passed="activeTest.pass = true"
      />
    </div>

    <template v-if="(activeTest && course_page) || (activeTest == null && pageCount == page)">
      <button class="next-btn btn btn-primary" 
        v-if="activeTest.pass"
        @click="nextElement()">
        Продолжить курс
        <i class="fa fa-angle-double-right ml-2"></i>
      </button>
    </template>
   

  </div>
</template>

<script>
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
export default {
  name: "UpbooksRead",
  components: {
    VuePdfEmbed
  },
  props: ["book_id", "mode", 'showBackBtn', 'course_page', 'active_page', 'course_item_id'],
  data() {
    return {
      page: 1,
      activeCategory: null,
      activeTest: null,
      activeBook: null,
      pageCount: 0,
      zoom: 800,
      isLoading:true,
      tests: [],
      test_key: 1,
      checkpoint: 1, // last page
      pass: false // pass test
    };
  },
  created() {

      this.checkpoint = this.pageCount
      this.getTests()

  },

  mounted() {
    document.addEventListener("keyup", this.keyup);
  },

  methods: {
    moveTo(page, pass) {
      if(pass) {
        this.page = page;

        let i = this.tests.findIndex(el => el.page == page);
        if(i != -1) {
          this.activeTest = this.tests[i]
          this.test_key++;
        } else {
          this.activeTest = null; 
        }

      }
    },

    nextElement() {
      
      if(this.activeTest.item_model == null) {
        this.setSegmentPassed();
        this.activeTest.item_model = {status: 1}; 
      }

      let index = this.tests.findIndex(el => el.page >= this.page);

      if(index != -1 && this.tests.length - 1 > index) {
        this.activeTest = null;
        this.page++;
      } else {
        this.$parent.after_click_next_element();
      }
      
    },

    setSegmentPassed() {
      axios
        .post("/my-courses/pass", {
          id: this.page,
          type: 1,
          course_item_id: this.course_item_id,
        })
        .then((response) => {
         // this.activeVideo.item_models.push(response.data.item_model);
        })
        .catch((error) => {
          alert(error);
        });
    },

    getTests() {
      let loader = this.$loading.show();

      axios
        .post("/admin/upbooks/tests/get", {
          id: this.book_id,
        })
        .then((response) => {
          this.tests = response.data.tests;
          this.activeBook = response.data.activeBook;


          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    keyup(e) {
      if (e.keyCode == 37) {
        this.prevPage();
      }
      if (e.keyCode == 39) {
        this.nextPage();
      }
    },

    setCurrentTestNext(action = 'nothing') {
      
      
    },

    setCurrentTestPrev(action = 'nothing') {
      let last_test = this.activeTest;
      // find current test

      console.log('last_test', last_test)
      if(last_test == null) {

           console.log('no_test')

         let i = this.tests.findIndex(el => el.page == this.page - 1);

        // already passed
        if(i != -1) {
            console.log('already passed')
          this.activeTest = this.tests[i]
          this.test_key++;
        } else {
               console.log('no passed')
          this.activeTest = null; 
          return true;
        }
      } else {
          console.log('has_Test')
        this.activeTest = null; 
        return true;
      }
     

      return false;
    },

    nextPage() {
      if (this.page == this.pageCount) return 0;

      if(this.activeTest && !this.activeTest.pass) {
        this.$message.info('Ответьте на вопросы, чтобы пройти дальше');
        return 0;   
      }
      
      // find current test
      let i = this.tests.findIndex(el => el.page == this.page);
      if(i != -1) {
        this.activeTest = this.tests[i]
        this.test_key++;
      } else {
        this.activeTest = null; 
        this.page++;
      }
      
    }, 

    prevPage() { 
      if (this.page == 1) return 0;
      console.log(this.page)
      let move = this.setCurrentTestPrev()

      
      // move to prev page
      if(move) {
        this.page--;
      }
    },

    zoomIn() {
      if (this.zoom == 0) this.zoom = 1600;
      if (this.zoom < 1600) {
        this.zoom += 100;
      }
    },

    zoomOut() {
      if (this.zoom == 0) this.zoom = 1500;
      if (this.zoom > 600) {
        this.zoom -= 100;
      }
    },

    handleDocumentRender() {
      this.isLoading = false
      this.pageCount = this.$refs.pdfRef.pageCount
    },
  },
};
</script>

<style>

</style>