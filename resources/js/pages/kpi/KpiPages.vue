<template>
<div class="kpi-pages">
    
    <b-tabs type="card" :value="active" @activate-tab="(n,p,e) => active = n">
        <b-tab title="KPI" :key="0" card >
            <kpi v-if="active == 0"></kpi>
        </b-tab>
        <b-tab title="Бонусы" :key="1" card >
            <bonuses v-if="active == 1"></bonuses>
        </b-tab>
        <b-tab title="Квартальная премия" :key="2" card >
            <quartal-premium v-if="active == 2"></quartal-premium>
        </b-tab>
        <b-tab title="Статистика" :key="3" card>
            <stats v-if="active == 3"></stats>
        </b-tab>
        <b-tab title="Показатели" :key="4" card>
            <indicators v-if="active == 4"></indicators>
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
            active: 0,
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