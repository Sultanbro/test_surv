@extends('layouts.admin')

@section('content')
<div class="">
    <div class="row">
        <div class="col-lg-12">
            Добавить книгу
            <form method="post" enctype='multipart/form-data'>
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="title">Заголовок </label>
                    <input required name="title" type="text" class="form-control" id="title" required value="">
                </div>


                <div class="form-group">
                    <label for="small_text">Описание книги</label>
                    <textarea  name="desk" class="form-control editor2" id="small_text" rows="3" ></textarea>
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
