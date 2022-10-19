@extends('layouts.app')
@section('title', 'Мой профиль')
@section('content')

<div class="intro content">
	<new-intro-top />
	<new-intro-stats />
	<new-intro-smart-table />
</div>

<new-profit />
<new-courses />
<new-trainee-estimation />
<new-compare-indicators />


@endsection


@section('popups')
<popup-award />
<popup-kpi />
<popup-balance />
<popup-bonuses />
<popup-nominations />
@endsection

