@extends('layouts.admin')
@section('title', 'База знаний')
@section('content')

<page-kb :auth_user_id="{{ auth()->user()->ID }}"/>

@endsection 
@section('scripts')
@endsection