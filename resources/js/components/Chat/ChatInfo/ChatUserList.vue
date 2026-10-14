<template>
	<div class="ChatUserList">
		<ChatUserListUser
			v-for="user in users"
			:key="user.id"
			:user="user"
			:actions="actions"
			:owner="owner"
			class="ChatUserList-user"
			@remove="onRemove"
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
			default: null
		},
		owner: {
			type: Boolean,
			default: false
		}
	},
	methods: {
		onRemove(payload){
			this.$emit('remove', payload)
		}
	}
}
</script>

<style lang="scss">
	.ChatUserList{
		&-user{
			margin-bottom: 7px;
		}
	}
</style>
