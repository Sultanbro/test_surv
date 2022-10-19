<div class="header__profile _anim _anim-no-hide custom-scroll-y">
    <div class="profile__content">
        <a href="#" class="profile__logo">
            <img src="images/dist/logo-download.svg" alt="logo download">
            Загрузить логотип
        </a>
        <a href="#" class="profile__button"><p>Начать рабочий день</p></a>
        <div class="profile__balance">Текущий баланс <p>3,567.12 <span>KZT</span></p></div>
        <select class="select-css profile-select">
            <option>Май</option>
            <option>Апрель</option>
            <option>Март</option>
            <option>Февраль</option>
            <option>Январь</option>
        </select>
        <div class="profile__about">
            <div class="profile__name">Каримов Адиль</div>
            <div class="profile__job profile-border">Старший специалист группы</div>
            <div class="profile__salary profile-border">ОКЛАД 500 000</div>
            <div class="profile__wrapper">
                <p class="profile-border">5 - 2</p>
                <p class="profile-border">09:00 - 18:00</p>
                <p class="profile-border">8 часов</p>
            </div>
            <select class="select-css">
                <option>KZT Казахстанский тенге</option>
                <option>KZT Казахстанский тенге</option>
                <option>KZT Казахстанский тенге</option>
                <option>KZT Казахстанский тенге</option>
                <option>KZT Казахстанский тенге</option>
            </select>
        </div>
        <div class="profile__point profile-box">
            <div class="profile__title">Цель на сегодня</div>
            <div class="profile__point-wrapper profile-slick">
                <div class="profile__point-start">
                    <p>Начало рабочего дня</p>
                    <p>10 : 20 am</p>
                    <div class="profile__point-time">
                        <div class="profile__time-row">
                            <p class="blue time spent">1 h 23 m</p>
                            <p>пройдено</p>
                        </div>
                        <div class="profile__time-row">
                            <p class="time left">0 h 34 m</p>
                            <p>осталось</p>
                        </div>
                    </div>
                </div>
                <div class="profile__progressbar">
                    <svg class="progress-ring" width="80" height="80">
                        <circle stroke="#fff" stroke-width="8" cx="40" cy="40" r="30" fill="#8FAF00"/>
                        <circle class="progress-ring__circle" stroke="rgba(96,142,233,0.5)" stroke-width="4" cx="40" cy="40" r="36" fill="transparent"/>
                    </svg>
                    <div class="profile__progressbar-number">
                        <span>87</span>%
                    </div>
                </div>
            </div>
            <img src="images/dist/close.svg" alt="close icon" class="point-close">
        </div>

        <div class="profile__active profile-box">
            <div class="profile__title">График активности</div>
            <div class="tabs__include profile-slick">
                <div class="tab__content-include">
                    <div class="tab__content-item-include is-active"  data-content="1">
                        <img src="images/dist/schedule.png" alt="schedule image">
                    </div>
                    <div class="tab__content-item-include"  data-content="2">
                        <img src="images/dist/profile-active.png" alt="schedule image">
                    </div>
                    <div class="tab__content-item-include"  data-content="3">
                        <img src="images/dist/schedule.png" alt="schedule image">
                    </div>
                </div>
                <div class="tabs__wrapper">
                    <div  class="tab__item-include is-active" onclick="switchTabsInclude(this)"  data-index="1">День</div>
                    <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="2">месяц</div>
                    <div  class="tab__item-include" onclick="switchTabsInclude(this)"  data-index="3">год</div>
                </div>
            </div>
            <img src="images/dist/close.svg" alt="close icon" class="point-close">
        </div>

        <a href="#" class="profile__more">
            <img src="images/dist/logo-download.svg" alt="more download">
        </a>
    </div>

</div>

<header class="header">

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
    <div class="header__right closedd">
        <div class="header__right-nav">
            <a href="#" class="header__right-icon">
                <img src="images/dist/header-right-1.svg" alt="nav icon" class="header__icon-img">
            </a>
            <a href="#" class="header__right-icon bell red">
                <img src="images/dist/header-right-2.svg" alt="nav icon" class="header__icon-img">
            </a>
            <a href="#" class="header__right-icon loop">
                <img src="images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img">
            </a>
            <a href="#" class="header__right-icon">
                <img src="images/dist/header-right-4.svg" alt="nav icon" class="header__icon-img">
            </a>
            <a href="#" class="header__right-icon">
                <img src="images/dist/header-right-5.svg" alt="nav icon" class="header__icon-img">
            </a>
            <a href="#" class="header__right-icon check">
                <img src="images/dist/header-right-6.svg" alt="nav icon" class="header__icon-img">
            </a>
        </div>
        <div class="header__right-messages">
            <a href="#" class="header__message-item new">
                <img src="images/dist/header-right-avatar-1.png" alt="header avatar">
            </a>
            <a href="#" class="header__message-item new">
                <img src="images/dist/header-right-avatar-2.png" alt="header avatar">
            </a>
            <a href="#" class="header__message-item new">
                <img src="images/dist/header-right-avatar-3.png" alt="header avatar">
            </a>
            <a href="#" class="header__message-item read">
                <img src="images/dist/header-right-avatar-4.png" alt="header avatar">
            </a>
            <a href="#" class="header__message-item read">
                <img src="images/dist/header-right-avatar-5.png" alt="header avatar">
            </a>
            <a href="#" class="header__message-item read">
                <img src="images/dist/header-right-avatar-6.png" alt="header avatar">
            </a>
        </div>
        <div class="header__right-arrow">
            <a href="#"><img src="images/dist/header-arrow.svg" alt="arrow icon"></a>
        </div>
    </div>

    <div class="header__arrow">
        <a href="#"><img src="images/dist/header-arrow.svg" alt="arrow icon"></a>
    </div>
</header>