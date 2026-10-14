@extends('layouts.spa')
@section('title', 'Сотрудник')
@section('content')

<script type="application/json" id="async-page-data">
    {
        "csrf": "{{ csrf_token() }}",
        "user": {{isset($user) ? json_encode($user) : 'null'}},
        "groups": {{json_encode($groups)}},
        "positions": {{json_encode($positions)}},
        "programs": {{json_encode($programs)}},
        "workingDays": {{json_encode($workingDays)}},
        "workingTimes": {{json_encode($workingTimes)}},
        "errors": {{ json_encode($errors->all()) }},
        "fire_causes": {{ isset($fire_causes) ? json_encode($fire_causes) : '[]' }},
        "auth_identifier": "{{auth()->user()->getAuthIdentifier()}}",
        "old_name": "{{old('name')}}",
        "old_last_name": "{{old('last_name')}}",
        "old_email": "{{old('email')}}",
        "old_birthday": "{{old('birthday')}}",
        "old_phone": "{{old('phone')}}",
        "old_phone_1": "{{old('phone_1')}}",
        "old_phone_2": "{{old('phone_2')}}",
        "old_phone_3": "{{old('phone_3')}}",
        "old_phone_4": "{{old('phone_4')}}",
        "old_zarplata": "{{old('zarplata')}}",
        "old_kaspi_cardholder": "{{old('kaspi_cardholder')}}",
        "old_kaspi": "{{old('kaspi')}}",
        "old_card_kaspi": "{{old('card_kaspi')}}",
        "old_jysan_cardholder": "{{old('jysan_cardholder')}}",
        "old_jysan": "{{old('jysan')}}",
        "old_card_jysan": "{{old('card_jysan')}}",
        "head_in_groups": {{ isset($user) ? json_encode($user->head_in_groups) : '[]' }},
        "in_groups": {{ isset($user) ? json_encode($user->in_groups) : '[]' }},
        "profile_contacts": {{ isset($user) ? json_encode($user->profileContacts) : 'null' }},
        "taxes": {{ isset($user) ? json_encode($user->taxes) : 'null' }}
    }
</script>
@endsection


@section('scripts')
@endsection

@section('styles')
@endsection