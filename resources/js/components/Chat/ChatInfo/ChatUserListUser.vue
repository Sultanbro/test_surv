<template>
	<div
		v-if="user"
		class="ChatUserListUser"
	>
		<!-- Avatar -->
		<div class="ChatUserListUser-avatar">
			<JobtronAvatar
				:image="`/users_img/${user.img_url}`"
				:title="title"
				:status="status === 'Онлайн' ? 'online' : ''"
				:size="40"
			/>
		</div>

		<!-- Name -->
		<div class="ChatUserListUser-info">
			<div class="ChatUserListUser-name">
				{{ title }}
			</div>
			<div
				class="ChatUserListUser-status"
				:class="statusClass"
			>
				{{ status }}
			</div>
		</div>

		<!-- Actions -->
		<div
			v-if="actions"
			class="ChatUserListUser-actions ml-a"
		>
			<template v-for="action, name in actions">
				<template v-if="action.icon">
					<div
						:key="name"
						class="ChatUserListUser-actionIcon"
						:class="action.className"
						@click="$emit('action', {
							action: name,
							userId: user.id
						})"
					>
						<component :is="action.icon" />
					</div>
				</template>
			</template>
		</div>
	</div>
</template>

<script>
import JobtronAvatar from '@ui/Avatar'

export default {
	name: 'ChatUserListUser',
	components: {
		JobtronAvatar,
	},
	props: {
		user: {
			type: Object,
			default: null
		},
		actions: {
			type: Object,
			default: null
		}
	},
	computed: {
		status(){
			if(!this.user) return ''
			if(!this.user.last_seen) return 'Не посещал(а) приложение'
			const prefix = 'Был(а) '
			const $time = this.$moment(this.user.last_seen)
			const $now = this.$moment()
			const duration = this.$moment.duration($now.diff($time))
			const hours = duration.asHours()
			const minutes = duration.asMinutes()
			if(minutes < 2) return 'Онлайн'
			if(hours < 3) return prefix + $time.format('HH:mm')
			if(hours < 24) return prefix + 'сегодня' // сделать вариант с 'вчера'
			if(hours < 168) return prefix + 'в ' + $time.format('dddd')
			return prefix + $time.format('DD.MM.YYYY')
		},
		title(){
			if(!this.user) return ''
			return `${this.user.name} ${this.user.last_name}`
		},
		statusClass(){
			return this.status === 'Онлайн' ? 'ChatUserListUser-status_online' : ''
		}
	}
}
</script>

<style lang="scss">
.ChatUserListUser{
	display: flex;
	flex-flow: row nowrap;
	justify-content: flex-start;
	align-items: stretch;
	gap: 15px;
	/* &-avatar{} */
	&-info{
		display: flex;
		flex-flow: column;
		justify-content: space-between;
	}
	&-name{
		font-weight: 500;
		font-size: 13px;
		line-height: 16px;

		letter-spacing: -0.01em;

		color: #152136;
	}
	&-status{
		font-weight: 400;
		font-size: 11px;
		line-height: 18px;

		letter-spacing: -0.01em;

		color: #8DA0C1;
		&_online{
			color: #27AE60;
		}
	}
	&-actions{
		display: flex;
		align-items: center;
		justify-content: center;
	}
}
</style>
