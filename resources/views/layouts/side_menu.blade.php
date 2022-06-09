<aside class="side-menu">

    <div class="user pointer">
        <a href="/profile">
            <img src="/images/logo2.jpg" alt="avatar">
        </a>
        
       
        <!-- profile menu -->
        <div class="profile-menu">
            <div class="top">
                <p class="name">{{ auth()->user()->last_name }} {{ auth()->user()->name }} <span>#{{ auth()->user()->id }} </span></p>
                <p class="email">{{ auth()->user()->email }} </p>
            </div>
            <ul> 
       
                @if(auth()->user()->is_admin)
                <li>
                    <a href="/cabinet" class="link link-start">
                        <i class="fas fa-cogs"></i>
                        <span>Настройки</span> 
                    </a>
                </li> 
                @endif
                <li>
                    <form action="/logout" method="POST">
                        <button class="link link-start">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Выйти</span>
                        </button>
                        @csrf
                    </form>
                </li>
            </ul>
        </div>


    </div>

    <ul class="main-menu">
        <li class="menu-item">
            <a href="/profile" class="side-btn @if($menu == 'profile') active @endif">
                <i class="far fa-address-card"></i>
                <span>Мой профиль</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="/upbooks" class="side-btn @if($menu == 'learning') active @endif">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Обучение</span>
            </a>

            <ul class="sub-menu">
                <li>
                    <a href="/admin/upbooks" class="link">
                        <i class="fas fa-book"></i>
                        <span>Читать книги</span>
                    </a>
                </li>
                <li>
                    <a href="/video_playlists" class="link">
                        <i class="fas fa-play"></i>
                        <span>Смотреть видео</span>
                    </a>
                </li>
                @if(auth()->user()->is_admin)
                <li>
                    <a href="/courses" class="link">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Курсы</span>
                    </a>
                </li>
                @endif
            </ul>

        </li>

        <li class="menu-item">
            <a href="/kb" class="side-btn @if($menu == 'kb') active @endif">
                <i class="fas fa-database"></i>
                <span>База знаний</span>
            </a>
        </li>




        <li class="menu-item">
            <a href="/timetracking/reports" class="side-btn @if($menu == 'surv') active @endif">
                <i class="fas fa-calendar-alt"></i>
                <span>Отчеты</span>
            </a>

            <ul class="sub-menu">
                @if(auth()->user()->can('top_view'))
                    <li>
                        <a href="/timetracking/top" class="link">
                            <i class="fas fa-chart-pie"></i>
                            <span>ТОП</span>
                        </a>
                    </li>
                @endif
                    <li>
                        <a href="/timetracking/info" class="link">
                            <i class="fas fa-question-circle"></i>
                            <span>Частые вопросы</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/fines" class="link">
                            <i class="fas fa-minus-circle"></i>
                            <span>Депримирование</span>
                        </a>
                    </li>
                    @if(auth()->user()->can('tabel_view'))
                    <li>
                        <a href="/timetracking/reports" class="link">
                            <i class="fas fa-clock"></i>
                            <span>Табель</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('entertime_view'))
                    <li>
                        <a href="/timetracking/reports/enter-report" class="link">
                            <i class="fas fa-user-clock"></i>
                            <span>Время прихода</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('hr_view'))
                    <li>
                        <a href="/timetracking/analytics" class="link">
                            <i class="fas fa-user-secret"></i>
                            <span>HR</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('analytics_view'))
                    <li>
                        <a href="/timetracking/an" class="link">
                            <i class="fas fa-chart-area"></i>
                            <span>Аналитика</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('salaries_view'))
                    <li>
                        <a href="/timetracking/salaries" class="link">
                            <i class="fas fa-comment-dollar"></i>
                            <span>Начисления</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('quality_view'))
                    <li>
                        <a href="/timetracking/quality-control" class="link">
                            <i class="fas fa-list-ol"></i>
                            <span>Контроль качества</span>
                        </a>
                    </li>
                    @endif


                </ul>
        </li>
       
        <li class="menu-item">
            <a href="/maps" class="side-btn @if($menu == 'maps') active @endif">
                <i class="fas fa-map-signs"></i>
                <span>Карта</span>
            </a>
        </li>
      


    </ul>
    @if(auth()->user()->can('settings_view') ||  auth()->user()->can('users_view'))
    <ul class="after-main-menu">
        <li class="menu-item">
            <a href="/timetracking/settings" class="side-btn @if($menu == 'settings') active @endif">
                <i class="fa fa-cogs"></i>
                <span>Настройки</span>
            </a>
        </li>
    </ul>
    @endif

</aside>


<div class="kolokolchik" id="noti_panel" style="display:none;">
    @include('includes.admin_notifications', [
            'unread_notifications' => $unread_notifications,
            'read_notifications' => $read_notifications,
            'unread' => $unread,
            'head_users' => $head_users,
            'bonus_notification' => $bonus_notification,
        ])
</div>