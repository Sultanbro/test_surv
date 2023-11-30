<template>
	<div
		v-if="isReady"
		class="RefWidget"
	>
		<div class="RefWidget-title">
			Реферальная программа «Business&nbsp;Friends»
		</div>

		<div class="RefWidget-border">
			Ваш статус:
			<span
				v-if="status === 'promoter'"
				class="underdot ml-2 relative"
				@mouseenter="isHoverStatus = true"
				@mouseleave="isHoverStatus = false"
			>
				{{ status }}
				<PopupMenu
					v-if="isHoverStatus"
					position="topLeft"
					class="px-3"
					style="width: 150px;"
				>
					Следующий статус - activist (+10% к&nbsp;начислениям)
				</PopupMenu>
			</span>
			<span
				v-else-if="status === 'activist'"
				class="underdot ml-2 relative"
				@mouseenter="isHoverStatus = true"
				@mouseleave="isHoverStatus = false"
			>
				{{ status }}
				<PopupMenu
					v-if="isHoverStatus"
					position="topLeft"
					class="px-3"
					style="width: 200px;"
				>
					Следующий статус - Ambassador (главный приз каждые 3 месяца 200 000₸ лучшему сотруднику - Амбассадору за наибольшее количество оставшихся после 1 месяца)
				</PopupMenu>
			</span>
			<span
				v-else
				class="ml-2"
			>{{ status }}</span>
			<template v-if="['activist', 'ambassador'].includes(status)">
				<img
					:src="status === 'activist' ? '/images/dist/second-place.png' : '/images/dist/first-place.png'"
					alt=""
				>
			</template>
			<a
				href="https://youtu.be/kKrnrBhx1ck"
				target="_blank"
				class="RefWidget-play ml-a"
			>
				<img
					src="/images/ref-play.png"
					alt=""
					class="RefWidget-playImage"
				>
				Play
			</a>
		</div>

		<div class="RefWidget-border">
			<router-link :to="'/profile/referral-prsentation'">
				«Как заработать миллион»
			</router-link>
			<a
				href="https://www.youtube.com/watch?v=amIRBHrUjSg"
				target="_blank"
				class="RefWidget-play ml-a"
			>
				<img
					src="/images/ref-play.png"
					alt=""
					class="RefWidget-playImage"
				>
				Play
			</a>
		</div>

		<div
			class="RefWidget-border pointer"
			@click="copyLink"
		>
			{{ reflink }}
		</div>

		<div class="RefWidget-border">
			<router-link :to="'/profile/promotional-material'">
				Рекламный материал
			</router-link>
		</div>

		<div class="RefWidget-subtitle">
			Заработанно:
		</div>
		<div class="RefWidget-border RefWidget-border_small">
			за все время: {{ total }} ₸
		</div>
		<div class="RefWidget-border RefWidget-border_small">
			свои за месяц: {{ month }} ₸
		</div>
		<div class="RefWidget-border RefWidget-border_small">
			от рефералов: {{ monthRef }} ₸
		</div>
		<div class="RefWidget-border RefWidget-border_small">
			<a
				v-if="leads"
				href="#RefStat"
			>
				статистика
			</a>
			<span
				v-else
				v-b-popover.hover.righttop="''"
				class="underdot relative"
			>
				статистика
				<PopupMenu
					v-if="isHoverStat"
					position="topLeft"
					class="px-3"
					style="width: 150px;"
				>
					У Вас пока нет приглашенных кандидатов и&nbsp;статистики. Начните скорее отправлять реферальную ссылку
				</PopupMenu>
			</span>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { mapGetters } from 'vuex'
import { copy2clipboard } from '@/composables/copy2clipboard'
import { useReferralStore } from '@/stores/Referral'

import PopupMenu from '@ui/PopupMenu.vue'

export default {
	name: 'RefWidget',
	components: {
		PopupMenu,
	},
	data(){
		/* global Laravel */
		return {
			reflink: 'https://job.bpartners.kz/ref?r=' + Laravel.userId,
			isHoverStatus: false,
			isHoverStats: false,
		}
	},
	computed: {
		...mapState(useReferralStore, [
			'isReady',
			'total',
			'month',
			'monthRef',
			'leads',
		]),
		...mapGetters(['user']),
		status(){
			return this.user ? this.user.referrer_status : ''
		}
	},
	mounted(){},
	methods: {
		copyLink () {
			copy2clipboard(this.reflink)
			this.$toast.info('Ссылка скопированна')
		},
	},
}
</script>

<style lang="scss">
.RefWidget{
  padding: 1.7rem 1rem;
  margin-top: 20px;

  position: relative;

	background: #fff;
  border-radius: 1rem;

	opacity: 0;
	visibility: hidden;
	transform: translateY(10px);
	transition: all 1s .6s;

	&-title{
		margin-bottom: 1rem;

		text-align: center;
		font-size: 1.8rem;
		font-weight: 600;
		color: #5b6166;
	}
	&-border{
		display: flex;
		justify-content: flex-start;
		align-items: center;

		width: 100%;
		min-height: 4rem;
		padding: 0 1.5rem;
		margin-bottom: 1rem;
		border: 1px solid #e7eaea;

		font-size: 1.2rem;
		color: #62788b;

		border-radius: 1rem;
		&_small{
			min-height: 3.25rem;
		}
	}
	&-subtitle{
		text-align: center;
		font-size: 14px;
		margin-bottom: 0.5rem;
	}
	&-play{
		display: inline-flex;
		align-items: center;
		gap: 4px;

		padding: 4px 8px 0;

		color: #fff;
		font-weight: 700;
		font-family: 'Courier New';

		background-color: rgb(253, 66, 100);
		border-radius: 8px;
		transform-origin: center right;
		transform: scale(0.9);
	}
	&-playImage{
		width: 20px;
		margin-bottom: 4px;
	}
}
.header__profile._active{
	.RefWidget{
		opacity: 1;
    transform: translateY(0);
    visibility: visible;
	}
}
</style>
