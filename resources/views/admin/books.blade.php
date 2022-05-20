@extends('layouts.admin')
@section('title', 'База знаний')
@section('content')

<page-kb :auth_user_id="{{ auth()->user()->ID }}" can_edit="{{ auth()->user()->is_admin }}"/>

@endsection 
@section('scripts')
@endsection