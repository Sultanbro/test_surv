<template>
<div
    id="courses__anchor"
    class="courses__wrapper block _anim _anim-no-hide mt-4"
    :class="{'hidden': data.length == 0}"
>
    <div class="courses__content" :class="{'hidden': activeCourse !== null}">
        <div class="courses__title">
            Ваши курсы
        </div>
        <div class="courses__content__wrapper">
            <div class="courses__item"
                v-for="(course, index) in data"
                :key="index"
                :class="{'current': index == 0}"
            >
                <img
                    v-if="course.img !== null && course.img !== ''"
                    :src="course.img"
                    alt="курс"
                    class="courses__image"
                    @click="selectCourse(index)"
                    onerror="this.src = '/images/course.jpg';"
                >
                <img
                    v-else src="/images/dist/courses-image.png"
                    alt=""
                    class="courses__image"
                    @click="selectCourse(index)"
                    onerror="this.src = '/images/course.jpg';"
                >

                <div class="courses__name">
                    {{ course.name }}
                </div>
                <div class="courses__progress">
                    <div
                        v-if="coursesMap[course.id]"
                        class="courses__line"
                        :style="`width: ${getResults(course.id).progress}%`"
                    ></div>
                </div>
                <!-- Линия зависит от процентов в span-->
                <div class="courses__percent">
                    <template v-if="coursesMap[course.id]">
                        Пройдено: <span>{{ getResults(course.id).progress }}%</span>
                    </template>
                    <template v-else>
                        &nbsp;
                    </template>
                </div>
                <a :href="'/my-courses?id=' + course.id" class="courses__button">
                    <span>{{ coursesMap[course.id] ? 'Продолжить курс' : 'Начать курс' }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="profit__info active" v-if="activeCourse !== null">
        <div class="profit__info-title" >
            Информация о курсе: {{ activeCourse.name }}
        </div>
        <div class="profit__info-back" @click="back">
            Назад
        </div>
        <div class="profit__info-back-mobile"></div>
        <div class="profit__info__inner">
            <div class="profit__info__item">
                <img v-if="activeCourse.img !== null && activeCourse.img !== ''" :src="activeCourse.img" alt="info image" class="profit__info-image">
                <img v-else src="/images/dist/courses-image.png" alt="info image" class="profit__info-image">

                <div class="profit__info-about">
                    <div class="profit__info-text" v-html="activeCourse.text"></div>
                    <div class="profit__info-text mobile" v-html="activeCourse.text"></div>
                    <div class="profit__info__wrapper">

                        <div v-for="(item, index) in items" 
                            :key="index"
                            class="info__wrapper-item"
                            :class="{'done': item.status == 1}"
                        >
                            <a class="info__item-box">
                                <img src="/images/dist/info-circle.png" alt="play image" onerror="this.src = '/images/course.jpg';">
                                <p>{{ item.completed_stages }} / {{ item.all_stages }}</p>
                            </a>
                            <div class="info__item-value">{{ itemProgress(item) }}%</div>
                            <div class="info__item-value" v-if="item.item_model == 'App\\Models\\Books\\Book'">Книга</div>
                            <div class="info__item-value" v-if="item.item_model == 'App\\Models\\Videos\\VideoPlaylist'">Видеоплейлист</div>
                            <div class="info__item-value" v-if="item.item_model == 'App\\KnowBase'">База знаний</div>
                            <div class="info__item-value">{{ item.title }}</div>
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
    name: 'Courses', 
    props: {},
    data: function () {
        return {
            data: [], 
            items: [],
            courses: [],
            activeCourse: null
        };
    },
    computed: {
        coursesMap(){
            return this.courses.reduce((map, item) => {
                map[item.id] = item
                return map
            }, {})
        }
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
                this.$nextTick(() => this.initSlider())
                loader.hide()
            }).catch((e) => console.log(e))

            axios.get('/my-courses/get', {}).then(response => {
                this.courses = response.data.courses
            }).catch((e) => console.log(e))
        },

        /**
         * select active course info
         */
        selectCourse(index) {
            console.log('clicked ' + index)
            this.activeCourse = this.data[index]
            this.fetchCourse();
            // this.$nextTick(() => this.initInnerSlider())
        },

        fetchCourse() {
            let loader = this.$loading.show();

            axios.get('/my-courses/get/' + this.activeCourse.id).then(response => {
                this.items = response.data.items
                // this.$nextTick(() => this.initSlider())
                loader.hide()
            }).catch((e) => console.log(e));
        },

        /**
         * back to all courses
         */
        back() {
            this.activeCourse = null;
            this.items = [];
        },

        /**
         * init slider
         */
        initSlider() {
            VJQuery('.courses__content__wrapper').slick({
                variableWidth: true,
                infinite: false
            });

            // https://github.com/kenwheeler/slick/issues/3694
            // but it's better to replace slick with native for vue
            const $slick_slider = VJQuery('.courses__content__wrapper')
            $slick_slider.on('afterChange', function (e, slick) {
                var lElRect = slick.$slides[slick.slideCount - 1].getBoundingClientRect()
                var rOffset = lElRect.x + lElRect.width
                var wraRect = $slick_slider.find('.slick-list').get(0).getBoundingClientRect()
                if (rOffset < (wraRect.x + wraRect.width)) {
                    $slick_slider.find('.slick-next').addClass('slick-disabled')
                }
            })
        },

        /**
         * init inner slider that opens when click concrete course
         */
        initInnerSlider() {
            VJQuery('.profit__info__wrapper').slick({
                variableWidth: false,
                infinite: false,
                slidesToScroll: 2,
                slidesToShow: 10,
                responsive: [
                    {
                        breakpoint: 2140,
                        settings: {
                            variableWidth: false,
                            infinite:false,
                            swipeToSlide: false,
                            slidesToScroll: 2,
                            slidesToShow: 9,

                        }
                    },
                    {
                        breakpoint: 2000,
                        settings: {
                            variableWidth: false,
                            infinite:false,
                            swipeToSlide: false,
                            slidesToScroll: 2,
                            slidesToShow: 6,

                        }
                    },
                    {
                        breakpoint: 1800,
                        settings: {
                            variableWidth: false,
                            infinite:false,
                            swipeToSlide: false,
                            slidesToScroll: 2,
                            slidesToShow: 5,

                        }
                    },
                    {
                        breakpoint: 1600,
                        settings: {
                            infinite: true,
                            variableWidth: true,
                            swipeToSlide: true,
                            slidesToShow: 1,

                        }
                    },

                    {
                        breakpoint: 780,
                        settings: {
                            variableWidth: false,
                            infinite:false,
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            swipeToSlide: false,
                        }
                    },
                    {
                        breakpoint: 500,
                        settings: {
                            variableWidth: false,
                            infinite:false,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            swipeToSlide: false,
                        }
                    },

                ]

            });
        },

        /**
         * private: helper for template
         * count progress of course item
         */
        itemProgress(item) {
            return item.all_stages > 0 
                ? Number(item.completed_stages / item.all_stages).toFixed(1)
                : 0;
        },

        getResults(courseId){
            const course = this.coursesMap[courseId]
            if(!course.course_results || !course.course_results[0]) return null
            return course.course_results[0]
        }
    }
};
</script>

<style lang="scss">
// https://github.com/kenwheeler/slick/issues/3694
.slick-disabled {
    cursor: no-drop;
    opacity: 0.5;
    pointer-events: none;
}

.courses__item{
    &:hover{
        box-shadow: inset 0 0 5px #8FAF00;
    }
}
</style>
