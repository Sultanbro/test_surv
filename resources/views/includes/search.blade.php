
<div class="container g-padding-y-80--xs g-padding-y-125--sm">
<h4>Результаты поиска: <i>{{$query}} </i></h4>
<h5>Обнаружено: {{$total}} совпадений</h5>

 @foreach($autocall_search as $search)
 <h4 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md">
{{$search->name}}</h4>
<h4 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md">
{{$search->description}}</h4>
 <p class="g-font-size-18--xs"> <span class="glyphicon glyphicon-calendar"></span> {{$search->created_at ->format('d m Y')}}</p>
</div>  
<h4 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md">
{{$search->status}}</h4>             
<br>
<a  href="{{ URL::to('show/'.$search->id) }}" button type="button" class="text-uppercase s-btn s-btn--sm s-btn--primary-bg g-radius--50 g-padding-x-50--xs">Смотреть дальше</a></button>
</div>
@endforeach    
{!!$autocall_search->links() !!}
</div>