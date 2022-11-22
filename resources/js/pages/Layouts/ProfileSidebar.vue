<template>
    <div class="header__profile _anim _anim-no-hide custom-scroll-y" :class="{
        'v-loading': loading,
        hidden: hide
    }">
        <div class="profile__content">
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
                v-if="userInfo.user && userInfo.user.user_type === 'remote'"
                @currentBalance="currentBalance"
            ></start-day-btn>

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

            <!-- <a href="#" class="profile__more" v-if="$laravel.userId == 5 || $laravel.userId == 18" @click="addWidget">
                <img src="/images/dist/logo-download.svg" alt="more download">
            </a> -->
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ProfileSidebar',
    props: {},
    computed: {
        canChangeLogo(){
            return this.$laravel.is_admin == 1 || this.$laravel.is_admin == 18
        }
    },
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
            userInfo: {}
        };
    },
    mounted(){
        this.getLogo();
        const isRoot = window.location.pathname === '/'
        const isProfile = window.location.pathname === '/profile'
        if(!isRoot && !isProfile){
            this.hide = true
            document.body.classList.add('no-profile')
        }
    },
    methods: {
        getLogo(){
            const _this = this;
            axios.post('/settings/get', {
                type: 'company'
            }).then((response) => {
                const settings = response.data.settings;
                if (settings.logo){
                    _this.logo.image =  settings.logo;
                }
                console.log(settings)
                console.log(settings.logo)
                console.log(_this.logo.image)
            }).catch((error) => {
                alert(error);
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
                this.loading = false
            }).catch((e) => console.log(e))
        }
    }
};
</script>

<style>
.logo-img{
    object-fit: cover;
    display: block;
    width: 100%;
    height: auto;
    border-radius: 12%/18%;
}
.logo-img-wrap{
    width: 100%;
    max-width: 30rem;
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
</style>