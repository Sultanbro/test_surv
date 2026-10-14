@if(Auth::user())


<div class="panel">
        <strong>Колл Трекинг</strong>
        <a href="#" class="exit">exit</a>
        <span class="time">{{ date('H:i') }}</span>
        <span class="balanse">Баланс: <em>30 000 Т.</em></span>
        <span class="id"><em>Ваш ID: </em>{{Auth::user()->id}}</span>
        <div class="info-hidden">
            <strong>info</strong>
            <div class="info-hidden-drop">
                <span class="id"><em>Ваш ID: </em>{{Auth::user()->id}}</span>
                <span class="balanse">Баланс: <em>30 000 Т.</em></span>
            </div>
        </div>
        <div class="panel-logo">
            <div>
                <img src="/images/logo-1.png" alt="logo">
            </div>
        </div>
    </div>

@else
@endif