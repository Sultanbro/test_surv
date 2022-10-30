<template>
<div class="intro__top _anim _anim-no-hide block">
    <a href="#" class="intro__top-burger"><img src="images/dist/intro-burger.svg" alt="burger menu" class=""></a>
    <div class="intro__top-buttons">
        <a href="#courses__anchor"	class="intro__button button-blue" v-if="courses">Вашиу курсы</a>
        <a href="#profit"	class="intro__button button-blue" v-if="profit">Как можно зарабатывать больше</a>
        <a href="#trainee" class="intro__button button-blue" v-if="estimation">Вот как стажеры оценили ваше обучение</a>
        <a href="#index" class="intro__button button-blue" v-if="indicators">Ваши показатели</a>
    </div>
</div>
</template>

<script>
export default {
    name: "IntroTop", 
    props: {
        profit: false,
        courses: false,
        estimation: false,
        indicators: false,
    },
    data: function () {
        return {
            fields: [], 
        };
    },
    methods: {
        
    }
};
</script>