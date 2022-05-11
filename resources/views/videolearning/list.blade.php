@extends('layouts.admin')
@section('head')
<title>{{ $cat }}</title>
@endsection
@section('content')


<div>
    <div class="lp">
        <h1 class="page-title">Категории</h1>
        @foreach($cats as $category)
        <div class="section d-flex aic jcsb my-2">
            <a href="/videolearning/{{ $category->id }}">{{ $category->title }}</a>
        </div>
        @endforeach
      </div>

        <div class="row pt-3">

            @if($playlists->count() == 0)
            <div class="col-lg-8">
                <div class="block">
                    <h1 class="title" style="padding: 0">{{ $cat }}</h1>
                    В категории нет плейлистов
                </div>
            </div>
            @else 
            <div class="col-lg-12">
                <div class="block  ">
                    <h1 class="title">{{ $cat }}</h1>


                    @foreach($playlists as $playlist)
                    <a href="/videolearning/playlists/{{ $playlist->id }}" class="article">
                        <div class="left">
                            @if($playlist->poster() == '')
                            <img src="/video_learning/noimage.png" alt="" class="img-fluid" />
                            @else
                            <img src="{{ $playlist->poster() }}" alt="" class="img-fluid" />
                            @endif
                        </div>
                        <div class="right">
                            <p class="cat">{{ $playlist->category->title }}</p>
                            <h2>{{ $playlist->title }}</h2>
                            <p>{{ $playlist->text }}</p>
                        </div>
                    </a>
                    @endforeach

                    <div>
                        {{ $playlists->links()}}
                    </div>

                </div>
            </div>
    
        </div>

    </div>


    @endif
    @endsection