@extends('layouts.admin')
@section('head') <title>Edit Видео обучение - Админ панель </title> @endsection
@section('content')
<div class="col-lg-12">
    <h1 class="h3">Группы</h1>
</div>

<form class="col-lg-12" action="/video_groups/{{ $group->id}}" method="POST">
    <div class="row">
        <div class="col-lg-6">
            <label for="title">Название</label>
            <div class="form-group">
                <input type="text" class="form-control" value="{{ $group->title }}" name="title">
            </div>
        </div>   
        <div class="col-lg-6">
            <label for="category_id">Плейлист</label>
            <select name="category_id" class="form-control">
                    @if($group->category_id == 0) <option value="0" selected="true">Выберите плейлист</option> @endif
                    @foreach($playlists as $playlist)
                        <option value="{{ $playlist->id}}" @if($playlist->id == $group->category_id) selected="true" @endif>{{ $playlist->title }}</option>
                    @endforeach
            </select>
        </div>

        <div class="col-lg-6">

        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="parent_id">Родительская группа</label>
                <select name="parent_id" class="form-control">
                    <option value="0" @if($group->parent_id == 0) selected="true" @endif>Нет родительской группы</option>
                    @foreach($groups as $groupy)
                        <option value="{{ $groupy->id}}" @if($groupy->id == $group->parent_id) selected="true" @endif>{{ $groupy->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $group->id }}">
    {{ method_field('PATCH') }}
    {{ csrf_field() }}
</form>
@endsection