<aside class="side-menu">

    <div class="user pointer">
        <a href="/">
            <img src="/images/profile.jpg" alt="avatar">
        </a>
        
        @if($unread > 0)
        <div class="nn-number" style="display:none">
         {{ $unread}}
         <div class="pulse"></div>
         <div class="marker"></div>
        </div>
        @endif
        <!-- profile menu -->
        <div class="profile-menu">
            <ul>
                <li>
                    <a href="#" class="link link-start" id="toggle_panel">
                        <i class="fas fa-bell"></i>
                        <span class="d-flex">Уведомления <i class="nn">{{ $unread }}</i></span>
                    </a>
                </li>
                <li>
                    <a href="/cabinet" class="link link-start">
                        <i class="fas fa-cogs"></i>
                        <span>Настройки</span> 
                    </a>
                </li>
                <li>
                    <a href="/logout" class="link link-start">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Выйти</span>
                    </a>
                </li>
            </ul>
        </div>


    </div>

    <ul class="main-menu">
        <li class="menu-item">
            <a href="/" class="side-btn @if($menu == 'profile') active @endif">
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
                    <a href="/upbooks" class="link">
                        <i class="fas fa-book"></i>
                        <span>Читать книги</span>
                    </a>
                </li>
                <li>
                    <a href="/videolearning" class="link">
                        <i class="fas fa-play"></i>
                        <span>Смотреть видео</span>
                    </a>
                </li>
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
                <span>Учет времени</span>
            </a>

            <ul class="sub-menu">

                    <li>
                        <a href="/timetracking/top" class="link">
                            <i class="fas fa-chart-pie"></i>
                            <span>ТОП</span>
                        </a>
                    </li>
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
                    <li>
                        <a href="/timetracking/reports" class="link">
                            <i class="fas fa-clock"></i>
                            <span>Табель</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/reports/enter-report" class="link">
                            <i class="fas fa-user-clock"></i>
                            <span>Время прихода</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/analytics" class="link">
                            <i class="fas fa-user-secret"></i>
                            <span>HR</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/an" class="link">
                            <i class="fas fa-chart-area"></i>
                            <span>Аналитика</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/salaries" class="link">
                            <i class="fas fa-comment-dollar"></i>
                            <span>Начисления</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/exam" class="link">
                            <i class="fas fa-list"></i>
                            <span>Повышение квалификаци</span>
                        </a>
                    </li>
                    <li>
                        <a href="/timetracking/quality-control" class="link">
                            <i class="fas fa-list-ol"></i>
                            <span>Контроль качества</span>
                        </a>
                    </li>


                </ul>
        </li>

        <li class="menu-item">
            <a href="/courses" class="side-btn @if($menu == 'courses') active @endif">
                <i class="fas fa-graduation-cap"></i>
                <span>Курсы</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="/video_playlists" class="side-btn @if($menu == 'video_edit') active @endif">
                <i class="fas fa-play"></i>
                <span>Видеокурсы</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="/admin/upbooks" class="side-btn @if($menu == 'upbook_edit') active @endif">
                <i class="fas fa-book-open"></i>
                <span>Книги</span>
            </a>
        </li>



    </ul>
    <ul class="after-main-menu">
        <li class="menu-item">
            <a href="/timetracking/settings" class="side-btn @if($menu == 'settings') active @endif">
                <i class="fa fa-cogs"></i>
                <span>Настройки</span>
            </a>
        </li>
    </ul>


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