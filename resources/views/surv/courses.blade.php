@extends('layouts.app')
@section('title', 'Курсы')
@section('content')
<div class="old__content">
<page-courses />      
</div>
@endsection
@section('scripts')

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 7rem !important;
    padding-right: 6rem !important;
}
}
</style>
@endsection
