<template>
<div class="super-select" ref="select" :class="posClass">

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
    
    <div class="show" v-if="show">
        <div class="search">
            <input 
                v-model="searchText"
                type="text"
                placeholder="Поиск..."
                ref="search"
                @change="onSearch">
        </div>
        
        <div class="options-window">
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
                    v-for="(option, index) in filtered_options"
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
            searchText: ''
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

        this.options = [
            {id: 1, type: 1, name: 'Text 1'},
            {id: 2, type: 1, name: 'Text Agl 1'},
            {id: 3, type: 1, name: 'Text Ali 1'},
            {id: 4, type: 1, name: 'Text Ruslan 1'},
            {id: 2, type: 2, name: 'Group Agl 1'},
            {id: 1, type: 2, name: 'Group 1'},
            {id: 4, type: 2, name: 'Group Ruslan 1'},
            {id: 3, type: 2, name: 'Group Ali 1'},
            {id: 1, type: 3, name: 'Pos 1' },
            {id: 2, type: 3, name: 'Pos Agl 1'},
            {id: 4, type: 3, name: 'Poss Ruslan 1'},
            {id: 3, type: 3, name: 'Pos Ali 1'},
        ];

        this.filtered_options = this.options;
    },
    methods: {
        toggleShow() {
            this.show = !this.show;
            console.log(this.$refs)
           
            this.$nextTick(() => {
                 this.$refs.search.focus();
                if(this.$refs.search) {
                   
                }
            });
            this.setPosClass();
        },

        setPosClass() {
            let pos = this.$refs["select"].getBoundingClientRect();
            let viewport_h = document.documentElement.clientHeight;
            this.posClass = (viewport_h - pos.top > 450) ? 'bottom' : 'top';
        },

        changeType(i) {
            this.type = i;
            this.filtered_options = this.options.filter((el, index) => {
                return el.type = i
            });
        },

        addValue(index) {
            let item = this.filtered_options[index];
            if(this.values.findIndex(v => v.id == item.id && v.type == this.type) == -1) {
           
                this.values.push({
                    name: item.name,
                    id: item.id,
                    type: this.type,
                });
            }
        },

        removeValue(i) {
            this.values.splice(i, 1);
        },

        onSearch() {
            this.filtered_options = this.options.filter((el, index) => {
                return el.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1
            });

            if(this.searchText == '') {
                this.filtered_options = this.options;
            }
        }
    },

}
</script>
