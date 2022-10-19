<template>
<div class="header__left closedd">
    <div class="header__avatar">
        <img src="images/dist/header-avatar.png" alt="avatar image" >
        <div class="header__menu">
            <div class="header__menu-title">
                Специалист тутми <a href="#">#14182</a>
                <p>mikle@tutmee.ru</p>
            </div>
            <a href="#" class="menu__item">
                <img src="images/dist/icon-settings.svg" alt="settings icon">
                <span class="menu__item-title">Настройки</span>
            </a>
            <a href="#"  class="menu__item">
                <img src="images/dist/icon-exit.svg" alt="settings icon">
                <span class="menu__item-title">Выход</span>
            </a>
        </div>
    </div>
    <nav class="header__nav">
        <div class="header__nav-link profile">
            <a href="#profile">
                <span class="_icon-nav-1 header__nav-icon"></span>
                <span class="header__nav-name">Профиль</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="#news">
                <span class="_icon-nav-2 header__nav-icon"></span>
                <span class="header__nav-name">Новости</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="#kaspi">
                <span class="_icon-nav-3 header__nav-icon"></span>
                <span class="header__nav-name">Структура</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="#nominations">
                <span class="_icon-nav-4 header__nav-icon"></span>
                <span class="header__nav-name">Обучение</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="#base" >
                <span class="_icon-nav-5 header__nav-icon"></span>
                <span class="header__nav-name">База знаний</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="#balance">
                <span class="_icon-nav-6 header__nav-icon"></span>
                <span class="header__nav-name">Отчеты</span>
            </a>
            <div class="header__menu">
                <div class="header__menu-title">
                    Отчеты
                </div>
                    <a href="#" class="menu__item">
                        <img src="images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">ТОП</span>
                    </a>
                    <a href="#" class="menu__item">
                        <img src="images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Частые вопросы</span>
                    </a>
                    <a href="#" class="menu__item">
                        <img src="images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Депремирование</span>
                    </a>
                    <a class="menu__item">
                        <img src="images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Табель</span>
                    </a>
                    <a href="#" class="menu__item">
                        <img src="images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Время прихода</span>
                    </a>
                    <a href="#"  class="menu__item">
                        <img src="images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Аналитика</span>
                    </a>
            </div>
        </div>

        <div class="header__nav-link">
            <a href="#kpi" >
                <span class="_icon-nav-7 header__nav-icon"></span>
                <span class="header__nav-name">KPI</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="#award" >
                <span class="_icon-nav-8 header__nav-icon"></span>
                <span class="header__nav-name">Депрем</span>
            </a>
        </div>
    </nav>
    <div class="header__nav-link last">
        <a href="#">
            <span class="_icon-nav-9 header__nav-icon"></span>
            <span class="header__nav-name">Настройка</span>
        </a>
    </div>
</div>
</template>

<script>
export default {
    name: "LeftSidebar", 
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