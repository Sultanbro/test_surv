<template>
<div class="super-filter"  v-click-outside="close">

    <input 
        class="searcher mr-2"
        v-model="searchText"
        type="text"
        placeholder="Поиск по совпадениям..."
        @click="show = true"
        @keyup="$emit('search-text-changed', this.searchText)"
    >

    <div class="block" :class="{'show': show}">
        <!-- item -->
        <div class="item">
            <div class="label">Отдел</div>
            <select v-model="group_id" class="form-control " @change="change('group_id')">
                <option v-for="key in Object.keys(groups)" :key="key"
                    :value="key">
                    {{ groups[key] }}
                </option>
            </select>
        </div>

        <!-- item -->
        <div class="item d-flex" >
            <div class="label">Дата создания</div>
            <select v-model="created_at.variant" class="form-control mr-2" @change="changeDate('created_at', 'variant')">
                <option v-for="key in Object.keys(dates)" :key="key"
                    :value="key">
                    {{ dates[key] }}
                </option>
            </select>

            <!-- Choose month and year -->
            <select v-model="created_at.month" class="form-control mr-2" v-if="created_at.variant == 5" @change="changeDate('created_at', 'month')">
                <option v-for="(month, i) in $moment.months()" :value="i + 1" :key="month">{{month}}</option>
            </select>

            <select v-model="created_at.year" class="form-control" v-if="created_at.variant == 5" @change="changeDate('created_at', 'year')">
                <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
            </select>

            <!-- Choose month and year -->
            <input
                type="date"
                v-if="created_at.variant == 6"
                v-model="created_at.from"
                class="form-control form-control-sm mr-2"
                @change="changeDate('created_at', 'from')" />
            <input
                type="date"
                v-if="created_at.variant == 6"
                v-model="created_at.to"
                class="form-control form-control-sm"
                @change="changeDate('created_at', 'to')" />
            
        </div>
        
        <!-- search button -->
        <div class="mt-3">
            <button class="btn btn-primary" @click="applyFilter">
                <i class="fa fa-search mr-2"></i>
                <span>Найти</span>
            </button>
        </div>
    </div>


</div>
</template>

<script>
export default {
    name: "SuperFilter", 
    props: {
        groups: {
            default: []
        }
    },
    data() {
        return {
            show: false,
            searchText: '',

            // options
            years: [],
            dates: {},
            
            filters: {},
            // filters
            group_id: 0,
            created_at: {
                variant: 0,
                month: new Date().getMonth() + 1,
                year: new Date().getFullYear(),
                from: null,
                to: null
            }
        }
    }, 

    created() {
        this.prepare()
    },

    methods: {

        prepare() {
            this.fillYears()
            this.fillFiltersObj()
        },

        clear() {
            this.searchText = ''
        },

        fillFiltersObj() {
            this.group_id = 0; 
            
           

            this.dates = {
                0: 'Любая дата',
                1: 'Вчера',
                2: 'Сегодня',
                3: 'Завтра',
                4: 'Текущий месяц',
                5: 'Месяц',
                6: 'Диапазон',
            }

            

        },

        fillYears() {
            let years = []
            let currentYear = new Date().getFullYear();
            for(let i = currentYear; i > 1999; i--) years.push(i)
            this.years = years
        },

        applyFilter() {
            this.$emit('apply', this.filters)
            this.show = false;
        },

        change(field) {
            if(field == 'group_id') {
                if(this.group_id == 0) {
                    delete this.filters.group_id;
                } else {
                    this.filters.group_id = this.group_id
                }
            }
        },

        changeDate(field, prop) {
            if(field == 'created_at') {
                this.filters.created_at = this.created_at
            }
        },

        close() {
            this.show = false
        }
     
    } 
}
</script>