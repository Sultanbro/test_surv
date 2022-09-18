<template>
<div class="row jcsb px-5">
    <div class="flexy col-md-3" >
        <div class="filler" @click="showSalarySidebar = true" id="hoverPulse">
            <img src="/images/money1.png"
                alt="icon"
                class="img-fluid w-120 back">
            <div class="front" :style="'height:'+ data.salary_percent +'%'">
                <img src="/images/money1.png"
                    alt="icon"
                    class="img-fluid w-120">
            </div>
        </div>
        <p class="mb-0 font-bold">Баланс оклада<span></span></p>
        <p class="text-center">{{ data.salary }}</p>
    </div>
    <div class="flex ycol-md-3" >
        <div class="filler" @click="openKpi">
            <img src="/images/money2.png"
                alt="icon"
                class="img-fluid w-120 back">
            <div class="front" :style="'height:'+ data.kpi_percent +'%'">
                <img src="/images/money2.png"
                    alt="icon"
                    class="img-fluid w-120">
            </div>
        </div>
        <p class="mb-0  font-bold text-center">KPI <span></span></p>
        <p class="text-center">{{ data.kpi }}</p>
    </div>
    <div class="flexy col-md-3">
        <div class="filler" @click="showBonusSidebar = true" style="top:15px">
            <img src="/images/money3.png"
                alt="icon"
                class="img-fluid w-120 back">
            <div class="front" style="height:100%">
                <img src="/images/money3.png"
                    alt="icon"
                    class="img-fluid w-120">
            </div>
        </div>
        <p class="mb-0  font-bold">Бонусы <span></span></p>
        <p class="text-center">{{ data.bonus }}</p>
    </div>

    <div class="flexy col-md-3"  v-if="has_quartal_premiums" style="margin-top: 11px">
        <div class="filler" @click="openQuartalPrems" style="top: 10px;left: 15px;">
            <img src="/images/bonus_type_2.png"
                alt="icon"
                class="img-fluid w-120 back">
            <div class="front" style="height:100%">
                <img src="/images/bonus_type_2.png"
                    alt="icon"
                    class="img-fluid w-120">
            </div>
        </div>
        <p class="mb-0 font-bold">Квартальная премия <span></span></p>
        <p class="text-center">{{ data.quarter_bonus }}</p>
    </div>

    <sidebar
        title="Баланс оклада"
        :open="showSalarySidebar"
        @close="showSalarySidebar = false"
        width="40%">
        
        <div class="mt-2">
            <table class="table table-bordered table-sm ue-table">
                <tr>
                    <td class="text-left">На данный момент ваш оклад</td>
                    <td>{{ data.oklad }}</td>
                </tr>
                <tr>
                    <td class="text-left">Уже заработали в этом месяце</td>
                    <td>{{ data.salary }}</td>
                </tr>
                <tr>
                    <td class="text-left">Вы работаете у нас</td>
                    <td>{{ data.salary_info.worked_days }} дней</td>
                </tr>
                <tr v-if="data.salary_info.indexation_sum > 0" class="text-left">
                    <td>До повышения оклада на {{ data.salary_info.indexation_sum  }} KZT</td>
                    <td>{{ data.salary_info.days_before_indexation }} дней</td>
                </tr>
                <tr v-if="data.salary_info.indexation_sum > 0" class="text-left">
                    <td>В течение года после принятия на работу ваш оклад вырастет на</td>
                    <td>{{ data.salary_info.indexation_sum * 4 }} KZT</td>
                </tr>
            </table>
        </div>
        <p></p>
    </sidebar>


    <sidebar
      title="KPI"
      :open="showKpiSidebar"
      @close="showKpiSidebar = false"
      v-if="showKpiSidebar"
      width="80%"
    >
     <!-- table -->
        <t-stats 
            :activities="activities"
            :groups="groups"
            :items="kpis"
            :editable="false"
        />

    <p class="text-red mt-2">
        * сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
    </p>

    </sidebar>


    <!--<sidebar
      title="Bonus"
      :open="showBonusSidebar"
      @close="showBonusSidebar = false"
      v-if="showBonusSidebar"
      width="80%"
    >
        <t-stats-bonus 
            :groups="bonus_groups"
            :group_names="groups"
            :key="bonus_groups"
        />

    <p class="text-red mt-2">
        * сумма премии за выполнение показателей начнет меняться после достижения 80% от целевого значения на месяц
    </p>

    </sidebar>-->
    <sidebar
      title="Бонусы"
      :open="showBonusSidebar"
      @close="showBonusSidebar = false"
      width="40%"
    >
      <div class="mt-2 p-2">
        <template v-if="data.bonusHistory.length > 0">

          <table class="table table-bordered table-sm">
            <tr>
              <th class="left mark">Дата</th>
              <th class=" mark">Бонус</th>
              <th class=" mark">Комментарии</th>
            </tr>
            <tr v-for="(item, index) in data.bonusHistory" :key="index">
              <td>{{item.date}}</td>
              <td>{{ item.sum }}</td>
              <td v-html="item.comment"></td>
            </tr>
          </table>
            
        </template>

        <div class="mt-2">
            <h6 style="color:#13547e;">Зарабатывайте бонусы выполняя дополнительные активности:</h6>
            <div v-html="data.potential_bonuses"></div>
        </div>

        <div v-if="data.editedBonus !== null" class="mt-5">
            <p style="color:red">Бонус изменен на {{ data.editedBonus.amount }} KZT <br>Комментарии: {{ data.editedBonus.comment }}</p>
        </div>
      </div>
    </sidebar>


    <sidebar
      title="Квартальная премия"
      :open="showQuartalPremiumSidebar"
      @close="showQuartalPremiumSidebar = false"
      width="60%"
    >

        <t-stats-quartal
            :users="quartal_premiums"
            :groups="quartal_groups"
            searchText=""
        />

    </sidebar>
</div>
</template>
    
<script>
export default {
    name: "UserEarnings",

    props: {
        month: {},
        data: Object,
        activeuserid: Number,
        has_quartal_premiums: Boolean
    },

    data() {
        return {
            visible: true, 
            activeClass: 'col-md-4',
            showQuartalPremiumSidebar:false, 
            showBonusSidebar: false,
            showKpiSidebar: false,
            showSalarySidebar: false,
            editedBonus: null,
            activities: [],
            groups: {},
            quartal_premiums: [],
            kpis: [],
            bonus_groups: [],
            quartal_groups: [],
        }
    },

    created() {
        this.activeClass = this.has_quartal_premiums ? 'col-md-3' : 'col-md-4';
    },

    methods: {
        openQuartalPrems(){
            this.fetchQP({
                data_from: {
                    year: new Date().getFullYear(),
                    month: this.$moment(this.month, 'MMMM').format('M')
                },
                user_id: this.activeuserid
            })

            this.showQuartalPremiumSidebar = true
        },

        fetchQP(filters) {
            let loader = this.$loading.show();

            axios.post('/statistics/quartal-premiums', {
                filters: filters 
            }).then(response => {
                
                // items
                this.quartal_premiums = response.data[0].map(res=> ({...res, expanded: false}));
                // this.quartal_premiums = this.quartal_premiums.map(res=> ({...res, my_sum: 0}))
                
                // this.activities = response.data.activities;
                this.quartal_groups = response.data[1].map(res=> ({...res, expanded: false}));

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        openBonus(){
            this.fetchBonus({
                data_from: {
                    year: new Date().getFullYear(),
                    month: this.$moment(this.month, 'MMMM').format('M')
                },
                user_id: this.activeuserid
            })
            this.showBonusSidebar = true
        },

        fetchBonus(filter){
            let loader = this.$loading.show();
            axios.post('/statistics/bonus', {
                filters: filters 
            }).then(response => {
                // items
                this.bonus_groups = response.data;
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

        openKpi(){
            this.fetchKPI({
                data_from: {
                    year: new Date().getFullYear(),
                    month: this.$moment(this.month, 'MMMM').format('M')
                },
                user_id: this.activeuserid
            })
            this.showKpiSidebar = true
        },

       
        fetchKPI(filters) {
            let loader = this.$loading.show();

            axios.post('/statistics/kpi', {
                filters: filters 
            }).then(response => {
                
                // items
                this.kpis = response.data.items;
                this.kpis = this.kpis.map(res=> ({...res, my_sum: 0}))
                
                this.activities = response.data.activities;
                this.groups = response.data.groups;

                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },

    },
};
</script>

<style lang="scss">
.w-120 {
    width: 120px !important;
    
}
.flexy {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.filler {
    position: relative;
    margin-bottom: 25px;
    cursor: pointer;
    .back {
        filter: grayscale(1);
        opacity: 0.3;
    }
    .front {
        position: absolute;
        width: 100%;
        height: 0;
        bottom: 0;
        left:0;
        z-index: 1;
        overflow:hidden;
        img {
            position: absolute;
            bottom: 0;
        }
    }
}
.font-bold {
    font-weight: bold;
}
.ue-table td:first-child {
    text-align: left !important;
}
</style>
    