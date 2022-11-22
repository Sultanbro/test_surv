<template>
    <div class="popup__content  mt-5">
        <div class="tabs ">
            <div class="popup__filter">
                <div class="trainee__tabs tabs__wrapper">
                    <div class="trainee__tab tab__item is-active" onclick="switchTabs(this)" data-index="1">Номинации
                    </div>
                    <div class="trainee__tab tab__item" onclick="switchTabs(this)" data-index="2">Сертификаты</div>
                    <div class="trainee__tab tab__item" onclick="switchTabs(this)" data-index="3">Лучшие сотрудники
                    </div>
                </div>


                <!--							<div class="nominations__arrows">-->
                <!--								<a id="nominationBack" href="#" class="arrow__item"><img src="images/dist/arrow-back.svg" alt="arrow icon"></a>-->
                <!--								<a id="nominationNext" href="#" class="arrow__item"><img src="images/dist/arrow-next.svg" alt=" arrow icon"></a>-->
                <!--							</div>-->

            </div>

            <div class="tab__content">
                <div class="nominations__content custom-scroll-y tab__content-item is-active" data-content="1">
                    <div class="nominations__wrapper">
                        <div class="tabs__include">
                            <div class="tabs__wrapper wrapper-include">
                                <div class="tab__item-include is-active" onclick="switchTabsInclude(this)"
                                     data-index="1">Мои номинации
                                </div>
                                <div class="tab__item-include" onclick="switchTabsInclude(this)" data-index="2">
                                    Доступные номинации
                                </div>
                                <div class="tab__item-include" onclick="switchTabsInclude(this)" data-index="3">
                                    Номинации других участников
                                </div>
                            </div>
                            <div class="tab__content-include">
                                <div class="tab__content-item-include is-active" data-content="1">
                                    <div class="certificates__title">
                                        Сертификатов: <span class="current">{{myAwards.length}}</span> из <span
                                            class="all">{{availableAwards.length}}</span>
                                    </div>

                                    <BRow>
                                        <BCol cols="12" md="4" lg="3" v-for="award in myAwards"
                                              :key="award.created_at">
                                            <div class="certificates__item" @click="modalShow(award)">
                                                <img :src="award.path" alt="certificate image">
                                            </div>
                                        </BCol>
                                    </BRow>

                                </div>
                                <div class="tab__content-item-include" data-content="2">

                                    <div class="certificates__title">
                                        Сертификатов: <span class="current">{{availableAwards.length - myAwards.length}}</span> из <span
                                            class="all">{{availableAwards.length}}</span>
                                    </div>

                                    <BRow>
                                        <BCol cols="12" md="4" lg="3" v-for="award in availableAwards"
                                              :key="award.created_at">
                                            <div class="certificates__item" @click="modalShow(award)">
                                                <img :src="award.path" alt="certificate image">
                                            </div>
                                        </BCol>
                                    </BRow>
                                </div>
                                <div class="tab__content-item-include" data-content="3">
<!--                                    <div class="certificates__title">-->
<!--                                        Сертификатов: <span class="current">3</span> из <span class="all">80</span>-->
<!--                                    </div>-->
                                    <BRow>
                                        <BCol cols="12" md="4" lg="3" v-for="award in otherAwards"
                                              :key="award.created_at">
                                            <div class="certificates__item" @click="modalShow(award)">
                                                <img :src="award.path" alt="certificate image">
                                            </div>
                                        </BCol>
                                    </BRow>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="nominations__content custom-scroll-y tab__content-item" data-content="2">
                    <div class="tabs__include">
                        <div class="tabs__wrapper wrapper-include">
                            <div class="tab__item-include is-active" onclick="switchTabsInclude(this)" data-index="1">
                                Мои номинации
                            </div>
                            <div class="tab__item-include" onclick="switchTabsInclude(this)" data-index="2">Доступные
                                номинации
                            </div>
                            <div class="tab__item-include" onclick="switchTabsInclude(this)" data-index="3">Номинации
                                других участников
                            </div>
                        </div>
                        <div class="tab__content-include">
                            <div class="tab__content-item-include is-active" data-content="1">
                                <div class="certificates__title">
                                    Сертификатов: <span class="current">{{myAwards.length}}</span> из <span class="all">17</span>
                                </div>

                                <div class="certificates__wrapper" v-for="award in myAwards" :key="award.created_at">
                                    <div class="certificates__item">
                                        <img :src="award.path" alt="certificate image">
                                    </div>
                                </div>

                            </div>
                            <div class="tab__content-item-include" data-content="2">
                                <div class="certificates__title">
                                    Сертификатов: <span class="current">0</span> из <span class="all">80</span>
                                </div>

                                <div class="certificates__wrapper">
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-3.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-2.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-1.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-3.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-1.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-2.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-3.png" alt="certificate image">
                                    </div>
                                </div>
                            </div>
                            <div class="tab__content-item-include" data-content="3">
                                <div class="certificates__title">
                                    Сертификатов: <span class="current">3</span> из <span class="all">80</span>
                                </div>

                                <div class="certificates__wrapper">
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-2.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-1.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-2.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-3.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-1.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-2.png" alt="certificate image">
                                    </div>
                                    <div class="certificates__item">
                                        <img src="images/dist/certificate-3.png" alt="certificate image">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="nominations__content custom-scroll-y tab__content-item" data-content="3">
                    <div class="tabs__include">
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
                    </div>
                </div>
            </div>
            <b-modal v-if="itemModal" centered size="xl" v-model="modal" :title="itemModal.name">
                <img :src="itemModal.path" alt="" class="img-fluid">
                <template #modal-footer>
                    <BButton variant="primary" @click="modal = !modal">Закрыть</BButton>
                </template>
            </b-modal>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PopupNominations",
        props: {},
        data: function () {
            return {
                modal: false,
                itemModal: null,
                fields: [],
                awardTypes: null,
                myAwards: [],
                availableAwards: [],
                availableAwardsCurrent: [],
                otherAwards: []
            };
        },
        mounted() {
            let loader = this.$loading.show();
            this.axios
                .get('/awards/type?award_type_id=1')
                .then(response => {
                    const data = response.data.data;
                    for (let i = 0; i < data.my.length; i++) {
                        this.myAwards.push(data.my[i]);
                    }
                    for (let i = 0; i < data.available.length; i++) {
                        this.availableAwards.push(data.available[i]);
                    }
                    for (let i = 0; i < data.other.length; i++) {
                        this.otherAwards.push(data.other[i]);
                    }
                    this.availableAwards.forEach((item, index, array) => {

                    });

                    // let i = 0;
                    // // надо исключить id того и того и вернуть остаток
                    // while (i < this.availableAwards.length) {
                    //     if(this.myAwards[i]){
                    //         const myId = this.myAwards[i].id;
                    //         for(let j = 0; j < this.availableAwards.length; j++){
                    //             const awId = this.availableAwards[j].id;
                    //             if(awId !== myId){
                    //                 this.availableAwardsCurrent.push(this.availableAwards[j]);
                    //                 j = 9999999;
                    //                 i++;
                    //             }
                    //         }
                    //     } else{
                    //         i = 9999999;
                    //     }
                    // }
                    // console.log(this.availableAwardsCurrent);
                    loader.hide();
                })
                .catch(error => {
                    console.log(error);
                    loader.hide();
                });

        },
        methods: {
            modalShow(item){
                this.itemModal = item;
                this.modal = !this.modal;
            },
        }
    };
</script>


<style lang="scss" scoped>
    .nominations__wrapper{
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

</style>