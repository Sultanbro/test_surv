<template>
<div class="super-filter"  v-click-outside="close">

    <div class="d-flex relative">
        <input 
            class="searcher mr-2 pr-3"
            v-model="searchText"
            type="text"
            placeholder="Поиск по совпадениям..."
            @click="show = true"
            @keyup="$emit('search-text-changed', this.searchText)"
        >
        <i class="fa fa-search search-btn" @click="applyFilter()" ></i>
    </div>

    <div class="block _active" :class="{'show': show}">
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
        <div class="item">
            <div class="label">Что ищем</div>
            <select v-model="s_type" class="form-control " @change="change(s_type)">
                <option v-for="key in Object.keys(s_types)" :key="key"
                    :value="key">
                    {{ s_types[key] }}
                </option>
            </select>
        </div>

         <!-- item -->
         <div class="item d-flex" >
            <div class="label">Данные за</div>

            <!-- Choose month and year -->
            <select v-model="data_from.month" class="form-control mr-2"  @change="changeDate('data_from', 'month')">
                <option v-for="(month, i) in $moment.months()" :value="i + 1" :key="month">{{month}}</option>
            </select>

            <select v-model="data_from.year" class="form-control"  @change="changeDate('data_from', 'year')">
                <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
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
            s_types: {},
            
            filters: {},
            // filters
            group_id: 0,
            s_type: 1,
            data_from: {
                month: new Date().getMonth() + 1,
                year: new Date().getFullYear(),
                s_type: 1,
            },
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

            this.changeDate('data_from', 'month')
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

            this.s_types = {
                1: 'KPI',
                2: 'Бонусы',
                3: 'Квартальная премия',
            }

            

        },

        fillYears() {
            let years = []
            let currentYear = new Date().getFullYear();
            for(let i = currentYear; i > 1999; i--) years.push(i)
            this.years = years
        },

        applyFilter() {
            console.log(this.filters);
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
            this.data_from.s_type = field;
        },

        changeDate(field, prop) {
            if(field == 'created_at') this.filters.created_at = this.created_at
            if(field == 'data_from')  this.filters.data_from  = this.data_from
        },

        close() {
            this.show = false
        }
     
    } 
}
</script>
  
<style scoped>
.search-btn {
    position: absolute;
    right: 20px;
    top: 12px;
}
.block {
  transform: translateY(20px);
  transition: 1.5s;
}
</style>