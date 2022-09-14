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
        <template v-for="(page_item, p) in items">
            <tr>
                <td 
                    @click="page_item.expanded = !page_item.expanded"
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
                        <span class="ml-2">{{ page_item.name }}</span>
                    </div>
                </td>
            </tr>
            <template v-if="page_item.expanded">
                <tr class="collapsable"
                    :class="{'active': page_item.expanded }">
                    <td :colspan="fields.length + 2">
                        <div class="table__wrapper">
                            <table class="table b-table table-bordered table-sm table-responsive mb-0 table-inner"  v-for="(user, i) in page_item.users" v-if="obtained_bonuses.filter(b => {return b.user_id === user.id && isCurrentMonth(b.date) && b.comment.substring( b.comment.indexOf(':') + 1, b.comment.lastIndexOf(';') ) > 0}).length > 0">
                                <tr>
                                    <th class="b-table-sticky-column text-center px-1 pointer" @click="user.expanded = !user.expanded">
                                        <div class="d-flex px-2 ">
                                            <i class="fa fa-plus mt-1"></i>
                                            <span class="ml-2 bg-transparent ">{{ user.id }}</span>
                                        </div>
                                    </th>
                                    <th
                                        class="text-left"
                                        >
                                        {{ user.full_name }}
                                    </th> 
                                    <th v-for="bonus in bonuses" v-if="bonus.targetable_id == page_item.id">
                                        {{ bonus.title}} <b> 0 тг</b>
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
                                        <tr v-for="(bonus, p) in obtained_bonuses.filter(b => {return b.user_id === user.id && isCurrentMonth(b.date) })" v-if="bonus.comment.substring( bonus.comment.indexOf(':') + 1, bonus.comment.lastIndexOf(';') ) > 0" >
                                            <td class="text-white text-center">{{ p + 1}}</td>
                                            <td>{{ activities[page_item.activity_id].name }}</td>
                                            <td>{{ bonus.comment }}</td>
                                            <td>{{ bonus.comment.substring( bonus.comment.indexOf(":") + 1, bonus.comment.lastIndexOf(";") ) }}</td>
                                            <td>{{ bonus.amount }}</td>
                                            <td>{{ bonus.date }}</td>
                                            <td>{{ bonus.amount * parseInt(bonus.comment.substring( bonus.comment.indexOf(":") + 1, bonus.comment.lastIndexOf(";") )) }}</td>
                                        </tr>
                                    </table>
                                </template>
                            </table>
                        </div>
                    </td>
                </tr>
            </template>
            <!--<template v-if="page_item[0]">
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
            </template>-->
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
            current_date: '2022-09-14',
            bonuses: [],
            obtained_bonuses: [],
            activities: [],
            fields: [],
            items: [],
            user_collapsed: false,
        }
    }, 
    
    created() {
        this.getActivities();
        this.generateBonuses();
    },

    mounted() {
    },
    computed: {

    },
    methods: {
        isCurrentMonth(date){
            const d = new Date();
            let month = d.getMonth();
            var from = date.split("-")
            if(from[1] == month) return true;
            else return false;

        },
        getActivities(){
            axios.get('/statistics/activities').then(response => {
                this.activities = response.data;
            });
        },
        generateBonuses(){
            this.groups.forEach(group => {
                if(group[0]){
                    group[0].bonuses.forEach(bonus1 => {
                        if(!this.bonuses.some(bonus2 => { return bonus2.id === bonus1.id })) this.bonuses.push(bonus1);
                    });
                    group[0].users.forEach(user => {
                        this.obtained_bonuses = this.obtained_bonuses.concat(user.obtained_bonuses);
                    });
                    var item = this.items.filter(item => { return item.id === group[0].id });

                    if(item.length > 0){    
                       // this.items[group[0].id].bonuses.push(group[0].bonuses);
                        //this.items.filter(item => { return item.id === group[0].id })[0].bonuses.concat(group[0].bonuses);
                       
                    }else{
                        this.items.push({
                            id: group[0].id,
                            name: group[0].name,
                            targetable_id: group[0].targetable_id,
                            targetable_type: group[0].targetable_type,
                            users: group[0].users.map(res=> ({...res, expanded: false})),
                            activity_id: group[0].activity_id,
                            expanded: false
                        });
                    }
                    
                }
            });
            console.log(this.obtained_bonuses);
        },
        getActivityText(activity_id, group_id){
            
        },
        adjustFields(){
        },
        expand(item){
            item.expanded = !item.expanded;
        },
        /*bonuses(data){
            var bonuses = [];
            data.forEach(bonus => {
                bonuses.push({
                    amount: bonus.amount,
                    bonus_id: bonus.bonus_id,
                    comment: bonus.comment,
                    date: bonus.date,
                    id: bonus.id,
                    user_id: bonus.user_id
                });
            });

            
            return bonuses;
        },*/
        calculated_bonuses(obtained_bonuses){
            var bonus_titles = [];
            var bonus_prices = [];
            obtained_bonuses.forEach(bonus => {
                if(!bonus_titles.some(s => {return s === bonus.comment})){
                    bonus_titles.push(bonus.comment);
                    bonus_prices.push({title: bonus.comment, sum: bonus.amount, date: bonus.date});
                }else{
                    bonus_prices.filter(p => {return p.title === bonus.title})[0].quantity += bonus.quantity;
                }
            });
            return bonus_prices;
        }
    },
 
}
</script>