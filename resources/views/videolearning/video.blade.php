@extends('layouts.admin')
@section('head')
<title>@if(isset($video)) {{ $video->title }} - @endif {{ $playlist->title }}</title>
@endsection
@section('content')


<div>
    <div class="lp">
        <h1 class="page-title">Категории</h1>
        @foreach($cats as $category)
        <div class="section d-flex aic jcsb my-2">
            <a href="/videolearning/{{ $category->id }}">{{ $category->title }}</a>
        </div>
        @endforeach
    </div>


    <div class="row pt-3">

        @if(!isset($video))
        <div class="col-lg-12">
            <div class="block">
                В плейлисте нет видео
            </div>
        </div>
        @else
        <div class="col-lg-8">
            <div class="block  br">
                <div id="player"></div>
                <div class="description">
                    <a class="cat"
                        href="/videolearning/{{ $playlist->category->id }}">#{{ $playlist->category->title }}</a>
                    <p class="js_group">{{ $video->group_info }}</p>
                    <h1 class="js_title">{{ $video->title }}</h1>
                    @if($videos->count() > 1)<button class="views js_next btn btn-primary mb-2 mt-1" id="showNextVideo"
                        data-next='{{ $videos[1]->id }}'>След. видео:
                        <span>{{ $videos[1]->title }}</span></button>@endif
                    <p class="views js_views">{{ $video->views }} просмотров</p>
                    <div class="info bordero paddingo mt-3">
                        <p class="title">{{ $playlist->title }}</p>
                        <p>{{ $playlist->text }}</p>
                    </div>
                    <div class="text js_text">
                        {{ $video->text }}
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="block ">
                <div id="accordion">

                    @foreach($groups as $group)
                    <!--  -->
                    <div class="card card-{{ $group->id }}">
                        <div class="card-header" id="heading{{ $group->id }}">
                            <h5 class="mb-0">
                                <button class=" btn-block btn btn-link collapsed" data-toggle="collapse"
                                    data-target="#collapse{{ $group->id }}" aria-expanded="true"
                                    aria-controls="collapse{{ $group->id }}">
                                    {{ $group->title }} <svg viewBox="0 0 284.929 284.929" width="5px">
                                        <path d="M282.082,195.285L149.028,62.24c-1.901-1.903-4.088-2.856-6.562-2.856s-4.665,0.953-6.567,2.856L2.856,195.285
                                    C0.95,197.191,0,199.378,0,201.853c0,2.474,0.953,4.664,2.856,6.566l14.272,14.271c1.903,1.903,4.093,2.854,6.567,2.854
                                    c2.474,0,4.664-0.951,6.567-2.854l112.204-112.202l112.208,112.209c1.902,1.903,4.093,2.848,6.563,2.848
                                    c2.478,0,4.668-0.951,6.57-2.848l14.274-14.277c1.902-1.902,2.847-4.093,2.847-6.566
                                    C284.929,199.378,283.984,197.188,282.082,195.285z"></path>
                                    </svg>
                                </button>
                            </h5>
                        </div>

                        <div id="collapse{{ $group->id }}" class="collapse @if($loop->index == 0) show @endif"
                            aria-labelledby="heading{{ $group->id }}" data-parent="#accordion">
                            <div class="card-body">

                                @if($group->groups->count() > 0)
                                <div id="accordionsub{{ $group->id }}">
                                    @foreach($group->groups as $subgroup)
                                    <div class="card card-{{ $subgroup->id }}">
                                        <div class="card-header" id="heading{{ $subgroup->id }}">
                                            <h5 class="mb-0">
                                                <button class=" btn-block btn btn-link" data-toggle="collapse"
                                                    data-target="#subcollapse{{ $subgroup->id }}" aria-expanded="true"
                                                    aria-controls="subcollapse{{ $subgroup->id }}">
                                                    {{ $subgroup->title }} <svg viewBox="0 0 284.929 284.929"
                                                        width="5px">
                                                        <path d="M282.082,195.285L149.028,62.24c-1.901-1.903-4.088-2.856-6.562-2.856s-4.665,0.953-6.567,2.856L2.856,195.285
                                    C0.95,197.191,0,199.378,0,201.853c0,2.474,0.953,4.664,2.856,6.566l14.272,14.271c1.903,1.903,4.093,2.854,6.567,2.854
                                    c2.474,0,4.664-0.951,6.567-2.854l112.204-112.202l112.208,112.209c1.902,1.903,4.093,2.848,6.563,2.848
                                    c2.478,0,4.668-0.951,6.57-2.848l14.274-14.277c1.902-1.902,2.847-4.093,2.847-6.566
                                    C284.929,199.378,283.984,197.188,282.082,195.285z"></path>
                                                    </svg>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="subcollapse{{ $subgroup->id }}"
                                            class="collapse @if($loop->index == 0) show @endif"
                                            aria-labelledby="heading{{ $subgroup->id }}"
                                            data-parent="#accordionsub{{ $group->id }}">
                                            <div class="card-body">

                                                @foreach($subgroup->videos as $vid)
                                                <div class="videolink" data-id="{{ $vid->id }}">
                                                    <span>{{ $vid->title }}</span>
                                                    <span>{{ $vid->duration }}</span>
                                                </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>

                                    @endforeach
                                </div>
                                @foreach($group->videos as $vid)
                                <div class="videolink" data-id="{{ $vid->id }}">
                                    <span>{{ $vid->title }}</span>
                                    <span>{{ $vid->duration }}</span>
                                </div>
                                @endforeach
                                @else

                                @foreach($group->videos as $vid)
                                <div class="videolink" data-id="{{ $vid->id }}">
                                    <span>{{ $vid->title }}</span>
                                    <span>{{ $vid->duration }}</span>
                                </div>
                                @endforeach

                                @endif




                            </div>
                        </div>
                    </div>
                    <!--  -->
                    @endforeach


                </div>

                <!--  -->
                <!-- <div class="info bordero paddingo mt-3">
      <p class="title">{{ $playlist->title }}</p>
      <p>{{ $playlist->text }}</p>
    </div> -->

            </div>
        </div>

        <div class="col-lg-12">
            <p class="title mb-2">Обсуждение видеоурока</p>
        </div>
        <div class="col-lg-9">
            <div class="info">
                <div class="form-group">
                    <textarea name="comment" id="comment" class="form-control"
                        placeholder="Что думаете после просмотра видеоурока? (Минимум 30 символов, чтобы донести свои мысли)"></textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div>
                <div class="btn btn-primary btn-svg mb-2" id="add_comment">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" fill="#039be5" r="12" />
                        <path
                            d="m5.491 11.74 11.57-4.461c.537-.194 1.006.131.832.943l.001-.001-1.97 9.281c-.146.658-.537.818-1.084.508l-3-2.211-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953z"
                            fill="#fff" />
                    </svg>
                    Отправить
                </div>
                <div id="count"></div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="bordero info paddingo bg-blue">
                <div class="comments">

                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@section('scripts')
<script src="/video_learning/playerjs.js"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

@if(isset($video))
video_id = {{ $video->id}}
getComments(video_id);

var player = new Playerjs({
    id: "player",
    poster: '{{ $video->poster }}',
    file: '{{ $video->links }}',
});

var comments = []
var videos = {
    @foreach($videos as $v) {{ $v->id }}: {
        id: '{{ $v->id }}',
        title: '{{ $v->title }}',
        text: '{{ $v->text }}',
        duration: '{{ $v->duration }}',
        group: '{{ $v->group_info }}',
        poster: '{{ $v->poster }}',
        file: '{{ $v->links }}',
        views: '{{ $v->views }} просмотров',
        prev: '{{ $v->prev ? $v->prev : 0 }}',
        next: '{{ $v->next ? $v->next : 0 }}'
    },
    @endforeach
}


$('.videolink').click(function() {
    let id = $(this).data('id');
    getVideo(id)
});

$('#showNextVideo').click(function() {
    let next_id = $(this).data('next');
    getVideo(next_id)
});



@endif

function getVideo(id) {
    player = new Playerjs({
        id: "player",
        poster: videos[id].poster,
        group: videos[id].group,
        file: videos[id].file,
    });

    $('.js_title').text(videos[id].title);
    $('.js_group').text(videos[id].group);
    $('.js_text').text(videos[id].text);

    next_btn = $('.js_next')


    if (videos[id].next != 0 && videos[videos[id].next] !== undefined) {
        next_btn.show()
        next_btn.text('След. видео: ' + videos[videos[id].next].title);
        next_btn.data('next', videos[videos[id].next].id);
    } else {
        next_btn.hide()
    }

    iterateViews(id);
    getComments(id);
    video_id = id
}
/////////////////////////////////////////////////
// AJAX VIDEO VIEWS COUNTER



function iterateViews(id) {
    var formData = new FormData();
    formData.append('id', id);

    $.ajax({
        url: '/videos/views',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(result) {
            $('.js_views').text(result);
        },
        error: function(data) {
            console.log('error');
        }
    });
}

function getComments(id) {
    var formData = new FormData();
    formData.append('id', id);

    $.ajax({
        url: '/videos/get_comment',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(result) {
            appendComments(result)
        },
        error: function(data) {
            console.log('error');
        }
    });
}

function addComments(id, text) {
    var formData = new FormData();
    formData.append('id', id);
    formData.append('text', text);

    $.ajax({
        url: '/videos/add_comment',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(result) {
            appendComments(result)
        },
        error: function(data) {
            console.log('error');
        }
    });
}

function appendComments(result) {

    $('.comments').empty()
    result.forEach(el => {

        let html = '<div class="com">';
        html += '   <div class="d-flex justify-content-between mb-2">';
        html += '       <div class="name">' + el.user + '</div>';
        html += '       <div class="date">' + el.created_at + '</div>';
        html += '   </div>';
        html += '   <pre>' + el.text + '</pre>'
        html += '</div>';

        $('.comments').append(html)
    });



}

commentBlock = $('#comment')
counterBlock = $('#count')
$('#add_comment').click(function() {
    if (commentBlock.val().length < 30) {
        commentBlock.focus();
        counterBlock.text(commentBlock.val().length + ' / 30');
    } else {
        addComments(video_id, commentBlock.val())
        commentBlock.val('')
        counterBlock.text('')
    }

})
</script>
<style>
.card .btn-block svg {
    width: 20px;
    height: 12px;
    position: absolute;
    right: 14px;
    top: 17px;
    transform: rotate(0deg);
    transition: 0.3s ease all;
}

.card .btn-block {
    width: 100%;
    padding-right: 25px;
    position: relative;
}

.card .btn-block.collapsed svg {
    transform: rotate(180deg);
}

.card-header {
    background-color: #062d43;
    border-bottom: 1px solid #076599;


}

.card-header:first-child {
    border-radius: 0
}

.card-header .btn {
    color: #fff;
    font-weight: 400;
}

.card-header .btn svg {
    fill: #fff
}

.card-body {
    padding: 0 15px;
}

.card-body .card-header {
    background: #f0f2f5;
    margin: 0 -15px;
    border-bottom: 1px solid #e7e7e7;
}

.card-body .card-header .btn {
    color: #000;
}

.card-body .card-header .btn svg {
    fill: #000;
}

.js_group {
    font-size: 14px;
    font-weight: 600;
}

textarea.form-control {
    border: 1px solid #f1f1f1;
}

.com {
    font-size: 13px;
    padding-bottom: 10px;
    padding-top: 10px;
    border-bottom: 1px solid #f1f1f1;
}

.com .date,
.com .name {
    font-weight: 600;
    color: #045e92;
}

.bg-blue {
    background: #f5fcff;
    border: 1px solid #e2f6ff;
}

.info.paddingo {
    padding-top: 0;
    padding-bottom: 0;
}

#count {
    font-weight: 600;
    color: #045e92;
    font-size: 13px;
    margin-bottom: 15px;
}

pre {
    font-family: 'Open Sans';
    display: block;
    font-size: 13px;
    white-space: pre-wrap;
    color: #212529;
}
</style>

@endsection