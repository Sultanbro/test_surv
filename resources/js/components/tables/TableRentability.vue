<template>
<div class="mb-3" :key="skey">
   
    
    <table class="table b-table table-bordered table-sm table-responsive r-to">
        <tr>
            <th></th>
            <th></th>

            <template v-for="(m, key) in months">
                <th colspan="2" class="text-center">{{m}}</th>   
                <th class="br1 text-center">{{ tops[key]  }}</th>   
            </template>
        </tr>

        <tr>
            <th> 
                <div class="d-flex align-items-center">
                    <p @click="sort('name')" class="mb-0 fz-12">Название <i class="fa fa-sort ml-1"></i></p>
                </div>
                
            </th>
            <th>
                <div class="d-flex align-items-center">
                    <p @click="sort('date')" class="mb-0 fz-12">Дата <i class="fa fa-sort ml-1"></i></p>
                </div>
            </th>
            <template v-for="i in 12">
               <th class="font-bold text-center  bb1" @click="sort('l' + i)" :key="i">
                   <div class="d-flex align-items-center">
                       выручка <i class="fa fa-sort ml-1"></i>
                   </div>
                </th> 
                <th class="font-bold text-center bb1" @click="sort('c' + i)" :key="i">
                    <div class="d-flex align-items-center">
                      ФОТ <i class="fa fa-sort ml-1"></i>
                   </div>
                </th>
                <th class="font-bold text-center br1 bb1" @click="sort('r' + i)" :key="i">
                    <div class="d-flex align-items-center">
                        Маржа <i class="fa fa-sort ml-1"></i>
                   </div>
                </th>
            </template>
        </tr>

        <tr v-for="(item, index) in items" :key="index">
            <td class="table-primary b-table-sticky-column text-left px-2 t-name wdf">
                <div>{{ item.name }}</div>
            </td>
            
            <td class="text-center">
                <div>{{ item.date_formatted }}</div>
            </td>

            <template v-for="i in 12">
                <td class="text-center" :class="{'p-0': index != 0}">
                    <input v-if="index != 0" class="input" 
                    :class="{'edited':item['ed' + i]}"
                    type="number" v-model="item['l' + i]" @change="update(i, index)">
                    <div v-else>{{ item['l' + i] }}</div>
                </td>
                <td class="text-center">
                    
                   {{  numberWithCommas( item['c' + i] ) }}
                    
                </td>
                <td class="text-center br1"
                    :class="{
                        'c-red': item['rc' + i] < 20 && item['rc' + i] != '',
                        'c-orange': item['rc' + i] >= 20 && item['rc' + i] < 50,
                        'c-yellow': item['rc' + i] >= 50 && item['rc' + i] < 75,
                        'c-green': item['rc' + i] >= 75,
                    }"
                >{{ item['r' + i] }}</td>
            </template>
            
        </tr>
    </table>
</div>
</template>

<script>
export default {
    name: "TableRentability",
    props: {
        year: Number,
    },
    data() {
        return {
            items: [],
            months: {
                1: 'Январь',
                2: 'Февраль',
                3: 'Март',
                4: 'Апрель',
                5: 'Май',
                6: 'Июнь',
                7: 'Июль',
                8: 'Август',
                9: 'Сентябрь',
                10: 'Октябрь',
                11: 'Ноябрь',
                12: 'Декабрь',
            },
            tops: {},
            skey: 1,
            sorts: {}
        };
    },
    watch: { 
        year: function(newVal, oldVal) { 
            this.fetchData();
        },
    },

    created() {
        this.fetchData(); 
    },

    methods: {
          
        countTop() {
            Object.keys(this.months).forEach(key => {
                let s = this.items[0]['c' + key];
                let a = (this.items[0]['l' + key] - s) / s * 100;
                this.tops[key] = isNaN(a) ? '' : Number(a).toFixed(1) + '%';
            });
        },

        countRents() {
            this.items.forEach(item => {
                for(let i = 1;i<=12;i++) {
                    let l = item['l' + i];
                    let c = item['c' + i];
                    let a = (l- c) / l * 100;
                    item['r' + i] = !isFinite(a)  ? '' : Number(a).toFixed(1) + '%';
                    item['rc' + i] = !isFinite(a) ? 0 : Number(a);
                }
            });
        },

        fetchData() {
            axios 
                .post("/timetracking/top/get-rentability", {
                    year: this.year
                })
                .then((response) => {
                    this.items = response.data
                    this.countRents();
                    this.countTop();
                    this.skey++;
                });
        },

        update(month, index) {

            let item = this.items[index];

            axios 
                .post("/timetracking/top/top_edited_value/update", {
                    year: this.year,
                    month: month,
                    value: item['l' + month],
                    group_id: item.group_id,
                })
                .then((response) => {
                    let i = month;

                    item['r' + i] = Number(item['c' + i]) > 0 ? Number(Number(item['l' + i]) * 1000 / Number(item['c' + i]) ).toFixed(1) : 0;
                    item['rc' + i] = item['r' + i] + '%';
                    
                    item['ed' + i] = true;

                    this.$message.success('Сохранено');
                });
        },

        numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        sort(field) {

            if(this.sorts[field] === undefined) {
                this.sorts[field] = 'asc';
            } 

            let item = this.items[0];

            this.items.shift();
            if(this.sorts[field] === 'desc') {
                this.items.sort((a, b) => (a[field] > b[field]) ? 1 : -1);
                this.sorts[field] = 'asc';
            } else {
                this.items.sort((a, b) => (a[field] < b[field]) ? 1 : -1);
                this.sorts[field] = 'desc';
            }
            
            this.items.unshift(item);
        },

    },
};
</script>
<style scoped>
.br1 {
    border-right: 1px solid #bababa;
}
.bb1 {
    border-bottom: 1px solid #a7a7a7;
}
.c-red {background: rgb(247, 88, 88);}
.c-orange {background: rgb(255, 196, 85);}
.c-yellow {background: rgb(255, 255, 107);}
.c-green {background: rgb(86, 172, 86);}
.edited {background: rgb(239, 236, 130);}
.input{
        border: 0;
    margin: 0;
    width: 110px;
    padding: 5px 0px 5px 13px;
    text-align: center;
}
.fz-12 {
    font-size: 12px;
}
</style>
