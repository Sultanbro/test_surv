<template>
    <div class="header__profile _anim _anim-no-hide custom-scroll-y" :class="{
        'v-loading': loading,
        hidden: hide,
        '_active': inViewport
    }">
        <div class="profile__content">
            <div class="profile__col">
                <div
                    class="profile__logo logo-img-wrap"
                    v-if="logo.image || canChangeLogo"
                >
                    <template v-if="canChangeLogo && !logo.image">
                        <img src="/images/dist/logo-download.svg" alt="logo download">
                        Загрузить логотип
                    </template>
                    <img v-if="logo.image" :src="logo.image" class="logo-img">
                    <template v-if="canChangeLogo">
                        <input
                            type="file"
                            class="hidden-file-input"
                            id="inputGroupFile04"
                            aria-describedby="inputGroupFileAddon04"
                            ref="file"
                            accept="image/*"
                            v-on:change="handleFileUpload()"
                        >
                        <label class="hidden-file-label" for="inputGroupFile04"/>
                    </template>
                </div>
                <start-day-btn
                    v-if="showButton"
                    :status="buttonStatus"
                    :workdayStatus="workdayStatus"
                    @clickStart="startDay"
                />
                <div class="profile__balance">
                    Текущий баланс
                    <p>{{ balance }} <span>{{ currency }}</span></p>
                </div>
                <b-modal :headerClass="{'border-radius':'1rem'}" id="modal-sm" title="Загрузить логотип" size="lg"  hide-footer >
                    <form class="logo-upload-modal">
                        <cropper
                            ref="mycrop"
                            class="cropper"
                            :src="imagePreview"
                            :stencil-props="{ aspectRatio: 32/10 }"
                            @change="change"
                        />
                        <div class="clearfix mt-3">
                            <a href="#"
                                class="add-btn float-right"
                                v-on:click.prevent="uploadLogo()"
                            >
                                <p>Добавить</p>
                            </a>
                        </div>
                    </form>
                </b-modal>
            </div>

            <div class="profile__col">
                <profile-info :data="userInfo"></profile-info>
                <!--
                <div class="profile__point profile-box">
                    <div class="profile__title">Цель на сегодня</div>
                    <div class="profile__point-wrapper profile-slick">
                        <div class="profile__point-start">
                            <p>Начало рабочего дня</p>
                            <p>10 : 20 am</p>
                            <div class="profile__point-time">
                                <div class="profile__time-row">
                                    <p class="blue time spent">1 h 23 m</p>
                                    <p>пройдено</p>
                                </div>
                                <div class="profile__time-row">
                                    <p class="time left">0 h 34 m</p>
                                    <p>осталось</p>
                                </div>
                            </div>
                        </div>
                        <div class="profile__progressbar">
                            <svg class="progress-ring" width="80" height="80">
                                <circle stroke="#fff" stroke-width="8" cx="40" cy="40" r="30" fill="#8FAF00"/>
                                <circle class="progress-ring__circle" stroke="rgba(96,142,233,0.5)" stroke-width="4" cx="40" cy="40" r="36" fill="transparent"/>
                            </svg>
                            <div class="profile__progressbar-number">
                                <span>87</span>%
                            </div>
                        </div>
                    </div>
                    <img src="images/dist/close.svg" alt="close icon" class="point-close">
                </div> -->
            </div>

            <div class="profile__col">
                <div class="profile__active profile-box">
                    <div class="profile__title _slicked">График активности</div>
                    <div class="tabs__include profile-slick" style="display: none;">
                        <div class="tab__content-include">
                            <div class="tab__content-item-include is-active"  data-content="1">
                                <img src="/images/dist/schedule.png" alt="schedule image">
                            </div>
                            <div class="tab__content-item-include"  data-content="2">
                                <img src="/images/dist/profile-active.png" alt="schedule image">
                            </div>
                            <div class="tab__content-item-include"  data-content="3">
                                <img src="/images/dist/schedule.png" alt="schedule image">
                            </div>
                        </div>
                        <div class="tabs__wrapper">
                            <div  class="tab__item-include is-active" onclick="switchTabsInclude(this)"  data-index="1">День</div>
                            <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="2">месяц</div>
                            <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="3">год</div>
                        </div>
                    </div>
                    <img src="/images/dist/close.svg" alt="close icon" class="point-close">
                </div>
            </div>

            <!-- <a href="#" class="profile__more" v-if="$laravel.userId == 5 || $laravel.userId == 18" @click="addWidget">
                <img src="/images/dist/logo-download.svg" alt="more download">
            </a> -->
        </div>



        <!-- Corp book page when day has started -->
        <b-modal
            v-model="showCorpBookPage"
            title="Н"
            size="xl"
            class="modalle"
            hide-footer
            hide-header
            no-close-on-backdrop
        >
            <div class="corpbook" v-if="corp_book_page !== undefined && corp_book_page !== null">
                <div class="inner">
                    <h5 class="text-center aet mb-3">Ознакомьтесь с одной из страниц Вашей базы знаний</h5>
                    <h3 class="text-center">{{ corp_book_page.title }}</h3>

                    <div v-html="corp_book_page.text"/>

                    <button
                        @click="testBook"
                        :disabled="!!bookTimer"
                        id="readCorpBook"
                        class="button-blue m-auto mt-5"
                    >
                        <span v-if="bookTimer" class="timer">{{ bookTimer }}</span>
                        <span v-else class="text">Я прочитал</span>
                    </button>
                </div>
            </div>
        </b-modal>

        <b-modal
            v-model="isBookTest"
            size="xl"
            class="modalle"
            hide-footer
            hide-header
            no-close-on-backdrop
        >
            <questions
                v-if="corp_book_page"
                :course_item_id="0"
                :questions="corp_book_page.questions"
                :pass_grade="corp_book_page.questions.length"
                type="kb"
                :dont-repat="true"
                @passed="hideBook"
                @failed="repeatBook"
            />
        </b-modal>
    </div>
</template>

<script>
import Vue from 'vue'
import axios from 'axios'
import { bus } from '../../bus'

export default {
    name: 'ProfileSidebar',
    props: {},
    data: function () {
        return {
            fields: [],
            balance: 0,
            currency: 'KZT',
            file: '',
            showPreview: false,
            imagePreview: '',
            logo:{
                image: '',
                canvas: null
            },
            loading: false,
            hide: false,
            userInfo: {},
            inViewport: false,
            buttonStatus: 'init',
            workdayStatus: 'stopped',
            // corp book
            corp_book_page: null,
            showCorpBookPage: false,
            bookTimer: 0,
            bookTimerInterval: 0,
            isBookTest: false,
            isRoot: false,
            isProfile: false,
        };
    },
    computed: {
        canChangeLogo(){
            return this.$laravel.is_admin == 1 || this.$laravel.is_admin == 18
        },
        showButton(){
            if(this.$can('ucalls_view') && !this.$laravel.is_admin) return false
            return this.workdayStatus === 'started' || (this.userInfo.user && this.userInfo.user.user_type === 'remote')
        }
    },
    mounted(){
        if(!this.isRoot && !this.isProfile){
            this.hide = true
            document.body.classList.add('no-profile')
        }
        const scrollObserver = new IntersectionObserver(() => {
            this.inViewport = true
        })
        scrollObserver.observe(this.$el)
    },
    created(){
        this.isRoot = window.location.pathname === '/'
        this.isProfile = window.location.pathname === '/profile'

        bus.$data.profileSidebar = Vue.observable({
            userInfo: {},
            balance: 0,
            currency: 'KZT',
            buttonStatus: this.buttonStatus,
            workdayStatus: this.workdayStatus,
        })
        bus.$on('MobileProfileSidebarStartDay', this.startDay)

        window.addEventListener('blur', this.pauseBookTimer)
        window.addEventListener('focus', this.unpauseBookTimer)

        this.getLogo()
        this.fetchUserInfo()
        this.fetchTTStatus()
    },
    methods: {
        getLogo(){
            axios.post('/settings/get', {
                type: 'company'
            }).then(response => {
                const settings = response.data.settings;
                if (settings.logo){
                    this.logo.image =  settings.logo;
                }
                console.log(settings)
                console.log(settings.logo)
                console.log(this.logo.image)
            }).catch((error) => {
                this.$toast(error);
            });
        },

        /**
         * Загрузить лого открыть модальный окно
         */
        modalLogo() {
            this.$bvModal.show('modal-sm');
        },

        modalHideLogo() {
            this.$bvModal.hide('modal-sm');
        },

        change({ coordinates, canvas }) {
            this.logo.canvas = canvas;
            console.log(coordinates, canvas)
        },

        /**
         * Загрузить лого
         */
        uploadLogo(){
            this.logo.canvas.toBlob(blob => {
                this.loading = true

                const formData = new FormData();
                formData.append('file', blob);
                formData.append('type', 'company');

                axios.post('/settings/save',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                )
                .then((response) => {
                    console.log('success')
                    console.log(response)
                    this.logo.image = response.data.logo;
                    this.loading = false
                })
                .catch((response)=> {
                    console.log('failure!!');
                    console.log(response)
                    this.loading = false
                });
            })
            this.modalHideLogo();
            this.imagePreview = '';
            this.showPreview = false;
        },

        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            let reader = new FileReader();

            reader.addEventListener('load', function () {
                this.showPreview = true;
                this.imagePreview = reader.result;
                this.modalLogo();
            }.bind(this), false);

            if( this.file ){
                if ( /\.(jpe?g|png|gif)$/i.test( this.file.name ) ) {
                    reader.readAsDataURL( this.file );
                }
            }
            else{
                this.$toast.error('Неподдерживаемый формат: ' + this.file.name.split('.').reverse()[0])
            }
        },

        /**
         * Добавить виджет
         */
        addWidget() {
            alert('Вы не можете, потому что не работают Виджеты');
        },

        /**
         * callback c startDayBtn
         * возвращает текущий баланс
         */
        currentBalance(balance) {
            this.balance = balance.sum
            this.currency = balance.currency
        },

        fetchUserInfo(){
            this.loading = true

            axios.get('/profile/personal-info').then(response => {
                this.userInfo = response.data
                bus.$data.profileSidebar.userInfo = this.userInfo
                this.loading = false
            }).catch((e) => console.log(e))
        },

        /**
         * Узнать текущий статус
         * Начат или завершен рабочий день
         */
        fetchTTStatus(){
            this.buttonStatus = 'loading'

            axios.post('/timetracking/status', {}).then((response) => {
                this.workdayStatus = response.data.status

                if(this.workdayStatus === 'started' && response.data.corp_book) {
                    this.corp_book_page = response.data.corp_book
                    this.showCorpBookPage = this.corp_book_page !== null
                    this.bookCounter()
                }

                this.currentBalance(response.data.balance)
                bus.$data.profileSidebar.balance = this.balance
                bus.$data.profileSidebar.currency = this.currency

                this.buttonStatus = 'init'
            })
            .catch((error) => {
                this.buttonStatus = 'error'
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
            if(this.buttonStatus === 'loading') return

            this.buttonStatus = 'loading'

            axios.post('/timetracking/starttracking', this.getParams()).then((response) => {

                this.buttonStatus = 'init'

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
                    this.workdayStatus = 'stopped';
                    this.$toast.info('День завершен');
                }
            })
            .catch((error) => {
                this.buttonStatus = 'error'
                console.log(error);
            });
        },

        /**
         *  Time to read book before "I have read" btn became active
         */
        bookCounter() {
            if(!this.isRoot && !this.isProfile) return
            this.bookTimer = 60
            this.unpauseBookTimer()
        },

        pauseBookTimer(){
            clearInterval(this.bookTimerInterval)
        },

        unpauseBookTimer(){
            if(this.bookTimer === 0) return
            this.bookTimerInterval = setInterval(() => {
                --this.bookTimer
                if(this.bookTimer === 0) {
                    clearInterval(this.bookTimerInterval)
                }
            }, 1000)
        },

        /**
         * Set read corp book page
         */
        hideBook() {
            axios.post('/corp_book/set-read/', {})
                .then(res => this.showCorpBookPage = false)
                .catch(error => console.log(error))
        },

        repeatBook(){
            this.showCorpBookPage = true
            this.isBookTest = false
            this.bookCounter()
        },

        testBook(){
            if(this.bookTimer) return
            this.isBookTest = true
            this.showCorpBookPage = false
        },
    }
};
</script>

<style lang="scss">
.header__profile{
    margin: 0 auto;
    padding: 2rem 2rem 1rem;
    overflow-y:auto;
    overflow-x:hidden;
    font-family: "Open Sans",sans-serif;
    background: #F6F7FC;
    .tab__item-include{
        font-family: "Inter",sans-serif;
        font-size:1.2rem;
        font-weight: 600;
        padding: .7rem 1.2rem;
        min-height: 2rem;
        border-radius:1.5rem;
        text-transform: lowercase;
        &.is-active,
        &:hover{
            background: #608EE9;
        }
    }
    .tabs__wrapper{
        gap:1rem;
        border:none;
        justify-content: center;
    }
}
.profile__logo {
    font-size: 21px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 24px 0;
    min-height: 80px;
    width: 100%;
    margin-bottom: 2rem;
    position: relative;
    background-image: url("data:image/svg+xml;utf8,<svg width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><rect width='98%' height='98%' x='1%' y='1%' ry='12%' rx='12%' style='fill: none; stroke: %23D9D9D9; stroke-width: 1; stroke-dasharray: 8'/></svg>");
}
.profile__balance{
    width: 100%;
    max-width: 28rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-radius:1rem;
    background: #608EE9;
    min-height:8rem;
    color:#fff;
    font-size:1.4rem;
    text-transform: uppercase;
    padding-top: .9rem;
    p{
        font-size:3.5rem;
        span{
            font-size:2.8rem;
        }
    }

}

.profile__more{
  background-image: url("data:image/svg+xml;utf8,<svg width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><rect width='98%' height='98%' x='1%' y='1%' ry='12%' rx='12%' style='fill: none; stroke: %23D9D9D9; stroke-width: 1; stroke-dasharray: 8'/></svg>");
  min-height: 8rem;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 2rem;
}

.logo-img-wrap{
    width: 100%;
    max-width: 30rem;
}

.logo-img{
    display: block;
    width: 100%;
    border-radius: 1rem;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    object-fit: cover;
}

.hidden-file-wrapper{
    position: relative;
}
.hidden-file-input{
    display: none;
}
.hidden-file-label{
    position: absolute;
    z-index: 1;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
}

.logo-upload-modal{
    background: #fff;
    border-radius: 1rem;
    padding: 1.7rem 1rem;
}
.add-btn{
    background: #8FAF00;
    color: #fff;
    text-align: center;
    padding: 1rem;
    border-radius: 1rem;
}
.modal-title{
    color: #62788B;
}
.custom-file-label::after{
    color:inherit;
}
.modal-content{
    border-radius: 0.5rem;
}
@media(max-width:1359px){
    .header__profile{
        border-radius: 1.5rem;
    }
    .profile__content{
        display: flex;
        flex-flow: row nowrap;
        padding: 2rem 7rem 1rem;
        justify-content: space-evenly;
    }
    .profile__about{
        margin-top: 0;
    }
    .profile__col{
        flex: 0 1 28rem;
    }
}
@media(max-width:1200px){
    .profile__content{
        gap: 2rem;
        padding: 2rem 2rem 1rem;
    }
    .profile__col{
        flex: 0 1 50%;
    }
}
@media(max-width:900px){
    .profile__content{
        flex-flow: row wrap;
    }
    .profile__col{
        flex: 0 0 28rem;
    }
}
@media(max-width:440px){
    .header__profile{
        top: 0rem;
        height: 100%;
        width: 100%;
        left: 0;
        display: flex;
        padding: 2rem;
        z-index: 1001;
    }
    .profile__content{
        padding: 6rem 0 2rem;
        display: flex;
        flex-direction: column;
        max-width: 28rem;
        margin: auto;
        max-height: 100%;
        position:relative;
    }
    .profile__col{
        flex: 1 1 auto;
    }
}
@media(min-width:1360px){
    .header__profile {
        position:fixed;
        height: 100%;
        left: 7rem;
        top: 0;
        width: 32rem;
        z-index: 1001;
        min-height: 100vh;
        transform:translateX(-20px);
        transition: all .7s;
        &::-webkit-scrollbar {
            width: 6px; /* высота для горизонтального скролла */
        }
        &.opened{
            opacity:1;
            visibility: visible;
            transform:translateX(0);
        }
        opacity: 1;
        -webkit-transform: translateX(0);
        -ms-transform: translateX(0);
        transform: translateX(0);
        visibility: visible;
    }
    .header__profile._active{
        .profile__logo{
            opacity:1;
            visibility: visible;
            transform:translateY(0);
        }
        .profile__balance{
            opacity:1;
            visibility: visible;
            transform:translateY(0);
        }
        .profile__about{
            opacity:1;
            visibility: visible;
            transform:translateY(0);
        }
        .profile-select{
            opacity:1;
            visibility: visible;
            transform:translateY(0);
        }
        .profile__point{
            opacity:1;
            visibility: visible;
            transform:translateY(0);
        }
        .profile__active{
            opacity:1;
            visibility: visible;
            transform:translateY(0);
        }
    }
    .profile__logo{
        opacity:0;
        visibility: hidden;
        transform:translateY(10px);
        transition:1s;
    }


    .profile__balance{
        transition: all 1s .4s;
        opacity:0;
        visibility: hidden;
        transform:translateY(10px);
    }
}
// @media(max-width:1909px){
//   .header__profile{}
// }
</style>