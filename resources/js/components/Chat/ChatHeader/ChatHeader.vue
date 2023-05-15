<template>
	<div class="messenger_header">
		<div class="messenger_header-text">
			Сообщения
		</div>
		<div
			class="messenger_header-button"
			@click="toggleNewChatDialog"
			title="Создание нового чата"
		>
			<ChatIconPlus class="pointer" />
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import { ChatIconPlus } from '@icons'
export default {
	name: 'ChatHeader',
	components: {
		ChatIconPlus,
	},
	data(){
		return {
			showAddMemberModal: false,
			selectedTargets: []
		}
	},
	computed: {
		...mapGetters([
			'contacts',
			'newChatContacts',
			'chat',
			'user',
		]),
	},
	mounted(){},
	methods: {
		...mapActions([
			'createChat',
			'addMembers',
			'removeMembers',
			'toggleNewChatDialog',
		]),
	}
}
</script>

<style lang="scss">
	.messenger_header{
		display: flex;

		align-items: center;
		justify-content: stretch;
		flex-flow: row nowrap;

		padding: 1.5rem 1.5rem 0.5rem;
		margin-bottom: 1.5rem;

		position: relative;
		z-index: 20;

		&-text{
			flex: 1 1 100%;
			font-weight: 600;
			font-size: 20px;
			line-height: 30px;
			letter-spacing: -0.02em;
		}
		// &-button{}
	}
</style>
