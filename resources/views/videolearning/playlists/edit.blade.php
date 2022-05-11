@extends('layouts.admin')
@section('title', 'Плейлист - Видео обучение')
@section('content')
<page-playlist-edit token="{{ csrf_token() }}" :id="{{ $playlist_id}}" />
@endsection

@section('scripts')
<script src="/video_learning/playerjs.js" ></script>
@endsection
