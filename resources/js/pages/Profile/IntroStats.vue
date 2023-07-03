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
						<div
							class="front"
							:style="`height: ${animateValues.balance.height}%`"
						>
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
						<div
							class="stat__value"
							:class="{active: user_earnings.sumSalary}"
						>
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ balance }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ animateValues.balance.value }}</span> {{ currency }}
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
						<div
							class="front"
							:style="`height: ${animateValues.kpi.height}%`"
						>
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
						<div
							class="stat__value"
							:class="{active: user_earnings.sumKpi}"
						>
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ kpi }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ animateValues.kpi.value }}</span> {{ currency }}
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
						<div
							class="front"
							:style="`height: ${animateValues.bonus.height}%`"
						>
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
						<div
							class="stat__value"
							:class="{active: user_earnings.sumBonuses}"
						>
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ bonus }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ animateValues.bonus.value }}</span> {{ currency }}
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
					Доступен новый бонус
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
						<div
							class="front"
							:style="`height: ${animateValues.premium.height}%`"
						>
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
						<div
							class="stat__value"
							:class="{active: user_earnings.sumQuartalPremiums}"
						>
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ premium }}</span> <span
								v-show="animated"
								class="IntroStats-fake"
							>{{ animateValues.premium.value }}</span> {{ currency }}
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
						<div
							class="front"
							:style="`height: ${animateValues.nominations.height}%`"
						>
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
						<div
							class="stat__value"
							:class="{active: user_earnings.sumNominations}"
						>
							<span
								v-show="!animated"
								class="IntroStats-value"
							>{{ nominations }}</span>
							<span
								v-show="animated"
								class="IntroStats-fake"
							>{{ animateValues.nominations.value }}</span>
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
					Доступна новая награда
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
			animateValues: {
				balance: {
					value: '0',
					height: 0,
				},
				kpi: {
					value: '0',
					height: 0,
				},
				bonus: {
					value: '0',
					height: 0,
				},
				premium: {
					value: '0',
					height: 0,
				},
				nominations: {
					value: '0',
					height: 0,
				},
			}
		}
	},
	computed: {
		...mapState(useProfileSalaryStore, ['user_earnings', 'readed']),
		...mapState(usePersonalInfoStore, ['salary', 'currency']),
		balance(){
			return this.separateNumber(this.user_earnings.sumSalary)
		},
		kpi(){
			return this.separateNumber(this.user_earnings.sumKpi)
		},
		kpiUnread(){
			return !this.readed.kpis
		},
		bonus(){
			return this.separateNumber(this.user_earnings.sumBonuses)
		},
		bonusUnread(){
			return !this.readed.bonuses
		},
		premium(){
			return this.separateNumber(this.user_earnings.sumQuartalPremiums)
		},
		premiumUnread(){
			return !this.readed.premiums
		},
		nominations(){
			return this.separateNumber(this.user_earnings.sumNominations)
		},
		nominationsUnread(){
			return !this.readed.awards
		},
	},
	watch:{
		balance(){
			this.$nextTick(() => this.animateStats())
		},
	},
	created(){},
	mounted(){
		if(this.user_earnings) this.$nextTick(() => this.animateStats())
	},
	methods: {
		animateStat(prop, value, max, duration = 4000, easing = 'swing'){
			/* global VJQuery */
			return new Promise(resolve => {
				VJQuery({a: 0}).animate({a: 1}, {
					duration,
					easing,
					step: step => {
						this.animateValues[prop].value = this.separateNumber(parseInt(value * step))
						this.animateValues[prop].height = value / max * 100 * step
					},
					complete: resolve
				})
			})
		},
		fixStat(prop, value, max = 1){
			this.animateValues[prop].value = this.separateNumber(parseInt(value))
			this.animateValues[prop].height = value / max * 100
		},

		async animateStats(){
			const MAXBALANCE = parseInt(this.salary.replace(/\s/g, '')) || 0
			const MAXKPI = this.user_earnings.kpiMax
			const MAXBONUSES = 1
			const MAXKVARTAL = 1
			const MAXNOMINATIONS = 1

			this.animated = true
			await Promise.allSettled([
				this.animateStat('balance', this.user_earnings.sumSalary, MAXBALANCE),
				this.animateStat('kpi', this.user_earnings.sumKpi, MAXKPI),
				this.animateStat('bonus', this.user_earnings.sumBonuses, MAXBONUSES),
				this.animateStat('premium', this.user_earnings.sumQuartalPremiums, MAXKVARTAL),
				this.animateStat('nominations', this.user_earnings.sumNominations, MAXNOMINATIONS),
			]);
			this.fixStat('balance', this.user_earnings.sumSalary, parseInt(this.salary.replace(/\s/g, '')) || 0)
			this.fixStat('kpi', this.user_earnings.sumKpi, this.user_earnings.kpiMax)
			this.fixStat('bonus', this.user_earnings.sumBonuses)
			this.fixStat('premium', this.user_earnings.sumQuartalPremiums)
			this.fixStat('nominations', this.user_earnings.sumNominations)
			this.animated = false
		},
		separateNumber(x){
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		},
	},
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
