@extends('layouts.app')
@section('title', 'Настройка кабинета')
@section('content')

<div class="old__content">
<cabinet auth_role="{{ auth()->user() }}" />
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
}
}
</style>
@endsection
