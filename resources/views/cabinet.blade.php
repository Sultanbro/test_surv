@extends('layouts.admin')
@section('title', 'Настройка кабинета')
@section('content')




<cabinet authRole::{{ 'admin' }} />

@endsection