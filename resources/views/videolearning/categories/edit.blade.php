@extends('layouts.admin')
@section('head') <title>Категория - Видео обучение - Админ панель </title> @endsection
@section('content')
<div class="col-lg-12">
    <h1 class="h3">Категория # {{ $category->id }}</h1>
</div>
<form class="col-lg-12" action="/video_categories/{{ $category->id}}" method="POST">
<div class="row">
        <div class="col-lg-6">
            <label for="title">Название</label>
            <div class="form-group">
                <input type="text" class="form-control" value="{{ $category->title }}" name="title">
            </div>
        </div> 
        <div class="col-lg-12">
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
    <input type="hidden" value="{{ $category->id }}" name="id">  
    {{ method_field('PATCH') }}
    {{ csrf_field() }}
    
</div>
</form>
@endsection