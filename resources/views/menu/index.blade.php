
<div id="sidebar">
           
 <a href="{{ url('/home') }}" class="logo">logo</a>
 <strong>Меню</strong>
     

<ul class="custom-accordion">
    <li class="active"><a href="/account/contacts" class="item-1 root-item-selected">Контакты</a></li>
    <li class="root-item">
        
        <!-- <a href="/account/forwards/" class="opener-ac item-2 pener-ac ">
            <span>Рассылка</span>
        </a> -->
        
        <a href="/sms/list" class="opener-ac item-2 pener-ac ">
            <span>Рассылка</span>
        </a>

        <div class="slide-ac">
            <ul>
                <li>
                    <a href="{{ URL::to('/autocalls')}}">Голосовая Рассылка</a>
                     <a href="{{ URL::to('/autocalls-new')}}">Создать Новую Голосовую Рассылку</a>
                       <a href="/sms/list">SMS рассылка</a>
                </li>
            </ul>
        </div>
    </li>    

    <li class="root-item"><a href="/account/statistics/" class="opener-ac item-3 pener-ac "><span>Статистика</span></a>
        <div class="slide-ac">
        <ul>

                            <li><a href="/account/statistics/sms-forwards">SMS рассылка</a></li>

                            <li><a href="/account/statistics/sms">Интеграция SMS</a></li>

                            <li><a href="/account/statistics/sip">SIP звонки</a></li>

            </ul></div></li>    

                    <li class="root-item"><a href="/account/integration/" class="opener-ac item-4 pener-ac "><span>Интеграция</span></a>
                <div class="slide-ac">
                        <ul>

                            <li><a href="/account/integration/voice">Голосовые интеграции</a></li>

                            <li><a href="/account/integration/sms">SMS интеграции</a></li>

            </ul></div></li>    

                    <li class="root-item"><a href="/account/settings/" class="opener-ac item-5 pener-ac "><span>Настройки</span></a>
                <div class="slide-ac">
                        <ul>

                            <li><a href="/account/settings/profile">Профиль</a></li>

                            <li><a href="/account/settings/transactions">Денежные транзакции</a></li>

    </ul></div></li>
</ul>
<a class="opener" href="#"><span>+ View More</span><em>- View Less</em></a>           
</div>

