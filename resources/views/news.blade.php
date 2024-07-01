@extends('layouts.app')
@section('title', 'Новости')
@section('content')
    <script type="application/json" id="async-page-data">
        {
            "page": "{{ $page }}",
            "access": "{{ auth()->user()->can('news_edit') ? 'edit' : 'view' }}"
        }
    </script>
@endsection

@section('styles')

    <!-- <link rel="stylesheet" href="{{ url('/css/news.css') }}"> -->

@endsection
