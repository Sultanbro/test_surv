<aside class="right-menu">

<div class="top">

<div class="btn-rm">
    <i class="fa fa-question"></i>
</div>
  
<div class="btn-rm" id="toggle_panel">
    <i class="fa fa-bell"></i>
    <i class="fa fa-bell belly" style="display:none;"></i>
     <div class="blink-notification" style="border-color: rgb(0, 242, 246); position: absolute; inset: 0px; border-radius: 50%;z-index: -1;"></div>
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
    @if(auth()->user()->id == 13865) 

    
    <div id="dance" style="display:none">
    @for($i = 1;$i<=30;$i++)
    
        <div class="btn-rm" style="filter:hue-rotate({{ $i * 157 }}deg);overflow:hidden" >
            <img src="/users_img/{{ auth()->user()->img_url }}" alt="avatar" style="animation: 1.5s {{ $i / 5  }}s bell ease infinite;position:absolute;width:80px;height:140px;left:0" >
        </div>
    
    @endfor
    </div>
    @endif

    @php
        $a = \App\User::where('img_url', '!=', '')->inRandomOrder()->select('img_url', 'name', 'last_name')->where('id', '!=', 4444)->limit(15)->get();
    @endphp
    @foreach($a as $b)
    <div class="btn-rm">
        <img src="/users_img/{{ $b->img_url }}" title="{{ $b->name . ' ' . $b->last_name}}" alt="avatar">
    </div>
    @endforeach
</div>
</aside>