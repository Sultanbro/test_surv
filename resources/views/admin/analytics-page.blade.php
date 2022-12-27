@extends('layouts.spa')
@section('title', 'Аналитика')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "groups": {{json_encode($groups)}},
        "activeuserid": {{json_encode(auth()->user()->id)}}
    }
</script>
@endsection
@section('scripts')
<<<<<<< HEAD
=======

<style>
.header__profile {
    display:none !important;
}
    .table  .form-control{
        height: auto!important;
        padding: 0 10px !important;
        border: none !important;
        background-color: transparent !important;
    }
.table  .form-control:active{
    border: none !important;
    background-color: transparent !important;
    box-shadow: none!important;
}
.table  .form-control:focus{
    border: none !important;
    background-color: transparent !important;
    box-shadow: none!important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 9rem !important;
}
}
</style>
>>>>>>> main
@endsection
