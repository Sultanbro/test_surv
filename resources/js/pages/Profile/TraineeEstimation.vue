<template>
<div class="trainee block _anim _anim-no-hide content" id="trainee" >
    <div class="trainee__title title">
        Оценка стажеров
    </div>
    <div class="trainee__subtitle subtitle">
        Подробная информация об оценке стажерами вашего обучения
    </div>
    <div class="trainee__table ">
        <div class="trainee__table-name">
            Каримов Адиль Kaspi
        </div>
        <div class="tabs custom-scroll">
            <div class="trainee__tabs tabs__wrapper">
                <div  class="trainee__tab tab__item is-active" onclick="switchTabs(this)"  data-index="1">16.05.2022</div>
                <div  class="trainee__tab tab__item" onclick="switchTabs(this)"  data-index="2">17.05.2022</div>
            </div>
            <select class="select-css trainee-select mobile-select">
                <option value="1">16.05.2022</option>
                <option value="2">17.05.2022</option>
            </select>

            <div class="tab__content">
                <div class="trainee__content tab__content-item is-active"  data-content="1">
                    <table>
                        <thead>
                            <tr>
                                <th>Суть работы</th>
                                <th>График работы</th>
                                <th>Заработная плата</th>
                                <th>Оценка тренера</th>
                                <th>Рекомендации</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">Не понятно (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
                            <td><div class="colored" data-color="#DDE9FF"><div class="text">Еще не знаю (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
                            <td><div class="colored" data-color="#DDE9FF"><div class="text">Не знаю (<span>4</span>) <div class="semibold value">66.6%</div></div></div></td>
                            <td class="td-star" rowspan="4"><div class="trainee-star">
                                <div class="trainee__star-value">
                                    9.4 (<span>9</span>)
                                </div>
                                <div class="trainee__star-wrapper">
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                </div>
                            </div></td>
                            <td rowspan="4"><div class="trainee__review">
                                <p>Все устраивает</p>
                                <p>Пока что все понятно</p>
                                <p>Даже не знаю</p>
                                <p>Все понятно, обьяснения супер. Айджан</p>
                                <p>молодец, отвечает на все вопросы.</p>
                                <p>Мне понравилось</p>
                            </div>
                                </td>

                        </tr>
                        <tr>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">Сет маркетинг (<span>0</span>)<div class="semibold value">0%</div></div></div></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">6 - 1 (<span>5</span>)<div class="semibold value">55.6%</div></div></div></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 100 - 200 (<span>0</span>)<div class="semibold value">55.6%</div></div></div></td>

                        </tr>
                        <tr>
                            <td ><div class="colored" data-color="#B7E100"><div class="text">Обраб. вх и исх <div class="semibold value">66.7%</div></div></div></td>
                            <td ><div class="colored" data-color="#FF82AF"><div class="text">Свободный (<span>1</span>)<div class="semibold value">11.1%</div></div></div></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">оплата за каждый час (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>

                        </tr>
                        <tr>
                            <td class="none"></td>
                            <td class="none"></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 80 + 40 бон(<span>2</span>)<div class="semibold value">22.2%</div></div></div></td>

                        </tr>



                        </tbody>



                    </table>
                    <table class="invite">
                        <thead>
                            <tr>
                                <th class="first-td">Приглашенные</th>
                                <th>1 день</th>
                                <th>2 день</th>
                                <th>3 день</th>
                                <th>4 день</th>
                                <th>5 день</th>
                                <th>6 день</th>
                                <th>7 день</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="first-td">64</td>
                                <td>16</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="trainee__content tab__content-item "  data-content="2">
                    <table>
                        <thead>
                        <tr>
                            <th>Суть работы</th>
                            <th>График работы</th>
                            <th>Заработная плата</th>
                            <th>Оценка тренера</th>
                            <th>Рекомендации</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">Не понятно (<span>8</span>)<div class="semibold value">88.8%</div></div></div></td>
                            <td><div class="colored" data-color="#DDE9FF"><div class="text">Еще не знаю (<span>6</span>)<div class="semibold value">66.6%</div></div></div></td>
                            <td><div class="colored" data-color="#DDE9FF"><div class="text">Не знаю (<span>4</span>) <div class="semibold value">44.4%</div></div></div></td>
                            <td class="td-star" rowspan="4"><div class="trainee-star">
                                <div class="trainee__star-value">
                                    7.2 (<span>7</span>)
                                </div>
                                <div class="trainee__star-wrapper">
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                    <div class="star__item"><img src="images/dist/trainee-star.svg" alt="star icon"><img class="star-done-img"
                                                                                                                            src="images/dist/trainee-star-done.svg" alt="star done icon"></div>
                                </div>
                            </div></td>
                            <td rowspan="4"><div class="trainee__review">
                                <p>Все устраивает</p>
                                <p>Пока что все понятно</p>
                                <p>Даже не знаю</p>
                                <p>Все понятно, обьяснения супер. Айджан</p>
                                <p>молодец, отвечает на все вопросы.</p>
                                <p>Мне понравилось</p>
                            </div>
                            </td>

                        </tr>
                        <tr>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">Сет маркетинг (<span>1</span>)<div class="semibold value">11.1%</div></div></div></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">6 - 1 (<span>9</span>)<div class="semibold value">99%</div></div></div></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 100 - 200 (<span>7</span>)<div class="semibold value">77.6%</div></div></div></td>

                        </tr>
                        <tr>
                            <td ><div class="colored" data-color="#B7E100"><div class="text">Обраб. вх и исх <div class="semibold value">44.4%</div></div></div></td>
                            <td ><div class="colored" data-color="#FF82AF"><div class="text">Свободный (<span>3</span>)<div class="semibold value">33.3%</div></div></div></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">оплата за каждый час (<span>8</span>)<div class="semibold value">88.8%</div></div></div></td>

                        </tr>
                        <tr>
                            <td class="none"></td>
                            <td class="none"></td>
                            <td ><div class="colored" data-color="#DDE9FF"><div class="text">фикс 80 + 40 бон(<span>4</span>)<div class="semibold value">44.4%</div></div></div></td>

                        </tr>



                        </tbody>



                    </table>
                    <table class="invite">
                        <thead>
                        <tr>
                            <th class="first-td">Приглашенные</th>
                            <th>1 день</th>
                            <th>2 день</th>
                            <th>3 день</th>
                            <th>4 день</th>
                            <th>5 день</th>
                            <th>6 день</th>
                            <th>7 день</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="first-td">64</td>
                            <td>16</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "TraineeEstimation", 
    props: {},
    data: function () {
        return {
            data: {}, 
        };
    },
    created(){
        this.fetchData()
    },
    methods: {
        fetchData() {
            let loader = this.$loading.show();
            
            axios.post("/profile/trainee-report", {})
                .then((response) => {
                    this.data = response.data
                    console.log(this.data)
                    loader.hide();
                }).catch(e => {
                    console.log(e)
                    loader.hide();
                });
        },
    }
};
</script>