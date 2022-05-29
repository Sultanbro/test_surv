@extends('layouts.admin')

@section('content')
<div class="animated fadeIn">
    <div class="row">
        <div class="col-lg-12">
            <table id="report-table-new" class="table table-striped table-bordered" data-url="/bonus" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Почта</th>
                    <th>Имя</th>

                </tr>
                </thead>
                <tbody>

                @foreach($users as $key => $user)

                <tr>
                  <td>{{$user->id}}</td>
                  <td><a href="/userroles/update/{{$user->id}}">{{$user->email}}</a></td>
                  <td>{{$user->NAME}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
