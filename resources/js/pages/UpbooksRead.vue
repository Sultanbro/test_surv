<template>
  <div class="upbook-read-page">
    <div class="controls">
      <div @click="$emit('back')" class="btn">
        <i class="fa fa-arrow-left"></i>
        Назад
      </div>
      <div>
        <p class="text-center">
          <b>{{ page }} / {{ pageCount }}</b>
        </p>
        <input
          v-model.number="page"
          class="form-control text-center mt-2 mb-2"
          type="number"
          min="1"
          disabled
          :max="pageCount"
        />
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
        <button class="btn rounded mr-1 p-1" @click="zoomIn">
          <i class="fa fa-search-plus"></i>
        </button>
        <button class="btn rounded mr-1 p-1" @click="zoom = 0">
          <i class="fa fa-bars"></i>
        </button> 
        <button class="btn rounded p-1" @click="zoomOut">
          <i class="fa fa-search-minus"></i>
        </button>
      </div>

      <div class="mt-3">
        <img :src="activeBook.img == '' ? '/images/book_cover.jpg' : activeBook.img" class="w-full" />
      </div>  
      <div v-if="isLoading">
        <p class="text-center mt-3">
          <b>Загружается...</b>
        </p>
      </div>

      
    </div>

    <div
      class="pdf"
       v-if="mode == 'edit' || (mode == 'read' && status == 'reading')"
      :class="{
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
      <!-- <pdf
        :src="activeBook.link"
        v-if="activeBook !== null"
        class="plugin"
        :page="page"
        @keyup.right="keypress"
        @link-clicked="linkClicked($event)"
        @num-pages="pageCount = $event"
      ></pdf> -->

      <vue-pdf-embed 
        v-if="activeBook !== null"
        :source="activeBook.link"
        ref="pdfRef"
        :page="page"
        class="plugin"
        @rendered="handleDocumentRender"
      />
    </div>

    <div class="test" style="width:1000px;max-width: 100%;" v-if="status == 'testing'">
      <questions :questions="questions" :id="0" type="book" mode="read" @continueRead="continueRead"/>
    </div>
  </div>
</template>

<script>
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
export default {
  name: "UpbooksRead",
  components: {
    VuePdfEmbed
  },
  props: ["activeBook", "mode"],
  data() {
    return {
      page: 1,
      activeCategory: null,
      pageCount: 0,
      zoom: 800,
      questions: [],
      isLoading:true,
      tests: [],
      checkpoint: 1,
      status: 'reading'
    };
  },
  created() {
    if (this.mode == "read") {
      this.checkpoint = this.pageCount
      let loader = this.$loading.show();

      axios
        .post("/admin/upbooks/tests/get", {
          id: this.activeBook.id,
        })
        .then((response) => {
          this.tests = response.data.tests;
          this.getCheckpoint()
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    }
  },
  mounted() {
    document.addEventListener("keyup", this.keyup);
  },
  methods: {

    continueRead() {
      this.status = 'reading';
      this.page++;
    },

    getCheckpoint() { // read
      if(this.tests.length > 0) {
        this.checkpoint = this.tests[0].page
        this.questions = this.tests[0].questions
      }
    },

    keyup(e) {
      if (e.keyCode == 37) {
        this.prevPage();
      }
      if (e.keyCode == 39) {
        this.nextPage();
      }
    },

    linkClicked(e) {
      e.preventDefault();
    },

    nextPage() {
      if (this.page == this.pageCount) return 0;
      if(this.status == 'testing') {
        return;
      }
      
      if(this.checkpoint == this.page)  {
        this.status = 'testing'; 
      } else {
        this.page++;
      }
      
    }, 

    prevPage() {
      if (this.page == 1) return 0;
      if(this.status == 'testing') {
        return;
      }
      if(this.checkpoint == this.page)  {
        this.status = 'testing'; 
      } else {
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
