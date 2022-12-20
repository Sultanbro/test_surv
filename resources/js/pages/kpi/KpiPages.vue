<template>
    <div class="kpi-pages">
        
        <div v-if="access == 'edit'">
            <b-tabs type="card" class="mt-4" :value="active" @activate-tab="(n,p,e) => active = n" >
                <b-tab title="KPI" :key="0" card>
                    <kpi v-if="active == 0"></kpi>
                </b-tab>
                <b-tab title="Бонусы" :key="1" card>
                    <bonuses v-if="active == 1"></bonuses>
                </b-tab>
                <b-tab title="Квартальная премия" :key="2" card>
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

        <div v-else>
            <b-tabs type="card" :value="active" @activate-tab="(n,p,e) => active = n">
                <b-tab title="Статистика" :key="0" card>
                    <stats v-if="active == 0"></stats>
                </b-tab>
                <b-tab title="Показатели" :key="1" card>
                    <indicators v-if="active == 1"></indicators>
                </b-tab>
            </b-tabs>
        </div>
        
    </div>
    </template>
    
    <script>
    export default {
        name: "KPIPages", 
        props: {
            page: {
                type: String,
                default: 'kpi'
            },
            access: {
                default: 'view'
            }
        },
        data() {
            return {
                active: 0,
            }
        },
    
        created() {
           // this.fetchData()
           let uri = window.location.search.substring(1); 
            let params = new URLSearchParams(uri);
            this.active = params.get("target") ? 3 : 0;
        },
        mounted() {
            let uri = window.location.search.substring(1); 
            let params = new URLSearchParams(uri);
            if(params.get("target")){
                window.history.pushState({}, document.title, "/" + "kpi"); 
            }
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