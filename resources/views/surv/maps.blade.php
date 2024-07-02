@extends('layouts.spa')
@section('title', 'Карта Мира')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "json": {{json_encode($maps_array)}}
    }
</script>
@endsection

@section('scripts')
@endsection
@section('styles')
@endsection




