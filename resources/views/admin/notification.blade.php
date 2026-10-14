@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <form action="" method="POST" enctype="multipart/form-data" id="notification-form">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header"><strong>Уведомление</strong></div>
                    <div class="card-body card-block">

                        <div class="row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Заголовок</label>
                                    <input type="text" name="title" required="" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Изображение</label>
                                    <input type="file" name="image" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Сообщение</label>
                                    <textarea class="form-control" name="message" maxlength="1000" rows="5" cols="5" placeholder="Введите текст сообщения" required=""></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Отправить почту</label>
                                    <input type="checkbox" name="email" class="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Тип</label>
                                    <select name="type">
                                        <option value="normal">Обычный</option>
                                        <option value="important">Важный</option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" id="preview-field" name="preview" class="form-control">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-lg btn-info btn-block">
                                Создать уведомление
                            </button>
                            <button type="button" id="notification-preview" name="preview" class="btn btn-lg btn-info btn-block">
                                Предварительный просмотр почты
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-12">
            <table id="report-table-new" class="table table-striped table-bordered" data-url="/bonus" style="width:100%">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Сообщение</th>
                    <th>Тип</th>
                    <th>Прочитали</th>
                    <th>Получили письмо</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($all_notifications as $notification)
                    <tr>
                        <td>{{$notification->created_at}}</td>
                        <td>{{$notification->user->email}}</td>
                        <td>{{$notification->message}}</td>
                        <td>{{$notification->type=='important'?'Важный':'Обычный'}}</td>
                        <td>{{$notification->read}}/{{$notification->total_sent}}</td>
                        <td>{{$notification->emails_sent}}</td>
                        <td><a href="/notification/delete/{{$notification->id}}">Удалить</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
