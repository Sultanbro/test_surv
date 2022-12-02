<template>
    <div class="popup__content awards-profile mt-5">
        <div class="spinner-container" v-if="loading">
            <div class="throbber-loader"></div>
        </div>
        <b-tabs ref="tabis" v-model="tabIndex"
                v-if="Object.keys(nominations).length > 0 || Object.keys(certificates).length > 0 || Object.keys(accrual).length > 0">
            <div class="prev-next">
                <span class="prev" @click="tabIndex--"><i class="fa fa-chevron-left"></i></span>
                <span class="next" @click="tabIndex++"><i class="fa fa-chevron-right"></i></span>
            </div>
            <b-tab no-body :title="award.name" v-for="(award, index) in nominations" :key="award.id">
                <b-tabs class="inside-tabs">
                    <b-tab no-body title="Мои номинации" active>
                        <div class="certificates__title">
                            Сертификатов: <span class="current">{{award.my.length}}</span> из <span
                                class="all">{{award.available.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" class="mb-5" v-for="(item, key) in award.my"
                                  :key="item.id + key">
                                <div class="certificates__item" @click="modalShow(item)">
                                    <img :src="item.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                    <b-tab no-body title="Доступные номинации">
                        <div class="certificates__title">
                            Сертификатов: <span
                                class="current">{{award.available.length - award.my.length}}</span> из <span
                                class="all">{{award.available.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" class="mb-5" v-for="(item, key) in award.available"
                                  :key="item.id + key">
                                <div class="certificates__item" @click="modalShow(item)">
                                    <img :src="item.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                    <b-tab no-body title="Номинации других участников">
                        <BRow>
                            <BCol cols="12" md="4" lg="3" class="mb-5" v-for="(item, key) in award.other"
                                  :key="item.id + key">
                                <div class="certificates__item" @click="modalShow(item)">
                                    <img :src="item.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                </b-tabs>
            </b-tab>

            <b-tab no-body :title="award.name" v-for="(award, index) in certificates" :key="award.id">
                <b-tabs class="inside-tabs">
                    <b-tab no-body title="Мои номинации" active>
                        <div class="certificates__title">
                            Сертификатов: <span class="current">{{award.my.length}}</span> из <span
                                class="all">{{award.available.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" class="mb-5" v-for="(item, key) in award.my"
                                  :key="item.id + key">
                                <div class="certificates__item" @click="modalShow(item)">
                                    <img :src="item.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                    <b-tab no-body title="Доступные номинации">
                        <div class="certificates__title">
                            Сертификатов: <span
                                class="current">{{award.available.length - award.my.length}}</span> из <span
                                class="all">{{award.available.length}}</span>
                        </div>

                        <BRow>
                            <BCol cols="12" md="4" lg="3" class="mb-5" v-for="(item, key) in award.available"
                                  :key="item.id + key">
                                <div class="certificates__item" @click="modalShow(item)">
                                    <img :src="item.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>
                    <b-tab no-body title="Номинации других участников">
                        <BRow>
                            <BCol cols="12" md="4" lg="3" class="mb-5" v-for="(item, key) in award.other"
                                  :key="item.id + key">
                                <div class="certificates__item" @click="modalShow(item)">
                                    <img :src="item.path" alt="certificate image">
                                </div>
                            </BCol>
                        </BRow>
                    </b-tab>

                </b-tabs>
            </b-tab>
            <b-tab class="accrual-tab" no-body :title="award.name" active v-for="(award, index) in accrual"
                   :key="index">
                <BRow>
                    <BCol cols="12" md="4" v-for="(item, index) in award.top" :key="item.id">
                        <div class="nominations__item" :class="{green: index === 1}">
                            <div class="nominations__item-title">
                                Процент успешных
                                исходящих продаж
                            </div>
                            <div class="nominations__item-avatar" :class="'gift-' + (index + 1)">
                                <img :src="item.path" alt="profile avatar" v-if="item.path.length > 10">
                                <img src="images/avatar.png" alt="profile avatar" v-else>
                            </div>
                            <div class="nominations__item-name">
                                {{item.name}} {{item.last_name}}
                            </div>
                            <div class="nominations__item-subtext">
                                {{item.position}}
                            </div>
                            <div class="nominations__item-value">
                                {{item.total | splitNumber(item.total)}} ₸
                            </div>
                            <div class="nominations__item-wrapper">
                                <div class="nominations__item-row">
                                    <p>KPI</p>
                                    <p> {{item.kpi | splitNumber(item.kpi)}} ₸</p>
                                </div>
                                <div class="nominations__item-row">
                                    <p>БОНУСЫ</p>
                                    <p>{{item.bonuses | splitNumber(item.bonuses)}} ₸</p>
                                </div>
                                <div class="nominations__item-row">
                                    <p>ОКЛАД</p>
                                    <p>{{item.earnings | splitNumber(item.earnings)}} ₸</p>
                                </div>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </b-tab>
        </b-tabs>
        <div v-else>
            <h4 class="not-awards">У Вас пока нет ни одной награды</h4>
        </div>
        <b-modal v-if="itemModal" centered size="xl" v-model="modal" :title="itemModal.name">
            <img :src="itemModal.path" alt="" class="img-fluid">
            <template #modal-footer>
                <BButton variant="primary" @click="modal = !modal">Закрыть</BButton>
            </template>
        </b-modal>

    </div>
</template>


<script>
    import {SpinnerPlugin} from 'bootstrap-vue';

    export default {
        name: "PopupNominations",
        components: {SpinnerPlugin},
        props: {},
        data: function () {
            return {
                tabIndex: 0,
                loading: true,
                modal: false,
                itemModal: null,
                fields: [],
                awardTypes: null,
                nominations: {},
                certificates: {},
                accrual: {},
            };
        },
        filters: {
            splitNumber: function (val) {
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            }
        },
        watch: {
            tabIndex(val) {
                let buttons = this.$refs.tabis.$refs.buttons;
                buttons[val].$refs.link.$el.scrollIntoView({inline: "end", behavior: "smooth"});
            }
        },
        async mounted() {
            await this.axios
                .get('/awards/type?key=nomination')
                .then(response => {
                    if (response.data.data) {
                        this.nominations = response.data.data;
                    }
                })
                .catch(error => {
                    console.log(error);
                });

            await this.axios
                .get('/awards/type?key=certificate')
                .then(response => {
                    this.certificates = response.data.data;
                })
                .catch(error => {
                    console.log(error);
                });
            await this.axios
                .get('/awards/type?key=accrual')
                .then(response => {
                    this.accrual = response.data.data;
                })
                .catch(error => {
                    console.log(error);
                });
            this.loading = false;
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

        .prev-next {
            position: absolute;
            top: 0;
            right: 0;
            height: 63px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            background-color: #fff;
            width: 120px;
            span {
                width: 40px;
                height: 40px;
                border-radius: 50px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #ED2353;
                cursor: pointer;

                i {
                    font-size: 18px;
                    color: #ED2353;
                }

                &:hover {
                    background-color: #ED2353;

                    i {
                        color: #fff;
                    }
                }
            }

            .next {
                margin-left: 10px;
            }
        }

        .not-awards {
            font-size: 3.1rem;
            text-transform: uppercase;
            color: rgba(64, 64, 64, 0.4);
        }

        .nominations__item-avatar {
            img {
                width: 150px;
                height: 150px;
                border-radius: 50%;
            }
        }

        .tabs {
            .accrual-tab {
                margin-top: 30px;
                overflow-x: hidden;
                padding-bottom: 30px;
            }

            .nav-tabs {
                border-top: 1px solid #dee2e6;
                flex-wrap: nowrap;
                white-space: nowrap;
                overflow: hidden;
                margin-right: 120px;
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

        .certificates__title {
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

        .spinner-container {
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