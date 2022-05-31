@extends('layouts.admin')
@section('title', 'Начисления')
@section('content')

<div class="animated fadeIn">
  <div class="row">
    <div class="col-md-12   mt-4 mb-3">
      <nav>
        <div class="nav nav-tabs" id="nav-tab">
        @if(auth()->user()->can['top_view'])<a class="nav-item nav-link" id="nav-top-tab" href="/timetracking/top">TOП</a>@endif
                        @if(auth()->user()->can['tabel_view'])<a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports" >Табель</a>@endif
                        @if(auth()->user()->can['entertime_view'])<a class="nav-item nav-link " id="nav-home-tab" href="/timetracking/reports/enter-report" >Время прихода</a>@endif
                        @if(auth()->user()->can['hr_view'])<a class="nav-item nav-link" id="nav-profilex-tab" href="/timetracking/analytics">HR</a> @endif
                        @if(auth()->user()->can['analytics_view'])<a class="nav-item nav-link" id="nav-profile-tab" href="/timetracking/an">Аналитика</a>@endif
                        @if(auth()->user()->can['salaries_view'])<a class="nav-item nav-link active" id="nav-salary-tab" href="/timetracking/salaries">Начисление</a>@endif
                        @if(auth()->user()->can['quality_view'])<a class="nav-item nav-link" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a>@endif
        </div>
      </nav>
    </div>
    <div class="col-md-12">
        <t-accrual :groupss="{{json_encode($groups)}}" :years="{{json_encode($years)}}" activeuserid="{{json_encode(auth()->user()->id)}}" :activeuserpos="{{json_encode(auth()->user()->position_id)}}"></t-accrual>
      </div>

  </div>
</div>

@endsection