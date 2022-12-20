@extends('layouts.app')
@section('title', 'Редактор книг')
@section('content')

<div class="old__content">
<page-upbooks token="{{ csrf_token() }}" :can_edit="{{ auth()->user()->can('books_edit') ? 'true' : 'false' }}"/>
</div>
@endsection
 
@section('styles')
<style>

</style>
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
