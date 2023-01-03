<template>
<div class="header__left closed">
    <!-- avatar  -->
    <div class="header__avatar">
        <img :src="avatar" alt="avatar image" >

        <!-- hover menu -->
        <div class="header__menu">
            <div
                class="header__menu-project"
                v-scroll-lock="isCreatingProject"
            >
                <img src="/images/dist/icon-settings.svg" alt="settings icon">
                Проект: {{ project }}
                <div class="header__submenu">
                    <a
                        v-for="cabinet in cabinets"
                        :href="cabinet.tenant_id === project ? 'javascript:void(0)' : `/login/${cabinet.tenant_id}`"
                        class="header__submenu-item"
                        :class="{'header__submenu-item_active': cabinet.tenant_id === project}"
                    >
                        {{ cabinet.tenant_id }} <i v-if="cabinet.owner === 1" aria-hidden="true" class="fa fa-star"></i>
                    </a>
                    <div class="header__submenu-divider"/>
                    <div
                        v-if="isOwner"
                        @click="onNewProject"
                        class="header__submenu-item"
                    >
                        Добавить проект
                    </div>
                </div>
            </div>
            <div class="header__menu-title">
                Пользователь <a
                    class="header__menu-userid"
                    href="javascript:void(0)"
                >#{{ $laravel.userId }}</a>
                <p class="header__menu-email">{{ $laravel.email }}</p>
            </div>
            <router-link to="/cabinet" class="menu__item">
                <img src="/images/dist/icon-settings.svg" alt="settings icon">
                <span class="menu__item-title">Настройки</span>
            </router-link>
            <form action="/logout" method="POST">
                <button class="menu__item w-full">
                    <img src="/images/dist/icon-exit.svg" alt="settings icon">
                    <span class="menu__item-title">Выход</span>
                </button>
                <input
                    type="hidden"
                    :value="$laravel.csrfToken"
                    name="_token"
                />
            </form>
        </div>
    </div>

    <nav
        ref="nav"
        class="header__nav"
        :class="{'header__nav_even': height - filteredItems.totalHeight < 50}"
    >
        <template v-for="item in filteredItems.visible">
            <LeftSidebarItem
                :key="item.name"
                v-if="!item.hide"
                @calcsize="item.height = $event.offsetHeight"
                :name="item.name"
                :class="item.className"
                :to="item.to"
                :href="item.href"
                :icon="item.icon"
                :img="item.img"
                :menu="item.menu"
                :popover="item.popover"
            />
        </template>
        <template v-if="filteredItems.more.length === 1">
            <LeftSidebarItem
                :key="filteredItems.more[0].name"
                v-if="!filteredItems.more[0].hide"
                :name="filteredItems.more[0].name"
                :class="filteredItems.more[0].className"
                :href="filteredItems.more[0].href"
                :to="filteredItems.more[0].to"
                :icon="filteredItems.more[0].icon"
                :img="filteredItems.more[0].img"
                :menu="filteredItems.more[0].menu"
            />
        </template>
        <LeftSidebarItem
            v-if="filteredItems.more.length > 1"
            name="Еще"
            class="header__nav-link-more"
            icon="icon-nd-more"
            :menu="filteredItems.more"
        />
    </nav>
    <LeftSidebarItem
        v-if="showSettings"
        name="Настройка"
        class="last"
        icon="icon-nd-settings"
        to="/timetracking/settings"
    />
</div>
</template>

<script>
import LeftSidebarItem from './LeftSidebarItem'
import { bus } from '../../bus'

export default {
    name: 'LeftSidebar',
    components: {
        LeftSidebarItem
    },
    props: {},
    data: function () {
        return {
            height: 300,
            fields: [],
            avatar: Laravel.avatar,
            token: Laravel.csrfToken,
            isAdmin: this.$laravel.is_admin,
            project: window.location.hostname.split('.')[0],
            cabinets: Laravel.cabinets,
            isCreatingProject: false,
            resizeObserver: null,
        };
    },
    methods: {
        onResize(){
            if(!this.$refs.nav) return
            this.height = this.$refs.nav.offsetHeight
        },
        onNewProject(){
            if(!confirm('Вы уверены? Создатся еще один кабинет под другим субдоменом. Вам это нужно ?')) return
            this.isCreatingProject = true
            const loader = this.$loading.show({
                zIndex: 99999
            })
            this.$toast.info('Ваш кабинет создается', {
                timeout: 20000,
                closeOnClick: false,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: false,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: false,
                icon: true
            })
            axios.post('/projects/create', {}).then(response => {
                if(response.data) location.assign(response.data.link)
            }).catch(error => {
                loader.hide()
                this.isCreatingProject = false
                this.$toast.error('Ошибка при создании кабинета')
                console.error(error)
            })
        },
        updateAvatar(avatar){
            this.avatar = avatar
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
                || (this.$can('hr_view') && window.location.host.split('.')[0] == 'bp')
                || this.$can('analytics_view')
                || this.$can('salaries_view')
                || this.$can('quality_view')
        },
        showEducation(){
            return this.$can('books_view')
                || this.$can('videos_view')
                || this.$can('courses_view')
        },
        items(){
            return [
                {
                    name: 'Профиль',
                    to: '/',
                    icon: 'icon-nd-profile',
                    height: 0
                },
                {
                  name: 'Новости',
                  to: '/news',
                  icon: 'icon-nd-news',
                  height: 0,
                  // hide: !this.$can('news_edit')
                },
                {
                    name: 'Структура',
                    // to: '/struct',
                    icon: 'icon-nd-struct',
                    popover: 'Структура - Этот функционал в разработке',
                    height: 0
                },
                {
                    name: 'База знаний',
                    to: '/kb',
                    icon: 'icon-nd-kdb',
                    height: 0
                },
                {
                    name: 'Обучение',
                    icon: 'icon-nd-education',
                    height: 0,
                    menu: [
                        {
                            name: 'Читать книги',
                            icon: 'icon-nd-books',
                            to: '/admin/upbooks'
                        },
                        {
                            name: 'Смотреть видео',
                            icon: 'icon-nd-video',
                            to: '/video_playlists'
                        },
                        {
                            name: 'Курсы',
                            icon: 'icon-nd-courses',
                            to: '/courses'
                        }
                    ]
                },
                {
                    hide: !this.showReports,
                    name: 'Отчеты',
                    to: '/timetracking/reports',
                    icon: 'icon-nd-reports',
                    height: 0,
                    menu: [
                        {
                            name: 'ТОП',
                            icon: 'icon-nd-dashboard',
                            to: '/timetracking/top',
                            hide: !this.$can('top_view')
                        },
                        {
                            name: 'Табель',
                            icon: 'icon-nd-tabel',
                            to: '/timetracking/reports',
                            hide: !this.$can('tabel_view')
                        },
                        {
                            name: 'Время прихода',
                            icon: 'icon-nd-enter-time',
                            to: '/timetracking/reports/enter-report',
                            hide: !this.$can('entertime_view')
                        },
                        {
                            name: 'HR',
                            icon: 'icon-nd-hr',
                            to: '/timetracking/analytics',
                            hide: !(this.$can('hr_view') && window.location.host.split('.')[0] === 'bp')
                        },
                        {
                            name: 'Аналитика',
                            icon: 'icon-nd-analytics',
                            to: '/timetracking/an',
                            hide: !this.$can('analytics_view')
                        },
                        {
                            name: 'Начисления',
                            icon: 'icon-nd-salary',
                            to: '/timetracking/salaries',
                            hide: !this.$can('salaries_view')
                        },
                        {
                            name: 'Контроль качества',
                            icon: 'icon-nd-quality',
                            to: '/timetracking/quality-control',
                            hide: !this.$can('quality_view')
                        },
                    ]
                },
                {
                    name: 'Карта',
                    to: '/maps',
                    icon: 'icon-nd-map',
                    height: 0
                },
                {
                    name: 'KPI',
                    to: '/kpi',
                    icon: 'icon-nd-kpi',
                    height: 0,
                    hide: !this.$can('kpi_view')
                },
                {
                    name: 'KK',
                    to: '/',
                    icon: 'icon-nd-kk',
                    height: 0,
                    hide: true
                },
                {
                    name: 'Частые вопросы',
                    to: '/timetracking/info',
                    icon: 'icon-nd-questions',
                    height: 0,
                    hide: !this.$can('faq_view')
                },
                {
                    name: 'Депре мирование',
                    to: '/timetracking/fines',
                    icon: 'icon-nd-deduction',
                    height: 0,
                    hide: !this.$can('penalties_view')
                },
                {
                    name: 'U-calls',
                    href: '/callibro/login',
                    icon: 'icon-nd-u-calls',
                    height: 0,
                    hide: !(this.$can('ucalls_view') && window.location.host.split('.')[0] == 'bp')
                },
            ]
        },
        filteredItems(){
            return this.items.reduce((res, item) => {
                if(item.hide) return res;
                res.totalHeight += item.height + 4
                if(this.height - res.totalHeight > 0){
                    res.visible.push(item)
                }
                else{
                    res.more.push(item)
                }
                return res;
            }, {
                visible: [],
                more: [],
                totalHeight: this.items[0].height
            })
        },
        isOwner(){
            return this.cabinets && this.cabinets.includes(this.project)
        }
    },
    mounted(){
        this.onResize()
        this.resizeObserver = new ResizeObserver(this.onResize).observe(this.$refs.nav)

        bus.$on('user-avatar-update', this.updateAvatar)
    },
    beforeUnmount(){
        if(this.resizeObserver) this.resizeObserver.disconnect()
        bus.$off('user-avatar-update', this.updateAvatar)
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
    background-color: darken(#F6F7FC, 3%);
    transform:translateX(0);
    opacity:1;
    visibility: visible;
    transition: all 0.5s;
    // box-shadow: -0.1rem 0px 0.5rem rgba(0, 0, 0, 0.25);
}

.header__avatar{
    cursor:pointer;
    display: block;
    width: 100%;
    max-width: 100%;
    margin-bottom: 0.5rem;
    position:relative;
    border-radius: 10px;
    padding: 0 5px;
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

.header__menu-project{
    padding: 1.2rem 1.3rem;
    text-align: left;
    cursor: pointer;
    position: relative;
    &:hover{
        background: #FAFCFD;
        .header__submenu{
            opacity: 1;
            visibility: visible;
        }
    }
}
.header__submenu{
    display: flex;
    width: auto;
    flex-direction: column;
    padding-top: 0;

    position: absolute;
    z-index: 1010;
    top: 100%;
    right: 0;

    background: #fff;
    color: #657A9F;
    font-size: 1.3rem;
    box-shadow: 1rem 0 2rem rgba(0, 0, 0, 0.25);
    opacity: 0;
    visibility: hidden;
    transition: .5s;
}
.header__submenu-item{
    display: flex;
    gap:1rem;
    align-items: center;

    height: 3.4rem;
    padding: 1rem 2rem;

    background: #fff;
    cursor:pointer;
    color:#657A9F;

    &:first-of-type{
        border-radius: 0 1rem 0 0;
    }

    &:last-of-type{
        border-radius: 0 0 1rem 0;
    }

    &:hover{
        background: #FAFCFD;
        color: #156AE8;
        .menu__item-title,
        .menu__item-icon{
            color: #156AE8;
        }
    }
}
.header__submenu-item_active{
    cursor: default;
    font-weight: 700;
    &:hover{
        background: #fff;
        color: #657A9F;
        .menu__item-title,
        .menu__item-icon{
            color: #657A9F;
        }
    }
}
.header__submenu-divider{
    margin: 0.5rem 0;
    border-top: 1px solid lighten(#657A9F, 25%);
}

.header__nav{
    display: flex;
    flex-direction: column;
    flex: 0 1 100%;
    gap:.3rem;
    overflow-y: auto;
    &::-webkit-scrollbar {
        width: 0; /* высота для горизонтального скролла */
        height: 0;
    }
    &_even{
        justify-content: space-evenly;
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

    &:hover,
    &.opened{
        .header__nav-link-a{
            background: #608EE9;
            color: #fff;
        }
        .header__nav-name{
            color: #fff;
        }
        .header__menu{
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
.header__nav-link-a{
    display: flex;
    flex-direction: column;
    align-items: center;
    gap:.5rem;

    width: 100%;
    height: 100%;
    padding:  0.9rem 0.5rem;

    text-align: center;
    font-size: 1.2rem;
    font-weight: 400;
    color:#8DA0C1;

    transition:.3s;
    cursor:pointer;
    &:visited{
        color:#8DA0C1;
    }
}
.header__nav-icon {
    font-size:2rem;
    &::before{
        transition:.2s;
        width: auto;
    }
}

.header__menu{
    display: flex;
    flex-direction: column;
    width: 25rem;
    padding-top: 0;
    border-radius: 0 1rem 1rem 0;

    position: fixed;
    z-index: 1005;
    left: 7rem;

    background: #fff;
    color: #657A9F;
    font-size: 1.3rem;
    box-shadow: 1rem 0 2rem rgba(0, 0, 0, 0.15);
    opacity: 0;
    visibility: hidden;
}

.header__menu-title{
    padding: 2rem 1.3rem 1.2rem 2rem;
    text-align: left;
    text-transform: uppercase;
}

.header__menu-email{
    border-bottom: 1px solid #EDEFF3;
    font-size:1.1rem;
    padding-top: 1rem;
    padding-bottom: 1rem;
    text-transform: none;
}

.header__menu-userid{
    color:#608EE9;
}

.menu__item{
    display: flex;
    gap:1rem;
    align-items: center;

    height: 3.4rem;
    padding-right: 3rem;
    padding-left: 2rem;

    background: #fff;
    cursor:pointer;

    &:first-of-type{
        border-radius: 0 1rem 0 0;
    }

    &:last-of-type{
        border-radius: 0 0 1rem 0;
    }

    &:hover{
        background: #FAFCFD;
        .menu__item-title,
        .menu__item-icon{
            color: #156AE8;
        }
    }
    &-title{
        color:#657A9F;
        padding: 1rem 0;
    }
}

.menu__item-icon{
    color: #A6B7D4;
}

.header__nav-link-more{
    .header__menu{
        transform: translateY(calc(-100% + 5rem));
    }
}

@media(max-width:900px){
    .header__left{
        &.closed{
            transform:translateX(-30px);
            opacity:0;
            visibility: hidden;
        }
    }
}
</style>
