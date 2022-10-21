<template>
<div class="courses__wrapper block _anim _anim-no-hide">
    <div class="courses__content">
        <div class="courses__title">
            Ваши курсы
        </div>
        <div class="courses__content__wrapper">

            <div class="courses__item current"
                v-for="(course, index) in data"
                :key="index"
                @click="selectCourse(index)" 
            >
                <img src="images/dist/courses-image.png" alt="" class="courses__image">

                <div class="courses__progress">
                    <div class="courses__line"></div>
                </div>
                <!-- Линия зависит от процентов в span-->
                <div class="courses__percent">
                    Пройдено: <span>32%</span>
                </div>
                <a href="#" class="courses__button">
                    <span>Продолжить курс</span>
                </a>
            </div>

        </div>

    </div>

    <div class="profit__info" v-if="activeCourse != null">
        <div class="profit__info-title">
            Информация о курсе
        </div>
        <div class="profit__info-back" @click="back">
            Назад
        </div>
        <div class="profit__info-back-mobile">

        </div>
        <div class="profit__info__inner">
            <div class="profit__info__item">
                <img src="images/dist/courses-image.png" alt="info image" class="profit__info-image">
                <div class="profit__info-about">
                    <div class="profit__info-text">
                        Описание курса или его содержания, которое может быть оформлено в несколько строчек для удобства чтения и восприятия информации о курсе.
                        Здесь так же могут быть данные об авторах курса и другая дополнительная информация. Описание курса или его содержания, которое может быть
                        оформлено в несколько строчек для удобства чтения и восприятия информации о курсе. Здесь так же могут быть данные об авторах курса и другая
                        дополнительная информация. Описание курса или его содержания, которое может быть оформлено в несколько строчек для удобства чтения и
                        восприятия информации о курсе. Здесь так же могут быть данные об авторах курса и другая дополнительная информация
                    </div>
                    <div class="profit__info-text mobile">
                        Описание курса или его содержания, которое может быть оформлено в несколько строчек для удобства чтения и восприятия информации о курсе.
                        Здесь так же могут быть данные об авторах курса и другая дополнительная информация.
                    </div>
                    <div class="profit__info__wrapper">
                        <div class="info__wrapper-item done">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>01 / 10</p>
                            </a>
                            <div class="info__item-value">100%</div>
                        </div>
                        <div class="info__wrapper-item ">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>02 / 10</p>
                            </a>
                            <div class="info__item-value">14%</div>
                        </div>
                        <div class="info__wrapper-item ">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>03 / 10</p>
                            </a>
                            <div class="info__item-value">7%</div>
                        </div>
                        <div class="info__wrapper-item ">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>04 / 10</p>
                            </a>
                            <div class="info__item-value">4%</div>
                        </div>
                        <div class="info__wrapper-item ">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>05 / 10</p>
                            </a>
                            <div class="info__item-value">0%</div>
                        </div>
                        <div class="info__wrapper-item">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>06 / 10</p>
                            </a>
                            <div class="info__item-value">0%</div>
                        </div>
                        <div class="info__wrapper-item">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>07 / 10</p>
                            </a>
                            <div class="info__item-value">0%</div>
                        </div>
                        <div class="info__wrapper-item">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>08 / 10</p>
                            </a>
                            <div class="info__item-value">0%</div>
                        </div>
                        <div class="info__wrapper-item">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>09 / 10</p>
                            </a>
                            <div class="info__item-value">0%</div>
                        </div>
                        <div class="info__wrapper-item">
                            <a href='#' class="info__item-box">
                                <img src="images/dist/info-circle.png" alt="play image">
                                <p>10 / 10</p>
                            </a>
                            <div class="info__item-value">0%</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="info__tip">
            Курс доступен до 24.01
        </div>
    </div>

</div>
</template>

<script>
export default {
    name: "Courses", 
    props: {},
    data: function () {
        return {
            data: [], 
            activeCourse: null
        };
    },
    created() {
        this.fetchData()
    },

    methods: {
        /**
         * Загрузка данных 
         */
        fetchData() {
            let loader = this.$loading.show();

            axios.post('/profile/courses').then(response => {
                this.data = response.data
                loader.hide()
            }).catch((e) => console.log(e))
        },

        /**
         * select active course info
         */
        selectCourse(index) {
            this.activeCourse = this.data[index]
        },

        /**
         * back to all courses
         */
        back() {
            this.activeCourse = null;
        }
    }
};
</script>