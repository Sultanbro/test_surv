<template>
    <div class="settings-company-shifts">
        <b-button variant="success" class="mb-2" @click="createNewShift">Создать новую смену</b-button>
        <div class="table-container" v-if="shiftsData.length">
            <b-table-simple
                    id="awards-table"
                    bordered
                    :hover="false"
                    class="table-shifts"
            >
                <b-thead>
                    <b-tr>
                        <b-th>№</b-th>
                        <b-th>Название</b-th>
                        <b-th>Рабочий график</b-th>
                        <b-th>Выходные</b-th>
                        <b-th class="w-100px"></b-th>
                    </b-tr>
                </b-thead>
                <b-tbody>
                    <b-tr v-for="(shift, index) in shiftsData" :key="index">
                        <b-td>{{index + 1}}</b-td>
                        <b-td>{{shift.name}}</b-td>
                        <b-td>с {{shift.workStartTime}} по {{shift.workEndTime}}</b-td>
                        <b-td>
                            <div class="weekdays">
                                <template v-for="(day, index) in convertWeekdaysString(shift.weekdaysString)">
                                    <div class="weekday" v-if="shift.weekdaysString[index] === '1'" :key="day">{{day}}
                                    </div>
                                </template>
                            </div>
                            <div v-if="shift.weekdaysString === '0000000'">
                                Без выходных
                            </div>
                        </b-td>
                        <b-td>
                            <div class="d-flex mx-2">
                                <b-button class="btn btn-primary btn-icon" @click="editShift(shift, index)"><i
                                        class="fa fa-edit"></i></b-button>
                                <b-button class="btn btn-danger btn-icon" @click="deleteShift(index)"><i class="fa fa-trash"></i></b-button>
                            </div>
                        </b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </div>
        <div class="mt-4" v-else>
            <h4>Нет ни одной смены</h4>
        </div>

        <sidebar
                id="edit-shift-sidebar"
                :title="sidebarName ? sidebarName : 'Сертификат'"
                :open="showSidebar"
                @close="showSidebar = false"
                width="600px"
        >
            <b-form @submit.prevent="onSubmit">
                <b-form-group label="Название графика" label-cols="4">
                    <b-form-input v-model="form.name"/>
                </b-form-group>
                <div
                        id="workShedule"
                        class="form-group work-schedule row"
                >
                    <label
                            for="workStartTime"
                            class="col-sm-4 col-form-label"
                    >Рабочий график</label>
                    <div class="col-sm-8 form-inline">
                        <input
                                name="work_start_time"
                                v-model="form.workStartTime"
                                type="time"
                                id="workStartTime"
                                class="form-control mr-2 work-start-time"
                        >
                        <label for="workEndTime" class="col-form-label mx-3">До </label>
                        <input
                                name="work_start_end"
                                v-model="form.workEndTime"
                                type="time"
                                id="workEndTime"
                                class="form-control mx-2 work-end-time"
                        >
                    </div>
                </div>
                <div id="weekdays" class="form-group row">
                    <label for="weekdays-input" class="col-sm-4 col-form-label">Выходные</label>
                    <div class="col-sm-8 form-inline weekdays-container">
                        <input
                                name="weekdays"
                                type="hidden"
                                v-model="form.weekdaysString"
                                id="weekdays-input"
                        >

                        <div
                                class="weekday"
                                :class="{'active': weekdays[0] === '1'}"
                                data-id="1"
                                @click="toggleWeekDay(0)"
                        >Пн
                        </div>
                        <div
                                class="weekday"
                                :class="{'active': weekdays[1] === '1'}"
                                data-id="2"
                                @click="toggleWeekDay(1)"
                        >Вт
                        </div>
                        <div
                                class="weekday"
                                :class="{'active': weekdays[2] === '1'}"
                                data-id="3"
                                @click="toggleWeekDay(2)"
                        >Ср
                        </div>
                        <div
                                class="weekday"
                                :class="{'active': weekdays[3] === '1'}"
                                data-id="4"
                                @click="toggleWeekDay(3)"
                        >Чт
                        </div>
                        <div
                                class="weekday"
                                :class="{'active': weekdays[4] === '1'}"
                                data-id="5"
                                @click="toggleWeekDay(4)"
                        >Пт
                        </div>
                        <div
                                class="weekday"
                                :class="{'active': weekdays[5] === '1'}"
                                data-id="6"
                                @click="toggleWeekDay(5)"
                        >Сб
                        </div>
                        <div
                                class="weekday"
                                :class="{'active': weekdays[6] === '1'}"
                                data-id="0"
                                @click="toggleWeekDay(6)"
                        >Вс
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <b-button type="submit" variant="success">Сохранить</b-button>
            </b-form>
        </sidebar>
    </div>
</template>


<script>
    export default {
        name: 'shifts',
        data() {
            return {
                shiftsData: [],
                shiftEditIndex: null,
                sidebarName: 'Создание новой смены',
                showSidebar: false,
                form: {
                    name: '',
                    workStartTime: null,
                    workEndTime: null,
                    weekdaysString: null
                },
                weekdaysNames: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                weekdays: '0000000'.split(''),
            }
        },
        methods: {
            convertWeekdaysString(text) {
                const daysArr = text.split('');
                for (let i = 0; i < daysArr.length; i++) {
                    daysArr[i] = this.weekdaysNames[i];
                }
                return daysArr;
            },
            createNewShift() {
                this.showSidebar = !this.showSidebar;
                this.sidebarName = 'Создание новой смены';
            },
            editShift(shift, index) {
                this.shiftEditIndex = index;
                this.showSidebar = !this.showSidebar;
                this.form.name = shift.name;
                this.form.workStartTime = shift.workStartTime;
                this.form.workEndTime = shift.workEndTime;
                this.form.weekdaysString = shift.weekdaysString;
                this.weekdays = shift.weekdaysString.split('');
                this.sidebarName = `Редактирование ${shift.name}`;
            },
            deleteShift(index){
                if (window.confirm("Вы уверены, что хотите удалить смену?")) {
                    this.shiftsData.splice(index, 1);
                }
            },
            toggleWeekDay(id) {
                this.$set(this.weekdays, id, this.weekdays[id] === '1' ? '0' : '1');
            },
            resetForm() {
                this.shiftEditIndex = null;
                this.form.name = '';
                this.form.workStartTime = null;
                this.form.workEndTime = null;
                this.form.weekdaysString = null;
                this.weekdays = '0000000'.split('');
            },
            onSubmit() {
                this.form.weekdaysString = this.weekdays.join('');
                const data = Object.assign({}, this.form);
                if (this.shiftEditIndex !== null) {
                    this.shiftsData[this.shiftEditIndex] = data;
                } else {
                    this.shiftsData.push(data);
                }
                this.showSidebar = false;
                this.resetForm();
            }
        }
    }
</script>


<style lang="scss" scoped>
    .table-shifts {
        tbody {
            th, td {
                padding: 3px 15px !important;
            }
        }

        .weekdays {
            display: flex;
            align-items: center;
            justify-content: center;

            .weekday {
                width: 25px;
                height: 25px;
                font-size: 14px;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-right: 5px;
                background-color: green;
                color: #fff;

                &:last-child {
                    margin-right: 0;
                }
            }
        }
    }

    #edit-shift-sidebar {
        form {
            padding-right: 10px;
        }

        .work-schedule {
            .form-inline {
                margin-left: -7px;
            }
        }

        .col-form-label {
            color: #8DA0C1 !important;
            font-size: 16px;
        }

        .weekdays-container {
            display: flex;
            align-items: center;

            .weekday {
                width: 35px;
                height: 35px;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-right: 5px;
                cursor: pointer;
                color: #333;
                border: 1px solid #ddd;

                &.active {
                    background-color: green;
                    color: #fff;
                    border: 1px solid green;
                }
            }
        }
    }
</style>