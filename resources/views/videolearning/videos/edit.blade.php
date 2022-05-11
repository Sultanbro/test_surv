@extends('layouts.admin')
@section('head') <title>Видео обучение - Админ панель </title> @endsection
@section('content')
<div class="col-lg-12">
    <h1 class="h3">Видео #{{ $video->id }}</h1>
</div>
<div class="col-lg-6 mb-2">
    <div id="video"></div>
</div>
<form class="col-lg-6" action="/videos/upload" method="POST" enctype="multipart/form-data" name="filer">
<div class="fallback mb-3">
        <input name="file" type="file" id="file"  />
    </div>
    <div>
        <p><b>Ссылка</b></p>
        <p id="link_text">Не загружено видео</p>
        <p id="remaining_time"></p>
        <p id="status"> 
            <span></span>
            <svg style="display:none;" width="50px" height="50px" viewBox="0 0 100 100">
                <g transform="rotate(0 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(30 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(60 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(90 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(120 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(150 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(180 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(210 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(240 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(270 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(300 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
                </rect>
                </g><g transform="rotate(330 50 50)">
                <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#46b4ed">
                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
                </rect>
                </g>
            </svg>
        </p>
        <div class="form-group">
            <input type="text" class="form-control" value="" id="folder" name="folder" placeholder="Папка загрузки на удаленном сервере">
        </div>
    </div>
    {{ csrf_field() }}
</form>

<form class="col-lg-12 mt-2" action="/videos/{{ $video->id}}" method="POST">
    <div class="row">
        <div class="col-lg-6">
            <label for="title">Название</label>
            <div class="form-group">
                <input type="text" class="form-control" value="{{ $video->title }}" name="title">
            </div>
        </div>
        <div class="col-lg-6">
            <label for="title">Ссылка (Ручной ввод URL)</label>
            <div class="form-group">
                <input type="text" class="form-control" value="{{ $video->links }}" name="links" id="link">
            </div>
        </div>   
        <div class="col-lg-6">
            <div class="form-group">
                <label for="playlist_id">Плейлист</label>
                <select name="playlist_id" class="form-control">
                    @foreach($playlists as $playlist)
                        <option value="{{ $playlist->id}}" @if($playlist->id == $video->playlist_id) selected="true" @endif>{{ $playlist->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="form-group">
                <label for="group_id">Группа</label>
                <select name="group_id" class="form-control">
                    <option value="0">Выберите группу</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id}}" @if($group->id == $video->group_id) selected="true" @endif>{{ $group->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="playlist_id">Текст</label>
                <textarea name="text" class="form-control"></textarea>
            </div>
        </div>
        <div class="col-lg-6">
            <label for="title">Длительность (Пока не автоматизировано)</label>
            <div class="form-group">
                <input type="number" class="form-control" value="{{ $video->duration }}" name="duration">
            </div>
        </div>
        <div class="col-lg-12">
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
    <input type="hidden" value="{{ $video->id }}" name="id">  
    {{ method_field('PATCH') }}
    {{ csrf_field() }}
</form>
@endsection
@section('scripts')
<script>
var player = new Playerjs({ 
  id:"video",
  poster: '',
  file:'{{ $video->links }}',
}); 
</script>
@endsection