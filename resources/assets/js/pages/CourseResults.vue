<template>
<div class="course-results">
    <p>Тут ничего нет</p>
    <div>
        {{ monthInfo }}
        {{ currentGroup }}
    </div>
</div>
</template>

<script>
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
        }
    },
    created() {

        
    },
    methods: {

    } 
}
</script>
