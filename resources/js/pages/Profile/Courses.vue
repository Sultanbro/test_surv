<template>
<div class="courses__wrapper block _anim _anim-no-hide">
    <div class="courses__content" v-if="activeCourse === null">
        <div class="courses__title">
            Ваши курсы
        </div>
        <div class="courses__content__wrapper">

            <div class="courses__item current"
                v-for="(course, index) in data"
                :key="index"
            >
                <img v-if="course.img !== null && course.img !== ''" :src="course.img" alt="курс" class="courses__image" @click="selectCourse(index)" >
                <img v-else src="/images/dist/courses-image.png" alt="" class="courses__image" @click="selectCourse(index)" >

                <div class="courses__progress">
                    <div class="courses__line"></div>
                </div>
                <!-- Линия зависит от процентов в span-->
                <div class="courses__percent">
                    Пройдено: <span>99%</span>
                </div>
                <a :href="'/my-courses?id=' + course.id" class="courses__button">
                    <span>Продолжить курс</span>
                </a>
            </div>

        </div>

    </div>

    <div class="profit__info" v-else>
        <div class="profit__info-title">
            Информация о курсе: {{ activeCourse.name }}
        </div>
        <div class="profit__info-back" @click="back">
            Назад
        </div>
        <div class="profit__info-back-mobile">

        </div>
        <div class="profit__info__inner">
            <div class="profit__info__item">
                <img v-if="activeCourse.img !== null && activeCourse.img !== ''" :src="activeCourse.img" alt="info image" class="profit__info-image">
                <img v-else src="/images/dist/courses-image.png" alt="info image" class="profit__info-image">

                <div class="profit__info-about">
                    <div class="profit__info-text" v-html="activeCourse.text"></div>
                    <div class="profit__info-text mobile" v-html="activeCourse.text"></div>
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
            console.log('clicked ' + index)
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