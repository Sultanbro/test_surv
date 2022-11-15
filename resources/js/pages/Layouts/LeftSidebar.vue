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

    <nav class="header__nav" ref="nav">
        <template v-for="item in filteredItems.visible">
            <LeftSidebarItem
                :key="item.name"
                v-if="!item.hide"
                @calcsize="item.height = $event.offsetHeight"
                :name="item.name"
                :class="item.className"
                :href="item.href"
                :icon="item.icon"
                :img="item.img"
                :menu="item.menu"
            />
        </template>
        <LeftSidebarItem
            v-if="filteredItems.more.length"
            name="Еще"
            class="header__nav-link-more"
            :img="{ src: '/images/dist/icon-settings.svg' }"
            :menu="filteredItems.more"
        />
    </nav>
    <div class="header__nav-link last" v-if="showSettings">
        <a href="/timetracking/settings">
            <span class="_icon-nav-9 header__nav-icon"></span>
            <span class="header__nav-name">Настройка</span>
        </a>
    </div>
</div>
</template>

<script>
import LeftSidebarItem from './LeftSidebarItem'

export default {
    name: 'LeftSidebar', 
    components: {
        LeftSidebarItem
    },
    props: {},
    data: function () {
        return {
            items: [
                {
                    name: 'Профиль',
                    className: 'profile',
                    href: '/',
                    icon: '_icon-nav-1',
                    height: 0
                },
                {
                    name: 'Новости',
                    href: '/news',
                    icon: '_icon-nav-2',
                    height: 0
                },
                {
                    name: 'Структура',
                    href: '/struct',
                    icon: '_icon-nav-3',
                    height: 0
                },
                {
                    name: 'Обучение',
                    icon: '_icon-nav-4',
                    height: 0,
                    menu: [
                        {
                            title: 'Читать книги',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/admin/upbooks'
                        },
                        {
                            title: 'Смотреть видео',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/video_playlists'
                        },
                        {
                            title: 'Курсы',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/courses',
                            hide: !this.$can('courses_view')
                        }
                    ]
                },
                {
                    name: 'База знаний',
                    href: '/kb',
                    icon: '_icon-nav-5',
                    height: 0
                },
                {
                    hide: this.showReports,
                    name: 'Отчеты',
                    href: '/timetracking/reports',
                    icon: '_icon-nav-6',
                    height: 0,
                    menu: [
                        {
                            title: 'ТОП',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/top',
                            hide: !this.$can('top_view')
                        },
                        {
                            title: 'Табель',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/reports',
                            hide: !this.$can('tabel_view')
                        },
                        {
                            title: 'Время прихода',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/reports/enter-report',
                            hide: !this.$can('entertime_view')
                        },
                        {
                            title: 'HR',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/analytics',
                            hide: !this.$can('hr_view')
                        },
                        {
                            title: 'Аналитика',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/an',
                            hide: !this.$can('analytics_view')
                        },
                        {
                            title: 'Начисления',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/salaries',
                            hide: !this.$can('salaries_view')
                        },
                        {
                            title: 'Контроль качества',
                            img: '/images/dist/icon-settings.svg',
                            alt: 'settings icon',
                            href: '/timetracking/quality-control',
                            hide: !this.$can('quality_view')
                        },
                    ]
                },
                {
                    name: 'Карта',
                    href: '/maps',
                    icon: '_ico-nav- fas fa-map-signs',
                    height: 0
                },
                {
                    name: 'Частые вопросы',
                    href: '/timetracking/info',
                    icon: '_icon-nav-7',
                    height: 0
                },
                {
                    name: 'Депре мирование',
                    href: '/timetracking/fines',
                    icon: '_icon-nav-8',
                    height: 0
                },
                {
                    name: 'U-calls',
                    href: '/callibro/login',
                    img: {
                        src: 'https://cp.callibro.org/files/img/item-8h.png',
                        style: 'filter: grayscale(1) hue-rotate(290deg);opacity: 0.6;',
                        className: '_ico-nav- header__nav-icon fas fa-question'
                    },
                    height: 0,
                    hide: !this.$can('ucalls_view')
                },
                {
                    name: 'KPI',
                    href: '/kpi',
                    icon: '_icon-nav-7',
                    height: 0,
                    hide: !this.$can('kpi_view')
                },
            ],
            height: 300,
            fields: [],
            token: Laravel.csrfToken,
        };
    },
    methods: {
        onResize(){
            this.height = this.$refs.nav.offsetHeight
        }
    },
    computed: {
        showSettings(){
            return this.$can('settings_view')
            || this.$can('users_view')
            || this.$can('positions_view')
            || this.$can('groups_view')
            || this.$can('fines_view')
            || this.$can('notifications_view')
            || this.$can('permissions_view')
            || this.$can('checklists_view')
        },
        showReports(){
            return this.$can('top_view')
                || this.$can('tabel_view')
                || this.$can('entertime_view')
                || this.$can('hr_view')
                || this.$can('analytics_view')
                || this.$can('salaries_view')
                || this.$can('quality_view')
        },
        filteredItems(){
            let h = this.items[0].height
            return this.items.reduce((res, item) => {
                if(item.hide) return res;
                h += item.height + 5
                if(this.height - h > 0){
                    res.visible.push(item)
                }
                else{
                    res.more.push({
                        title: item.name,
                        img: '/images/dist/icon-settings.svg',
                        alt: 'settings icon',
                        href: item.href,
                        hide: item.hide
                    })
                }
                return res;
            }, {
                visible: [],
                more: []
            })
        }
    },
    mounted(){
        this.onResize()
        new ResizeObserver(this.onResize).observe(this.$refs.nav)
    }
};
</script>

<style lang="scss">
.header__left{
    display: flex;
    flex-direction: column;
    align-items: center;
    width:7rem;
    min-height: inherit;
    max-height: inherit;
    padding-top: 0.5rem;
    padding-bottom: 1rem;
    background-color: #F6F7FC;
    transform:translateX(0);
    opacity:1;
    visibility: visible;
    transition: all 0.5s;

    &.closed{
        transform:translateX(-30px);
        opacity:0;
        visibility: hidden;
    }
}

.header__avatar{
    cursor:pointer;
    margin-bottom: 0.5rem;
    display: block;
    max-width: 6rem;
    width: 100%;
    position:relative;
    border-radius: 10px;
    z-index: 1003;
    .header__menu{
        max-width: 24rem;
        top: 0.5rem;
    }

    > img{
        display: block;
        height: auto;
        border-radius: 10px;
        width: 100%;
        object-fit:cover;
    }
    &:hover{
        .header__menu{
            opacity: 1;
            visibility: visible;
        }
    }
}
.header__nav{
    display: flex;
    flex-direction: column;
    flex: 0 1 100%;
    gap:.5rem;
    &::-webkit-scrollbar {
        width: 0; /* высота для горизонтального скролла */
        height: 0; 
    }
}

.header__nav-link{
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    font-size: 1rem;
    gap: 1rem;
    font-weight: 400;
    color: #8D9CA9;
    position:relative;
    z-index: auto;
    transition:.3s;

    & > a{
        padding:  1.5rem 1.2rem .5rem;
        width: 100%;
        height: 100%;
        font-size:inherit;
        font-weight: inherit;
        display: flex;
        transition:.3s;

        color:inherit;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap:.7rem;
    }

    .header__nav-icon {
        font-size:2rem;
        &::before{
            transition:.2s;
        }
    }
    &:hover,
    &.opened{
    & > a{
        background: #608EE9;
        font-weight:600;
        color:#fff;
    }
        & .header__menu{
            opacity:1;
            visibility: visible;
        }
        .header__nav-icon::before{
            color:#fff;
        }
    }

    &.last{
        margin-top: auto;
    }
}

.header__menu{
    display: flex;
    flex-direction: column;
    width: 25rem;
    padding-top: 0;
    border-radius: 0.3rem;

    position: absolute;
    z-index: 1005;
    left: 8rem;

    background: #F8FAFB;
    color: #62788B;
    font-size: 1.3rem;
    box-shadow: 0 4px 4px 0 rgba(0,0,0,.25);
    opacity: 0;
    visibility: hidden;
    transition: .5s;

    .header__menu-title{
        text-align: left;
        padding-left: 2rem;
        text-transform: uppercase;
        padding-bottom: 1.2rem;
        padding-right: 1.3rem;
        p{
            border-bottom: 1px solid #EDEFF3;
            font-size:1.1rem;
            padding-top: 1rem;
            padding-bottom: 1rem;
            text-transform: none;
        }

        a{
            color:#608EE9;
        }
    }

    .menu__item{
        display: flex;
        gap:1rem;
        align-items: center;
        cursor:pointer;
        height: 3.4rem;
        padding-right: 3rem;
        padding-left: 2rem;
        background: #f6f7fd;

        &:hover{
            background: #EBF1FF;
        }
        &-title{
            color:#62788B;
            padding: 1rem 0;
        }
    }
}

.header__nav-link-more{
    .header__menu{
        bottom: 0;
    }
}
</style>