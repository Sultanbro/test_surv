<style>
    /* width */
        ::-webkit-scrollbar {
            width: 1px;
            height: 3px;
            cursor: pointer;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        
        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888; 
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #136a9c; 
        }
        #left-panel {
            left: -280px;
            position: fixed;
            left: 0;
            z-index: 1000;
            display: block;
            -webkit-transition: all .35s ease;
            -moz-transition: all .35s ease;
            -ms-transition: all .35s ease;
            -o-transition: all .35s ease;
            transition: all .35s ease;
        }
        #right-panel {
            position: relative;
            display: block;
            left: 0;
            width: 100%;
        }
        #right-panel.show-sidebar {
            display: block;
            left: 280px;
            width: calc(100vw - 285px);
        }
        #left-panel{
            left: -280px;
        }
        #left-panel.show-sidebar{
            left: 0;
            overflow: auto;
            border-right: 1px solid #646566;
        }
        /* width */
        #left-panel.show-sidebar::-webkit-scrollbar {
        width: 3px;
        }

        /* Track */
        #left-panel.show-sidebar::-webkit-scrollbar-track {
        background: #f1f1f1; 
        }
        
        /* Handle */
        #left-panel.show-sidebar::-webkit-scrollbar-thumb {
        background: #646566; 
        }

        /* Handle on hover */
        #left-panel.show-sidebar::-webkit-scrollbar-thumb:hover {
        background: #555; 
        }
       
        .sidemenu {
            position: absolute;
            right: -30px;
            top: 0;
            width: 30px;
            height: 30px;
            cursor: pointer;
            background: #00273e;
            z-index: 2;
            color: #7d7d7d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            -webkit-transition: all .35s ease;
            -moz-transition: all .35s ease;
            -ms-transition: all .35s ease;
            -o-transition: all .35s ease;
            transition: all .35s ease;
        }
        #left-panel.show-sidebar .sidemenu {
            color: #5f5f5f;
            padding-top: 10px;
            padding-right: 22px;
            right: 3px;
            top: 0;
            font-size: 6px;
            background: #272c33;
        }
        .sidemenu i.show{
            display: block;
        }
        .sidemenu i {
            display: none;
        }
        .btn-primary {
    color: #fff;}
    </style>
   <style>
        .navbar .navbar-nav li.menu-item-has-children .sub-menu a.active {
            color: #1076b0 !important;
            text-decoration: underline;
        }

        aside.left-panel {
            min-width: 280px;
        }

        @media (max-width: 1380px) {
            .container {
                max-width: 1000px;
            }
        }
    </style>
<style>

.side-menu {
    display: none;
    padding: 15px 0;
    position: fixed;
    left: 280px;
    bottom: 0;
    z-index: 999;
    background: #045e92;
    width: 280px;
    height: 100vh;
    list-style: none;
    transition: 0.3s all ease;
}
.side-menu.show {
    display: block;
}
.side-menu a {
    color: #fff;
}
</style>
    <!-- Right Panel -->

<aside id="left-panel" class="left-panel show-sidebar">


        <div class="sidemenu">
            <i class="fa fa-bars fa-2x closer"></i>
            <i class="fa fa-chevron-left fa-2x show opener"></i>
        </div>
        <nav class="navbar navbar-expand-sm navbar-default" style="flex-flow: column;">

            <div class="navbar-header" style="    margin-bottom: 20px;">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="/" style="    padding: 10px 0 15px 0;"><img src="/admin/images/logo.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="/"><img src="/admin/images/logo2.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">


                    @if (auth()->user()->roles['page1'] =='on' || auth()->user()->roles['page2'] =='on' ||
                    auth()->user()->roles['page3'] =='on' || auth()->user()->roles['page4'] =='on' ||
                    auth()->user()->roles['page5'] =='on' || auth()->user()->roles['page6'] =='on' ||
                    auth()->user()->roles['page7'] =='on' || auth()->user()->roles['page8'] =='on' ||
                    auth()->user()->roles['page9'] =='on' || auth()->user()->roles['page10'] =='on' ||
                    auth()->user()->roles['page11'] =='on' || (isset(auth()->user()->roles['page18']) &&
                    auth()->user()->roles['page18'] =='on') || (isset(auth()->user()->roles['page19']) &&
                    auth()->user()->roles['page19'] =='on'))

                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-laptop"></i>Админка</a>
                        <ul class="sub-menu children dropdown-menu">

                            @if( auth()->user()->roles['page1'] =='on') <li><i class="menu-icon fa fa-comments-o"></i><a href="/report/sms"> СМС рекламные </a>
                            </li>
                            @endif

                            @if( auth()->user()->roles['page2'] =='on')
                            <li><i class="menu-icon fa fa-comments-o"></i><a href="/report"> СМС интеграции </a></li>
                            @endif
                            @if( auth()->user()->roles['page3'] =='on')
                            <li><i class="menu-icon fa fa-comments-o"></i><a href="/report/user"> СМС пользователи </a>
                            </li>
                            @endif
                            @if( auth()->user()->roles['page4'] =='on')
                            <li style="display:none;"><i class="menu-icon fa fa-bar-chart-o"></i><a href="/report/old">
                                    Отчет по шлюзам(старый) </a></li>
                            @endif
                            @if( auth()->user()->roles['page5'] =='on')
                            <li><i class="menu-icon fa fa-bar-chart-o"></i><a href="/sip"> Отчет по SIP</a></li>
                            <li><i class="menu-icon fa fa-bar-chart-o"></i><a href="/asterisk"> Отчет по Asterisk</a>
                            </li>
                            @endif
                            @if( auth()->user()->roles['page6'] =='on')
                            <li><i class="menu-icon fa fa-money"></i><a href="/balance"> Балансы </a></li>
                            @endif
                            @if( auth()->user()->roles['page7'] =='on')
                            <li><i class="menu-icon fa fa-money"></i><a href="/bonus"> Бонусы пользователей</a></li>
                            @endif
                            @if( auth()->user()->roles['page8'] =='on')
                            <li><i class="menu-icon fa fa-money"></i><a href="/max-session"> Лимит линий</a></li>
                            @endif
                            @if( auth()->user()->roles['page9'] =='on')
                            <li><i class="menu-icon fa fa-money"></i><a href="/tarrifs/message"> Цены для СМС</a></li>
                            @endif
                            @if( auth()->user()->roles['page10'] =='on')
                            <li><i class="menu-icon fa fa-money"></i><a href="/tarrifs/autocall"> Цены для
                                    Автозвонков</a></li>
                            @endif
                            @if( auth()->user()->roles['page11'] =='on')
                            <li style="display:none;"><i class="menu-icon fa fa-money"></i><a href="/clear-validator">
                                    Почистить валидатор</a></li>
                            @endif
                            @if(auth()->user()->ID ==18 || auth()->user()->ID == 5 || (isset(auth()->user()->roles['page18']) &&
                            auth()->user()->roles['page18'] =='on'))
                            <li><i class="menu-icon fa fa-money"></i><a href="/rent-numbers">Аренда номеров</a></li>
                            @endif
                            @if(auth()->user()->ID ==18 || auth()->user()->ID == 5 || (isset(auth()->user()->roles['page19']) &&
                            auth()->user()->roles['page19'] =='on'))
                            <li><i class="menu-icon fa fa-money"></i><a href="/pay_info">Платежи</a></li>
                            @endif
                            @if(auth()->user()->ID ==18 || auth()->user()->ID == 5 || (isset(auth()->user()->roles['page19']) &&
                            auth()->user()->roles['page19'] =='on'))
                            <li><i class="menu-icon fa fa-phone"></i><a href="/autocalls">Автозвонки</a></li>
                            @endif
                        </ul>
                    </li>
                    
                    @endif
                    @if( auth()->user()->roles['page12'] =='on' || auth()->user()->roles['page13'] =='on')
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-newspaper-o"></i>Новости на сайт</a>
                        <ul class="sub-menu children dropdown-menu">


                            @if( auth()->user()->roles['page12'] =='on')
                            <li><i class="fa fa-file-word-o"></i><a href="/bnews">bpartners</a></li> @endif
                            @if( auth()->user()->roles['page13'] =='on') <li><i class="fa fa-file-word-o"></i><a href="/mnews">Mediasend</a></li> @endif
                        </ul>
                    </li>
                    @endif


                    @if( auth()->user()->roles['page14'] =='on' || auth()->user()->roles['page15'] =='on')
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-th-large"></i>Партнерский раздел</a>
                        <ul class="sub-menu children dropdown-menu">
                            @if( auth()->user()->roles['page14'] =='on')
                            <li><i class="menu-icon fa fa-group"></i><a href="/partner"> Партнеры</a></li>
                            @endif
                            @if( auth()->user()->roles['page15'] =='on')
                            <li><i class="menu-icon fa fa-cc-visa"></i><a href="/partner/invoice"> Счета на оплату</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if( auth()->user()->roles['page16'] =='on')
                    <li class="menu-item-has-children dropdown">
                        <a href="/notification" class="dropdown-toggle" > <i class="menu-icon fa fa-bell"></i>Уведомления</a>
                    </li>
                    @endif
 
                    @if(in_array(auth()->user()->ID, [5,18]))
                    <li class="menu-item-has-children dropdown">
                        <a href="/userroles/" class="dropdown-toggle"  > <i class="menu-icon fa fa-check-square-o"></i>Права пользователям</a>
                    </li>
                    @endif

                    @if(auth()->user()->ID ==18 || auth()->user()->ID == 5 || (isset(auth()->user()->roles['page20']) &&
                    auth()->user()->roles['page20'] =='on'))
                    <li class="menu-item-has-children dropdown">
                        <a href="/books/0" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-book"></i>Книги</a>
                    </li>
                    @endif

                    @if(auth()->user()->ID ==18 || auth()->user()->ID == 5 || auth()->user()->ID == 6288 || auth()->user()->ID == 12914)
                    <li class="menu-item-has-children dropdown     @if(isset($menu) && $menu=='video_editor')  show @endif" >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-play"></i>Видео</a>

                        <ul style="width: 100%;position: static;" class="sub-menu children dropdown-menu @if(isset($menu) && $menu=='video_editor')  show @endif">
                            <li><i class="menu-icon fa fa-file-video-o"></i><a href="/videos">Видео</a></li>
                            <li><i class="menu-icon fa fa-folder-open-o"></i><a href="/video_groups">Группы</a></li>
                            <li><i class="menu-icon fa fa-film"></i><a href="/video_playlists">Плейлисты</a></li>
                            <li><i class="menu-icon fa fa-list"></i><a href="/video_categories">Категории</a></li>

                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()->ID == 18 || auth()->user()->ID == 5 || (isset(auth()->user()->roles['page17']) &&
                    auth()->user()->roles['page17'] =='on'))
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-credit-card"></i>Кредиты</a>
                        <ul style="width: 100%;position: static;" class="sub-menu children dropdown-menu {{( (isset($menu) && $menu=='faqsinfo') || isset($menu) && $menu=='postinfo' || isset($menu) && $menu=='usersinfo' || isset($menu) && $menu=='criterias' || isset($menu) && $menu=='infobank' || isset($menu) && $menu=='card-debit' || isset($menu) && $menu=='card-credit' || isset($menu) && $menu=='insurance' || isset($menu) && $menu=='mortgage' || isset($menu) && $menu=='credit' || isset($menu) && $menu=='card' || isset($menu) && $menu=='deposit') ? 'show':''}}">
                            <li><i class="menu-icon fa fa-group "></i><a class="{{(isset($menu) && $menu == 'credit') ? 'active' : ''}}" href="/infobank/banks/credit">Кредит</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{((isset($menu) && $menu=='card-debit') || (isset($menu) && $menu=='card-credit') || (isset($menu) && $menu=='card')) ? 'active' : ''}}" href="/infobank/banks/card">Карты</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{(isset($menu) && $menu == 'deposit') ? 'active' : ''}}" href="/infobank/banks/deposit">Депозит</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{(isset($menu) && $menu == 'mortgage') ? 'active' : ''}}" href="/infobank/banks/mortgage">Ипотека</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{(isset($menu) && $menu == 'insurance') ? 'active' : ''}}" href="/infobank/banks/insurance">Страхование</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{(isset($menu) && $menu == 'criterias') ? 'active' : ''}}" href="/infobank/criterias">Критерии</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{(isset($menu) && $menu == 'usersinfo') ? 'active' : ''}}" href="/infobank/users">Пользователи</a></li>
                            <li><i class="menu-icon fa fa-group"></i><a class="{{(isset($menu) && $menu == 'faqsinfo') ? 'active' : ''}}" href="/infobank/faqs">Вопрос-ответ</a></li>

                        </ul>
                    </li>
                    @endif

                    @if( auth()->user()->ID ==18 || auth()->user()->ID ==5)
                    <li class="menu-item-has-children фцвывфцвфывцвфцфцвцвффццфцвфцвфыфцвфц">
                        <a href="/passwords" class="1dropdown-toggle" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-key"></i>Пароли</a>
                    </li>
                    @endif


                
               
                    <li class="menu-item-has-children dropdown  @if( (isset($menu) && $menu=='timetracking') || 
                                                                (isset($menu) && $menu=='timetrackingsetting')||
                                                                (isset($menu) && $menu=='videolearning') ||
                                                                (isset($menu) && $menu=='timetrackingenters')
                                                                || (isset($menu) && $menu=='timetrackingusercreate')
                                                                || (isset($menu) && $menu=='timetrackinganalytics')
                                                                || (isset($menu) && $menu=='timetrackingaccruals')
                                                                || (isset($menu) && $menu=='timetracking_hr')
                                                                || (isset($menu) && $menu=='timetrackingexam')
                                                                || (isset($menu) && $menu=='timetrackingqc') ||
                                                                (isset($menu) && $menu=='timetrackinguser') ||
                                                                (isset($menu) && $menu=='timetrackingreports')) show @endif">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="menu-icon fa fa-laptop"></i>Учет времени</a>
                        <ul class="sub-menu children dropdown-menu @if( (isset($menu) && $menu=='timetracking')
                            || (isset($menu) && $menu=='timetrackingsetting')
                            || (isset($menu) && $menu=='timetrackinguser')
                            || (isset($menu) && $menu=='timetrackingusercreate')
                            || (isset($menu) && $menu=='timetrackingreports')
                            || (isset($menu) && $menu=='timetrackingenters')
                            || (isset($menu) && $menu=='timetrackinganalytics')
                            || (isset($menu) && $menu=='timetrackingaccruals')
                            || (isset($menu) && $menu=='timetrackingexam')
                            || (isset($menu) && $menu=='timetrackingqc')
                            || (isset($menu) && $menu=='timetrackingtop')
                            || (isset($menu) && $menu=='timetrackingnps')
                            || (isset($menu) && $menu=='timetracking_hr')
                            || (isset($menu) && $menu=='fines')
                            || (isset($menu) && $menu=='videolearning')
                            || (isset($menu) && $menu=='info')
                            ) show @endif">

                            <li><i class="menu-icon fa fa-user"></i>
                                <a class="{{(isset($menu) && $menu == 'timetrackinguser') ? 'active' : ''}}" href="/">Мой профиль</a>
                            </li>
                            <li><i class="menu-icon fa fa-info-circle"></i><a class="{{(isset($menu) && $menu == 'info') ? 'active' : ''}}" href="/timetracking/info">Частые вопросы</a></li>
                            <li><i class="menu-icon fa fa-university"></i><a class="{{(isset($menu) && $menu == 'kk') ? 'active' : ''}}" target="_blank" href="/timetracking/kk">База знаний</a></li>
                            <li><i class="menu-icon fa fa-minus-circle"></i><a class="{{(isset($menu) && $menu == 'fines') ? 'active' : ''}}" href="/timetracking/fines">Депримирование</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a class="{{(isset($menu) && $menu == 'books') ? 'active' : ''}}" target="_blank" href="/timetracking/books">Книги</a></li>
                            <li>
                                <i class="menu-icon fa fa-play"></i>
                                <a class="{{(isset($menu) && $menu == 'videolearning') ? 'active' : ''}}" id="videomenu">Видео обучение</a>
                                <ul class="side-menu">
                                    <li><a href="/videolearning/1">Обучение продажам</a></li>
                                    <li><a href="/videolearning/2">Управление</a></li>
                                    <li><a href="/videolearning/5">Рекрутинг</a></li>
                                    <li><a href="/videolearning/6">РОПу</a></li>
                                </ul>
                            </li>
                            

                            
                            
                            
                            @if( isset(auth()->user()->roles['page21']) && auth()->user()->roles['page21'] =='on')
                                    @if(in_array(auth()->user()->ID, [5,18,157,84]))
                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackingtop') ? 'active' : ''}}" href="/timetracking/top">ТОП</a>
                                    </li>
                                    @endif

                                    

                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackingreports') ? 'active' : ''}}" href="/timetracking/reports">Табель</a>
                                    </li>
                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackingenters') ? 'active' : ''}}" href="/timetracking/reports/enter-report">Время прихода</a>
                                    </li>
                                 
                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetracking_hr') ? 'active' : ''}}" href="/timetracking/analytics">HR</a>
                                    </li>
                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackinganalytics') ? 'active' : ''}}" href="/timetracking/an">Аналитика</a>
                                    </li> 

                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackingaccruals') ? 'active' : ''}}" href="/timetracking/salaries">Начисления</a>
                                    </li>
                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackingexam') ? 'active' : ''}}" href="/timetracking/exam">Повышение квалификации</a>
                                    </li>
                                    <li>
                                        <i class="menu-icon fa fa-calendar"></i>
                                        <a class="{{(isset($menu) && $menu == 'timetrackingqc') ? 'active' : ''}}" href="/timetracking/quality-control">Контроль качества</a>
                                    </li>
                                    
                            @endif
                            
                            @if   ((isset(auth()->user()->roles['page22']) && auth()->user()->roles['page22'] =='on')
                                || (isset(auth()->user()->roles['persons']) && auth()->user()->roles['persons'] =='on'))
                            <li><i class="menu-icon fa fa-cogs"></i><a class="{{(isset($menu) && $menu == 'timetrackingsetting') ? 'active' : ''}}" href="/timetracking/settings">Настройки</a></li>
                            @endif

                        </ul>
                    </li>
                 
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->