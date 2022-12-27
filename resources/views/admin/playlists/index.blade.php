@extends('layouts.spa')
@section('title', 'Плейлисты - Видео обучение')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "token": "{{ csrf_token() }}",
        "can_edit": "{{ auth()->user()->can('videos_edit') ? 'true' : 'false'}}",
        "category": "{{ isset($category) ? $category : 1 }}",
        "playlist": "{{ isset($playlist) ? $playlist : 0 }}",
        "video": "{{ isset($video) ? $video : 0 }}"
    }
</script>
@endsection

@section('scripts')
@endsection
