@extends('layouts.admin')
@section('title', 'Видеофайлы - Видеообучение') 
@section('content')

<div class="mt-3"></div>
<div class="col-lg-6">
    <h1 class="h3"  style="font-size: 20px">Видео файлы</h1>
</div>
<div class="col-lg-3">
    <select name="playlist_id" id="playlists" onchange="selectPlaylist()" class="form-control form-control-sm">
        @foreach($playlists as $pl)
        <option value="{{$pl->id}}" @if(isset($_GET['pl']) && $_GET['pl'] == $pl->id) selected @endif>{{$pl->title}}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-3 d-flex align-items-center">
    <a href="/videos/create" class="btn btn-primary ml-auto btn-p">
        Добавить
    </a>
</div>
<div class="col-lg-8">
    <table class="table table-admin">
        <tr>
            <th>№</th>
            <th>Видео</th>
            <th>Название</th>
            <th>Группа</th>
            <th>Плейлист</th>
            <th></th>
        </tr>

        @foreach($videos as $video)
        <tr>
            <td>{{ $video->id }}</td>
            <td class="poster_count">
                @if($video->poster == '')
                <img src="/video_learning/noimage.png" alt="" class="img-fluid"/>
                @else
                <img src="{{ $video->poster }}" alt="" class="img-fluid"/>
                @endif
            </td>
            <td>{{ $video->title }}</td>
            <td>@if($video->group){{ $video->group->title }}@endif</td>
            <td>@if($video->playlist){{ $video->playlist->title }}@endif</td>
            <td>
                <a href="/videos/{{$video->id}}/edit" class="btn btn-primary mb-2">
                    Редактировать
                </a>
                <a class="btn btn-danger" onclick="show_delete({{ $video->id }})">
                    Удалить
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="col-lg-12">
    {{ $videos->appends(request()->input())->links()}}
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
    document.getElementById("deleteform").action="/videos/" + id;
}
function cancel_delete() {
    document.getElementById('id01').style.display='none';
}
</script>
<script>
function selectPlaylist() {
    let el = document.getElementById('playlists');
 
    let val = el.value;

    window.location.href = "/videos" + '?pl='+ val;
}
</script>
@endsection