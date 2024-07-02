@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="row">
            <div class="col-lg-12">
                <table id="report-table-new" class="table table-striped table-bordered" data-url="/bonus" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Почта</th>
                        <th>Имя</th>
                        <th>Бонус</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->bonus}}</td>
                            <td><a href="/bonus/update/{{$user->id}}">Добавить бонус</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
