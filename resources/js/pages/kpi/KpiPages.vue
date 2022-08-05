<template>
<div>
    
    <b-tabs type="card" :defaultActiveKey="active">
        <b-tab title="KPI" :key="1" card>
                <kpi></kpi>
        </b-tab>
        <b-tab title="Показатели" :key="2" card>
    Показатели
        </b-tab>
        <b-tab title="Статистика" :key="3" card>
        Статистика
        </b-tab>
        <b-tab title="Бонусы" :key="4" card>
        Бонусы
        </b-tab>
        <b-tab title="Квартальная премия" :key="5" card>
            Квартальная премия
        </b-tab>
    </b-tabs>



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
            active: 1,
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