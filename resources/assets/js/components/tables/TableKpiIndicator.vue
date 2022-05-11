<template>
    <tr class="activitty" v-if="!kpi_indicator.deleted">  
        <td v-if="is_admin">
            <input type="checkbox" v-model="kpi_indicator.checked">
        </td>
        <td class="left">
            <input type="text" v-if="is_admin" class="form-control" v-model="kpi_indicator.name" /><span v-else>{{ kpi_indicator.name }}</span>
        </td>
        <td v-if="is_admin">
            <select v-model="kpi_indicator.plan_unit" class="form-control">
                <option value="minutes" selected>Сумма</option>
                <option value="percent">Среднее значение</option>
                <option value="less_sum">Не более, сумма</option>
                <option value="less_avg">Не более, сред. зн.</option>
                <option value="more_sum" v-if="type == 'individual'">Не менее, сумма</option>
            </select>
        </td>
        <td v-if="type == 'individual'">
            <select v-model="kpi_indicator.group_id" class="form-control" v-if="is_admin">
                <option value="0" selected>-</option>
                <option v-for="group in this.groups" :value="group.id" :key="group.id">{{ group.name }}</option>
            </select>
            <div v-else>{{ kpi_indicator.groups.find(x => x.id == kpi_indicator.group_id) !== unedfined ? kpi_indicator.groups.find(x => x.id == kpi_indicator.group_id).name : kpi_indicator.group_id }}</div>
        </td>
    
        <td v-if="is_admin">
            
            
            <div class="d-flex">
                <select v-if="type == 'common'" v-model="kpi_indicator.activity_id" class="form-control form-control-sm">
                    <option :value="0">Не связан</option>
                    <option :value="activity.id"  v-for="activity in activities" :key="activity.id">{{ activity.name }}</option>
                </select>
                <select v-else v-model="kpi_indicator.activity_id" class="form-control form-control-sm">
                    <option :value="0">Не связан</option>
                    <option :value="-1">Ячейка из сводной</option>
                    <option :value="activity.id"  v-for="activity in activities[kpi_indicator.group_id]" :key="activity.id">{{ activity.name }}</option>
                </select>

                <input v-if="kpi_indicator.activity_id == -1"
                    type="text"
                    v-model="kpi_indicator.cell"
                    placeholder="A1"
                    class="form-control kpi-cell">
            </div>
            
        </td>
        <td v-if="is_admin">
            <select v-model="kpi_indicator.unit" class="form-control">
                <option value="" selected></option>
                <option value="%">%</option>
                <option value="мин">мин</option>
            </select>  
        </td>
        <td :title="planPlaceholder" >
            <div v-if="is_admin">
                <div v-if="showInput" class="d-flex justify-content-center">
                    <b-input type="number" min="0" v-if="is_admin" class="form-control number_input mr-2" v-model="kpi_indicator.daily_plan"  />
                    <div style="display:inline-block" @click="showInput = !showInput"><i  class="fa fa-check"></i></div>
                </div>
                <div v-else>{{ plan }} <div style="display:inline-block" @click="showInput = !showInput"><i  class="fa fa-pencil"></i></div></div>
            </div>
            <span v-else>{{ plan }}</span>
            
        </td>
        <td v-if="!is_admin">{{ kpi_indicator.completed_value }}</td>
        <td>
            <div class="d-flex justify-content-center">
                <b-input type="number" min="0" max="100" v-if="is_admin" class="form-control number_input mr-2" v-model="kpi_indicator.ud_ves"  /><span v-else>{{ kpi_indicator.ud_ves }}</span>
            </div>
        </td>
        <td>{{ kpi_indicator.sum_prem }}</td>
        <td >
            <b-input type="number" v-if="is_admin" class="form-control number_input" v-model="kpi_indicator.completed"  /><span v-else>{{ kpi_indicator.completed }}</span>
        </td>
        <td>{{ kpi_indicator.result }}</td>
    </tr>
    
</template>

<script>
export default {
    name: "TableKpiIndicator",
    props:  {
        kpi_indicator: {
            default: null, 
        },
        is_admin: {
            default: false,
        },
        nijn_porok: {
            default: 80,
        },
        verh_porok: {
            default: 100,
        },
        kpi_80_99: {
            default: 0,
        },
        kpi_100: {
            default: 0,
        },
        activities: {
            default: null,
        },
        workdays: {
            default: 27,
        },
        type: {
            default: 'common',
        },
        groups: {
            default: null
        }
    },
    watch: {
        kpi_indicator: {
            handler: function(newValue) {this.recalc()},
            deep: true
        },
        nijn_porok: {
            handler: function(newValue) {this.recalc()},
        },
        verh_porok: {
            handler: function(newValue) {this.recalc()},
        },
        kpi_80_99: {
            handler: function(newValue) {this.recalc()},
        },
        kpi_100: {
            handler: function(newValue) {this.recalc()},
        }
    },
    data() {
        return {
            planPlaceholder: '',
            showInput: false,
        };
    },
    computed: {
        plan() {
            if(this.kpi_indicator.plan_unit == 'minutes') {
                

                if(this.type == 'individual') {
                    return this.kpi_indicator.daily_plan; //* Number(this.kpi_indicator.workdays);
                } else {
                    this.planPlaceholder = this.kpi_indicator.daily_plan.toString() + ' * ' + Number(this.kpi_indicator.workdays);
                    return this.kpi_indicator.daily_plan * Number(this.kpi_indicator.workdays);
                }
                
            }

            if(this.kpi_indicator.plan_unit == 'percent' || this.kpi_indicator.plan_unit == 'less_avg' || this.kpi_indicator.plan_unit == 'less_sum') {
                return this.kpi_indicator.daily_plan.toString() + this.kpi_indicator.unit;
            }
            
        }
    },
    created() {
        this.recalc()
    }, 
    methods: { 
        toggleInput() {
            
        }, 
        recalc() {
            this.result();
            this.sum_prem();
        },
        sum_prem() {
            this.kpi_indicator.sum_prem = Number(
                this.kpi_100 * (parseFloat(this.kpi_indicator.ud_ves) / 100.0)
            ).toFixed();
        },
        result() {
            let result; //=ЕСЛИ(F9>$D$3;ЕСЛИ(F9<$E$3;$B$3*D9*(F9-$D$3)*$E$3/($E$3-$D$3);$B$4*D9*F9);0)

            let nijn_porok = parseFloat(this.nijn_porok) / 100.0
            let verh_porok = parseFloat(this.verh_porok) / 100.0
            let completed = parseFloat(this.kpi_indicator.completed) / 100.0
            let ud_ves = parseFloat(this.kpi_indicator.ud_ves) / 100.0
            let kpi_80_99 = this.kpi_80_99
            let kpi_100 = this.kpi_100

            if(completed > nijn_porok) {
                
                if (completed < verh_porok) {
                    result = kpi_80_99 * ud_ves * (completed - nijn_porok) * verh_porok / (verh_porok - nijn_porok)
                } else {
                    result = kpi_100 * ud_ves * completed
                }
            } else {
                result = 0;
            }

            
            if (result < 0) result = 0;
            this.kpi_indicator.result = Number(result).toFixed(1);
        },
    }
    
};
</script>

<style lang="scss" scoped>
.number_input {
    width: 100px;
    display: inline-block;
    text-align: center;

    &.form-control {
        padding-left: 23px;
    }
}

.form-control {
    padding: 2px 7px;
    font-size: 14px;
    border: 0;
}

.call-norm {
    font-size: 18px;
    font-weight: 700;
    padding: 15px 0;
    color: #333;
    margin-bottom: 0;
}

.td-transparent {
    border-bottom-color: transparent !important;
    border-left-color: transparent !important;
}
select.form-control:not([size]):not([multiple]) {
    height: auto;
}

.table-bordered {
    th {
        font-weight: 600;
    }

    td,
    th {
        border: 1px solid #dee2e6;
        vertical-align: middle;
        text-align: center;

        &.left {
            text-align: left;
        }

        &.bold {
            font-weight: 600;
        }

        &.mark {
            background: aliceblue;
            color: #0077e0;
        }
    }
}
.kpi-cell {
    width: 58px;
    text-transform: uppercase;
    border: 1px solid #dee2e6;
    background: #fbfbfb;
    text-align: center;
    margin-left: 5px;
    font-weight: 600;
}
</style>
