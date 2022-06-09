@extends('layouts.admin')
@section('head') <title>Новая группа - Админ панель </title> @endsection
@section('content')
<div class="col-lg-12">
    <h1 class="h3">Новая группа</h1>
</div>

<form class="col-lg-12" action="/video_groups" method="POST">
    <div class="row">
        <div class="col-lg-6">
            <label for="title">Название</label>
            <div class="form-group">
                <input type="text" class="form-control" value="" name="title">
            </div>
        </div>   
        <div class="col-lg-6">
            <div class="form-group">
                <label for="playlist_id">Плейлист</label>
                <select name="category_id" class="form-control">
                    @foreach($playlists as $playlist)
                        <option value="{{ $playlist->id}}">{{ $playlist->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">

        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="parent_id">Родительская группа</label>
                <select name="parent_id" class="form-control">
                    <option value="0" selected="true">Нет родительской группы</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id}}">{{ $group->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
    
    {{ csrf_field() }}
</form>
@endsection