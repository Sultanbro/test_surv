<template>
  <div>
    <!-- Start day btn -->
    <a href="#"
      class="profile__button"
      @click="startDay"
      :class="{
        'profile__button_error': status === 'error',
        'profile__button_started': workdayStatus === 'started',
        'profile__button_loading': status === 'loading'
      }"
    >
      <svg :class="{'visible': status === 'loading'}" class="profile__loader" version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
        <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
          s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
          c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
        <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
          C22.32,8.481,24.301,9.057,26.013,10.047z">
          <animateTransform
            attributeType="xml"
            attributeName="transform"
            type="rotate"
            from="0 20 20"
            to="360 20 20"
            dur="0.5s"
            repeatCount="indefinite"/>
        </path>
      </svg>

      <template v-if="status === 'error'">
        <p class="profile__button-text">Ошибка сети</p>
      </template>

      <template v-else>
        <p v-if="workdayStatus === 'stopped'" class="profile__button-text">Начать рабочий день</p>
        <p v-if="workdayStatus === 'started'" class="profile__button-text">Завершить рабочий день</p>
      </template>

    </a>

    <!-- Corp book page when day has started -->
    <b-modal v-model="showCorpBookPage" title="Н" size="xl" class="modalle" hide-footer hide-header no-close-on-backdrop>
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
  props: {},
  data() {
    return {
      data: {},
      status: 'init',
      workdayStatus: 'stopped',

      // corp book
      corp_book_page: null,
      showCorpBookPage: false,
      isLoading: false
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
      this.status = 'loading'

      axios.post('/timetracking/status', {})
        .then((response) => {

            this.workdayStatus = response.data.status

            if(this.workdayStatus === 'started' && response.data.corp_book.has) {
              this.corp_book_page = response.data.corp_book.page
              this.showCorpBookPage = this.corp_book_page !== null
              this.bookCounter()
            }

            this.$emit('currentBalance', response.data.balance)

            this.status = 'init'
        })
        .catch((error) => {
            this.status = 'error'
            console.log('StartDayBtn:', error)
        })
    },

    /**
     * private
     *
     * Получить параметры для начатия и завершения дня
     */
    getParams() {
      let params = {start: moment().format('HH:mm:ss')};
      if(this.workdayStatus === 'started') params = {stop: moment().format('HH:mm:ss')};

      return params;
    },

    /**
     * Начать или завершить день
     */
    startDay() {
      if(this.status === 'loading') return

      this.status = 'loading'

      axios.post('/timetracking/starttracking', this.getParams())
        .then((response) => {

          this.status = 'init'

          if (response.data.error) {
            this.$toast.info(response.data.error.message);
            return;
          }

          if(response.data.status === 'started') {

            this.workdayStatus = 'started';

            if(response.data.corp_book.has) {
              this.corp_book_page = response.data.corp_book.page
              this.showCorpBookPage = this.corp_book_page != null;
              this.bookCounter();
            }
            this.$toast.info('День начат');
          }

          if(response.data.status === 'stopped' || response.data.status === '') { // stopped
            this.status = 'stopped';
            this.$toast.info('День завершен');
          }



        })
        .catch((error) => {
          this.status = 'error'
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
      axios.post('/corp_book/set-read/', {})
        .then((res) => this.showCorpBookPage = false)
        .catch((error) => console.log(error))
    },
  }
}
</script>

<style lang="scss" scoped>
.profile__button{
  display: none;
  background: #8FAF00;
  color:#fff;
  text-align: center;
  padding: 2rem 1.5rem;
  width: 100%;
  max-width: 28rem;
  border-radius:1rem;
  margin-bottom: 1.5rem;
  align-items:center;
  text-transform: uppercase;
  transition: background .3s 0s;
  opacity:0;
  visibility: hidden;

  &:hover{
    background: #88a402;
  }
}
.profile__button_started,
.profile__button_error{
  background: #fc5757;
  &:hover{
    background: darken(#fc5757, 10%);
  }
}
.profile__button_loading{
  background-color: #555;
  cursor: default;

  .profile__button-text::before {
    display: none;
  }
}

.profile__button-text{
  padding-left: 3rem;
  font-size:1.4rem;
  font-weight: 600;
  position:relative;
  white-space: nowrap;

  &::before{
    content:"";
    position:absolute;
    top: 0;
    left:0;
    width: 2rem;
    height: 2rem;
    background: url("../../../../public/images/dist/start-icon.svg") center no-repeat;
    background-size: cover;
  }
}

.profile__loader {
  opacity: 0;
  &.visible {
    opacity: 1;
  }
}
.aet {
  background: #608EE9 !important;
}
.corpbook {
  font-size: 14px;
}

@media(min-width: 900px){
  .profile__button{
    display: flex;
    opacity:1;
    visibility: visible;
  }
}

@media(min-width:1360px){
  .header__profile._active{
    .profile__button{
      opacity:1;
      visibility: visible;
      transform: translateY(0);
    }
  }
  .profile__button{
    transition: all 1s .2s, background .3s 0s;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
  }
}
</style>
