@extends('layouts.admin')
@section('title', 'Редактор книг')
@section('content')

<page-upbooks token="{{ csrf_token() }}" :can_edit="{{ auth()->user()->can('books_edit') ? 'true' : 'false' }}"/>

@endsection
 
@section('styles')
<style>

</style>
@endsection