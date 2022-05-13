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
           
            <table class="table b-table table-bordered table-sm table-responsive" :class="{'inverted' : color_invert}">

                <tr>
                    <th class="text-left" v-for="(field, index) in users.fields" :key="index">
                        <div>{{ field.name }}</div>
                    </th>
                </tr>


                <tr v-for="(item, i) in users.items" :key="i">
                    <td v-for="(field, f) in users.fields" :key="f">
                        <div>{{ item[f] }}</div>
                    </td>
                </tr>

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


     
    } 
}
</script>
