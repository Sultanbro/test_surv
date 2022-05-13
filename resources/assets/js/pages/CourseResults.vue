<template>
<div class="course-results">
    <p>Тут ничего нет</p>
    <div>
        {{ monthInfo }}
    </div>
</div>
</template>

<script>
export default {
    name: "CourseResults", 
    props: {
        monthInfo: {
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
