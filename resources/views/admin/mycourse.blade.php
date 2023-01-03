@extends('layouts.spa')
@section('title', 'Мои курсы')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "user_id": {{auth()->user()->getAuthIdentifier()}}
    }
</script>
@endsection

@section('scripts')
@endsection