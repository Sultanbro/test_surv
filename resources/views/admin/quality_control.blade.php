@extends('layouts.admin')
@section('title', 'ОКК')
@section('content')

<div class="">
  <div class="row">
  
    <div class="col-md-12 mt-4 mb-3">
      <nav>
        <div class="nav nav-tabs" id="nav-tab">
        @if(auth()->user()->can('top_view'))<a class="nav-item nav-link" id="nav-top-tab" href="/timetracking/top">TOП</a>@endif
        @if(auth()->user()->can('tabel_view'))<a class="nav-item nav-link" id="nav-home-tab" href="/timetracking/reports" >Табель</a>@endif
        @if(auth()->user()->can('entertime_view'))<a class="nav-item nav-link " id="nav-home-tab" href="/timetracking/reports/enter-report" >Время прихода</a>@endif
        @if(auth()->user()->can('hr_view'))<a class="nav-item nav-link" id="nav-profilex-tab" href="/timetracking/analytics">HR</a> @endif
        @if(auth()->user()->can('analytics_view'))<a class="nav-item nav-link" id="nav-profile-tab" href="/timetracking/an">Аналитика</a>@endif
        @if(auth()->user()->can('salaries_view'))<a class="nav-item nav-link " id="nav-salary-tab" href="/timetracking/salaries">Начисление</a>@endif
        @if(auth()->user()->can('quality_view'))<a class="nav-item nav-link active" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a>@endif
        </div>
      </nav>
  

    </div>
    <div class="col-md-12">
     <t-quality :groups="{{ json_encode($groups)}}" active_group="{{ $group_id }}" check="{{ $check }}" user="{{ json_encode(auth()->user()) }}"></t-quality>

    </div>
    
  </div>
</div>

@endsection