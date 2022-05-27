@extends('layouts.admin')
@section('title', 'Аналитика')
@section('content')

<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12 mt-4 mb-3">
            <nav>
                <div class="nav nav-tabs" id="nav-tab">
                    @if(auth()->user()->ID == 18 || auth()->user()->ID == 5)
                        <a class="nav-item nav-link" id="nav-top-tab" href="/timetracking/top">TOП</a>
                    @endif
                    <a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports">Табель</a>
                    <a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports/enter-report">Время прихода</a>
                    <a class="nav-item nav-link active" id="nav-profilex-tab" href="/timetracking/analytics">HR</a> 
                    <a class="nav-item nav-link" id="nav-profile-tab" href="/timetracking/an">Аналитика</a>
                    <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/salaries">Начисление</a>
                    <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/exam">Повышение квалификации</a>
                    <a class="nav-item nav-link" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a>
                </div>
            </nav>    
        </div>
        
        <div class="col-md-12 analytics">
            <analytics :groups="{{json_encode($groups)}}" activeuserid="{{json_encode(auth()->user()->ID)}}"></analytics>
        </div>
    </div>
</div>

@endsection