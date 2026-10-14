@extends('layouts.app')
@section('title', 'База знаний')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "auth_user_id": {{ auth()->user()->id }},
        "can_edit": {{ auth()->user()->can('kb_edit') ? 'true' : 'false'}}
    }
</script>
@endsection
@section('scripts')
@endsection