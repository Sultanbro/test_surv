@extends('layouts.admin')
@section('head') <title>Группы - Видео обучение - Админ панель </title> @endsection
@section('content')



<div class="col-lg-9">
    <h1 class="h3">Группы</h1>
</div>
<div class="col-lg-3 d-flex align-items-center">
    <a href="/video_groups/create" class="btn btn-primary ml-auto btn-p">
        Добавить
    </a>
</div>
<div class="col-lg-12">
    <table class="table table-admin">
        <tr>
            <th>№</th>
            <th>Название</th>
            <th>Плейлист</th>
            <th>Группа</th>
            <th></th>
        </tr>

        @foreach($groups as $group)
        <tr>
            <td>{{ $group->id }}</td>
            <td>{{ $group->title }}</td>
            
            <td>@if($group->playlist) {{ $group->playlist->title }} @endif</td>
            <td>{{ $group->parent ? $group->parent->title : '-'}}</td>
            <td>
                <a href="/video_groups/{{ $group->id }}/edit" class="btn btn-primary">
                    Edit
                </a>
                <a  class="btn btn-danger" onclick="show_delete({{ $group->id }})">
                    Delete
                </a>
            </td>
        </tr>
        @endforeach
    </table>

    
</div>
<div class="col-lg-12">
    {{ $groups->links()}}
</div>

<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
  <form class="modal-content" action="/" method="POST" id="deleteform">
    <div class="container">
      <h1>Потверждение удаления</h1>
      <p>Вы точно хотите удалить группу? </p>
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
      <div class="clearfix">
        <button type="button" class="cancelbtn" onclick="cancel_delete()">Отмена</button>
        <button type="button" class="deletebtn" onclick="submit()">Удалить</button>
      </div>
    </div>
  </form>
</div>


<script>
function show_delete(id) {
    document.getElementById('id01').style.display='block';
    document.getElementById("deleteform").action="/video_groups/" + id;
}
function cancel_delete() {
    document.getElementById('id01').style.display='none';
}
</script>
@endsection