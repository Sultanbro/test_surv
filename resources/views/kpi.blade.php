@extends('layouts.spa')
@section('title', 'KPI')
@section('content')
@if(auth()->user()->can('kpi_view'))
<script type="application/json" id="async-page-data">
    {
        "page": "{{ $page }}",
        "access": "{{ auth()->user()->can('kpi_edit') ? 'edit' : 'view' }}"
    }
</script>
@else
<script type="application/json" id="async-page-data">
    {
        "page": "none",
        "access": ""
    }
</script>
@endif
@endsection

@section('scripts')
@endsection
