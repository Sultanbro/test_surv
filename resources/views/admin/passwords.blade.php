@extends('layouts.admin')

@section('content')
<div class="">
    <div class="row">
        <div class="col-lg-12">
            <form method="post">
            	{{ csrf_field() }}
            	<label for="login">{{$access->page}}</label>
            	<input placeholder="login..." type="text" name="login" value="{{$access->login}}" id="login">
            	<input placeholder="password..." type="text" name="password" value="{{$access->password}}">
            	<input type="hidden" name="page" value="{{$access->page}}">
            	<input type="hidden" name="id" value="{{$access->id}}">
            	<input type="submit" value="Сохранить">
            </form>
        </div>
    </div>
</div>
@endsection