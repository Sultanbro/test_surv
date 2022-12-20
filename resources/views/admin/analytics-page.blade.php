@extends('layouts.app')
@section('title', 'Аналитика')
@section('content')

<div class="old__content">
    <div class="row">
        <div class="col-md-12 mt-4 mb-3">
            <nav>
                <div class="nav nav-tabs" id="nav-tab">

                    @if(auth()->user()->can('top_view'))<li class="nav-item"><a class="nav-link" id="nav-top-tab" href="/timetracking/top">TOП</a></li>@endif
                    @if(auth()->user()->can('tabel_view'))<li class="nav-item"><a class="nav-link" id="nav-home-tab" href="/timetracking/reports" >Табель</a></li>@endif
                        @if(auth()->user()->can('entertime_view'))<li class="nav-item"><a class="nav-link " id="nav-home-tab" href="/timetracking/reports/enter-report">Время прихода</a></li>@endif
                        @if(auth()->user()->can('hr_view'))<li class="nav-item"><a class="nav-link" id="nav-profilex-tab" href="/timetracking/analytics">HR</a></li>@endif
                        @if(auth()->user()->can('analytics_view'))<li class="nav-item"><a class="nav-link active" id="nav-profile-tab" href="/timetracking/an">Аналитика</a></li>@endif
                        @if(auth()->user()->can('salaries_view'))<li class="nav-item"><a class="nav-link " id="nav-salary-tab" href="/timetracking/salaries">Начисление</a></li>@endif
                        @if(auth()->user()->can('quality_view'))<li class="nav-item"><a class="nav-link" id="nav-quality-tab" href="/timetracking/quality-control">ОКК</a></li>@endif
                </div>
            </nav>    
        </div>
        
        <div class="col-md-12 analytics">
            <analytics-page :groups="{{json_encode($groups)}}" activeuserid="{{json_encode(auth()->user()->id)}}"></analytics-page>
        </div>
    </div>
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
