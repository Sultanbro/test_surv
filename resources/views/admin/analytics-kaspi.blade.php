@extends('layouts.admin')

@section('content')

<div class="">
    <div class="row">
        <nav>
            <div class="nav nav-tabs" id="nav-tab">
                <a class="nav-item nav-link " id="nav-home-tab" href="/timetracking/reports">Табель</a>
                <a class="nav-item nav-link " id="nav-home-tab" href="/timetracking/reports/enter-report">Время
                    прихода</a>
                <a class="nav-item nav-link" id="nav-profile-tab" href="/timetracking/analytics">Аналитика
                    групп</a><a class="nav-item nav-link active" id="nav-profile-tab" href="/timetracking/analytics-kaspi">Аналитика
                    групп Каспи</a>
                <a class="nav-item nav-link " id="nav-salary-tab" href="/timetracking/salaries">Начисление</a>
                <a class="nav-item nav-link" id="nav-salary-tab" href="/timetracking/exam">Повышение квалификации</a>
            </div>
        </nav>
        <div class="col-md-12" style="overflow: hidden">
            <analytick-kaspi-component :groups="{{json_encode($groups)}}" activeuserid="{{json_encode(auth()->user()->id)}}">
            </analytick-kaspi-component>
        </div>


    </div>
</div>

@endsection