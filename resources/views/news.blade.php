@extends('layouts.app')
@section('title', 'Новости')
@section('content')
    <div class="news-page">
        <news-pages
                page="{{ $page }}"
                access="{{ auth()->user()->can('news_edit') ? 'edit' : 'view' }}"
        ></news-pages>
        <birthday-feed></birthday-feed>
    </div>


@endsection

@section('styles')

    <link rel="stylesheet" href="{{ url('/css/news.css') }}">

@endsection
