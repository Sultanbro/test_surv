@extends('layouts.app')
@section('title', 'KPI')
@section('content')
<div class="old__content">
@if(auth()->user()->can('kpi_view'))
<kpi-pages 
    page="{{ $page }}"
    access="{{ auth()->user()->can('kpi_edit') ? 'edit' : 'view' }}"
>
@else
Нет доступа
@endif
</div>
@endsection

@section('scripts')

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 9rem !important;
}
}
</style>
@endsection
