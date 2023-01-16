<template>
    <div class="intro__stats _anim _anim-no-hide block">
        <div class="stat__item" @click="$emit('pop', 'balance')">
            <div class="stat__image">
                <div class="back">
                    <img src="/images/dist/image-1.svg" alt="stat image" class="stat__front">
                </div>
                <div class="front">
                    <img src="/images/dist/image-1.svg" alt="stat image" class="stat__front">
                </div>
            </div>
            <div class="stat__about">
                <div class="stat__name">Баланс оклада</div>
                <div class="stat__value"><span>{{ user_earnings.sumSalary }}</span> {{ user_earnings.currency }}</div>
            </div>
        </div>
        <div class="stat__item" @click="$emit('pop', 'kpi')">
            <div class="stat__image">
                <div class="back">
                    <img src="/images/dist/image-2.svg" alt="stat image" class="stat__back">
                </div>
                <div class="front">
                    <img src="/images/dist/image-2.svg" alt="stat image" class="stat__front">
                </div>
            </div>
            <div class="stat__about">
                <div class="stat__name">KPI</div>
                <div class="stat__value"><span>{{ user_earnings.sumKpi }}</span> {{ user_earnings.currency }}</div>
            </div>
        </div>
        <div class="stat__item" @click="$emit('pop', 'bonus')">
            <div class="stat__image">
                <div class="back">
                    <img src="/images/dist/image-3.svg" alt="stat image" class="stat__back">
                </div>
                <div class="front">
                    <img src="/images/dist/image-3.svg" alt="stat image" class="stat__front">
                </div>
            </div>
            <div class="stat__about">
                <div class="stat__name">Бонусы</div>
                <div class="stat__value"><span>{{ user_earnings.sumBonuses }}</span> {{ user_earnings.currency }}</div>
            </div>
        </div>
        <div class="stat__item" @click="$emit('pop', 'qp')">
            <div class="stat__image">
                <div class="back">
                    <img src="/images/dist/image-4.svg" alt="stat image" class="stat__back">
                </div>
                <div class="front">
                    <img src="/images/dist/image-4.svg" alt="stat image" class="stat__front">
                </div>
            </div>
            <div class="stat__about">
                <div class="stat__name">Квартальный</div>
                <div class="stat__value"><span>{{ user_earnings.sumQuartalPremiums }}</span> {{ user_earnings.currency }}</div>
            </div>
        </div>
        <div class="stat__item" @click="$emit('pop', 'nominations')">
            <div class="stat__image">
                <div class="back">
                    <img src="/images/dist/image-5.svg" alt="stat image" class="stat__back">
                </div>
                <div class="front">
                    <img src="/images/dist/image-5.svg" alt="stat image" class="stat__front">
                </div>
            </div>
            <div class="stat__about">
                <div class="stat__name">Номинации</div>
                <div class="stat__value"><span>{{ user_earnings.sumNominations }}</span></div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'pinia'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'

export default {
	name: 'IntroStats',
	props: {},
	data: function () {
		return {}
	},
	computed: {
		...mapState(useProfileSalaryStore, ['user_earnings', 'has_qp'])
	},
	watch:{
		user_earnings(){
			this.$nextTick(() => this.OpacityStats())
		}
	},
	methods: {
		fetch() {
			this.loading = true

			this.axios.post('/profile/salary/get', {
				month: new Date().getMonth() + 1,
				year: new Date().getFullYear()
			}).then(response => {
				this.data = response.data.user_earnings
				this.has_quartal_premiums = response.data.has_qp

				this.$nextTick(() => this.OpacityStats())

				this.loading = false
			}).catch(error => {
				this.loading = false
				alert(error)
			});
		},

		/**
         * animate opacity in blocks
         */
		OpacityStats() {
			/* global VJQuery */
			let MAXBALANCE = this.user_earnings.oklad,
				MAXKPI = this.user_earnings.kpiMax,
				MAXBONUSES = 1,
				MAXKVARTAL = 1,
				MAXNOMINATIONS = 1,
				maxArray = [MAXBALANCE, MAXKPI,MAXBONUSES, MAXKVARTAL, MAXNOMINATIONS];

			let values = VJQuery('.stat__value span');
			for(let i=0;i<values.length;i++){

				let value = values[i].textContent.replace(/,/g,'')
				if(value !== '0'){
					VJQuery(values[i]).closest('.stat__value').addClass('active')
				}

				VJQuery({numberValue: 0}).animate({numberValue: value/maxArray[i] * 100}, {
					duration: 4000,
					easing: 'swing',
					step: function(val) {
						VJQuery(values[i]).closest('.stat__item').find('.front').css('height',val+'%')
					},
					complete: function(){
						VJQuery(values[i]).closest('.stat__item').find('.front').css('height',value/maxArray[i] * 100 + '%')
					}
				});
			}

			VJQuery('.stat__value').each(function(){
				let n = VJQuery(this).children('span').text().replace(/\D/g,'');
				let element = VJQuery(this);

				function separateNumber(x) {
					return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
					//разделитель можно задать тут вторым аргументом для метода replace. Сейчас, как видно, пробел
				}

				VJQuery({numberValue: 0}).animate({numberValue: n}, {
					duration: 4000,
					easing: 'swing',
					step: function(val) {
						element.children('span').text(separateNumber(Math.round(val)));
					},
					complete: function(){
						element.children('span').text(separateNumber(Math.round(n)));
					}
				});
			})
		}, // end of opacity
	},
	created(){},
	mounted(){
		if(this.user_earnings.oklad) this.$nextTick(() => this.OpacityStats())
	}
};
</script>