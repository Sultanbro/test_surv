@extends('layouts.spa')
@section('title', 'Табель')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "activeTab": "nav-home-tab",
        "groups": {{json_encode($groups)}},
        "fines": {{json_encode($fines)}},
        "years": {{json_encode($years)}},
        "can_edit": true,
        "activeuserid": {{json_encode(auth()->user()->id)}},
        "activeuserpos": {{json_encode(auth()->user()->position_id)}}
    }
</script>
@endsection
@section('scripts')
@endsection
