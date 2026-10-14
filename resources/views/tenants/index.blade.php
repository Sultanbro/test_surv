@extends('layouts.tenant')
@section('title', 'Настройка кабинета')
@section('content')

<div class="container my-3">
    <div class="row">

        <div class="card col-12 mb-3">
        <div class=" d-flex justify-content-between my-2">
            Мои проекты
       
        @if(auth()->id() == 1)
        <a class="btn btn-primary " href="/projects/create">Создать</a>
        @endif
        </div>
        </div>
       
        <div class="col-12 card mb-3">
            <table class="table">
                <tr>
                    <th>Субдомен проекта</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            
                @foreach($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->id }}</td>
                    <td>{{ $tenant->created_at }}</td>
                    <td>
                        @if(auth()->id() == $tenant->global_id)
                        <a class="btn btn-primary mr-2" href="/projects/edit/{{ $tenant->id }}">Изменить</a>
                        @endif
                        <a class="btn btn-primary" href="/enter/{{ $tenant->id }}">Войти</a>
                    </td>
                </tr>

                 @endforeach
            </table>
        </div>
     
    </div>
    
</div>

@endsection