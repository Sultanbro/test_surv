<template>
    <div class="popup__content awards-profile mt-5">
        <div class="spinner-container" v-if="Object.keys(nominations).length === 0">
            <div class="throbber-loader"></div>
        </div>
        <b-tabs>
            <b-tab no-body title="Номинации" v-if="Object.keys(nominations).length !== 0">
                <b-tabs class="inside-tabs">
                    <b-tab no-body title="Мои номинации">
                        <div class="certificates__title">
                            Сертификатов: <span class="current">{{nominations.my.length}}</span> из <span
                                class="all">{{nominations.available.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" v-for="(award, key) in nominations.my"
                                  :key="award.id + key">
                                <div class="certificates__item" @click="modalShow(award)">
                                    <img :src="award.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                    <b-tab no-body title="Доступные номинации">
                        <div class="certificates__title">
                            Сертификатов: <span
                                class="current">{{nominations.available.length - nominations.my.length}}</span> из <span
                                class="all">{{nominations.available.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" v-for="(award, key) in nominations.available"
                                  :key="award.id + key">
                                <div class="certificates__item" @click="modalShow(award)">
                                    <img :src="award.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                    <b-tab no-body title="Номинации других участников">
                        <BRow>
                            <BCol cols="12" md="4" lg="3" v-for="(award, key) in nominations.other"
                                  :key="award.id + key">
                                <div class="certificates__item" @click="modalShow(award)">
                                    <img :src="award.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                </b-tabs>
            </b-tab>
            <!--
            <b-tab no-body title="Сертификаты">
                <b-tabs class="inside-tabs">
                    <b-tab no-body title="Мои номинации">
                        <div class="certificates__title">
                            Сертификатов: <span class="current">{{myAwards.length}}</span> из <span
                                class="all">{{availableAwards.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" v-for="(award, key) in myAwards"
                                  :key="award.id + key">
                                <div class="certificates__item" @click="modalShow(award)">
                                    <img :src="award.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                    <b-tab no-body title="Доступные номинации">
                        <div class="certificates__title">
                            Сертификатов: <span class="current">{{myAwards.length}}</span> из <span
                                class="all">{{availableAwards.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" v-for="(award, key) in myAwards"
                                  :key="award.id + key">
                                <div class="certificates__item" @click="modalShow(award)">
                                    <img :src="award.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                    <b-tab no-body title="Номинации других участников">
                        <div class="certificates__title">
                            Сертификатов: <span class="current">{{myAwards.length}}</span> из <span
                                class="all">{{availableAwards.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" v-for="(award, key) in myAwards"
                                  :key="award.id + key">
                                <div class="certificates__item" @click="modalShow(award)">
                                    <img :src="award.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                </b-tabs>
            </b-tab>
            -->
            <b-tab no-body title="Лучшие сотрудники">
                <b-tabs class="inside-tabs">
                    <b-tab no-body title="По отделу">
                        <BRow>
                            <BCol cols="12" md="4">
                                <div class="nominations__item">
                                    <div class="nominations__item-title">
                                        Процент успешных
                                        исходящих продаж
                                    </div>
                                    <div class="nominations__item-avatar gift-1">
                                        <img src="images/dist/profile-avatar.png" alt="profile avatar">
                                    </div>
                                    <div class="nominations__item-name">
                                        Елена Линовская
                                    </div>
                                    <div class="nominations__item-subtext">
                                        колл-центр
                                    </div>
                                    <div class="nominations__item-value">
                                        15 500 ₸
                                    </div>
                                    <div class="nominations__item-wrapper">
                                        <div class="nominations__item-row">
                                            <p>KPI</p>
                                            <p>1300 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>БОНУСЫ</p>
                                            <p>200 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>ОКЛАД</p>
                                            <p>14000 ₸</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                            <BCol cols="12" md="4">
                                <div class="nominations__item green">
                                    <div class="nominations__item-title">
                                        Процент успешных
                                        исходящих продаж
                                    </div>
                                    <div class="nominations__item-avatar gift-2">
                                        <img src="images/dist/profile-avatar.png" alt="profile avatar">
                                    </div>
                                    <div class="nominations__item-name">
                                        Елена Линовская
                                    </div>
                                    <div class="nominations__item-subtext">
                                        колл-центр
                                    </div>
                                    <div class="nominations__item-value">
                                        15 500 ₸
                                    </div>
                                    <div class="nominations__item-wrapper">
                                        <div class="nominations__item-row">
                                            <p>KPI</p>
                                            <p>1300 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>БОНУСЫ</p>
                                            <p>200 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>ОКЛАД</p>
                                            <p>14000 ₸</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                            <BCol cols="12" md="4">
                                <div class="nominations__item">
                                    <div class="nominations__item-title">
                                        Процент успешных
                                        исходящих продаж
                                    </div>
                                    <div class="nominations__item-avatar gift-3">
                                        <img src="images/dist/profile-avatar.png" alt="profile avatar">
                                    </div>
                                    <div class="nominations__item-name">
                                        Елена Линовская
                                    </div>
                                    <div class="nominations__item-subtext">
                                        колл-центр
                                    </div>
                                    <div class="nominations__item-value">
                                        15 500 ₸
                                    </div>
                                    <div class="nominations__item-wrapper">
                                        <div class="nominations__item-row">
                                            <p>KPI</p>
                                            <p>1300 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>БОНУСЫ</p>
                                            <p>200 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>ОКЛАД</p>
                                            <p>14000 ₸</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                    <b-tab no-body title="По должностям">
                        <BRow>
                            <BCol cols="12" md="4">
                                <div class="nominations__item">
                                    <div class="nominations__item-title">
                                        Процент успешных
                                        исходящих продаж
                                    </div>
                                    <div class="nominations__item-avatar gift-1">
                                        <img src="images/dist/profile-avatar.png" alt="profile avatar">
                                    </div>
                                    <div class="nominations__item-name">
                                        Елена Линовская
                                    </div>
                                    <div class="nominations__item-subtext">
                                        колл-центр
                                    </div>
                                    <div class="nominations__item-value">
                                        15 500 ₸
                                    </div>
                                    <div class="nominations__item-wrapper">
                                        <div class="nominations__item-row">
                                            <p>KPI</p>
                                            <p>1300 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>БОНУСЫ</p>
                                            <p>200 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>ОКЛАД</p>
                                            <p>14000 ₸</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                            <BCol cols="12" md="4">
                                <div class="nominations__item green">
                                    <div class="nominations__item-title">
                                        Процент успешных
                                        исходящих продаж
                                    </div>
                                    <div class="nominations__item-avatar gift-2">
                                        <img src="images/dist/profile-avatar.png" alt="profile avatar">
                                    </div>
                                    <div class="nominations__item-name">
                                        Елена Линовская
                                    </div>
                                    <div class="nominations__item-subtext">
                                        колл-центр
                                    </div>
                                    <div class="nominations__item-value">
                                        15 500 ₸
                                    </div>
                                    <div class="nominations__item-wrapper">
                                        <div class="nominations__item-row">
                                            <p>KPI</p>
                                            <p>1300 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>БОНУСЫ</p>
                                            <p>200 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>ОКЛАД</p>
                                            <p>14000 ₸</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                            <BCol cols="12" md="4">
                                <div class="nominations__item">
                                    <div class="nominations__item-title">
                                        Процент успешных
                                        исходящих продаж
                                    </div>
                                    <div class="nominations__item-avatar gift-3">
                                        <img src="images/dist/profile-avatar.png" alt="profile avatar">
                                    </div>
                                    <div class="nominations__item-name">
                                        Елена Линовская
                                    </div>
                                    <div class="nominations__item-subtext">
                                        колл-центр
                                    </div>
                                    <div class="nominations__item-value">
                                        15 500 ₸
                                    </div>
                                    <div class="nominations__item-wrapper">
                                        <div class="nominations__item-row">
                                            <p>KPI</p>
                                            <p>1300 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>БОНУСЫ</p>
                                            <p>200 ₸</p>
                                        </div>
                                        <div class="nominations__item-row">
                                            <p>ОКЛАД</p>
                                            <p>14000 ₸</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                </b-tabs>
            </b-tab>
        </b-tabs>
        <b-modal v-if="itemModal" centered size="xl" v-model="modal" :title="itemModal.name">
            <img :src="itemModal.path" alt="" class="img-fluid">
            <template #modal-footer>
                <BButton variant="primary" @click="modal = !modal">Закрыть</BButton>
            </template>
        </b-modal>

    </div>
</template>


<script>
    import { SpinnerPlugin } from 'bootstrap-vue';

    export default {
        name: "PopupNominations",
        components: {SpinnerPlugin},
        props: {},
        data: function () {
            return {
                modal: false,
                itemModal: null,
                fields: [],
                awardTypes: null,
                nominations: {},
                certificates: {},
                favorites: {}
            };
        },
        mounted() {
            let loader = this.$loading.show();
            this.axios
                .get('/awards/type?award_type_id=1')
                .then(response => {
                    this.nominations = response.data.data;
                    loader.hide();
                })
                .catch(error => {
                    console.log(error);
                    loader.hide();
                });

            // this.axios
            //     .get('/awards/type?award_type_id=2')
            //     .then(response => {
            //         const data = response.data.data;
            //         for (let i = 0; i < data.length; i++) {
            //             this.certificates.push(data[i]);
            //         }
            //         loader.hide();
            //     })
            //     .catch(error => {
            //         console.log(error);
            //         loader.hide();
            //     });
            //
            this.axios
                .get('/awards/type?award_type_id=3')
                .then(response => {
                    console.log(response);
                    this.favorites = response.data.data;
                    loader.hide();
                })
                .catch(error => {
                    console.log(error);
                    loader.hide();
                });
        },
        methods: {
            modalShow(item) {
                this.itemModal = item;
                this.modal = !this.modal;
            },
        }
    };
</script>


<style lang="scss">
    .awards-profile {
        position: relative;
        .tabs {
            .nav-tabs {
                border-top: 1px solid #dee2e6;

                .nav-item {
                    .nav-link {
                        font-size: 2.1rem;
                        border-bottom: none;
                        margin-top: 0.1rem;
                        line-height: 2em;
                        color: #8D8D8D;
                        font-family: "Open Sans", sans-serif;
                        font-weight: 600;
                        transition: color 0.3s;
                        padding: 1.5rem 0 0 0;
                        cursor: pointer;
                        margin-right: 40px;
                        background-color: transparent;
                        border-top: 4px solid transparent;

                        &:hover {
                            border-color: transparent;
                            color: #ED2353;
                        }

                        &.active {
                            border-top: 4px solid #ED2353;
                            color: #ED2353;
                        }
                    }
                }
            }
        }

        .inside-tabs {
            .tab-pane {
                margin-top: 30px;
                overflow-x: hidden;
                padding-bottom: 30px;
            }

            .nav-tabs {
                border: none;
                margin-top: 2rem;

                .nav-item {
                    border-radius: 0 !important;
                }

                .nav-link {
                    margin-top: 0 !important;
                    line-height: 1.3 !important;
                    border: none !important;
                    color: #FFFFFF !important;
                    font-size: 1.4rem !important;
                    padding: 2rem !important;
                    background: #AEBEE0 !important;
                    transition: 0.3s !important;
                    border-radius: 0 !important;
                    margin-right: 30px !important;

                    &:hover {
                        background: #ED2353 !important;
                    }

                    &.active {
                        background: #ED2353 !important;
                    }
                }
            }
        }

        .nominations__wrapper {
            width: 100%;
            display: block;
        }

        .certificates__item {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: rgb(50 50 93 / 25%) 0px 13px 27px -5px, rgb(0 0 0 / 30%) 0px 8px 16px -8px;

            img {
                width: auto;
                height: 200px;
                object-fit: cover;
            }
        }
        .certificates__title{
            margin-top: 0;
        }

        @keyframes throbber-loader {
            0% {
                background: #dde2e7;
            }
            10% {
                background: #6b9dc8;
            }
            40% {
                background: #dde2e7;
            }
        }

        .spinner-container{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .throbber-loader {
            animation: throbber-loader 2000ms 300ms infinite ease-out;
            background: #dde2e7;
            display: inline-block;
            position: relative;
            text-indent: -9999px;
            width: 0.9em;
            height: 1.5em;
            margin: 0 1.6em;
            z-index: 22;
        }
        .throbber-loader:before, .throbber-loader:after {
            background: #dde2e7;
            content: '\x200B';
            display: inline-block;
            width: 0.9em;
            height: 1.5em;
            position: absolute;
            top: 0;
        }
        .throbber-loader:before {
            -moz-animation: throbber-loader 2000ms 150ms infinite ease-out;
            -webkit-animation: throbber-loader 2000ms 150ms infinite ease-out;
            animation: throbber-loader 2000ms 150ms infinite ease-out;
            left: -1.6em;
        }
        .throbber-loader:after {
            -moz-animation: throbber-loader 2000ms 450ms infinite ease-out;
            -webkit-animation: throbber-loader 2000ms 450ms infinite ease-out;
            animation: throbber-loader 2000ms 450ms infinite ease-out;
            right: -1.6em;
        }
    }

</style>