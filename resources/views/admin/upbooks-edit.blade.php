@extends('layouts.admin')
@section('title', 'Редактор книг')
@section('content')

<page-upbooks-edit token="{{ csrf_token() }}" access="edit" :can_edit="{{ auth()->user()->is_admin == 1 }}"/>

@endsection

@section('styles')
<style>

</style>
@endsection