<template>
<div class="super-select">

    <div class="selected-items">
        <div class="selected-item">
            text
            <i class="fa fa-times"></i>
        </div>
    </div>
    
    <div class="search">
        <input type="text">
    </div>
    
    <div class="options-window">
        <div class="types">
            <div class="type">
                <i class="fa fa-user"></i>
                <div class="text"></div>
            </div>
            <div class="type">
                <i class="fa fa-user"></i>
                <div class="text"></div>
            </div>
            <div class="type">
                <i class="fa fa-user"></i>
                <div class="text"></div>
            </div>
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
