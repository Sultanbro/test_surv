<template>
<div class="course-results">
    <div class="d-flex mb-2">
        <button class="btn btn-grey mr-2 rounded"  :class="{'btn-primary': type == BY_USER}" @click="type = BY_USER">
            <span>По сотрудникам</span>
        </button>
        <button class="btn btn-grey mr-2 rounded" :class="{'btn-primary': type == BY_GROUP}" @click="type = BY_GROUP">
            <span>По группам</span>
        </button>
    </div>  
       
    <div v-if="type == BY_USER" class="by_user">

        <div class="table-responsive" v-if="users.items.length > 0">
           
            <table class="table b-table table-bordered table-sm">

                <tr>
                    <th v-for="(field, index) in users.fields" :key="index" :class="field.class">
                        <div>{{ field.name }}</div>
                    </th>
                </tr>

                
                <template v-for="(item, i) in users.items">
                    <tr class="pointer" :class="{
                        'expanded-title': item.expanded
                    }">
                        <td v-for="(field, f) in users.fields" :key="f" :class="field.class" @click="expandUser(item)">
                            <div>{{ item[field.key] }}</div>  
                        </td>
                    </tr>
                    <template v-for="course in item.courses">
                        <tr v-if="item.expanded" class="expanded">
                            <td v-for="(field, f) in users.fields" :key="f" :class="field.class">
                                <div v-if="field.key == 'progress'" class="d-flex jcc aic">
                                    <p class="mb-0 mr-1">{{ course[field.key] }}</p>
                                    <progress :value="course[field.key].slice(0, -1)" max="100"></progress>
                                </div>
                                <div v-else>{{ course[field.key] }}</div>
                            </td>
                        </tr>
                    </template>
                </template>
                

            </table>

        </div>
    </div>

    <div v-else class="by_group">
        <div class="table-responsive" v-if="groups.items.length > 0">
           
            <table class="table b-table table-bordered table-sm">

                <tr>
                    <th v-for="(field, index) in groups.fields" :key="index" :class="field.class">
                        <div>{{ field.name }}</div>
                    </th>
                </tr>

                
                <template v-for="(item, i) in groups.items">
                    <tr>
                        <td v-for="(field, f) in groups.fields" :key="f" :class="field.class" @click="expandUser(item)">
                            <div>{{ item[field.key] }}</div> 
                        </td>
                    </tr>
                </template>
                

            </table>

        </div>
    </div>

    
    
</div>
</template>

<script>
const BY_USER = 1;
const BY_GROUP = 2;

export default {
    name: "CourseResults", 
    watch: {
        monthInfo(val) {
            this.first = true;
            if(this.type == this.BY_GROUP) {
                this.fetchData('groups');
                this.first = false;
            } else {
                this.fetchData('users');
            }
        }, 
        currentGroup() {
            this.first = true;
            if(this.type == this.BY_GROUP) {
                this.fetchData('groups');
                this.first = false;
            } else {
                this.fetchData('users');
            }
        },
        type(val) {
            if(val == this.BY_GROUP && this.first) {
                this.fetchData('groups');
                this.first = false;
            }
        },
    },
    props: {
        monthInfo: {
            required: false
        },
        currentGroup: {
            required: false
        }
    },
    data() {
        return {
            data: [],
            type: BY_USER,
            first: true,
            BY_USER: BY_USER,
            BY_GROUP: BY_GROUP,
            users: {
                items: [],
                fields: [],
            },
            groups: {
                items: [],
                fields: [],
            },
        }
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData(type = 'users') {
            let loader = this.$loading.show();

            axios
                .post("/course-results/get", {
                    type: type,
                    month: this.monthInfo.month,
                    year: this.monthInfo.currentYear,
                    group_id: this.currentGroup !== undefined ? this.currentGroup :  null,
                })
                .then((response) => {
                    
                    if(type == 'users') {
                        this.users = response.data.items;
                    }
                    if(type == 'groups') {
                        this.groups = response.data.items;
                    }
                    
            
                    loader.hide();
                });
        },

        expandUser(item) {
            let ex = item.expanded;
            this.users.items.forEach(i => i.expanded = false);
            item.expanded = !ex;
            
        },
        
        
     
    } 
}
</script>
