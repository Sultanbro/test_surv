<template>
<div>
    <div class="d-flex">  
        <b-table responsive striped class="text-nowrap text-right my-table  my-table-ma mb-3 recruiter_stats" id="recruiter_stats" :small="true" :bordered="true" :items="items[currentDay]" :fields="fields" primary-key="a" :key="componentKey">
            
            <template slot="cell()" slot-scope="data">
                <div>{{ data.value }}</div>
            </template> 

            <template slot="cell(name)" slot-scope="data">
                <div class="d-flex justify-between bg-white">
                    <div>{{ data.value }}</div>
                    <select class="form-control form-control-sm special-select" 
                        v-model="data.item.profile" v-if="data.value != 'ИТОГО' && ![9974,9975,5263,7372].includes(data.item.user_id)"
                        @change="changeProfile(data.index)">
                        <option value="0">кз</option>
                        <option value="1">все удаленные</option>
                        <option value="2">вацап</option>
                        <option value="3">уведомления</option>
                        <option value="4">inhouse</option>
                        <option value="5">иностранные</option>
                        <option value="6">hh</option>
                    </select>
                </div>
            </template> 

            <template slot="cell(agrees)" slot-scope="data">
                <div v-html="data.value"></div>
            </template>            

        </b-table>
            
        <div class="ml-3 f-200">
            <button class="mt- mb-2 text-black fz-14 btn btn-primary btn-sm rounded" @click="showModal = !showModal" v-if="editable">
                <b>Кол-во лидов</b>
            </button>
            <select class="form-control form-control-sm" v-model="currentDay">
                <option v-for="day in days" :value="day" :key="day">{{day}}</option>
            </select>
            <p class="mt-2 mb-2 text-black fz-14">
                <b> Стандарт звонков:</b><br>
                <span class="aaa fz-12 text-red mb-2">20 звонков от 10 секунд (чтобы их сделать, нужно просто делать больше наборов в час)</span>
                <span class="aaa fz-12 text-red">30 минут диалога</span>
                <span class="aaa fz-12 text-red">2 согласия</span>
            </p>
        </div>
    </div>
    


    <b-modal v-model="showModal"  hide-footer title="Количество лидов">
        <div class="leads" v-for="lead in leads[currentDay]">
            <div class="d-flex justify-content-between">
                <p><b> {{ lead.name }}</b></p>
                <p class="ml-2">{{ lead.count }}</p>
            </div>
        </div>
    </b-modal>


</div>
</template>

<script>
export default {
    name: "TableRecruiterStats",
    props: {
        data: Array,
        rates: Array,
        leads_data: {
            default: [],
            type: Array,
        },
        daysInMonth: {
            default: new Date().getDate(),
            type: Number,
        },
        year: {
            default: new Date().getFullYear(),
            type: Number,
        },
        month: {
            default: Number(new Date().getMonth()) + 1,
            type: Number,
        },
        editable: {
            default: false,
            type: Boolean,
        }
    },
    data: function () {
        return {
            items: [],
            leads: [],
            days: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
            currentDay: 1,
            componentKey: 0,
            fields: [
                {
                    key: "name",
                    label: 'Сотрудники',
                    variant: "title",
                    class: "text-left rownumber b-table-sticky-column"
                },
                {
                    key: "agrees",
                    label: "Согласия",
                    variant: "title",
                    class: "text-center t-name"
                },
            ],
            showModal: false
        };
    },
    watch: {
        data: {
            immediate: true, 
            handler (val, oldVal) {
                this.fields[0].label = 'Сотрудники: ' + this.rates[this.currentDay];
                this.items  = this.data;
                this.leads  = this.leads_data;
                this.componentKey++;
            }
        },
        currentDay: {
            handler (val) {
                this.fields[0].label = 'Сотрудники: ' + this.rates[val];
                this.items  = this.data;
                this.leads  = this.leads_data;
                this.componentKey++;
            }
        },
    },
    mounted() {
        this.setFields()
        this.currentDay = this.daysInMonth
    },
    methods: {
      

        setFields() {

            let times = {                
                9: '09-10',
                10: '10-11',
                11: '11-12',
                12: '12-13',
                13: '13-14',
                14: '14-15',
                15: '15-16',
                16: '16-17',
                17: '17-18',
                18: '18-19',
            };

            Object.keys(times).forEach(key => {
                this.fields.push({
                    key: `${key}`,
                    label: times[key],
                    class: `day`
                });
            })
 
        },

        changeProfile(index) {
            
            if(!this.editable) return '';

            axios.post('/timetracking/analytics/recruting/change-profile', {
                user_id: this.items[this.currentDay][index]['user_id'],
                profile: this.items[this.currentDay][index]['profile'],
                day: this.currentDay,
                month: this.month,
                year: this.year,
            }).then(response => {
                this.$message.success('Успешно!');
            }).catch(error => {
                this.$message.error('Ошибка!');
                //alert(error)
            });
        }
   
    }
};
</script>
<style lang="scss">
.recruiter_stats {
    &.my-table .day {
        min-width: 63px;
    }
    &::-webkit-scrollbar-track {
        background: #ffffff;
    }
    table{
        max-width: 100px;
    }   
    
    th {
        background: #1176b0!important;
        color: #fff !important;
        font-weight: 400;
    }
}
.f-200 {
        flex: 0 0 200px;
}
.bg-white {
    background: #fff !important;
}
p.heading {
    color: black;
    font-weight: 600;
    font-family: 'Open sans', sans-serif;
}
.fz14 {
    font-size: 14px;
}
.fz-12 {
    font-size: 12px;
}
.text-red {
    color:red
}
.special-select {
    padding: 0;
    height: 20px !important;
    border: none;
    background: #1076b0;
    color: #fff;
        width: 45px;
    margin-left: 9px;
    font-size: 11px;    
    cursor: pointer;
}

.special-select:focus,
.special-select:active {
    background: #1076b0;
    color: #fff;    
}
.justify-between {
    justify-content: space-between;
}
.leads * {
    font-size: 12px;
    margin-bottom: 0;
}
span.aaa {
    font-size: 12px;
    margin-bottom: 12px !important;
    line-height: 15px;
    display: block;
}
</style>
