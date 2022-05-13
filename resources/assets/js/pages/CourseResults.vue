<template>
<div class="course-results">
        <div class="d-flex">
            <button class="btn btn-primary mr-2 rounded"  :class="{'btn-primary': type == BY_USER}" @click="type = BY_USER">
                <span>По сотрудникам</span>
            </button>
            <button class="btn btn-grey mr-2 rounded" :class="{'btn-primary': type == BY_GROUP}" @click="type = BY_GROUP">
                <span>По группам</span>
            </button>
        </div>  
       
    <div v-if="type == BY_USER" class="by_user">
        <p>Тут ничего нет</p>
        <div>
            {{ monthInfo }}
            {{ currentGroup }}
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
        }
    },
    created() {

        
    },
    methods: {

    } 
}
</script>
