@extends('layouts.admin')
@section('title', 'Плейлисты - Видео обучение')
@section('content')
<page-playlists 
token="{{ csrf_token() }}" 
:can_edit="{{ auth()->user()->is_admin == 1 ? 'true' : 'false'}}"
:category="{{ isset($category) ? $category : 1 }}"
:playlist="{{ isset($playlist) ? $playlist : 0 }}"
:video="{{ isset($video) ? $video : 0 }}" 
/>
@endsection

@section('scripts')

@endsection
