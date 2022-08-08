<template>
<div class="kpi">
    <div class="d-flex mb-2 mt-2 jcfe">
        <button class="btn btn-primary rounded" @click="addKpi">Добавить</button>
    </div>
    

    <div class="j-table">
        <div class="jt-body">
            <div class="jt-row">
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
            </div>
            <div class="jt-row">
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
            </div>
            <div class="jt-row">
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
            </div>
            <div class="jt-row">
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
                <div class="jt-cell">&nbsp;</div>
            </div>
        </div>
    </div>


</div>
</template>

<script>
export default {
    name: "KPI", 
    props: {

    },
    data() {
        return {
            active: 1,
            items: [
                {name: 'IT отдел'},
                {name: 'Али Акпанов'},
                {name: 'Руслан Ташметов'},
            ]
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

        addKpi() {
            this.$toast.info('Добавить KPI');
        },

        editKpi() {
            this.$toast.info('Редактировать KPI');
        }, 

        deleteKpi() {
            this.$toast.info('Удалить KPI');
        },

        showStat() {
            this.$toast.info('Показать статистику');
        }
 
    } 
}
</script>