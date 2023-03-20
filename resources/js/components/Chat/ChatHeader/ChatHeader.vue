<template>
	<div class="messenger_header">
		<div class="messenger_header-text">
			Сообщения
		</div>
		<div
			class="messenger_header-button"
			@click="openAddMemberModal"
		>
			<ChatIconPlus class="pointer" />
		</div>
		<AddMemberModal
			v-if="showAddMemberModal"
			@close="showAddMemberModal = false"
		/>
	</div>
</template>

<script>
import AddMemberModal from '../MessengerConversation/ConversationHeader/AddMemberModal/AddMemberModal.vue';
import { ChatIconPlus } from '../icons/chat-icons'
export default {
	name: 'ChatHeader',
	components: {
		ChatIconPlus,
		AddMemberModal,
	},
	data(){
		return {
			showAddMemberModal: false,
		}
	},
	methods: {
		openAddMemberModal(e) {
			e.stopPropagation();
			this.showAddMemberModal = true;
		},
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
