<template>
<div class="profit block _anim _anim-no-hide content" id="profit">
    <div class="profit__title title">
        Как можно зарабатывать больше
    </div>
    <div class="profit__subtitle subtitle">
        Информация, которая может быть полезна для Вашего карьерного роста
    </div>
    <div class="profit__inner">


        <div class="profit__inner-item" v-for="(slide, i) in slides" :key="i">
            <div class="profit__inner__left">

                <div class="profit__left-wrapper">
                    <div class="profit__inner-title">
                        {{ slide.left.title }}
                    </div>
                    <a href="#">
                        <img src="/images/dist/profit-info.svg" alt="info icon" v-b-popover.hover.right.html="'Описание того что это'">
                    </a>
                </div>
                <div class="profit__inner-text" v-html="slide.left.text"></div>
            </div>

            <div class="profit__inner-right">
                <div class="profit__inner-title">
                    {{ slide.right.title }}
                </div>
                <a href="#">
                    <img src="/images/dist/profit-info.svg" alt="info icon" v-b-popover.hover.right.html="'Описание того что это'">
                </a>
                <div class="profit__inner-text profit-right" v-html="slide.right.text"></div>
            </div>

            <div class="profit__arrows">
                <a href="#" class="profit__prev"></a>
                <a href="#" class="profit__next"></a>
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
            slides: []
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

            axios.post('/profile/payment-terms').then(response => {
                this.data = response.data
                this.form();
                loader.hide()
            }).catch((e) => console.log(e))
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

            let lastKey = 0;
            let lastLeftBlock = null;

            for(let i = 0; i < to; i++) {
                let left = null,
                    right = null;

                /**
                 * define left and right side of slide
                 */
                left = {
                    title: groups[lastKey].name,
                    text: groups[lastKey].text
                }

                lastKey++;

                if(groups[lastKey] !== undefined) {
                    right = {
                        title: groups[lastKey].name,
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
            if(this.data.position !== null) {
                this.addPositionSlides(lastLeftBlock)
            } else {
                this.slides.push({
                    left: lastLeftBlock,
                    right: null,
                });
            }
            
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

            // if(lastLeftBlock !== null) items.unshift(lastLeftBlock);
            
            if(lastLeftBlock !== null) {
                this.slides.push({
                    left: lastLeftBlock,
                    right: items[0]
                });

                this.slides.push({left: items[1], right: items[2]});
                this.slides.push({left: items[3], right: items[4]});
                this.slides.push({left: items[5], right: null});

            } else {
                this.slides.push({left: items[0], right: items[1]});
                this.slides.push({left: items[2], right: items[3]});
                this.slides.push({left: items[4], right: items[5]});
            }
        }
    }
};
</script>