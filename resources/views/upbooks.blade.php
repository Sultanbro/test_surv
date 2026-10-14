@extends('layouts.spa')
@section('title', 'Редактор книг')
@section('content')
<script type="application/json" id="async-page-data">
    {
        "token": "{{ csrf_token() }}",
        "can_edit": {{ auth()->user()->can('books_edit') ? 'true' : 'false' }}
    }
</script>
@endsection

@section('styles')
@endsection

@section('scripts')
@endsection
