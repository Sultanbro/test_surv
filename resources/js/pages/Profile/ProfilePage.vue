<template>
<div class="">
    <div class="intro content">
        <new-intro-top></new-intro-top>
        <new-intro-stats @pop="pop"></new-intro-stats>
        <new-intro-smart-table></new-intro-smart-table>
    </div>

    <new-profit></new-profit> 
    <new-courses></new-courses>
    <new-trainee-estimation></new-trainee-estimation>
    <new-compare-indicators></new-compare-indicators>

    <popup v-if="popBalance"
        title="Баланс оклада"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popBalance" 
        @close="popBalance=false"
        width="75%">
        <popup-balance></popup-balance>
    </popup>

    <popup v-if="popKpi"
        title="Kpi"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popKpi" 
        @close="popKpi=false"
        width="75%">
        <popup-kpi></popup-kpi>
    </popup>

    <popup v-if="popBonuses"
        title="Бонусы"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popBonuses" 
        @close="popBonuses=false"
        width="75%">
        <popup-kpi></popup-kpi>
    </popup>

    <popup v-if="popQuartalPremiums"
        title="Квартальные премии"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popQuartalPremiums" 
        @close="popQuartalPremiums=false"
        width="75%">
        <popup-kpi></popup-kpi>
    </popup>

    <popup v-if="popNominations"
        title="Номинации"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popNominations" 
        @close="popNominations=false"
        width="75%">
        <popup-kpi></popup-kpi>
    </popup>
    
</div>
</template>

<script>
export default {
    name: "ProfilePage", 
    props: {},
    data: function () {
        return {
            fields: [], 
            popBalance: false,
            popKpi: false,
            popBonuses: false,
            popQuartalPremiums: false,
            popNominations: false,
        };
    },
    methods: {
        pop(window) {
            console.log(window)
            if(window == 'balance') this.popBalance = true;
            if(window == 'kpi') this.popKpi = true;
            if(window == 'bonus') this.popBonuses = true;
            if(window == 'qp') this.popQuartalPremiums = true;
            if(window == 'nominations') this.popNominations = true;
        }
    }
};
</script>