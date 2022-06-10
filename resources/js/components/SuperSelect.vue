<template>
<div class="super-select">

    <div class="selected-items noscrollbar" @click="toggleShow">
        <div class="selected-item">
            text
            <i class="fa fa-times"></i>
        </div>
    </div>
    
    <div class="search" v-if="show">
        <input type="text">
    </div>
    
    <div class="options-window" v-if="show">
        <div class="types">
            <div class="type">
                <div class="text">Сотрудники</div>
                <i class="fa fa-user"></i>
            </div>
            <div class="type">
                <div class="text">Отделы</div>
                <i class="fa fa-users"></i>
            </div>
            <div class="type">
                <div class="text">Должности</div>
                <i class="fa fa-briefcase"></i>
            </div>
        </div>
 
        <div class="options">

            <div class="option">Text</div>
            <div class="option selected">
                Text2
                <i class="fa fa-check"></i>
            </div>
            <div class="option">Text3</div>
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
            active_type: 1,
            show: false
        };
    },
    mounted() {
        // var ignoreClickOnMeElement = document.getElementById('wow-table');

        // var self = this;
        // document.addEventListener('click', function(event) {
        //     var isClickInsideElement = ignoreClickOnMeElement.contains(event.target);
        //     if (!isClickInsideElement) {
        //         self.hideContextMenu();
        //     }
        // });
    },
    methods: {
        toggleShow() {
            this.show = !this.show;
        }
    },

}
</script>
