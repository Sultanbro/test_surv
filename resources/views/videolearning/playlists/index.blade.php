@extends('layouts.admin')
@section('title', 'Плейлисты - Видео обучение')
@section('content')
<page-playlists token="{{ csrf_token() }}" :can_edit="{{ auth()->user()->is_admin == 1 ? 'true' : 'false'}}" />
@endsection

@section('scripts')

@endsection
