@extends('layouts.spa')
@section('title', 'ОКК')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "groups": {{ json_encode($groups) }},
        "active_group": "{{ $group_id }}",
        "check": "{{ $check }}",
        "user": {{ json_encode(auth()->user()) }}
    }
</script>
@endsection

@section('scripts')
@endsection
