<template>
<div class="row">
    <!-- <div class="col-md-3">
        <p>Оклад </p>
        <p><b><span>{{ data.oklad }}</span></b></p>
    </div> -->
    <div class="flexy"  v-bind:class="[activeClass]">
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
    <div class="flexy"  v-bind:class="[activeClass]">
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
        <p class="mb-0  font-bold">KPI <span></span></p>
        <p class="text-center">{{ data.kpi }}</p>
    </div>
    <div class="flexy"  v-bind:class="[activeClass]">
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

    <div class="flexy"   v-bind:class="[activeClass]"  v-if="this.quarters.length != 0"  v-show="visible"  style="margin-top: 11px">
        <div class="filler" @click="showQuartalBonusSidebar = true" style="top: 10px;left: 15px;">
            <img src="/images/bonus_type_2.png"
                alt="icon"
                class="img-fluid w-120 back">
            <div class="front" style="height:100%">
                <img src="/images/bonus_type_2.png"
                    alt="icon"
                    class="img-fluid w-120">
            </div>
        </div>
        <p class="mb-0  font-bold">Квартальная премия <span></span></p>
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
      :open="showQuartalBonusSidebar"
      @close="showQuartalBonusSidebar = false"
      width="40%"
    >

        <div v-if="this.quarters.length > 0">
            <div class="mt-2"  v-for="quarter in this.quarters">
                <p class="font-bold mb-0" v-if="quarter.quartal === 1">Квартальная премия период с 01.01.2020 до 31.03.{{quarter.year}}</p>
                <p class="font-bold mb-0" v-if="quarter.quartal === 2">Квартальная премия период с 01.03.2020 до 31.06.{{quarter.year}}</p>
                <p class="font-bold mb-0" v-if="quarter.quartal === 3">Квартальная премия период с 01.06.2020 до 31.09.{{quarter.year}}</p>
                <p class="font-bold mb-0" v-if="quarter.quartal === 4">Квартальная премия период с 01.09.2020 до 31.12.{{quarter.year}}</p>
                <table class="table table-bordered table-sm ue-table">
                    <tr class="colspan">
                        <td class="text-left" style="width: 30%">Сумма</td>
                        <td>{{ quarter.sum }}</td>
                    </tr>
                    <tr>
                        <td class="text-left" style="width: 30%">Комментарии</td>
                        <td>{{ quarter.text }}</td>
                    </tr>

                </table>
            </div>
        </div>

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
        quarters: {
            type: Array
        },
    },
    data() {
        return {
            visible: true,
            activeClass:this.quarters.length == 0 ? 'col-md-4' : 'col-md-3',
            showQuartalBonusSidebar:false,
            showBonusSidebar: false,
            showKpiSidebar: false,
            showSalarySidebar: false,
            editedBonus: null,
            activities: [],
            groups: {},
            kpis: [],
            bonus_groups: [],
        };
    },
    created() {

        console.log(this.data,'imasheev')
    },
    methods: {
        openQuartal(){
            this.fetchData({
                data_from: {
                    year: new Date().getFullYear(),
                    month: this.$moment(this.month, 'MMMM').format('M')
                },
                user_id: this.activeuserid
            })
            this.showQuartalBonusSidebar = true
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
        openKpi(){
            this.fetchData({
                data_from: {
                    year: new Date().getFullYear(),
                    month: this.$moment(this.month, 'MMMM').format('M')
                },
                user_id: this.activeuserid
            })
            this.showKpiSidebar = true
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
        fetchData(filters) {
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
    