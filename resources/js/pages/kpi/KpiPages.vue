<template>
<div>
    test
</div>
</template>

<script>
export default {
    name: "KPI", 
    props: {
        page: {
            type: String,
            default: 'kpi'
        }
    },
    data() {
        return {
            test: 1
        }
    },

    created() {
       // this.fetchData()
    },
    methods: {

        fetchData() {
            let loader = this.$loading.show();

            axios.post('/kpi/' + this.page, {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
            }).then(response => {
              
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },
 
    } 
}
</script>