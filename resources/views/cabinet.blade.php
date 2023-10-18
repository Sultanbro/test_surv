@extends('layouts.spa')
@section('title', 'Настройка кабинета')
@section('content')

<script type="application/json" id="async-page-data">
    {
        "auth_role": {{ auth()->user() }}
    }
</script>
@endsection

@section('scripts')
@endsection
