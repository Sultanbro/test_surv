<template>
<div class="intro__top _anim _anim-no-hide block">
    <a href="#" class="intro__top-burger"><img src="images/dist/intro-burger.svg" alt="burger menu" class=""></a>
    <div class="intro__top-buttons">
        <a href="#profit"	class="intro__button button-blue">Как можно зарабатывать больше</a>
        <a href="#profitInfo" class="intro__button button-blue">Ваша личная информация</a>
        <a href="#trainee" class="intro__button button-blue">Вот как стажеры оценили ваше обучение</a>
        <a href="#index" class="intro__button button-blue">Ваши показатели</a>
    </div>
</div>
</template>

<script>
export default {
    name: "IntroTop", 
    props: {},
    data: function () {
        return {
            fields: [], 
        };
    },
    methods: {
        
    }
};
</script>