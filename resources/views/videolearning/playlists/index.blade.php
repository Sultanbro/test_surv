@extends('layouts.admin')
@section('title', 'Плейлисты - Видео обучение')
@section('content')
<page-playlists token="{{ csrf_token() }}" can_edit="{{ auth()->user()->is_admin }}" />
@endsection

@section('scripts')
<script src="/video_learning/playerjs.js" ></script>
@endsection
