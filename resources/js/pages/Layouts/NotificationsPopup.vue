<template>
	<div
		class="popup__con"
		:class="{ 'v-loading': loading }"
	>
		<div class="tabs">
			<div class="popup__filter">
				<div class="trainee__tabs tabs__wrapper">
					<div
						class="trainee__tab tab__item is-active"
						onclick="switchTabs(this)"
						data-index="1"
					>
						Новые
						<template v-if="unreadQuantity != 0">
							(<span>{{ unreadQuantity }}</span>)
						</template>
					</div>
					<div
						class="trainee__tab tab__item"
						onclick="switchTabs(this)"
						data-index="2"
					>
						Прочитанные
					</div>
				</div>
			</div>

			<div class="tab__content">
				<!-- Unread notifications -->
				<div
					class="kaspi__content custom-scroll-y tab__content-item is-active"
					data-content="1"
				>
					<div class="notifications__wrapper">
						<div
							class="notifications__item"
							v-for="(item, i) in unread"
							:key="i"
						>
							<div class="notifications__item-date">
								{{ $moment(item.created_at).format(dateFormat) }}
							</div>
							<div class="notifications__title">
								{{ item.title }}
							</div>
							<div
								class="notifications__text"
								v-html="item.message"
							/>
							<div
								class="notifications__read"
								@click="setRead(i)"
							/>
						</div>

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

				<!-- Read notifications -->
				<div
					class="kaspi__content custom-scroll-y tab__content-item"
					data-content="2"
				>
					<div class="notifications__wrapper">
						<div
							class="notifications__item"
							v-for="(item, i) in read"
							:key="i"
						>
							<div class="notifications__item-date">
								{{ $moment(item.created_at).format(dateFormat) }}
							</div>
							<div class="notifications__title">
								{{ item.title }}
							</div>
							<div
								class="notifications__text"
								v-html="item.message"
							/>
							<div class="notifications__item-date absolute">
								{{ $moment(item.read_at).format(dateFormat) }}
							</div>
						</div>

						<div
							v-if="read.length == 0"
							class="mt-5"
						>
							<h4>Нет прочитанных уведомлений</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useNotificationsStore } from '@/stores/Notifications'

export default {
	name: 'NotificationsPopup',
	props: {},
	data: function () {
		return {
			dateFormat: 'DD.MM.YYYY',
			loading: false,
		};
	},
	computed: {
		...mapState(useNotificationsStore, ['read', 'unread', 'unreadQuantity']),
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
				if(result) this.$toast.success('Все уведомления отмечены прочитанными')
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
	},
};
</script>

<style lang="scss">
.Notification{
	&-score{
		background-color: #ff1a00;
		&[data-score-rank=1]{
			background-color: #ff8d00;
		}
		&[data-score-rank=2]{
			background-color: #e3ff00;
		}
		&[data-score-rank=3]{
			background-color: #00ff04;
		}
		&[data-score-rank=4]{
			background-color: #0051ff;
		}
	}
}
</style>
