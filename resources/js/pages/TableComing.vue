<template>
<div class="mt-2 px-3">
    <div class="mb-0">
        <div class="row mb-3">
            <div class="col-3">
                <select class="form-control" v-model="currentGroup" @change="fetchData()">
                    <option v-for="group in groups" :value="group.id" :key="group.id">
                        {{ group.name }}
                    </option>
                </select>
            </div>
            <div class="col-3">
                <select class="form-control" v-model="dateInfo.currentMonth" @change="fetchData()">
                    <option v-for="month in $moment.months()" :value="month" :key="month">
                        {{ month }}
                    </option>
                </select>
            </div>
            <div class="col-2">
                <select class="form-control" v-model="dateInfo.currentYear" @change="fetchData()">
                    <option v-for="year in years" :value="year" :key="year">
                        {{ year }}
                    </option>
                </select>
            </div>
            <div class="col-1">
                <div class="btn btn-primary" @click="fetchData()">
                    <i class="fa fa-redo-alt"></i>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
 
        <div v-if="hasPremission">
            <b-modal v-model="modalVisible" ok-text="Да" cancel-text="Нет" title="Вы уверены?" @ok="setTimeManually" size="md">
                <template v-for="error in errors">
                    <b-alert show variant="danger" :key="error">{{ error }}</b-alert>
                </template>
                <b-form-input v-model="comment" placeholder="Комментарий" :required="true"></b-form-input>
            </b-modal>

            
            <b-table responsive striped :sticky-header="true" class="text-nowrap text-right my-table" id="comingTable" :small="true" :bordered="true" :items="items" :fields="fields" show-empty emptyText="Нет данных">
                <template slot="cell(name)" slot-scope="data">
                    <div>
                        {{ data.value }}
                        <b-badge v-if="data.field.key == 'name'" pill variant="success">{{data.item.user_type}}</b-badge>
                    </div>
                </template>
 
                <template slot="cell()" slot-scope="data">
                    <div @click="setCurrentEditingCell(data)" :class="{ fine: data.item.fines[data.field.key.toString()].length > 0}">
                        <b-form-input @mouseover="$event.preventDefault()" class="form-control cell-input" type="time" :value="data.value" :readonly="true" ondblclick="this.readOnly='';" @change="changeTimeInCell" v-on:keyup.enter="openModal">
                        </b-form-input>
                    </div>
                </template>
            </b-table>
        </div>
        <div v-else>
            <p>У вас нет доступа к этой группе</p>
        </div>
    </div>
</div>
</template>

<script>

export default {
    name: "TableComing",
    props: {
        groups: Array,
        years: Array,
        activeuserid: String,
    },
    watch: {
        // эта функция запускается при любом изменении данных
        data(newData, oldData) {
            if (oldData) {
                this.loadItems();
            }
        },
        scrollLeft(value) {
            var container = document.querySelector(".table-responsive");
            container.scrollLeft = value;
        },
    },
    data() {
        return {
            data: {},
            openSidebar: false,
            sidebarTitle: "",
            sidebarContent: {},
            items: [],
            fields: [],
            errors: [],
            comment: "",
            dayInfoText: "",
            hasPremission: false,
            dateInfo: {
                currentMonth: null,
                currentYear: new Date().getFullYear(),
                monthEnd: 0,
                workDays: 0,
                weekDays: 0,
                daysInMonth: 0,
            },
            dataLoaded: false,
            currentGroup: null,
            currentTime: null,
            maxScrollWidth: 0,
            currentEditingCell: null,
            scrollLeft: 0,
            modalVisible: false,
        };
    },
    created() {
        this.dateInfo.currentMonth = this.dateInfo.currentMonth ?
            this.dateInfo.currentMonth :
            this.$moment().format("MMMM");
        let currentMonth = this.$moment(this.dateInfo.currentMonth, "MMMM");

        //Расчет выходных дней
        this.dateInfo.monthEnd = currentMonth.endOf("month"); //Конец месяца
        this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [
            6,
        ]); //Колличество выходных
        this.dateInfo.daysInMonth = currentMonth.daysInMonth(); //Колличество дней в месяце
        this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays; //Колличество рабочих дней

        //Текущая группа
        this.currentGroup = this.currentGroup ?
            this.currentGroup :
            this.groups[0]["id"];

        this.fetchData();
    },
    methods: {
        //Установка выбранного года
        setYear() {
            this.dateInfo.currentYear = this.dateInfo.currentYear ?
                this.dateInfo.currentYear :
                this.$moment().format("YYYY");
        },
        //Установка выбранного месяца
        setMonth() {
            let year = this.dateInfo.currentYear;
            this.dateInfo.currentMonth = this.dateInfo.currentMonth ?
                this.dateInfo.currentMonth :
                this.$moment().format("MMMM");

            this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`;

            let currentMonth = this.$moment(this.dateInfo.currentMonth, "MMMM");
            //Расчет выходных дней
            this.dateInfo.monthEnd = currentMonth.endOf("month"); //Конец месяца
            this.dateInfo.weekDays = currentMonth.weekdayCalc(
                this.dateInfo.monthEnd,
                [6]
            ); //Колличество выходных
            this.dateInfo.daysInMonth = currentMonth.daysInMonth(); //Колличество дней в месяце
            this.dateInfo.workDays =
                this.dateInfo.daysInMonth - this.dateInfo.weekDays; //Колличество рабочих дней
        },
        //Установка заголовока таблицы
        setFields() {
            let fields = [];
            fields = [{
                key: "name",
                stickyColumn: true,
                label: "Имя",
                variant: "primary",
                sortable: true,
                class: "text-left px-3 t-name",
            }, ];

            let days = this.dateInfo.daysInMonth;

            for (let i = 1; i <= days; i++) {
                let dayName = this.$moment(`${i} ${this.dateInfo.date}`, "D MMMM YYYY")
                    .locale("en")
                    .format("ddd");
                fields.push({
                    key: `${i}`,
                    label: `${i}`,
                    sortable: true,
                    class: `day ${dayName}`,
                });
            }
            this.fields = fields;
        },
        //Загрузка данных для таблицы
        fetchData() {
            let loader = this.$loading.show();

            axios
                .post("/timetracking/reports/enter-report", {
                    month: this.$moment(this.dateInfo.currentMonth, "MMMM").format("M"),
                    year: this.dateInfo.currentYear,
                    group_id: this.currentGroup,
                })
                .then((response) => {
                    if (response.data.error && response.data.error == "access") {
                        console.log(response.data.error);
                        this.hasPremission = false;
                        loader.hide();
                        return;
                    }
                    this.hasPremission = true;

                    this.data = response.data;
                    this.setYear();
                    this.setMonth();
                    this.setFields();
                    this.loadItems();
                    this.dataLoaded = true;
                    setTimeout(() => {
                        var container = document.querySelector(".table-responsive");
                        this.maxScrollWidth = container.scrollWidth - container.offsetWidth;
                    }, 1000);
                    loader.hide();
                });
        },
        changeTimeInCell(time) {
            console.log("changeTimeInCell");
            this.currentTime = time;
        },
        setCurrentEditingCell(data) {
            this.currentTime = null;
            this.currentEditingCell = data;
        },
        openModal() {
            this.modalVisible = true;
        },
        setTimeManually() {
            let loader = this.$loading.show();

            if (this.comment.length > 0) {
                axios
                    .post("/timetracking/reports/enter-report/setmanual", {
                        month: this.$moment(this.dateInfo.currentMonth, "MMMM").format("M"),
                        year: this.dateInfo.currentYear,
                        day: this.currentEditingCell.field.key,
                        group_id: this.currentGroup,
                        time: this.currentTime,
                        comment: this.comment,
                        user_id: this.currentEditingCell.item.user_id,
                    })
                    .then((response) => {
                        this.currentEditingCell = null; 
                        this.currentTime = null;
                        this.modalVisible = false;
                        this.comment = "";
                        loader.hide();
                    });
            } else {
                this.errors = ["Комментарий обязателен"];
            }
        },
        //Добавление загруженных данных в таблицу
        loadItems() {
            this.items = this.data;
            
            // if (item.selectedFines[key]) {
            //     fine = item.selectedFines[key]
            // }
        },
    },
};
</script>

<style lang="scss">
.cell-input {
    background: transparent;
    border: none;
    text-align: center;
    -moz-appearance: textfield;
    font-size: 0.8rem;
    font-weight: normal;
    padding: 0;
    color: #000;
    border-radius: 0;
    outline: none;
    height: 100%;

    &:read-only:hover {
        cursor: pointer;
    }

    &:focus {
        background-color: #fff;
        color: #000;
        box-shadow: none;
    }

    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}

.form-control:disabled,
.form-control[readonly] {
    background-color: transparent;
}

.fine {
    background: red;
}
input[type="time"]::-webkit-calendar-picker-indicator {
    background: none;
    display:none;
}
</style>
