<template>
  <div>
    
    <div class="upbooks-page" v-if="vuePage == 'upbooks'">
      <div class="second-menu">
          <h1 class="page-title">Книги</h1>

          <p>Первые</p>
          <p>Вторые</p>
          <p>Третьи</p>

          <button>Добавить категорию</button>
      </div>
      <div class="cont">
          <p>Тут  книги</p>
          <div class="d-flex flex-wrap">
            <div class="box" @click="go()"></div>
            <div class="box" @click="go()"></div>
            <div class="box" @click="go()"></div>
            <div class="box" @click="go()"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
          </div>
      </div>
    </div>
    <div class="upbook-read-page" v-if="vuePage == 'upbook'">
    
      <div class="controls">
        <div @click="back">
          <i class="fa fa-arrow-left"></i>
          Назад
        </div>
        <div>
          <p class="text-center">
            <b>{{ page }} / {{ pageCount }}</b>
          </p>
          <input v-model.number="page" class="form-control text-center mt-2 mb-2" type="number" min="1" :max="pageCount"/>
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
          <button class="btn rounded mr-2" @click="zoomIn">
            <i class="fa fa-search-plus"></i>
          </button>
          <button class="btn rounded" @click="zoomOut">
            <i class="fa fa-search-minus"></i>
          </button>
        </div>

      </div>
  
      <div class="pdf" :class="{
          'w50': zoom == 50,
          'w60': zoom == 60,
          'w70': zoom == 70,
          'w80': zoom == 80,
          'w90': zoom == 90,
          'full': zoom == 100,
        }">
          <pdf :src="currentBookUrl"
          class="plugin"
          :page="page"
          @keyup.right="keypress"
          @link-clicked="linkClicked($event)"
          @num-pages="pageCount = $event"></pdf>
      </div>


    </div>
  </div>
  
</template>

<script>
import pdf from 'vue-pdf'
export default {
  components: {
    pdf
  },
  props: [],
  data() {
    return {
      currentBookUrl: '/files/asdadsa.pdf',
      page: 3,
      pageCount: 0,
      zoom: 100,
      vuePage: 'upbooks',
    }
  },
  created() {

  },
  mounted () {
    document.addEventListener("keyup", this.keyup);
  },
  methods: {
    keyup(e) {
      if(e.keyCode == 37) {
        this.prevPage();
      }
      if(e.keyCode == 39) {
        this.nextPage();
      }
    },

    fetchData() {
      let loader = this.$loading.show();

      axios.post('/timetracking/6u84', {}).then(response => {

        loader.hide()
      }).catch(error => {
        loader.hide()
        alert(error)
      });
    },

    linkClicked(e) {
      e.preventDefault();
    },

    nextPage() {
      if(this.page == this.pageCount) return 0;
      this.page++;
    },

    prevPage() {
      if(this.page == 1) return 0;
      this.page--;
    },

    zoomIn() {
      if(this.zoom == 100) return 0;
      this.zoom += 10;
    },

    zoomOut() {
      if(this.zoom == 50) return 0;
      this.zoom -= 10;
    },

    go() {
      this.vuePage = 'upbook';
    },

    back() {
      this.vuePage = 'upbooks';
    }

  }
}
</script>


