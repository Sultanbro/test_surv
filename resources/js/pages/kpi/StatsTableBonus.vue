<template>
<div class="bonuses px-3 py-1">
    <table class="table j-table table-bordered table-sm mb-3 collapse-table">
        <tr>
            <th class="b-table-sticky-column text-center px-1">
                <i class="fa fa-cogs" @click="adjustFields"></i>
            </th>
            <th class="text-left">
               Кому
            </th>
        </tr>
        <tr>
        
        </tr>
        <template v-for="(page_item, p) in groups" v-if="page_item.length != 0">
            <tr>
                <td 
                    @click="expand(p)"
                    class="pointer b-table-sticky-column"
                >
                        <div class="d-flex px-2">
                            <i class="fa fa-minus mt-1" v-if="page_item.expanded"></i>
                            <i class="fa fa-plus mt-1" v-else></i>
                            <span class="ml-2">{{ p + 1 }}</span>
                        </div>
                    </td>
                <td class="text-left">
                    <div class="d-flex aic p-1">
                        <span class="ml-2" v-if="page_item.name">{{ page_item.name }}</span>
                        <span class="ml-2" v-else>{{page_item[0].name}}</span>
                    </div>
                </td>
            </tr>
            <template v-if="page_item[0]">
                <tr  
                    class="collapsable"
                    :class="{'active': page_item.expanded }"
                >
                    <td :colspan="fields.length + 2">
                        <div class="table__wrapper">
                            <table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner"  v-for="(user, i) in page_item.users">
                                <tr>
                                    <th class="b-table-sticky-column text-center px-1 pointer" @click="user.expanded = !user.expanded">
                                        <div class="d-flex px-2">
                                            <i class="fa fa-plus mt-1"></i>
                                            <span class="ml-2 bg-transparent">{{ i + 1}}</span>
                                        </div>
                                    </th>
                                    <th
                                        class="text-left"
                                        >
                                        {{ user.name }} {{ user.last_name }}
                                    </th> 
                                    <th v-for="item in bonuses(user.obtained_bonuses)">
                                        {{ item.title }} <b>{{item.amount}} тг</b>
                                    </th>
                                </tr>
                                <template v-if="user.expanded">
                                    <table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner">
                                        <tr>
                                            <th></th>
                                            <th>Наименование активности</th>
                                            <th>За</th>
                                            <th>Кол-во</th>
                                            <th>Вознаграждение</th>
                                            <th>Период</th>
                                            <th>Заработано</th>
                                        </tr>
                                        <tr v-for="item1 in calculated_bonuses(user.obtained_bonuses)">
                                            <td></td>
                                            <td>{{ item1.title }}</td>
                                            <td>{{ item1.text }}</td>
                                            <td>{{ item1.quantity }}</td>
                                            <td>{{ item1.sum }}</td>
                                            <td v-if="item1.daypart == 0">Весь день</td>
                                            <td v-else >Пол дня</td>
                                            <td v-if="item1.daypart == 0">{{ item1.quantity * item1.sum }}</td>
                                            <td v-else>{{ item1.quantity * (item1.sum / 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Рейтинг оператора ОКК</td>
                                            <td>За каждую ед</td>
                                            <td>{{user.totals.quantity}}</td>
                                            <td>{{user.totals.sum}}</td>
                                            <td>Весь день</td>
                                            <td>{{user.totals.amount}}</td>
                                        </tr>
                                    </table>
                                </template>
                            </table>
                        </div>
                    </td>
                </tr>
            </template>
        </template>
        <tr>
            
        </tr>
    </table>
</div>
</template>

<script>
export default {
    name: "Bonuses", 
    props: {
        groups: Array,
        group_names: Object
    },
    watch: {

    },
    data() {
        return {
            fields: [],
            items: [],
            user_collapsed: false,
        }
    }, 
    
    created() {
        this.generateBonuses();
    },

    mounted() {
        console.log(this.groups);
    },
    computed: {

    },
    methods: {
        getActivityText(activity_id, group_id){
            return 'fuck';
        },
        adjustFields(){
        },
        expand(p){
            
        },
        bonuses(obtained_bonuses){
            var bonus_titles = [];
            var bonus_prices = [];
            obtained_bonuses.forEach(bonus => {
                if(!bonus_titles.some(s => {return s === bonus.bonus.title})){
                    bonus_titles.push(bonus.bonus.title);
                    bonus_prices.push({title: bonus.bonus.title, amount: bonus.amount});
                }else{
                    bonus_prices.filter(p => {return p.title === bonus.bonus.title})[0].amount += bonus.amount;
                }
            });
            return bonus_prices;
        },
        calculated_bonuses(obtained_bonuses){
            var bonus_titles = [];
            var bonus_prices = [];
            obtained_bonuses.forEach(bonus => {
                if(!bonus_titles.some(s => {return s === bonus.bonus.title})){
                    bonus_titles.push(bonus.bonus.title);
                    bonus_prices.push({title: bonus.bonus.title, text: bonus.bonus.text, quantity: bonus.bonus.quantity, sum: bonus.bonus.sum, daypart: bonus.bonus.daypart});
                }else{
                    bonus_prices.filter(p => {return p.title === bonus.bonus.title})[0].quantity += bonus.bonus.quantity;
                }
            });
            return bonus_prices;
        }
    },
 
}
</script>