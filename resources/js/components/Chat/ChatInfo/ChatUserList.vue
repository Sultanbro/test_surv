<template>
	<div class="ChatUserList">
		<ChatUserListUser
			v-for="user in users"
			:key="user.id"
			:user="user"
			:actions="actions"
			class="ChatUserList-user"
			@action="onAction"
		/>
	</div>
</template>

<script>
import ChatUserListUser from './ChatUserListUser'
export default {
	name: 'ChatUserList',
	components: {
		ChatUserListUser,
	},
	props: {
		users: {
			type: Array,
			default: () => []
		},
		actions: {
			type: Object,
			default: () => ({})
		}
	},
	methods: {
		onAction(payload){
			this.$emit('action', payload)
		}
	}
}
</script>

<style lang="scss">
	.ChatUserList{
		&-user{
			margin-bottom: 20px;
		}
	}
</style>
