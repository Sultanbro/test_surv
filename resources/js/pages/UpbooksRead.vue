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
        loading-failed="sad"
        @rendered="loaded"
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
      pass: false, // pass test
      page_map: [],
      map_index: 0,
      pdf_loaded: false,
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

    loaded() {
      this.formPageMap()
      this.pdf_loaded = true;
    },

    nextElement() {
      
      if(this.activeTest.item_model == null) {
        this.setSegmentPassed();
        this.activeTest.item_model = {status: 1}; 
      }

      this.nextPage()
      
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
          course_item_id: this.course_item_id
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

    formPageMap() {
      let arr = [];
      let page = 1;

      console.log(this.pageCount)
      while (page <= this.pageCount) {

        arr.push({
          page: page,
          has_test: false, 
        });

        let i = this.tests.findIndex(el => el.page == page);
        if(i != -1) {
          arr.push({
            page: page,
            has_test: true, 
          });
        }

        page++;
      }
     
      this.page_map = arr;
    },

    nextPage() {
      if (this.map_index == this.page_map.length - 1 || !this.pdf_loaded) return 0;

      // check current test
      if(this.activeTest && !this.activeTest.pass) {
        this.$message.info('Ответьте на вопросы, чтобы пройти дальше');
        return 0;   
      }

      this.map_index++;

      let next_page = this.page_map[this.map_index];
      // next page has test ?
      if(next_page.has_test) {

        let i = this.tests.findIndex(el => el.page == next_page.page);
        this.activeTest = this.tests[i]
        this.test_key++;

      } else {
        this.activeTest = null;
      }

      
    }, 

    prevPage() { 
      if(this.map_index == 0  || !this.pdf_loaded) return 0;

      this.map_index--;

      let prev_page = this.page_map[this.map_index];
      // prev_page has test ?
      if(prev_page.has_test) {

        let i = this.tests.findIndex(el => el.page == prev_page.page);
        this.activeTest = this.tests[i]
        this.test_key++;

      } else {
        this.activeTest = null;
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