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

@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 9rem !important;
}
}
</style>
>>>>>>> main
@endsection
