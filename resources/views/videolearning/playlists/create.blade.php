@extends('layouts.admin')
@section('head') <title>Плейлист - Видео обучение - Админ панель </title> @endsection
@section('content')
<div class="col-lg-12">
    <h1 class="h3">Новый Плейлист</h1>
</div>
<form class="col-lg-12" action="/video_playlists" method="POST">
<div class="row">
        <div class="col-lg-6">
            <label for="title">Название</label>
            <div class="form-group">
                <input type="text" class="form-control" value="" name="title" required>
            </div>
        </div>   
        <div class="col-lg-6">
            <div class="form-group"> 
                <label for="playlist_id">Категория</label>
                <select name="category_id" class="form-control">
                    @foreach($categories as $category)
                        <option value="{{ $category->id}}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group"> 
                <label for="playlist_id">Текст</label>
                <textarea name="text" class="form-control" required></textarea>
            </div>
        </div>

        <div class="col-lg-12">
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
    {{ csrf_field() }}
    
</div>
</form>
@endsection