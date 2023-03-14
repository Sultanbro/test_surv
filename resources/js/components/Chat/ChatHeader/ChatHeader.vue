<template>
	<div class="messenger_header">
		<div class="messenger_header-text">
			Сообщения
		</div>
		<div class="messenger_header-button">
			<!-- <ChatIconPlus class="pointer" /> -->
		</div>
	</div>
</template>

<script>
// import { ChatIconPlus } from '../icons/chat-icons'
export default {
	name: 'ChatHeader',
	components: {
		// ChatIconPlus
	},
}
</script>

<style lang="scss">
	.messenger_header{
		display: flex;
		align-items: center;
		justify-content: stretch;
		flex-flow: row nowrap;
		padding: 2rem 1.5rem;
		margin-bottom: 1.5rem;
		&-text{
			flex: 1 1 100%;
		}
		// &-button{}
	}
</style>
