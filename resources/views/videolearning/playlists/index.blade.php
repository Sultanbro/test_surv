@extends('layouts.admin')
@section('title', 'Плейлисты - Видео обучение') 
@section('content')

<div class="mt-3"></div>

<div class="col-lg-9">
    <h1 class="h3" style="font-size: 20px">Плейлисты</h1>
</div>
<div class="col-lg-3 d-flex align-items-center">
    <a href="/video_playlists/create" class="btn btn-primary ml-auto btn-p">
        Добавить
    </a>
</div>
<div class="col-lg-8">
    <table class="table table-admin" style="border-radius: 5px;">
        <tr>
            <th>№</th>
            <th>Постер</th>
            <th>Название</th>
            <th>Категория</th>
            <th></th>
        </tr>

        @foreach($playlists as $playlist)
        <tr>
            <td>{{ $playlist->id }}</td>
            <td class="poster_count">
                @if($playlist->poster() == '')
                <img src="/video_learning/noimage.png" alt="" class="img-fluid"/>
                @else
                <img src="{{ $playlist->poster() }}" alt="" class="img-fluid"/>
                @endif

                <span>{{ $playlist->videos->count()}}</span>
            </td>
            <td>{{ $playlist->title }}</td>
            <td>{{ $playlist->category->title }}</td>
            <td>
                <a href="/video_playlists/{{$playlist->id}}/edit" class="btn btn-primary mb-2">
                    Редактировать
                </a>
                <a class="btn btn-danger" onclick="show_delete({{ $playlist->id }})">
                    Удалить
                </a>
            </td>
        </tr>
        @endforeach
    </table>

    
</div>
<div class="col-lg-12">
    {{ $playlists->links()}}
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
    document.getElementById("deleteform").action="/video_playlists/" + id;
}
function cancel_delete() {
    document.getElementById('id01').style.display='none';
}
</script>

@endsection