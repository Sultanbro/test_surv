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

    <popup 
        v-if="popBalance"
        title="Баланс оклада"
        desc="Дополнительное поле с описанием функционала данного окна"
        :open="popBalance" 
        @close="popBalance=false"
        width="75%">
        <popup-balance></popup-balance>
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
            popSalary: false,
            popBonuses: false,
            popQuartalPremiums: false,
            popNominations: false,
        };
    },
    methods: {
        pop(window) {
            console.log(window)
            if(window == 'balance') this.popBalance = true;
            if(window == 'salary') this.popSalary = true;
            if(window == 'bonus') this.popBonuses = true;
            if(window == 'qp') this.popQuartalPremiums = true;
            if(window == 'nominations') this.popNominations = true;
        }
    }
};
</script>