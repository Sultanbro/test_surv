<template>
  <div>
    <!-- Start day btn -->
    <a href="#"
      class="profile__button"
      @click="startDay"
      :class="{
        'red': status == 'started'
      }"
    >
        <p v-if="status != 'started'">Начать рабочий день</p>
        <p v-else>Завершить рабочий день</p>
    </a>

    <!-- Corp book page when day has started --> 
    <b-modal v-model="showCorpBookPage"  title="Н" size="xl" class="modalle" hide-footer hide-header no-close-on-backdrop>
        <div class="corpbook" v-if="corp_book_page !== undefined && corp_book_page !== null">
          <div class="inner">
              <h5 class="text-center aet mb-3">Ознакомьтесь с одной из страниц Вашей корпоративной книги</h5>
              <h3 class="text-center">{{ corp_book_page.title }}</h3>

              <div v-html="corp_book_page.text"></div>

              <button href="#profitInfo" class="button-blue m-auto mt-5" id="readCorpBook" @click="hideBook" disabled>
                <span class="text">Я прочитал</span>
                <span class="timer"></span>
              </button>
          </div>      
      </div>
    </b-modal>
  
  </div>

</template>

<script>
export default {

  name: 'StartDayBtn',
  props: {

  },

  data() {
    return {
      data: {},
      status: 'stopped',
      // corp book
      corp_book_page: null,
      showCorpBookPage: false
    }
  },

  created() {
    this.workStatus()
  },

  methods: {
    /**
     * Узнать текущий статус 
     * Начат или завершен рабочий день
     */
    workStatus() {
        axios.post("/timetracking/status", {})
          .then((response) => {

              this.status = response.data.status

              if(response.data.status == 'started' && response.data.corp_book.has) {
                this.corp_book_page   = response.data.corp_book.page
                this.showCorpBookPage = this.corp_book_page != null; 
                this.bookCounter();
              } 

              this.$emit('currentBalance', response.data.balance)
          })
          .catch((error) => {
              console.log(error);
          });
    },

    /**
     * private
     * 
     * Получить параметры для начатия и завершения дня
     */
    getParams() {
      let params = {start: moment().format("HH:mm:ss")};
      if(this.status == 'started') params = {stop: moment().format("HH:mm:ss")};

      return params;
    },

    /**
     * Начать или завершить день
     */
    startDay() {

      axios.post("/timetracking/starttracking", this.getParams())
        .then((response) => {

          if (response.data.error) {
            this.$toast.error(response.data.error.message);
            return;
          }

          if(response.data.status == 'started') {

            this.status = 'started';

            if(response.data.corp_book.has) {
              this.corp_book_page = response.data.corp_book.page
              this.showCorpBookPage = this.corp_book_page != null; 
              this.bookCounter();
            }
          
            this.$toast.info('День начат');
          } 

          if(response.data.status == 'stopped') { // stopped
            this.status = response.data.status;
            this.$toast.info('День завершен');
          }

        })
        .catch((error) => {
          console.log(error);
        });
    },

    /**
     *  Time to read book before "I have read" btn became active
     */
    bookCounter() {
        let seconds = 60;
        let interv = setInterval(() => {
            seconds--;
            VJQuery('#readCorpBook .timer').text(seconds);
            if(seconds == 0) {
              VJQuery('#readCorpBook .timer').text('');
                clearInterval(interv);
            }
        }, 1000);

        setTimeout(() => {
          VJQuery('#readCorpBook').prop('disabled', false);
        }, seconds * 1000);
    },
    
    /**
     * Set read corp book page
     */
    hideBook() {
      axios.post("/corp_book/set-read/", {})
        .then((res) => this.showCorpBookPage = false)
        .catch((error) => console.log(error))
    },
  }
}
</script>

<style lang="scss" scoped>
.aet {
  background: #608EE9 !important;
}
.corpbook {
  font-size: 14px;
}
</style>
