@extends('layouts.admin')
@section('title', 'Редактор книг')
@section('content')

<page-upbooks-edit token="{{ csrf_token() }}" access="edit" />

@endsection

@section('styles')
<style>

</style>
@endsection