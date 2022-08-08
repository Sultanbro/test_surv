<template>
<div class="kpi-pages">
    
    <b-tabs type="card" :defaultActiveKey="active">
        <b-tab title="KPI" :key="1" card>
            <kpi></kpi>
        </b-tab>
        <b-tab title="Показатели" :key="2" card>
            <indicators></indicators>
        </b-tab>
        <b-tab title="Статистика" :key="3" card>
            <stats></stats>
        </b-tab>
        <b-tab title="Бонусы" :key="4" card>
            <bonuses></bonuses>
        </b-tab>
        <b-tab title="Квартальная премия" :key="5" card>
            <quartal-premium></quartal-premium>
        </b-tab>
    </b-tabs>

</div>
</template>

<script>
export default {
    name: "KPIPages", 
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