<template>
<div class="super-select" ref="select" :class="posClass" v-click-outside="close">
    <div class="selected-items flex-wrap noscrollbar" @click="toggleShow">
        <div 
            v-for="(value, i) in valueList"
            :key="i"
            class="selected-item"
            :class="'value' + value.type">
            {{ value.name }}
            <i class="fa fa-times" @click.stop="removeValue(i)" v-if="!one_choice_made"></i>
        </div>
        <div 
            id="placeholder"
            class="selected-item placeholder">
            {{ placeholder }}
        </div>
    </div>
    <div class="show" v-if="show">
        <div class="search">
            <input 
                v-model="searchText"
                type="text"
                placeholder="Поиск..."
                ref="search"
                @keyup="onSearch()">
        </div>
        
        <div class="options-window">
            <div class="types"> 
                <div class="type" v-if="disable_type !== 1" :class="{'active': type == 1}" @click="changeType(1)" >
                    <div class="text">Сотрудники</div>
                    <i class="fa fa-user"></i>
                </div>
                <div class="type" v-if="disable_type !== 2" :class="{'active': type == 2}" @click="changeType(2)">
                    <div class="text" >Отделы</div>
                    <i class="fa fa-users"></i>
                </div>
                <div class="type" v-if="disable_type !== 3" :class="{'active': type == 3}" @click="changeType(3)" >
                    <div class="text">Должности</div>
                    <i class="fa fa-briefcase"></i>
                </div>

                <div class="type mt-5 active all" v-if="select_all_btn && !single" @click="selectAll">
                    <div class="text">Все</div>
                    <i class="fa fa-check"></i>  
                </div>
            </div>
            
    
            <div class="options">

                <div 
                    class="option"
                    v-for="(option, index) in filtered_options"
                    :key="index"
                    @click="addValue(index)"
                    :class="{'selected': option.selected}" 
                >
                    <i class="fa fa-user" v-if="option.type == 1"></i> 
                    <i class="fa fa-users" v-if="option.type == 2"></i> 
                    <i class="fa fa-briefcase" v-if="option.type == 3"></i> 
                    {{ option.name }}
                    <i class="fa fa-times" v-if="option.selected" @click.stop="removeValueFromList(index)"></i> 
                </div>

            </div>
        </div>
    </div>
   
 
</div>
</template>

<script>
export default {
    name: 'Superselect',
    props: {
        pre_build: {
          type: Boolean,
          default: false
        },
        value_id: {
          type: Number,
          default: null
        },
        disable_type: {
          type: Number,
          default: null
        },
        onlytype:{
            type: Number,
            default: 0
        },
        placeholder:{
            type: String,
            default: '',
        },
        values: {
            type: Array,
            default: Array
        },
        single: {
            type: Boolean,
            default: false
        },
        select_all_btn: {
            type: Boolean,
            default: false
        },
        ask_before_delete: {
            type: String,
            default: ''
        },
        one_choice: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            valueList: [],
            show_placeholder: true,
            options: [],
            filtered_options: [],
            type: 1,
            show: false,
            posClass: 'top',
            searchText: '',
            first_time: true,
            selected_all: false,
            one_choice_made: false
        };
    },
    created() {
        if(this.pre_build){
            axios
                .get("/superselect/get", {})
                .then(response => {
                    if(this.disable_type){
                        response.data.options.forEach(item =>{
                            if(this.disable_type !== item.type){
                                this.options.push(item);
                            }
                        })
                    } else{
                        this.options = response.data.options;
                    }
                    if(this.value_id){
                        this.valueList.push(this.options.find(x => x.id === this.value_id));
                        document.getElementById('placeholder').style.display = "none";
                    }
                    console.log(this.options);
                    this.first_time = false;
                    this.filterType();
                    this.addSelectedAttr();
                })
        }
        this.valueList = this.values;
        if(this.one_choice && this.valueList.length > 0) this.one_choice_made = true;
        this.checkSelectedAll(); 
        if(this.onlytype > 0){
            this.changeType(2);
        } 
    },
    methods: {
        hidePlaceholder(){
            this.show_placeholder = !this.show_placeholder;
        },
        checkSelectedAll() {
            if(this.valueList.length == 1
                && this.valueList[0]['id']== 0
                && this.valueList[0]['type'] == 0) {
                this.selected_all = true;
                 console.log('okay');
            } else {
                console.log('wtf');
            }
        },
        
        filterType() {
            this.filtered_options = this.options.filter((el, index) => {
                return el.type == this.type
            });
        },

        addSelectedAttr() {
            this.filtered_options.forEach(el => {
                el.selected = this.valueList.findIndex(v => v.id == el.id && v.type == el.type) != -1
            });
        },

        toggleShow() {
            if(this.one_choice_made) {
                return;
            }
             
            this.show = !this.show;
            if(this.show){
                document.getElementById('placeholder').style.display = "none";
            }else{
                document.getElementById('placeholder').style.display = "block";
            }

            if(this.first_time) {
                this.fetch();
            }

            this.$nextTick(() => {
                if(this.$refs.search !== undefined) this.$refs.search.focus();
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
            this.searchText = '';
            this.filterType();
            this.addSelectedAttr();
        },

        addValue(index) {
            if(this.single) this.show = false;
            if(this.single && this.valueList.length > 0) {
                return;
            }; 

            
            if(this.selected_all) return;

            let item = this.filtered_options[index];

            if(this.valueList.findIndex(v => v.id == item.id && v.type == item.type) == -1) {

                let value = {
                    name: item.name,
                    id: item.id,
                    type: item.type
                };

                this.$emit('choose', value);
                this.valueList.push(value);

                item.selected = true
            }
        },

        removeValue(i) {
            if(this.ask_before_delete != '') {
                if(!confirm(this.ask_before_delete)) return;
            }
            
            let v = this.valueList[i];
            console.log(v);
            if(v.id == 0 && v.type == 0 && v.name == 'Все') this.selected_all = false;

            this.valueList.splice(i, 1);

            let index = this.filtered_options.findIndex(o => v.id == o.id && v.type == o.type);
            if(index != -1) {
                this.filtered_options.splice(index, 1);
                this.$emit('remove');
            }
        },

        removeValueFromList(i) {
            let fo = this.filtered_options[i];
            let index = this.valueList.findIndex(v => v.id == fo.id && v.type == fo.type);
            if(index != -1) {
                this.valueList.splice(index, 1);
                fo.selected = false;
            }
        },

        onSearch() {
              
            if(this.searchText == '') {
                this.filtered_options = this.options; 
            } else {
                this.filtered_options = this.options.filter((el, index) => {
                    return el.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1
                }); 
            }

            this.addSelectedAttr();
        }, 

        close() {
            this.show = false;
        },

        fetch() {
            axios
                .get("/superselect/get", {})
                .then(response => {
                    if(this.disable_type){
                        response.data.options.forEach(item =>{
                            if(this.disable_type !== item.type){
                                this.options.push(item);
                            }
                        })
                    } else{
                        this.options = response.data.options;
                    }
                    this.first_time = false;
                    this.filterType();
                    this.addSelectedAttr();
                })
            .catch((error) => {
                alert(error,'111');
            });
        },

        selectAll() {
            if(this.selected_all) return; 
            this.valueList.splice(0, this.valueList.length);
            this.valueList.push({
                name: 'Все',
                id: 0,
                type: 0
            });
            this.show = false;
            this.selected_all = true;

        }
    },

}
</script>
<style scoped lang="scss">
    .placeholder{
       color: rgba(0,0,0,0.5);
        background-color: transparent!important;
    }
</style>