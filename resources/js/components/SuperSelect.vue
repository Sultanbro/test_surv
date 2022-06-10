<template>
<div class="super-select" ref="select" :class="posClass" v-click-outside="show = false">

    <div class="selected-items" @click="toggleShow">
        <div 
            v-for="(value, i) in values"
            :key="i"
            class="selected-item"
            :class="'value' + value.type">
            {{ value.name }}
            <i class="fa fa-times" @click.stop="removeValue(i)"></i>
        </div>
    </div>
    
    <div class="show">
        <div class="search" v-if="show" ref="search">
            <input type="text">
        </div>
        
        <div class="options-window" v-if="show">
            <div class="types">
                <div class="type">
                    <div class="text" @click="changeType(1)">Сотрудники</div>
                    <i class="fa fa-user"></i>
                </div>
                <div class="type">
                    <div class="text" @click="changeType(2)">Отделы</div>
                    <i class="fa fa-users"></i>
                </div>
                <div class="type">
                    <div class="text" @click="changeType(3)">Должности</div>
                    <i class="fa fa-briefcase"></i>
                </div>
            </div>
    
            <div class="options">

                <div 
                    v-for="(option, index) in options"
                    :key="index"
                    @click="addValue(index)"
                    class="option selected" 
                >
                    {{ option.name }}
                    <!-- <i class="fa fa-check"></i> -->
                </div>

            </div>
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
            options: [],
            type: 1,
            show: false,
            posClass: 'top',
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

        this.users = [
            {id: 1, name: 'Text 1'},
            {id: 2, name: 'Text Agl 1'},
            {id: 3, name: 'Text Ali 1'},
            {id: 4, name: 'Text Ruslan 1'},
        ];

        this.groups = [
            {id: 2, name: 'Group Agl 1'},
            {id: 1, name: 'Group 1'},
            {id: 4, name: 'Group Ruslan 1'},
            {id: 3, name: 'Group Ali 1'},
        ];

        this.positions = [
            {id: 1, name: 'Pos 1'},
            {id: 2, name: 'Pos Agl 1'},
            {id: 4, name: 'Poss Ruslan 1'},
            {id: 3, name: 'Pos Ali 1'},
        ];

        this.options = this.users;
    },
    methods: {
        toggleShow() {
            this.show = !this.show;
            this.$refs.search.focus();
            this.setPosClass();
        },

        setPosClass() {
            let pos = this.$refs["select"].getBoundingClientRect();
            let viewport_h = document.documentElement.clientHeight;
            this.posClass = (viewport_h - pos.top > 450) ? 'bottom' : 'top';
        },

        changeType(i) {
            if(i == 1) this.options = this.users;
            if(i == 2) this.options = this.groups;
            if(i == 3) this.options = this.positions;
        },

        addValue(index) {
            let item = this.options[index];
            if(this.values.findIndex(v => v.id == item.id && v.type == item.type) == -1) {
                console.log(item)
                this.values.push({
                    name: item.name,
                    id: item.id,
                    type: this.type,
                });
            }
        },

        removeValue(i) {
            this.values.splice(i, 1);
        }
    },

}
</script>
