@extends('layouts.app')
@section('title', 'База знаний')
@section('content')

<div class="old__content">
<page-kb :auth_user_id="{{ auth()->user()->id }}" :can_edit="{{ auth()->user()->can('kb_edit') ? 'true' : 'false'}}"/>
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