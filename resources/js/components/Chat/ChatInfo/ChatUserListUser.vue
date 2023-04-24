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
			<div
				class="ChatUserListUser-name"
				@click="setAdmin"
			>
				{{ title }}
				<span
					v-if="isAdmin"
					class="ChatUserListUser-admin ml-4"
				>
					Администратор
				</span>
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
			<div
				v-if="actions.remove && (owner || !isAdmin) && !isOwner"
				class="ChatUserListUser-actionIcon ChatIcon-parent_red"
				@click="$emit('remove', user.id)"
			>
				<ChatIconHistoryDelete />
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import JobtronAvatar from '@ui/Avatar'
import {
	ChatIconHistoryDelete,
} from '@icons'

export default {
	name: 'ChatUserListUser',
	components: {
		JobtronAvatar,
		ChatIconHistoryDelete,
	},
	props: {
		user: {
			type: Object,
			default: null
		},
		actions: {
			type: Object,
			default: null
		},
		owner: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		...mapGetters(['chat']),
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
		},
		isOwner(){
			if(!this.user) return false
			return this.chat.owner_id === this.user.id
		},
		isAdmin(){
			if(!this.user) return false
			return this.user.pivot?.is_admin
		},
	},
	methods: {
		...mapActions([
			'setChatAdmin',
			'unsetChatAdmin',
		]),
		setAdmin(){
			if(!this.owner) return
			const payload = this.isAdmin ? {
				method: 'unsetChatAdmin',
				title: 'Забрать права администратора?',
				message: `Вы уверены, что хотите забрать права администратора у пользователя ${this.user.name}?`,
				action: 'Забрать'
			} : {
				method: 'setChatAdmin',
				title: 'Выдать права администратора?',
				message: `Вы уверены, что хотите выдать права администратора пользователю ${this.user.name}?`,
				action: 'Выдать'
			}
			this.$root.$emit('messengerConfirm', {
				title: payload.title,
				message: payload.message,
				button: {
					yes: payload.action,
					no: 'Отмена'
				},
				callback: confirm => {
					if (confirm) {
						this[payload.method]({chat: this.chat, user: this.user});
					}
				}
			})
		},
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

	user-select: none;
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
	&-admin{
		font-weight: 400;
		font-size: 12px;
		line-height: 16px;

		letter-spacing: -0.02em;

		color: #89A9DE;
	}
}
</style>
