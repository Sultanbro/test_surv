<template>
<div class="popup__content  mt-5">
    <div class="tabs ">
        <div class="popup__filter">
            <div class="trainee__tabs tabs__wrapper">
                <div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">Номинации</div>
                <div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">Сертификаты</div>
                <div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="3">Лучшие сотрудники</div>
            </div>


            <!--							<div class="nominations__arrows">-->
            <!--								<a id="nominationBack" href="#" class="arrow__item"><img src="images/dist/arrow-back.svg" alt="arrow icon"></a>-->
            <!--								<a id="nominationNext" href="#" class="arrow__item"><img src="images/dist/arrow-next.svg" alt=" arrow icon"></a>-->
            <!--							</div>-->

        </div>

        <div class="tab__content">
            <div class="nominations__content custom-scroll-y tab__content-item is-active"  data-content="1">
                <div class="nominations__wrapper">
                    <!--									<div class="nominations__left">-->
                    <!--										<div class="nominations__half">-->
                    <!--											<div class="nominations__half-title">Количество исходящих-->
                    <!--												вызовов</div>-->
                    <!--											<div class="nominations__value">597</div>-->
                    <!--											<div class="nominations__half-title">-->
                    <!--												План: 470-->
                    <!--											</div>-->
                    <!--										</div>-->
                    <!--										<div class="nominations__half">-->
                    <!--											<div class="nominations__half-title">Количество исходящих-->
                    <!--												вызовов</div>-->
                    <!--											<div class="nominations__value">597</div>-->
                    <!--											<div class="nominations__half-title">-->
                    <!--												План: 470-->
                    <!--											</div>-->
                    <!--										</div>-->
                    <!--									</div>-->

                </div>
            </div>
            <div class="nominations__content custom-scroll-y tab__content-item"  data-content="2">
                <div class="tabs__include">
                    <div class="tabs__wrapper wrapper-include">
                        <div  class="tab__item-include is-active" onclick="switchTabsInclude(this)"  data-index="1">Мои номинации</div>
                        <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="2">Доступные номинации</div>
                        <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="3">Номинации других участников</div>
                    </div>
                    <div class="tab__content-include">
                        <div class="tab__content-item-include is-active"  data-content="1">
                            <div class="certificates__title">
                                Сертификатов: <span class="current">10</span> из <span class="all">17</span>
                            </div>

                            <div class="certificates__wrapper">
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
                                <div class="certificates__item">
                                    <img src="images/dist/certificate-1.png" alt="certificate image">
                                </div>
                            </div>

                        </div>
                        <div class="tab__content-item-include"  data-content="2">
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
                        <div class="tab__content-item-include"  data-content="3">
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
            <div class="nominations__content custom-scroll-y tab__content-item"  data-content="3">
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
    </div>

</div>
</template>

<script>
export default {
    name: "PopupNominations", 
    props: {},
    data: function () {
        return {
            fields: [],
            awardTypes: null,
            awards: []
        };
    },
    mounted() {
        this.axios
            .get('/awards/type?award_type_id=1')
            .then(response => {
                console.log(response);
                // const data = response.data.data.awards.my;
                // for (let i = 0; i < data.length; i++){
                //     this.awards.push(data[i]);
                // }
            })
            .catch(error => {
                console.log(error);
            })
    },
    methods: {
        fetchData() {

        }
    }
};
</script>