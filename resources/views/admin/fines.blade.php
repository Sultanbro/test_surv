@extends('layouts.spa')
@section('title', 'Депремирование')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "fines": {{json_encode($fines)}}
    }
</script>
@endsection

@section('styles')
@endsection

@section('scripts')
@endsection
