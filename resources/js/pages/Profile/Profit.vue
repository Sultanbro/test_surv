<template>
<div
    id="profit"
    class="profit block _anim _anim-no-hide content"
    :class="{
        'hidden': slides.length == 0,
        'v-loading': loading
    }"
>
    <div class="profit__title title mt-5">
        Как можно зарабатывать больше
    </div>
    <div class="profit__subtitle subtitle">
        Информация, которая может быть полезна для Вашего карьерного роста
    </div>
    <div class="row profit__inner mr-1 ml-1">
        <div
            v-if="data.groups"
            class="col profit__carousel"
            :class="{'col-md-6': data.positions}"
        >
            <div class="profit__inner-item left-slide" v-for="(slide, i) in data.groups" :key="i">
                <div class="profit__inner__left" :class="{'profit__inner__one': !data.positions}">
                    <div class="profit__left-wrapper">
                        <div class="profit__inner-title">
                            {{ slide.title }}
                        </div>
                        <a href="javascript:void(0)">
                            <img
                                src="/images/dist/profit-info.svg"
                                alt="info icon"
                                v-b-popover.hover.right.html="'Тут описано именно то, за что в Вашем отделе оплачивается работа'"
                            >
                        </a>
                    </div>
                    <div class="profit__inner-text" v-html="slide.text"></div>
                </div>
                <div class="profit__arrows">
                    <a href="javascript:void(0)" class="profit__prev"></a>
                    <a href="javascript:void(0)" class="profit__next"></a>
                </div>
            </div>
        </div>
        <div
            v-if="data.positions"
            class="col profit__carousel"
            :class="{'col-md-6': data.groups}"
        >
            <div class="profit__inner-item right-slide" v-for="(slide, i) in data.positions" :key="i">
                <div  class="profit__inner-right" :class="{'profit__inner__one': !data.groups}">
                    <div class="profit__inner-title">
                        {{ slide.title }}
                        <a href="javascript:void(0)">
                            <img
                                src="/images/dist/profit-info.svg"
                                alt="info icon"
                                v-b-popover.hover.right.html="'У Вас обязательно будет карьерный рост в компании, и здесь описаны требования, необходимые знания и навыки для перехода на следующую ступень карьерной лестницы. Обязательно ознакомьтесь с разделом и задайте возникшие вопросы по карьерному росту Вашему руководителю.'"
                            >
                        </a>
                    </div>
                    <div class="profit__inner-text profit-right" v-html="slide.text"></div>
                </div>
                <div class="profit__arrows">
                    <a href="javascript:void(0)" class="profit__prev"></a>
                    <a href="javascript:void(0)" class="profit__next"></a>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
// слайдер с условиями оплаты для отделов и должности
export default {
    name: "Profit",
    props: {},
    data: function () {
        return {
            data: [],
            slides: [],
            loading: false
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
            this.loading = true

            axios.post('/profile/payment-terms').then(response => {

                this.showBtn(response.data)

                this.data = response.data
                this.form();
                this.loading = false
            }).catch((e) => console.log(e))
        },

        /**
         * private: show btn in introTop
         */
        showBtn(data) {
            if(data.groups.length != 0 || data.position !== null) {
                this.$emit('init')
            }
        },

        /**
         * form array for slider
         */
        form() {
            let groups = this.data.groups;

            /**
             * groups' terms
             */
            let to = Math.ceil(groups.length / 2);
            console.log(groups)

            let lastKey = 0;
            let lastLeftBlock = null;

            for(let i = 0; i < to; i++) {
                let left = null,
                    right = null;

                /**
                 * define left and right side of slide
                 */
                left = {
                    title: groups[lastKey].title,
                    text: groups[lastKey].text
                }


                lastKey++;

                if(groups[lastKey] !== undefined) {
                    right = {
                        title: groups[lastKey].title,
                        text: groups[lastKey].text
                    }
                }

                /**
                 * push to slides
                 */
                if(right !== null) {
                    this.slides.push({
                        left: left,
                        right: right,
                    });
                } else {
                    lastLeftBlock = left;
                }
            }

            /**
             * position terms
             */
            if (this.data.position !== null) {
                this.addPositionSlides(lastLeftBlock)
            } else if (lastLeftBlock !== null) {
                this.slides.push({
                    left: lastLeftBlock,
                    right: {title: '', text: ''},
                });
            }

            /**
             * init slider
             */
            this.$nextTick(() => this.initSlider())
        },

        /**
         * private: continue form slides
         */
        addPositionSlides(lastLeftBlock) {

            let pos = this.data.position;

            let items = [
                {
                    title: 'Следующая ступень карьерного ростa',
                    text: pos.next_step,
                },
                {
                    title: 'Требования к кандидату',
                    text: pos.require,
                },
                {
                    title: 'Что нужно делать',
                    text: pos.actions,
                },
                {
                    title: 'График работы',
                    text: pos.time,
                },
                {
                    title: 'Заработная плата',
                    text: pos.salary,
                },
                {
                    title: 'Нужные знания для перехода на следующую должность',
                    text: pos.knowledge,
                },
            ];
            this.data.positions = items;

            // if(lastLeftBlock !== null) items.unshift(lastLeftBlock);
            if(lastLeftBlock !== null) {
                this.slides.push({
                    left: lastLeftBlock,
                    right: items[0]
                });

                this.slides.push({left: items[1], right: items[2]});
                this.slides.push({left: items[3], right: items[4]});
                this.slides.push({left: items[5], right: {title:'', text: ''}});
            } else {
                this.slides.push({left: items[0], right: items[1]});
                this.slides.push({left: items[2], right: items[3]});
                this.slides.push({left: items[4], right: items[5]});
            }
        },

        /**
         * init slider for this block
         */
        initSlider() {

            VJQuery('.profit__carousel').slick({
                infinite: true,
                speed: 400,
                fade: true,
                adaptiveHeight: true,
            });
            VJQuery('.profit__prev').on('click', function(e) {
                e.preventDefault();
                VJQuery('.profit__inner').slick('slickPrev');
            });
            VJQuery('.profit__next').on('click', function(e) {
                e.preventDefault();
                VJQuery('.profit__inner').slick('slickNext');
            });

            /**
             * set some style
            *  */

            let leftSlides = document.getElementsByClassName("left-slide");
            let rightSlides = document.getElementsByClassName("right-slide");
            let height = 0;

            for(let i = 0; i < leftSlides.length; i++) {
                for(let j = 0; j < rightSlides.length; j++) {
                    const leftHeight = leftSlides[i].offsetHeight;
                    const rightHeight = rightSlides[j].offsetHeight;
                    height = leftHeight > rightHeight ? leftHeight : rightHeight;
                }
            }

            console.log('height');
            console.log(height);
            const arr = [1,1,1,2,3,4];
            console.log(leftSlides);
            [...leftSlides].forEach(data => {data.style.minHeight = height + "px"});
            [...rightSlides].forEach(data => {data.style.minHeight = height + "px"});
        }
    }
};
</script>

<style lang="scss">
.profit__inner{
    .col-6, .col-md-6{
        padding:0!important;
    }
}
</style>