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
        <!-- <div class="header__nav-link">
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
        </div> -->
        <div class="header__nav-link">
            <a>
                <span class="_icon-nav-4 header__nav-icon"></span>
                <span class="header__nav-name">Обучение</span>
            </a>
            <div class="header__menu">
                <!-- <div class="header__menu-title">
                    Обучение
                </div> -->
                <a href="/admin/upbooks" class="menu__item">
                    <img src="/images/dist/icon-settings.svg" alt="settings icon">
                    <span class="menu__item-title">Читать книги</span>
                </a>
                <a href="/video_playlists" class="menu__item">
                    <img src="/images/dist/icon-settings.svg" alt="settings icon">
                    <span class="menu__item-title">Смотреть видео</span>
                </a>
                <a href="/courses" class="menu__item" v-if="$can('courses_view')">
                    <img src="/images/dist/icon-settings.svg" alt="settings icon">
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
        <div class="header__nav-link"
            v-if=" $can('top_view')
                || $can('tabel_view')
                || $can('entertime_view')
                || $can('hr_view')
                || $can('analytics_view')
                || $can('salaries_view')
                || $can('quality_view')
            ">
            <a href="/timetracking/reports">
                <span class="_icon-nav-6 header__nav-icon"></span>
                <span class="header__nav-name">Отчеты</span>
            </a>
            <div class="header__menu">
                    <a href="/timetracking/top" class="menu__item" v-if="$can('top_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">ТОП</span>
                    </a>
                    <a href="/timetracking/reports" class="menu__item" v-if="$can('tabel_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Табель</span>
                    </a>
                    <a href="/timetracking/reports/enter-report" class="menu__item" v-if="$can('entertime_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Время прихода</span>
                    </a>
                    <a href="/timetracking/analytics"  class="menu__item" v-if="$can('hr_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">HR</span>
                    </a>
                    <a href="/timetracking/an"  class="menu__item" v-if="$can('analytics_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Аналитика</span>
                    </a>
                    <a href="/timetracking/salaries"  class="menu__item" v-if="$can('salaries_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Начисления</span>
                    </a>
                    <a href="/timetracking/quality-control"  class="menu__item" v-if="$can('quality_view')">
                        <img src="/images/dist/icon-settings.svg" alt="settings icon">
                        <span class="menu__item-title">Контроль качества</span>
                    </a>
            </div>
        </div>

        <div class="header__nav-link">
            <a href="/maps" >
                <span class="_icon-nav-7 header__nav-icon"></span>
                <span class="header__nav-name">Карта</span>
            </a>
        </div>

        <div class="header__nav-link">
            <a href="/timetracking/info" >
                <span class="_icon-nav-7 header__nav-icon"></span>
                <span class="header__nav-name">Частые вопросы</span>
            </a>
        </div>

        <div class="header__nav-link">
            <a href="/timetracking/fines" >
                <span class="_icon-nav-8 header__nav-icon"></span>
                <span class="header__nav-name">Депре мирование</span>
            </a>
        </div>

        <div class="header__nav-link"  v-if="$can('ucalls_view')">
            <a href="/callibro/login">
                <span class="_icon-nav-7 header__nav-icon"></span>
                <span class="header__nav-name">U-calls</span>
            </a>
        </div>

        <div class="header__nav-link"  v-if="$can('kpi_view')">
            <a href="/kpi" >
                <span class="_icon-nav-7 header__nav-icon"></span>
                <span class="header__nav-name">KPI</span>
            </a>
        </div>

    </nav>
    <div class="header__nav-link last"
        v-if=" $can('settings_view')
            || $can('users_view')
            || $can('positions_view')
            || $can('groups_view')
            || $can('fines_view')
            || $can('notifications_view')
            || $can('permissions_view')
            || $can('checklists_view')
        ">
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
            token: Laravel.csrfToken,
        };
    },
    methods: {

    }
};
</script>