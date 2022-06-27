@extends('layouts.admin')
@section('title', 'Настройка кабинета')
@section('content')

<cabinet auth_role="{{json_encode($authRole)}}" />

@endsection