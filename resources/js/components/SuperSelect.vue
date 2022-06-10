<template>
<div class="super-select">

    <div class="items">

    </div>
    
    <div class="search">

    </div>
    
    <div class="options-window">
        <div class="types">

        </div>

        <div class="options">

            <div class="option">Text</div>
            <div class="option">Text2</div>
            <div class="option">Text3</div>
            
        </div>
    </div>

</div>
</template>

<script>
export default {
    props: {
        values: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            groups: [],
            positions: [],
            users: [],
            active_type: 1
        };
    },
    mounted() {
    },
    methods: {

    },

}
</script>
