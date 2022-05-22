<aside class="right-menu">

<div class="top">

<div class="btn-rm">
    <i class="fa fa-question"></i>
</div>

<div class="btn-rm">
    <i class="fa fa-bell"></i>
</div>

<div class="btn-rm">
    <i class="fa fa-search"></i>
</div>

 
</div>
<div class="chat noscrollbar">

    @for($i = 1;$i<=10;$i++)
    <div class="btn-rm">
        <img src="/static/images/avatars/{{ rand(1,10) }}.png" alt="avatar">
    </div>
    @endfor
</div>
</aside>