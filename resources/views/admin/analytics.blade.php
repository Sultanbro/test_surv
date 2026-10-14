@extends('layouts.spa')
@section('title', 'HR Аналитика')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "groups": {{json_encode($groups)}},
        "activeuserid": {{json_encode(auth()->user()->id)}}
    }
</script>
@endsection

@section('scripts')
@endsection
