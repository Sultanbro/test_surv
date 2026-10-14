@extends('layouts.spa')
@section('title', 'ТОП')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "data":{{json_encode($data)}},
        "activeuserid":{{json_encode(auth()->id())}}
    }
</script>
@endsection
@section('scripts')
@endsection
