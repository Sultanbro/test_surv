@extends('layouts.admin')
@section('title', 'Категории - Видеообучение') 
@section('content')


<div class="mt-3"></div>
<div class="col-lg-9">
    <h1 class="h3" style="font-size: 20px">Категории</h1>
</div>
<div class="col-lg-3 d-flex align-items-center">
    <a href="/video_categories/create" class="btn btn-primary ml-auto btn-p">
        Добавить
    </a>
</div>
<div class="col-lg-8">
    <table class="table table-admin">
        <tr>
            <th>№</th>
            <th>Название</th>
            <th></th>
        </tr>

        @foreach($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->title }}</td>
            <td>
                <a href="/video_categories/{{$category->id}}/edit" class="btn btn-primary mb-2">
                Редактировать
                </a>
                <a class="btn btn-danger" onclick="show_delete({{ $category->id }})">
                Удалить

                </a>
            </td>
        </tr>
        @endforeach
    </table>

    
</div>
<div class="col-lg-12">
    {{ $categories->links()}}
</div>


<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
  <form class="modal-content" action="/" method="POST" id="deleteform">
    <div class="container">
      <h1>Потверждение удаления</h1>
      <p>Вы точно хотите удалить видео? </p>
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
    document.getElementById("deleteform").action="/video_categories/" + id;
}
function cancel_delete() {
    document.getElementById('id01').style.display='none';
}
</script>
@endsection