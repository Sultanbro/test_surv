<template>
<div class="stats p-3">
     <table class="j-table">
        <thead>
            <tr class="table-heading">
                
                <th class="first-column">
                    <i class="fa fa-cogs" @click="adjustFields"></i>
                </th>

                <th>Кому</th>
                <th>Процент выполнения</th>
                <th>Заработано</th>
                <th>???</th>

                <th>Действия</th>

            </tr>
        </thead>
    </table>
</div>
</template>

<script>
export default {
    name: "Stats", 
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
        adjustFields() {

        },

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