<aside class="right-menu">

<div class="top">

<div class="btn-rm">
    <i class="fa fa-question"></i>
</div>
  
<div class="btn-rm" id="toggle_panel">
    <i class="fa fa-bell"></i>
    @if($unread > 0)
    <div class="nn-number" style="display:none">
       
        <div class="pulse"></div>
        <div class="marker"></div>
    </div>
    @endif
</div>





    @if(!empty(auth()->user()->getCheckList->toArray()))
        <auth-check-list auth_check_list="{{json_encode(auth()->user()->getCheckList)}}" :open_check="{{ 0}}"></auth-check-list>
    @endif
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