@extends('layouts.app')
@section('title', 'Плейлисты - Видео обучение')
@section('content')

<div class="old__content">
    <page-playlists 
    token="{{ csrf_token() }}" 
    :can_edit="{{ auth()->user()->can('videos_edit') ? 'true' : 'false'}}"
    :category="{{ isset($category) ? $category : 1 }}"
    :playlist="{{ isset($playlist) ? $playlist : 0 }}"
    :video="{{ isset($video) ? $video : 0 }}" 
    />
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
