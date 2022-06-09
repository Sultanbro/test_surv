@extends('layouts.admin')
@section('title', 'Плейлисты - Видео обучение')
@section('content')
<page-playlists token="{{ csrf_token() }}" />
@endsection

@section('scripts')
<script src="/video_learning/playerjs.js" ></script>
@endsection
