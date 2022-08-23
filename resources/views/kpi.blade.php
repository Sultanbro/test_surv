@extends('layouts.admin')
@section('title', 'KPI')
@section('content')


@if(auth()->id() == 5)
Никто не тройгайте. 
Запрещено для редактирования Али, Руслан, Куаныш.
<div class="mt-2"></div>
<kpi-pages page="{{ $page }}">

@else
<div class="p-3">
<h2>Страница в разработке.</h2>
<p>Прошу подождать пока она не будет готова.</p>
</div>

@endif
@endsection