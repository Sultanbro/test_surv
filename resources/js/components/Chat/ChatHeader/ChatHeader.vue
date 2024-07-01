<template>
	<div class="messenger_header">
		<div class="messenger_header-text">
			<SearchBox />
		</div>
		<div
			class="messenger_header-button"
			title="Создание нового чата"
			@click="toggleNewChatDialog"
		>
			<ChatIconPlus
				class="pointer"
				width="15"
				height="15"
			/>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import { ChatIconPlus } from '@icons'
import SearchBox from '../SearchBox/SearchBox.vue'

export default {
	name: 'ChatHeader',
	components: {
		ChatIconPlus,
		SearchBox,
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
		gap: 1.5rem;

		padding: 1.5rem 1.5rem 0.5rem;

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
