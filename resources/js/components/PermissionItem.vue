<template>
<div class="item d-flex">
    <div class="person">
        <superselect :values="item.targets" class="w-full single" /> 
    </div>

    <div class="role">
        <multiselect 
            ref="role_select" 
            v-model="item.roles"
            :options="local_roles"
            :multiple="true"
            :preserve-search="true"
            :hide-selected="true"
            placeholder="Выберите"
            label="name"
            track-by="name" />
    </div>
    <div class="groups">
        <multiselect 
            ref="group_select" 
            v-model="item.groups"
            :options="local_groups"
            :multiple="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            :hide-selected="true"
            placeholder="Выберите"
            @select="onSelect"
            @remove="onRemove"
            label="name"
            track-by="name" />
    </div>
    <div class="actions d-flex">
        <button class="btn btn-default btn-sm" @click="$emit('updateItem')">
            <i class="fa fa-save" />
        </button>
        <button class="btn btn-default btn-sm" @click="$emit('deleteItem')">
            <i class="fa fa-times" />
        </button> 
    </div>
</div>
</template>

<script>
export default {
    props: ['item','groups', 'users', 'roles'],
    data() {
        return {
            local_groups: [],
            local_roles: [],
        }
    },
    watch: {
        item: {
            deep: true,
            handler (val, oldVal) {
                this.$emit('updated');
            }
        },
    },
    created() {

        console.log(this.item,'wwwssss')

        this.local_groups = this.groups;
        this.local_roles = this.roles;
        if(this.item.groups_all) {
            this.local_groups = [];
            this.item.groups.splice(0,this.item.groups.length + 1)
            this.item.groups.push({
                id: 0,
                name: 'Все отделы' 
            });
            this.item.groups_all = true;
        }
    },
    methods: {
        onSelect(selectedOption) {
            if(selectedOption.id == 0) {
                this.selectAll();
                //this.$refs.group_select.close();
            }   
        },
        selectAll() {
            this.local_groups = [];
            // this.item.groups.splice(0,this.item.groups.length + 1)
            // this.item.groups.push({
            //     id: 0,
            //     name: 'Все отделы' 
            // });
            this.item.groups_all = true;
        },
        onRemove(removedOption) {
            if(removedOption.id == 0) {
                this.local_groups = this.groups;
                this.item.groups_all = false;
            }   
        },
        
    }

}
</script>