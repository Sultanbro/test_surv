<template>
<div id="page-profile">
    <div class="intro content">
        <IntroTop
            :courses="intro['courses']"
            :profit="intro['profit']"
            :estimation="intro['estimation']"
            :indicators="intro['indicators']"
            :class="{ _active: anim.intro }"
        />
        <IntroStats
            @pop="pop"
            ref="intro"
            :class="{ _active: anim.intro }"
        />
        <!-- <new-intro-smart-table/> -->
    </div>
    <MobileProfileSidebar
        v-show="isProfileVisible"
        ref="profileSidebar"
        :class="{ _active: anim.profileSidebar }"
    />
    <Courses
        @init="intro['courses'] = true"
        ref="courses"
        :class="{ _active: anim.courses }"
    />
    <Profit
        @init="intro['profit'] = true"
        ref="profit"
        :class="{ _active: anim.profit }"
    />
    <TraineeEstimation
        @init="intro['estimation'] = true"
        ref="estimation"
        :class="{ _active: anim.estimation }"
    />
    <CompareIndicators
        @init="intro['indicators'] = true"
        ref="indicators"
        :class="{ _active: anim.indicators }"
    />

    <Popup
        v-if="popBalance"
        title="Баланс оклада"
        desc="Заработанная сумма именно от окладной части"
        :open="popBalance"
        @close="popBalance=false"
        :width="popupWidth"
    >
        <Balance/>
    </Popup>

    <Popup
        v-if="popKpi"
        title="Kpi"
        desc="Выполняя дополнительные активности, заработайте больше денег"
        :open="popKpi"
        @close="popKpi=false"
        :width="popupWidth"
    >
        <Kpi/>
    </Popup>

    <Popup
        v-if="popBonuses"
        title="Бонусы"
        desc="Зарабатывайте бонусы, выполняя дополнительные активности"
        :open="popBonuses"
        @close="popBonuses=false"
        :width="popupWidth"
    >
        <Bonuses/>
    </Popup>

    <Popup
        v-if="popQuartalPremiums"
        title="Квартальные премии"
        desc=""
        :open="popQuartalPremiums"
        @close="popQuartalPremiums=false"
        :width="popupWidth"
    >
        <PopupQuartal/>
    </Popup>

    <Popup
        v-if="popNominations"
        title="Номинации"
        :desc="desc"
        :open="popNominations"
        @close="popNominations=false"
        :width="popupWidth"
    >
        <Nominations @get-desc="getDesc"/>
    </Popup>
</div>
</template>

<script>
import IntroTop from '@/pages/Profile/IntroTop.vue'
import IntroStats from '@/pages/Profile/IntroStats.vue'
// import IntroSmartTable from '@/pages/Profile/IntroSmartTable.vue'
import MobileProfileSidebar from '@/pages/Layouts/MobileProfileSidebar.vue'
import Courses from '@/pages/Profile/Courses.vue'
import Profit from '@/pages/Profile/Profit.vue'
import TraineeEstimation from '@/pages/Profile/TraineeEstimation.vue'
import CompareIndicators from '@/pages/Profile/CompareIndicators.vue'
import Popup from '@/pages/Layouts/Popup.vue'
import Balance from '@/pages/Profile/Popups/Balance.vue'
import Kpi from '@/pages/Profile/Popups/Kpi.vue'
import Bonuses from '@/pages/Profile/Popups/Bonuses.vue'
import PopupQuartal from '@/pages/Profile/Popups/PopupQuartal.vue'
import Nominations from '@/pages/Profile/Popups/Nominations.vue'

export default {
	name: 'ProfilePage',
	components: {
		IntroTop,
		IntroStats,
		// IntroSmartTable,
		MobileProfileSidebar,
		Courses,
		Profit,
		TraineeEstimation,
		CompareIndicators,
		Popup,
		Balance,
		Kpi,
		Bonuses,
		PopupQuartal,
		Nominations,
	},
	props: {},
	data: function () {
		return {
			desc: 'Подождите, идет загрузка...',
			fields: [],
			popBalance: false,
			popKpi: false,
			popBonuses: false,
			popQuartalPremiums: false,
			popNominations: false,
			intro: {
				courses: false,
				profit: false,
				estimation: false,
				indicators: false,
			},
			anim: {
				intro: false,
				profileSidebar: false,
				courses: false,
				profit: false,
				estimation: false,
				indicators: false
			},
			intersectionObserver: null
		};
	},
	computed: {
		popupWidth(){
			const w = this.$viewportSize.width
			if(w < 651) return '100%'
			if(w < 1360) return '75%'
			return w - (19 * this.$viewportSize.rem) + 'px'
		},
		isProfileVisible(){
			return this.$viewportSize.width < 1360
		}
	},
	mounted(){
		this.initAnimOnScroll()
	},
	methods: {
		pop(window) {
			if(window == 'balance') this.popBalance = true;
			if(window == 'kpi') this.popKpi = true;
			if(window == 'bonus') this.popBonuses = true;
			if(window == 'qp') this.popQuartalPremiums = true;
			if(window == 'nominations') this.popNominations = true;
		},
		getDesc(text){
			this.desc = text;
		},
		initAnimOnScroll(){
			const w = this.$viewportSize.width
			if(w > 900){
				this.intersectionObserver = new IntersectionObserver(this.animOnScroll, {
					threshold: 0.1
				})
				this.intersectionObserver.observe(this.$refs.intro.$el)
				this.intersectionObserver.observe(this.$refs.profileSidebar.$el)
				this.intersectionObserver.observe(this.$refs.courses.$el)
				this.intersectionObserver.observe(this.$refs.profit.$el)
				this.intersectionObserver.observe(this.$refs.estimation.$el)
				this.intersectionObserver.observe(this.$refs.indicators.$el)
				return
			}
			Object.keys(this.anim).forEach(key => {
				this.anim[key] = true
			})
		},
		animOnScroll(entries){
			entries.forEach(entry => {
				if(entry.isIntersecting){
					Object.keys(this.anim).forEach(key => {
						if(this.$refs[key].$el === entry.target){
							this.anim[key] = true
						}
					})
				}
			})
		}
	}
};
</script>

<style lang="scss">
#page-profile{
    padding-bottom: 2rem;
    padding-right: 2rem;
}
@media(max-width:1910px){
    #page-profile{
        padding-right: 0;
    }
}
</style>