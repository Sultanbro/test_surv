<template>
	<div
		class="popup__con"
		:class="{ 'v-loading': loading }"
	>
		<ProfileTabs :tabs="[`Новые${unreadQuantity ? ` (${unreadQuantity})` : ''}`, 'Прочитанные']">
			<template #tab(0)>
				<!-- Unread notifications -->
				<div class="kaspi__content custom-scroll-y">
					<div class="notifications__wrapper">
						<NotificationsItem
							v-for="(item, i) in unread"
							:key="i"
							:active="true"
							:notification="item"
							@read="setRead(i)"
							@acceptOvertime="acceptOvertime(i)"
							@rejectOvertime="rejectOvertime(i)"
						/>

						<div
							v-if="unread.length == 0"
							class="mt-5"
						>
							<h4>Нет новых уведомлений</h4>
							<img
								class="notifications_img"
								:src="require('../../../assets/notification/notification.jpg').default"
								alt=""
							>
						</div>
					</div>

					<a
						v-if="unread.length > 0"
						href="javascript:void(0)"
						class="notifications__button mt-5"
						@click="setAllRead"
					>
						Отметить все как прочитанное
					</a>
				</div>
			</template>
			<template #tab(1)>
				<!-- Read notifications -->
				<div class="kaspi__content custom-scroll-y">
					<div class="notifications__wrapper">
						<NotificationsItem
							v-for="(item, i) in read"
							:key="i"
							:active="false"
							:notification="item"
						/>

						<div
							v-if="read.length == 0"
							class="mt-5"
						>
							<h4>Нет прочитанных уведомлений</h4>
						</div>
					</div>
				</div>
			</template>
		</ProfileTabs>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useNotificationsStore } from '@/stores/Notifications'
import ProfileTabs from '@ui/ProfileTabs'
import NotificationsItem from './NotificationsItem.vue'
import {
	acceptOvertime,
	rejectOvertime,
} from '@/stores/api.js'

export default {
	name: 'NotificationsPopup',
	components: {
		ProfileTabs,
		NotificationsItem,
	},
	props: {},
	data() {
		return {
			loading: false,
		};
	},
	computed: {
		...mapState(useNotificationsStore, [
			'read',
			'unread',
			'unreadQuantity'
		]),
	},
	created() {
		this.fetchData();
	},
	methods: {
		...mapActions(useNotificationsStore, [
			'fetchNotifications',
			'setNotificationsRead',
			'setNotificationsReadAll',
		]),

		fetchData() {
			this.loading = true;
			this.fetchNotifications().then(() => {
				this.loading = false
			})
		},

		/**
		 * Set all notifications as read
		 */
		setAllRead() {
			this.loading = true;
			this.setNotificationsReadAll().then(result => {
				this.loading = false
				if(result){
					this.$toast.success('Все уведомления отмечены прочитанными')
				}
			})
		},

		/**
		 * set notification as read
		 */
		setRead(i) {
			if(!this.unread[i]) return
			this.setNotificationsRead(this.unread[i].id).then(message => {
				if(message) return this.$toast.error(message)
				this.$toast.success('Уведомление прочитано')
			})
		},

		acceptOvertime(index){
			if(!this.unread[index]) return
			const item = this.unread[index]
			this.setRead(index)
			acceptOvertime(item.about_id)
		},
		rejectOvertime(index){
			if(!this.unread[index]) return
			const item = this.unread[index]
			this.setRead(index)
			rejectOvertime(item.about_id)
		},
	},
};
</script>

<style lang="scss">
.Notification{
	&-score{
		display: inline-block;
		width: 20px;
		height: 20px;
		background-color: #ff1a00;
		&[data-score-rank="1"]{
			background-color: #ff8d00;
		}
		&[data-score-rank="2"]{
			background-color: #e3ff00;
		}
		&[data-score-rank="3"]{
			background-color: #00ff04;
		}
		&[data-score-rank="4"]{
			background-color: #0051ff;
		}
	}
	// &-actions{}
}

.notifications__wrapper{
	width: 100%;
	max-width: 100%;
	// height: 64rem;
	padding-right: 1rem;

	overflow-y:auto;
	overflow-x:hidden;
	scrollbar-color: #ECF0F9 #AEBEE0;
	scrollbar-width: thin;
	-webkit-overflow-scrolling: touch;

	&::-webkit-scrollbar {
		width: 8px; /* высота для горизонтального скролла */
		border-radius: 1rem;
		background-color: #ECF0F9;
	}

	&::-webkit-scrollbar-thumb {
		background-color: #AEBEE0;
		border-radius: 1rem;
	}
}


.notifications__button{
	display: flex;
	justify-content: center;
	align-items: center;

	width: 100%;
	max-width: 26.5rem;
	min-height: 5rem;

	color:#fff;
	font-size:1.4rem;
	font-weight: 600;

	background: #AEBEE0;
}


.notifications_img {
  width: 100%
}


@media(max-width:779px){
	.nominations__wrapper{
    justify-content: space-around;
  }
  .nominations__left{
    max-width: 25rem;
  }
}
</style>
