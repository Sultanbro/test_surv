<template>
    <div class="regenerate">
        <div class="regenerate-title">Принудительно обновление всех сертификатов</div>
        <div class="regenerate-desc">Energistically customize accurate paradigms via market positioning mindshare. Dynamically
            pontificate collaborative action items with user friendly leadership skills.
        </div>
        <b-button variant="danger">Обновить все сертификаты</b-button>
    </div>
</template>

<script>
    export default {
        name: 'regenerate-certificates',
        props: {
            award: {
                type: Object,
                default: null
            },
            courses: {
                type: Array,
                default: []
            }
        },
        data() {
            return {}
        },
        mounted() {

        },
        methods: {}
    }
</script>