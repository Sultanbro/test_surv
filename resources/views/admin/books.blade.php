@extends('layouts.admin')
@section('title', 'База знаний')
@section('content')

<page-kb :auth_user_id="{{ auth()->user()->id }}" :can_edit="{{ auth()->user()->can('kb_edit') ? 'true' : 'false'}}"/>

@endsection 
@section('scripts')
@endsection