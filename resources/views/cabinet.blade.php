@extends('layouts.admin')
@section('title', 'Настройка кабинета')
@section('content')

<cabinet auth_role="{{$authRole['is_admin']}}" :auth_id="{{$authRole['id']}}"/>

@endsection