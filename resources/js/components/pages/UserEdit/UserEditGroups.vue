<script>
import ProfileGroups from '@/components/profile/ProfileGroups' // настройки user

export default {
    name: 'UserEditGroups',
    components: {
        ProfileGroups,
    },
    props: {
        user: {
            type: Object,
            default: null
        },
        groups:{
            type: Array,
            default: () => []
        },
        in_groups:{
            type: Array,
            default: () => []
        },
    }
}
</script>
<template>
    <div
        id="iphones3"
        class="mb-3 xfade none-block"
    >
        <h5 class="mt-4">Группы</h5>
        <ProfileGroups
            v-if="user"
            :groups="groups"
            :user_id="user.id"
            :in_groups="in_groups"
        />
        <select
            v-else
            name="group"
            id="group"
            class="form-control"
        >
            <option>Выберите отдел</option>
            <option
                v-for="group in groups"
                :key="group.id"
                :value="group.id"
            >{{group.name}}</option>
        </select>
    </div>
</template>