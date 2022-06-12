<template>
<div class="item d-flex">
    <div class="person">
        <v-select
            v-if="item.user.id == null"
            :options="users"
            label="name"
            v-model="item.user"
            class="noscrollbar" />
        <p v-else class="mb-0">{{ item.user.name }}</p>
    </div>
    <div class="role">
        <v-select
            :options="roles"
            label="name"
            v-model="item.role"
            class="noscrollbar" />
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
        }
    },
    created() {
        this.local_groups = this.groups;
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
            console.log(selectedOption)
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
        }
    }

}
</script>