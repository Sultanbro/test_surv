@extends('layouts.app')
@section('title', 'Мои курсы')
@section('content')

<div class="old__content">
<my-course />
</div>
@endsection

@section('scripts')
<script src="/video_learning/playerjs.js" ></script>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 7rem !important; 
}
}
</style>
@endsection 