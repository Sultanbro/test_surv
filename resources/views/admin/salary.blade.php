@extends('layouts.spa')
@section('title', 'Начисления')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "groupss": {{json_encode($groups)}},
        "years": {{json_encode($years)}},
        "activeuserid": "{{json_encode(auth()->user()->id)}}",
        "activeuserpos": {{json_encode(auth()->user()->position_id)}},
        "is_admin": {{ auth()->user()->is_admin == 1 && tenant('id') == 'bp' ? 'true' : 'false' }},
        "can_edit": {{ auth()->user()->can('salaries_edit') ? 'true' : 'false' }}
    }
</script>
@endsection

@section('scripts')
@endsection
