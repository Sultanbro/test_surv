@extends('layouts.tenant')
@section('title', 'Настройкв')
@section('content')

<div class="container my-3">
    <div class="row">
    <div class="card col-12 mb-3">
    <div class=" d-flex justify-content-between my-2">
            Редактировать проект
       
            <a class="btn btn-primary " href="/projects">Назад</a>
     
        </div>
        </div>

        <div class="col-12 card mb-3">
    <form action="/projects/update" method="POST" class="my-3">
        @csrf
        <input type="text" name="id" value="{{ $id }}" placeholder="субдомен" class="form-control">

        <button class="btn btn-primary mt-3" type="submit">Сохранить</button>

    </form>
</div>
</div>
</div>

@endsection