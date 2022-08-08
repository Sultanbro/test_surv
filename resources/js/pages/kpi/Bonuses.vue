<template>
<div class="bonuses">
          <div class="p-3">Бонусы</div>  

</div>
</template>

<script>
export default {
    name: "Bonuses", 
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