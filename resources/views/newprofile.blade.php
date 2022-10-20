@extends('layouts.app')
@section('title', 'Мой профиль')
@section('content')

<div class="intro content">
	<new-intro-top></new-intro-top>
	<new-intro-stats></new-intro-stats>
	<new-intro-smart-table></new-intro-smart-table>
</div>

<new-profit></new-profit>
<new-courses></new-courses>
<new-trainee-estimation></new-trainee-estimation>
<new-compare-indicators></new-compare-indicators>


@endsection


@section('popups')
<popup-award></popup-award>
<popup-kpi></popup-kpi>
<popup-balance></popup-balance>
<popup-bonuses></popup-bonuses>
<popup-nominations></popup-nominations> 
@endsection

