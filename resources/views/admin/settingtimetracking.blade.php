@extends('layouts.spa')
@section('title', 'Настройки')
@section('content')
<script type="application/json" id="async-page-data">
    {
    @if($active_tab == 1 && (auth()->user()->can('users_view') || auth()->user()->can('settings_view')))
        "is_admin": {{ auth()->user()->is_admin == 1 ? 'true' : 'false' }},
        "subdomain": "{{ tenant('id') }}",
        "positions": {{ json_encode(\App\Position::all()) }}
    @endif
    @if($active_tab == 2 && (auth()->user()->can('positions_view') || auth()->user()->can('settings_view')))
        "positions": {{json_encode($positions)}}
    @endif
    @if($active_tab == 3 && (auth()->user()->can('groups_view') || auth()->user()->can('settings_view')))
        "statuseses": {{json_encode($groups)}},
        "archived_groupss": {{json_encode($archived_groups)}},
        "book_groups": {{json_encode($book_groups)}},
        "corpbooks": {{json_encode($corpbooks)}},
        "activeuserid": {{json_encode(auth()->user()->id)}}
    @endif
    @if($active_tab == 4 && (auth()->user()->can('fines_view') || auth()->user()->can('settings_view')))
    @endif
    @if($active_tab == 5 && (auth()->user()->can('notification_view') || auth()->user()->can('settings_view')))
        "groups_with_id": {{json_encode($groupsWithId) }},
        "users": {{json_encode($tab5['users']) }},
        "positions": {{json_encode($tab5['positions']) }}
    @endif
    @if($active_tab == 6 && (auth()->user()->can('permissions_view') || auth()->user()->can('settings_view')))
    @endif
    @if($active_tab == 7 && (auth()->user()->can('checklists_view') || auth()->user()->can('settings_view')))
    @endif
    @if($active_tab == 8 && auth()->user()->is_admin == 1)
    @endif
    @if($active_tab == 9 && auth()->user()->is_admin == 1)
    @endif
    }
</script>
@endsection

@section('scripts')
@endsection

