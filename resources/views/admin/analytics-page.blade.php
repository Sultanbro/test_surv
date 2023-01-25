@extends('layouts.spa')
@section('title', 'Аналитика')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "groups": {{json_encode($groups)}},
        "activeuserid": {{json_encode(auth()->user()->id)}},
        "isadmin": {{json_encode(auth()->user()->is_admin)}}
    }
</script>
@endsection
@section('scripts')
@endsection
