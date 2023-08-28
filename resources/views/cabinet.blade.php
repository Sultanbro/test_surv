@extends('layouts.spa')
@section('title', 'Настройка кабинета')
@section('content')
<script src="https://api-maps.yandex.ru/2.1/?apikey=782ab94b-9310-410b-84b0-9c942252cc65&lang=ru_RU" type="text/javascript"></script>

<script type="application/json" id="async-page-data">
    {
        "auth_role": {{ auth()->user() }}
    }
</script>
@endsection

@section('scripts')
@endsection
