@extends('layouts.spa')
@section('title', 'Время прихода')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "groups": {{json_encode($groups)}},
        "years": {{json_encode($years)}},
        "activeuserid": {{json_encode(auth()->user()->id)}}
    }
</script>
@endsection
@section('scripts')
@endsection
