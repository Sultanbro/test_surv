<template>
<div class="header__left closedd">
    <!-- avatar  -->
    <div class="header__avatar">
        <img :src="$laravel.avatar" alt="avatar image" >
        
        <!-- hover menu -->
        <div class="header__menu">
            <div class="header__menu-title">
                Пользователь <a href="#">#{{ $laravel.userId }}</a>
                <p>{{ $laravel.email }}</p> 
            </div>
            <a href="/cabinet" class="menu__item">
                <img src="/images/dist/icon-settings.svg" alt="settings icon">
                <span class="menu__item-title">Настройки</span>
            </a>
            <form action="/logout" method="POST">
                <button class="menu__item w-full">
                    <img src="/images/dist/icon-exit.svg" alt="settings icon">
                    <span class="menu__item-title">Выход</span>
                </button>
                <input type="hidden" :value="$laravel.csrfToken" name="_token" />
            </form> 
        </div>
    </div>

    <nav class="header__nav">
        <div class="header__nav-link profile">
            <a href="/">
                <span class="_icon-nav-1 header__nav-icon"></span>
                <span class="header__nav-name">Профиль</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="/news">
                <span class="_icon-nav-2 header__nav-icon"></span>
                <span class="header__nav-name">Новости</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="/struct">
                <span class="_icon-nav-3 header__nav-icon"></span>
                <span class="header__nav-name">Структура</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a>
                <span class="_icon-nav-4 header__nav-icon"></span>
                <span class="header__nav-name">Обучение</span>
            </a>
            <div class="header__menu">
                <div class="header__menu-title">
                    Отчеты
                </div>
                <a href="/admin/upbooks" class="menu__item">
                    <img src="images/dist/icon-settings.svg" alt="settings icon">
                    <span class="menu__item-title">Читать книги</span>
                </a>
                <a href="/video_playlists" class="menu__item">
                    <img src="images/dist/icon-settings.svg" alt="settings icon">
                    <span class="menu__item-title">Смотреть видео</span>
                </a>
                <a href="/my-courses" class="menu__item">
                    <img src="images/dist/icon-settings.svg" alt="settings icon">
                    <span class="menu__item-title">Мои курсы</span>
                </a>
                <a href="/courses" class="menu__item" v-can="'courses_view'">
                    <img src="images/dist/icon-settings.svg" alt="settings icon">
                    <span class="menu__item-title">Курсы</span>
                </a>
            </div>
        </div>
        <div class="header__nav-link">
            <a href="/kb">
                <span class="_icon-nav-5 header__nav-icon"></span>
                <span class="header__nav-name">База знаний</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="/timetracking/reports">
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
            <a href="/kpi" >
                <span class="_icon-nav-7 header__nav-icon"></span>
                <span class="header__nav-name">KPI</span>
            </a>
        </div>
        <div class="header__nav-link">
            <a href="/timetracking/fines" >
                <span class="_icon-nav-8 header__nav-icon"></span>
                <span class="header__nav-name">Депрем</span>
            </a>
        </div>
    </nav>
    <div class="header__nav-link last">
        <a href="/timetracking/settings">
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
            token: Laravel.csrfToken
        };
    },
    methods: {

    }
};
</script>