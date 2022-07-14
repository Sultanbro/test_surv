<template>
<div class="super-select" ref="select-alt" :class="posClass" v-click-outside="close">

    <div class="selected-items flex-wrap noscrollbar" @click="toggleShow">
        <template v-if="!hide_selected">
            <div 
                v-for="(value, i) in values"
                :key="i"
                class="selected-item"
                :class="'value' + value.type">
                {{ value.name }}
                <i class="fa fa-times" @click.stop="removeValue(i)"></i>
            </div>
        </template>
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
                <div class="type" :class="{'active': type == 1}" @click="changeType(1)">
                    <div class="text">Книги</div>
                    <i class="fa fa-book"></i>
                </div>
                <div class="type" :class="{'active': type == 2}" @click="changeType(2)">
                    <div class="text" >Видеокурсы</div>
                    <i class="fa fa-play"></i>
                </div>
                <div class="type" :class="{'active': type == 3}" @click="changeType(3)">
                    <div class="text">Базы знаний</div>
                    <i class="fa fa-database"></i>
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
                    <i class="fa fa-book" v-if="option.type == 1"></i> 
                    <i class="fa fa-play" v-if="option.type == 2"></i> 
                    <i class="fa fa-database" v-if="option.type == 3"></i> 
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
    name: 'SuperselectAlt',
    props: {
        values: {
            type: Array,
        },
        single: {
            type: Boolean,
            default: false
        },
        select_all_btn: {
            type: Boolean,
            default: false
        },
        hide_selected: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            options: [],
            filtered_options: [],
            type: 1,
            show: false,
            posClass: 'top',
            searchText: '',
            first_time: true,
            selected_all: false
        };
    },
    created() {

      console.log(this.values,'019995');

        this.checkSelectedAll();  
    },
    methods: {
        checkSelectedAll() {
            if(this.values.length == 1
                && this.values[0]['id']== 0
                && this.values[0]['type'] == 0) {
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
                el.selected = this.values.findIndex(v => v.id == el.id && v.type == el.type) != -1
            });
        },

        toggleShow() {
            this.show = !this.show;
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
            if(this.single && this.values.length > 0) {
                return;
            };
            if(this.selected_all) return;

            let item = this.filtered_options[index];

            if(this.values.findIndex(v => v.id == item.id && v.type == item.type) == -1) {

                this.values.push({
                    name: item.name,
                    id: item.id,
                    type: item.type
                });

                item.selected = true
            }
        },

        removeValue(i) {
            let v = this.values[i];
            if(v.id == 0 && v.type == 0 && v.name == 'Все') this.selected_all = false;

            this.values.splice(i, 1);

            let index = this.filtered_options.findIndex(o => v.id == o.id && v.type == o.type);
            if(index != -1) this.filtered_options.splice(index, 1);
        },

        removeValueFromList(i) {
            let fo = this.filtered_options[i];
            let index = this.values.findIndex(v => v.id == fo.id && v.type == fo.type);
            if(index != -1) {
                this.values.splice(index, 1);
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
                .get("/superselect/get-alt", {})
                .then((response) => {

                    this.options = response.data.options;

                    this.filterType();
                    this.addSelectedAttr();
                })
            .catch((error) => {
                alert(error,'111');
            });
        },

        selectAll() {
            if(this.selected_all) return; 
            this.values.splice(0, this.values.length);
            this.values.push({
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
