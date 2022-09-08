@extends('layouts.admin')
@section('title', 'KPI')
@section('content')

@if(auth()->user()->can('kpi_view'))
<kpi-pages 
    page="{{ $page }}"
    :access="{{ auth()->user()->can('kpi_edit') ? 'edit' : 'view' }}"
>
@else
Нет доступа
@endif

@endsection