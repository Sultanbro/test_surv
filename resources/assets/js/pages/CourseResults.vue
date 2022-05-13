<template>
<div class="course-results">
    <div class="d-flex">
        <button class="btn btn-grey mr-2 rounded"  :class="{'btn-primary': type == BY_USER}" @click="type = BY_USER">
            <span>По сотрудникам</span>
        </button>
        <button class="btn btn-grey mr-2 rounded" :class="{'btn-primary': type == BY_GROUP}" @click="type = BY_GROUP">
            <span>По группам</span>
        </button>
    </div>  
       
    <div v-if="type == BY_USER" class="by_user">
        <p>Тут ничего нет</p>
        <div class="table-responsive" v-if="users.items.length > 0">
           
            <table class="table b-table table-bordered table-sm">

                <tr>
                    <th v-for="(field, index) in users.fields" :key="index" :class="field.class">
                        <div>{{ field.name }}</div>
                    </th>
                </tr>

                
                <template v-for="(item, i) in users.items">
                    <tr :key="i"  >
                        <td v-for="(field, f) in users.fields" :key="f" :class="field.class" @click="expandUser(item)">
                            <div>{{ item[field.key] }}</div> 
                        </td>
                    </tr>
                    <tr :key="i"  v-if="!item.expanded">
                        <td v-for="(course, f) in item.courses" :key="f">
                            <div>{{ course }}</div> 
                        </td>
                    </tr>
                </template>
                

            </table>







        </div>
    </div>

    <div v-else class="by_group">
         <p>Тут тоже отвечаю</p>
    </div>

    
    
</div>
</template>

<script>
const BY_USER = 1;
const BY_GROUP = 2;

export default {
    name: "CourseResults", 
    watch: {
        monthInfo() {
           
        },
        currentGroup() {

        }
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
        fetchData() {
            let loader = this.$loading.show();

            axios
                .post("/course-results/get", {
                    month: this.monthInfo.month,
                    year: this.monthInfo.currentYear,
                    group_id: this.currentGroup !== undefined ? this.currentGroup :  null,
                })
                .then((response) => {
                    
                    this.users = response.data.users;
                    this.groups = response.data.groups;
            
                    loader.hide();
                });
            },

        expandUser(item) {
            this.users.items.forEach(i => i.expanded = false);
            if(!item.expanded) {
                item.expanded = true;
            }
            
        },
        
        
     
    } 
}
</script>
