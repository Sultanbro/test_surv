@extends('layouts.admin')
@section('head') <title>Новая категория - Админ панель </title> @endsection
@section('content')
<div class="col-lg-12">
    <h1 class="h3">Новая категория</h1>
</div>

<form class="col-lg-12" action="/video_categories" method="POST">
    <div class="row">
        <div class="col-lg-6">
            <label for="title">Название</label>
            <div class="form-group">
                <input type="text" class="form-control" value="" name="title">
            </div>
        </div>   
        <div class="col-lg-12">
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
    
    {{ csrf_field() }}
</form>
@endsection