<template>
<div class="mb-3 index__content">
    <h4 class="d-flex align-items-center justify-content-between">
        <div class="mr-2">{{ activity.name }} </div>
        <div>
            <div v-if="is_admin">
                <a @click='showExcelImport = !showExcelImport' 
                    class="btn btn-success btn-sm rounded mr-2 text-white">
                    <i class="fa fa-upload"></i>
                    Импорт</a>
            </div>
        </div>
    </h4>
    
    <div class="custom-scroll">
        <table class="indicators-table-fixed" :id="'sticky-'+ activity.id">
            <tr>
                <th class="indicators-table-fixed-name sticky-left" rowspan="2">
                    <div class="text-left pl-4">
                        ФИО
                        <i v-if="is_admin" class="fa fa-sort ml-2" @click="sort('fullname')"></i>
                    </div>
                </th>
        
                <th class="indicators-table-fixed-hmonth sticky-left" rowspan="2">
                    <div class="text-left pl-4" v-if="is_admin">
                        Итог к выдаче
                        <i v-if="is_admin" class="fa fa-sort ml-2" @click="sort('plan')"></i>
                    </div>
                    <div class="" v-else></div>
                </th>
                <th class="indicators-table-fixed-hplan sticky-left" v-if="is_admin" rowspan="2">
                    <div class="text-left pl-4">
                        Сборы
                        <i class="fa fa-sort ml-2" @click="sort('count')"></i>
                    </div>
                </th>
        
                <th class="text-center" colspan="2" v-for="day in month.daysInMonth">
                    <div>{{ day }}</div>
                </th>
        
            </tr>
            <tr>
                <template v-for="day in month.daysInMonth">
                    <th class="stickyy-h2">сборы</th>
                    <th class="stickyy-h2">тенге</th>
                </template>
            </tr>
            <tr v-for="(item, index) in items" :key="index"
                :class="{
                    'prize first-place': item.show_cup == 1,
                    'prize second-place':item.show_cup == 2,
                    'prize third-place': item.show_cup == 3,
                }"
            >
                <td class="indicators-table-fixed-name sticky-left text-left" :title="item.id + ' ' + item.email">
                    <div class="d-flex align-items-center max-content">
                        {{ item.lastname }} {{ item.name }}
                    </div>
                </td>
                <td class="indicators-table-fixed-hmonth sticky-left px-2">{{ item.plan }}</td>
                <td v-if="is_admin" class="indicators-table-fixed-hplan sticky-left px-2">{{ item.count }}</td>
                <template v-for="day in month.daysInMonth">
                    <td v-if="item.editable"
                        :class="'text-center ' + item._cellVariants[day]"
                        :title="day + ': сборы'"
                    >
                        <div>
                            <input type="number"
                                v-model="item[day]"
                                @change="updateSettings($event, item, index, day)"
                                class="form-control cell-input">
                        </div>
                    </td>
                    <td v-else
                        :title="day + ': сборы'"
                        @click="editMode(item)"
                        :class="'text-center ' + item._cellVariants[day]"
                    >
                        <div>{{ item[day] }}</div>
                    </td>
        
                    <td v-if="!isNaN(Number(item[day]) * price)"
                        :title="day + ': тенге'"
                        class=""
                    >
                        {{ Number(item[day]) * price }}
                    </td>
                    <td v-else
                        :title="day + ': тенге'"
                        class=""
                    ></td>
                </template>
            </tr>
        </table>
    </div>

    <sidebar title="Импорт EXCEL"
        :open="showExcelImport"
        @close="showExcelImport=false"
        v-if="showExcelImport"
        width="75%"
    >
        <activity-excel-import
            :group_id="42"
            table="minutes"
            @close="showExcelImport=false"
            :activity_id="activity.id"
        ></activity-excel-import>
    </sidebar>
</div>
</template>

<script>
export default {
    name: "TableCollection",
    props: {
        month: Object,
        activity: Object,
        is_admin: Boolean,
        price: {
            default: 50,
        }
    },
    data() {
        return {
            items: [],
            sorts: {},
            fields: [],
            itemsArray: [],
            avgOfAverage: 0,
            showExcelImport: false,
            totalCountDays: 0,
            sum: {},
            percentage: [],
            records: [],
            totalRowName: '',
            accountsNumber: 0,
        };
    },
    watch: { 
        activity: function(newVal, oldVal) { // watch it
            this.fetchData();
        },
    },
    created() {
        this.fetchData();
        

    },
    mounted() {
        document.getElementById("sticky-" + this.activity.id).style.height = window.innerHeight + "px";
    },
    methods: {
        setFirstRowAsTotals() {
            this.totalRowName = 'Сумма сборов'
            this.records.unshift({
                is_date: false,
                name: this.totalRowName,
            });
        },
        addCellVariantsArrayToRecords(){
            this.itemsArray.forEach((element, key) => {
                this.itemsArray[key]["_cellVariants"] = [];
            });
        },

        updateAvgValuesOfRecords() {
            this.itemsArray.forEach((account, index) => {
                this.itemsArray[index]["plan"] = account.plan;
            });
        },
        fetchData() {
            let loader = this.$loading.show();
            
            this.records = this.activity.records;
            this.accountsNumber = this.activity.records.length
            if(this.is_admin) this.setFirstRowAsTotals()
            this.calculateRecordsValues()
            if(this.is_admin) this.calculateTotalsRow()
            if(!this.is_admin) this.setLeaders()
            if(this.is_admin) this.setAvgCell()
            
            this.items = this.itemsArray;

            this.addCellVariantsArrayToRecords();
            this.setCellVariants();
            loader.hide();    
        },
        updateTable(items) {
            let loader = this.$loading.show();
            
            this.records = items;
            this.calculateRecordsValues();
            if(this.is_admin) this.calculateTotalsRow();
             if(!this.is_admin) this.setLeaders()
            this.updateAvgValuesOfRecords();
            
            if(this.is_admin) this.setAvgCell()
            this.totalColumn()
            
            this.items = this.itemsArray;
            
            
            this.addCellVariantsArrayToRecords();
            this.setCellVariants();
            loader.hide();
        },

        setLeaders() {
            let arr = this.itemsArray;

            let first_item = this.itemsArray[0];

            arr.sort((a, b) => Number(a.plan) < Number(b.plan)  ?
                1 : Number(a.plan) > Number(b.plan) ? -1 : 0);

            if(this.itemsArray.length > 3) {
                arr[0].show_cup = 1;
                arr[1].show_cup = 2;
                arr[2].show_cup = 3;
            } 

        },

        totalColumn() {
            let row0_avg = 0;
            this.itemsArray.forEach((account, index) => {
                if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {
                    row0_avg += parseFloat(account['plan']);
                    console.log(account['plan'])
                }
            })    

            if(this.is_admin) this.itemsArray[0]['plan'] = row0_avg
        },
        setAvgCell() {
            this.itemsArray[0]["avg"] = (this.avgOfAverage / this.totalCountDays).toFixed(2);
            this.itemsArray[0]['avg'] = '';
        },
        

        calculateTotalsRow() {

            
            let total = 0
            // вот здесь я считаю итоговые суммы минут по всем сотрудникам, и мне их видимо придется сохранить в бд
            for (let key in this.sum) {
                if (this.sum.hasOwnProperty(key)) {
                    this.itemsArray[0][key] = parseFloat(this.sum[key]).toFixed(0);
                    total += parseFloat(this.sum[key])
                } else {
                    this.itemsArray[0][key] = 0;
                }

                
            
            }

            if(this.is_admin) this.itemsArray[0]['plan'] = parseFloat(total) * this.price
            

        },

        setCellVariants() {
            if (typeof this.activity === "object") {
                const SPECIAL_VALUE = this.activity.daily_plan;
                let minutes = this.items;
                minutes.forEach((account, index) => {
                    if (index > 0 || !this.is_admin) {
                        for (let key in account) {
                            if (key >= 1 && key <= 31) {
                                if (
                                    account[key] >= SPECIAL_VALUE &&
                                    account[key] !== undefined &&
                                    account[key] !== null
                                ) {
                                    this.items[index]._cellVariants[key] = "green";
                                } else if ( 
                                    account[key] < SPECIAL_VALUE &&
                                    account[key] !== undefined &&
                                    account[key] !== null
                                ) { 
                                    this.items[index]._cellVariants[key] = "red";
                                }
                            }
                        }
                    }
                });
            }
            
        },

        editMode(item) {
            if(!this.is_admin) return null;
            this.items.forEach((account, index) => {
                account.editable = false
            })
            item.editable = true
        },

        updateSettings(e, data, index, key) {
            
            data.editable = false
            
            //var index = data.index;
            var clearedValue = e.target.value.replace(",", ".");
            
            var value = parseFloat(clearedValue) || null;

            if(value < 0) {
                this.items[index][key] = 0;
            }

            if(value > 999) {
                this.items[index][key] = 999;
            }

            this.items[index][key] = Number(this.items[index][key])
        
            let settings = [];
            let employee_id = data.id;
    
            let items = this.items;
           
            let loader = this.$loading.show();
            let year = new Date().getFullYear();

            this.updateTable(items); 
            
             axios 
                .post("/timetracking/analytics/update-stat", {
                    month: this.month.month,
                    year: this.month.currentYear,
                    group_id: this.activity.group_id,
                    employee_id: employee_id,
                    id: this.activity.id,
                    day: key,
                    value: value,
                })
                .then((response) => {
                    loader.hide();
                });
            
        },

        exportData() {
            var link = "/timetracking/analytics/activity/export";
            link += "?month=" + this.$moment(
                        `${this.month.currentMonth}`,
                        "MMMM YYYY"
                    ).format("MM");
            link += "&year=" + new Date().getFullYear();
            window.location.href = link;
        },

        calculateRecordsValues() {
            this.sum = {};
            this.itemsArray = [];
            this.totalCountDays = 0;
            this.avgOfAverage = 0;
            this.percentage = []

            let row0_avg = 0; 
            let row0_avg_items = 0;

            this.records.forEach((account, index) => {
                let countWorkedDays = 0;
                let cellValues = [];
                

                
                if (account.name != this.totalRowName) {
                    let sumForOne = 0;
                    for (let key in account) {
                        let value = account[key];
                        
                        if (key >= 1 && key <= 31) {
                            cellValues[key] = Number(value);

                            if (isNaN(this.sum[key])) this.sum[key] = 0;
                            
                            if (isNaN(this.percentage[key])) this.percentage[key] = 0;
                            
                            this.sum[key] = this.sum[key] + account[key]; // vertical sum
                            
                            
                            
                        
                            
                            if(account[key] > 0) {
                                this.percentage[key] = this.percentage[key] + 1;
                            }


                            if (account[key] > 0) {
                                sumForOne += account[key]; // horizontal sum
                                countWorkedDays++;
                                this.totalCountDays++;
                            }
                        }
                    }
                    
                    cellValues["plan_unit"] = this.activity.plan_unit;
                    cellValues["plan"] = sumForOne * this.price;
                    cellValues["count"] = sumForOne;
                }

                
            

                this.itemsArray.push({
                    name: account.name,
                    lastname: account.lastname,
                    fullname: account.fullname,
                    id: account.id,
                    editable: false,
                    group: account.group,
                    email: account.email,
                    show_cup: 0,
                    ...cellValues,
                });  
                
            });

            
        },

        toFloat(number) {
            return Number(number).toFixed(2);
        },

        sort(field) {

            if(this.sorts[field] === undefined) {
                this.sorts[field] = 'asc';
            } 

            let item = this.items[0];

            this.items.shift();
            if(this.sorts[field] === 'desc') {
                if(field == 'name') {
                    this.items.sort((a, b) => (a[field] > b[field]) ? 1 : -1);
                } else {
                    this.items.sort((a, b) => (Number(a[field]) > Number(b[field])) ? 1 : -1);
                }
              
                this.sorts[field] = 'asc';
            } else {
                if(field == 'name') {
                    this.items.sort((a, b) => (a[field] < b[field]) ? 1 : -1);
                } else {
                    this.items.sort((a, b) => (Number(a[field]) < Number(b[field])) ? 1 : -1);
                }
                this.sorts[field] = 'desc';
            }
            
            this.items.unshift(item);
        },
    },
};
</script>

<style lang="scss">

</style>
