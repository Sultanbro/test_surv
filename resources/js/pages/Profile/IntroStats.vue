<template>
	<div class="intro__stats _anim _anim-no-hide block IntroStats">
		<!-- Баланс -->
		<FlipCard :flipped="false">
			<template #front>
				<div
					class="stat__item"
					@click="$emit('pop', 'balance')"
				>
					<div class="stat__image">
						<div class="back">
							<img
								src="/images/dist/image-1.svg"
								alt="stat image"
								class="stat__front"
							>
						</div>
						<div class="front">
							<img
								src="/images/dist/image-1.svg"
								alt="stat image"
								class="stat__front"
							>
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">
							Баланс оклада
						</div>
						<div class="stat__value">
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ balance }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ balance }}</span> {{ currency }}
						</div>
					</div>
				</div>
			</template>
			<template #back>
				<PulseCard
					class="IntroStats-pulse"
					@click="$emit('pop', 'balance')"
				/>
			</template>
		</FlipCard>

		<!-- kpi -->
		<FlipCard :flipped="!animated && kpiUnread">
			<template #front>
				<div
					class="stat__item"
					@click="$emit('pop', 'kpi')"
				>
					<div class="stat__image">
						<div class="back">
							<img
								src="/images/dist/image-2.svg"
								alt="stat image"
								class="stat__back"
							>
						</div>
						<div class="front">
							<img
								src="/images/dist/image-2.svg"
								alt="stat image"
								class="stat__front"
							>
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">
							KPI
						</div>
						<div class="stat__value">
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ kpi }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ kpi }}</span> {{ currency }}
						</div>
					</div>
				</div>
			</template>
			<template #back>
				<PulseCard
					class="IntroStats-pulse"
					@click="$emit('pop', 'kpi')"
				>
					<!-- У вас {{ unreadCount.kpis }} новых kpi! -->
					Установлен новый kpi
				</PulseCard>
			</template>
		</FlipCard>

		<!-- Бонус -->
		<FlipCard :flipped="!animated && bonusUnread">
			<template #front>
				<div
					class="stat__item"
					@click="$emit('pop', 'bonus')"
				>
					<div class="stat__image">
						<div class="back">
							<img
								src="/images/dist/image-3.svg"
								alt="stat image"
								class="stat__back"
							>
						</div>
						<div class="front">
							<img
								src="/images/dist/image-3.svg"
								alt="stat image"
								class="stat__front"
							>
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">
							Бонусы
						</div>
						<div class="stat__value">
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ bonus }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ bonus }}</span> {{ currency }}
						</div>
					</div>
				</div>
			</template>
			<template #back>
				<PulseCard
					class="IntroStats-pulse"
					@click="$emit('pop', 'bonus')"
				>
					<!-- У вас {{ unreadCount.bonuses }} новых бонусов! -->
					Установлен новый бонус
				</PulseCard>
			</template>
		</FlipCard>

		<!-- Квартальная -->
		<FlipCard :flipped="!animated && premiumUnread">
			<template #front>
				<div
					class="stat__item"
					@click="$emit('pop', 'qp')"
				>
					<div class="stat__image">
						<div class="back">
							<img
								src="/images/dist/image-4.svg"
								alt="stat image"
								class="stat__back"
							>
						</div>
						<div class="front">
							<img
								src="/images/dist/image-4.svg"
								alt="stat image"
								class="stat__front"
							>
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">
							Квартальный
						</div>
						<div class="stat__value">
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ premium }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ premium }}</span> {{ currency }}
						</div>
					</div>
				</div>
			</template>
			<template #back>
				<PulseCard
					class="IntroStats-pulse"
					@click="$emit('pop', 'qp')"
				>
					<!-- У вас {{ unreadCount.premiums }} новых квартальных премий! -->
					Установлена новая квартальная премия
				</PulseCard>
			</template>
		</FlipCard>

		<!-- Награды -->
		<FlipCard :flipped="!animated && nominationsUnread">
			<template #front>
				<div
					class="stat__item"
					@click="$emit('pop', 'nominations')"
				>
					<div class="stat__image">
						<div class="back">
							<img
								src="/images/dist/image-5.svg"
								alt="stat image"
								class="stat__back"
							>
						</div>
						<div class="front">
							<img
								src="/images/dist/image-5.svg"
								alt="stat image"
								class="stat__front"
							>
						</div>
					</div>
					<div class="stat__about">
						<div class="stat__name">
							Награды
						</div>
						<div class="stat__value">
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ nominations }}</span>
							<span
								v-show="animated"
								class="IntroStats-fake"
							>{{ nominations }}</span>
						</div>
					</div>
				</div>
			</template>
			<template #back>
				<PulseCard
					class="IntroStats-pulse"
					@click="$emit('pop', 'nominations')"
				>
					<!-- У вас {{ unreadCount.awards }} новых наград! -->
					Установлена новая награда
				</PulseCard>
			</template>
		</FlipCard>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { usePersonalInfoStore } from '@/stores/PersonalInfo'
import FlipCard from '@ui/FlipCard'
import PulseCard from '@ui/PulseCard'

export default {
	name: 'IntroStats',
	components: {
		FlipCard,
		PulseCard,
	},
	props: {},
	data: function () {
		return {
			animated: false,
		}
	},
	computed: {
		...mapState(useProfileSalaryStore, ['user_earnings', 'unreadCount']),
		...mapState(usePersonalInfoStore, ['salary', 'currency']),
		balance(){
			return this.separateNumber(this.user_earnings.sumSalary)
		},
		kpi(){
			return this.separateNumber(this.user_earnings.sumKpi)
		},
		kpiUnread(){
			return !!this.unreadCount.kpis
		},
		bonus(){
			return this.separateNumber(this.user_earnings.sumBonuses)
		},
		bonusUnread(){
			return !!this.unreadCount.bonuses
		},
		premium(){
			return this.separateNumber(this.user_earnings.sumQuartalPremiums)
		},
		premiumUnread(){
			return !!this.unreadCount.premiums
		},
		nominations(){
			return this.separateNumber(this.user_earnings.sumNominations)
		},
		nominationsUnread(){
			return !!this.unreadCount.awards
		},
	},
	watch:{
		balance(){
			this.$nextTick(() => this.OpacityStats())
		},
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
			let MAXBALANCE = parseInt(this.salary.replace(/\s/g, '')) || 0,
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
				} else {
					VJQuery(values[i]).closest('.stat__item').find('.front').css('height', 0)
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

			this.animated = true
			VJQuery('.stat__value').each((key, el) => {
				let element = VJQuery(el);
				let n = element.children('.IntroStats-value').text().replace(/\D/g,'');

				function separateNumber(x) {
					return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
					//разделитель можно задать тут вторым аргументом для метода replace. Сейчас, как видно, пробел
				}

				VJQuery({numberValue: 0}).animate({numberValue: n}, {
					duration: 4000,
					easing: 'swing',
					step: val => {
						element.children('.IntroStats-fake').text(separateNumber(Math.round(val)));
					},
					complete: () => {
						element.children('.IntroStats-fake').text(separateNumber(Math.round(n)));
						this.animated = false
					}
				});
			})
		}, // end of opacity
		separateNumber(x){
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		},
	},
	created(){},
	mounted(){
		if(this.user_earnings) this.$nextTick(() => this.OpacityStats())
	}
};
</script>

<style lang="scss">
.IntroStats{
	&-pulse{
		display: flex;
		align-items: center;
		justify-content: center;


		padding: 2rem;
		background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='25' ry='25' stroke='%23DADADAFF' stroke-width='4' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
		background-color: #fff;
		border-radius: 2.5rem;

		position: absolute;
		z-index: 5;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;

		text-align: center;
		font-size: 1.6rem;
		cursor: pointer;
		user-select: none;
	}
}
</style>
