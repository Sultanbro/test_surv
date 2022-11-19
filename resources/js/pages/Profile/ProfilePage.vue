<template>
<div id="page-profile" class="">
    <div class="intro content">
        <new-intro-top
            :courses="intro['courses']"
            :profit="intro['profit']"
            :estimation="intro['estimation']"
            :indicators="intro['indicators']"
        ></new-intro-top>
        <new-intro-stats @pop="pop"></new-intro-stats>
        <!-- <new-intro-smart-table></new-intro-smart-table> -->
    </div>

    <new-courses @init="intro['courses'] = true"></new-courses>
    <new-profit @init="intro['profit'] = true"></new-profit> 
    <new-trainee-estimation @init="intro['estimation'] = true"></new-trainee-estimation>
    <new-compare-indicators @init="intro['indicators'] = true"></new-compare-indicators>

    <popup v-if="popBalance"
        title="Баланс оклада"
        desc="Заработанная сумма именно от окладной части"
        :open="popBalance" 
        @close="popBalance=false"
        :width="popupWidth">
        <popup-balance></popup-balance>
    </popup>

    <popup v-if="popKpi"
        title="Kpi"
        desc="Выполняя дополнительные активности, заработайте больше денег"
        :open="popKpi" 
        @close="popKpi=false"
        :width="popupWidth">
        <popup-kpi></popup-kpi>
    </popup>

    <popup v-if="popBonuses"
        title="Бонусы"
        desc="Зарабатывайте бонусы, выполняя дополнительные активности"
        :open="popBonuses" 
        @close="popBonuses=false"
        :width="popupWidth">
        <popup-bonuses></popup-bonuses>
    </popup>

    <popup v-if="popQuartalPremiums"
        title="Квартальные премии"
        desc=""
        :open="popQuartalPremiums" 
        @close="popQuartalPremiums=false"
        :width="popupWidth">
        <popup-quartal></popup-quartal>
    </popup>

    <popup v-if="popNominations"
        title="Номинации"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popNominations" 
        @close="popNominations=false"
        :width="popupWidth">
        <popup-nominations></popup-nominations>
    </popup>
</div>
</template>

<script>
export default {
    name: 'ProfilePage',
    props: {},
    computed: {
        popupWidth(){
            const w = this.$viewportSize.width
            if(w < 651) return '100%'
            if(w < 1360) return '75%'
            return w - (39 * this.$viewportSize.rem) + 'px'
        }
    },
    data: function () {
        return {
            fields: [], 
            popBalance: false,
            popKpi: false,
            popBonuses: false,
            popQuartalPremiums: false,
            popNominations: false,
            intro: {
                courses: false,
                profit: false,
                estimation: false,
                indicators: false,
            }
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

<style lang="scss">
#page-profile{
    padding-bottom: 10rem;
    padding-right: 2rem;
}
@media(max-width:1910px){
    #page-profile{
        padding-right: 0;
    }   
}
</style>