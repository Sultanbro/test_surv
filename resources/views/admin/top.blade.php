@extends('layouts.admin')
@section('title', 'ТОП')
@section('content')

<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab">
                    <a class="nav-item nav-link active" id="nav-top-tab" href="/timetracking/top">TOП</a>
                    <a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports">Табель</a>
                    <a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports/enter-report">Время прихода</a>
                    <a class="nav-item nav-link" id="nav-profilex-tab" href="/timetracking/analytics">HR</a> 
                    @if(in_array(auth()->user()->ID, [5,18,157]))<a class="nav-item nav-link" id="nav-profile-tab" href="/timetracking/an">Аналитика</a>@endif
                    <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/salaries">Начисление</a>
                    <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/exam">Повышение квалификации</a>
                    <a class="nav-item nav-link" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a>
                </div>
            </nav>    
        </div>
        
        <div class="col-md-12 top-page">
            <page-top :data="{{json_encode($data)}}" activeuserid="{{json_encode(auth()->user()->ID)}}" />
        </div>
    </div>
</div>

@endsection