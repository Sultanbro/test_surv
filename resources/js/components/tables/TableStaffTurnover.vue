<template>
<div>

    <div class="staff-turnover">

        <div class="row mb-2">
            
            <div class="col-12 d-flex align-items-center">

                <div class="my-table table-responsive">
                    <table class="table b-table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-left t-name table-title first-width"></th>
                                <th class="text-center t-name table-title">Январь</th>
                                <th class="text-center t-name table-title">Февраль</th>
                                <th class="text-center t-name table-title">Март</th>
                                <th class="text-center t-name table-title">Апрель</th>
                                <th class="text-center t-name table-title">Май</th>
                                <th class="text-center t-name table-title">Июнь</th>
                                <th class="text-center t-name table-title">Июль</th>
                                <th class="text-center t-name table-title">Август</th>
                                <th class="text-center t-name table-title">Сентябрь</th>
                                <th class="text-center t-name table-title">Октябрь</th>
                                <th class="text-center t-name table-title">Ноябрь</th>
                                <th class="text-center t-name table-title">Декабрь</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stat in staff" :key="stat.id">
                                <td class="text-left t-name table-title">{{ stat.name }}</td>
                                <td class="text-center t-name table-title">{{ stat.m1 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m2 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m3 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m4 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m5 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m6 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m7 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m8 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m9 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m10 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m11 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m12 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            
            <div class="col-12 align-items-center mb-5 mt-5">

                <h4 class="mb-5 text-center">Текучка по группам</h4>
                <div class="my-table table-responsive">
                    <table class="table b-table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-left t-name table-title  first-width"></th>
                                <th class="text-center t-name table-title">Январь</th>
                                <th class="text-center t-name table-title">Февраль</th>
                                <th class="text-center t-name table-title">Март</th>
                                <th class="text-center t-name table-title">Апрель</th>
                                <th class="text-center t-name table-title">Май</th>
                                <th class="text-center t-name table-title">Июнь</th>
                                <th class="text-center t-name table-title">Июль</th>
                                <th class="text-center t-name table-title">Август</th>
                                <th class="text-center t-name table-title">Сентябрь</th>
                                <th class="text-center t-name table-title">Октябрь</th>
                                <th class="text-center t-name table-title">Ноябрь</th>
                                <th class="text-center t-name table-title">Декабрь</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stat in staff_by_group" :key="stat.id">
                                <td class="text-left t-name table-title">{{ stat.name }}</td>
                                <td class="text-center t-name table-title">{{ stat.m1 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m2 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m3 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m4 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m5 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m6 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m7 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m8 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m9 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m10 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m11 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m12 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="col-12  align-items-center mb-5 mt-5">

                <h4 class="mb-5 text-center">Продолжительность работы до 1 мес/ до 3 мес/ более 3 мес</h4>
                <div class="my-table table-responsive">
                    <table class="table b-table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-left t-name table-title  first-width"></th>
                                <th class="text-center t-name table-title">Январь</th>
                                <th class="text-center t-name table-title">Февраль</th>
                                <th class="text-center t-name table-title">Март</th>
                                <th class="text-center t-name table-title">Апрель</th>
                                <th class="text-center t-name table-title">Май</th>
                                <th class="text-center t-name table-title">Июнь</th>
                                <th class="text-center t-name table-title">Июль</th>
                                <th class="text-center t-name table-title">Август</th>
                                <th class="text-center t-name table-title">Сентябрь</th>
                                <th class="text-center t-name table-title">Октябрь</th>
                                <th class="text-center t-name table-title">Ноябрь</th>
                                <th class="text-center t-name table-title">Декабрь</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stat in staff_longevity" :key="stat.id">
                                <td class="text-left t-name table-title">{{ stat.name }}</td>
                                <td class="text-center t-name table-title">{{ stat.m1 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m2 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m3 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m4 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m5 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m6 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m7 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m8 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m9 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m10 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m11 }}</td>
                                <td class="text-center t-name table-title">{{ stat.m12 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</template>

<script>


export default {
    name: "TableStaffTurnover", // Раньше был нужен чтобы собирать скайпы, сейчас собираются стажеры для Zoom обучения
    props: {
        staff: Array,
        staff_by_group: Array,
        staff_longevity: Array,
        causes: Array,
    },
    data: function () {
        return {
            fields: [], // поля таблицы
        };
    },
    watch: {
        // month: {
        //     // the callback will be called immediately after the start of the observation
        //     deep: true,
        //     handler (val, oldVal) {
        //         this.filterTable()
        //     }
        // },
    },
    
    mounted() {
        
    },

    methods: {
        

    }
};
</script>

<style lang="scss" scoped >

.my-table-max {
    max-height: inherit !important;

    .day {
        padding: 0 !important;
        text-align: center;

        &.table-success {
            background-color: #29dc29 !important;
        }

        &.table-danger {
            background-color: #e4585f !important;
        }

        &.Sat,
        &.Sun {
            background-color: #FEF2CB;
        }

    }

}

.cell-input {
    background: none;
    border: none;
    text-align: center;
    -moz-appearance: textfield;
    font-size: .8rem;
    font-weight: normal;
    padding: 0;
    color: #000;
    border-radius: 0;

    &:focus {
        outline: none;
    }

    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}
.my-table td div {
    position: relative !important;
    padding: 0;
    // user-select: none;
    // width: min-content;
    // white-space: pre-line;
}

.my-table-max .day.Sat, .my-table-max .day.Sun {
    background-color: #cedaeb;
    border-color: #dfecfe;
}

input[type="time"]::-webkit-calendar-picker-indicator {
    background: none;
    display:none;
}

</style>
