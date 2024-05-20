<template>
	<button
		class="price-space-button"
		@click="openChat"
	>
		<MessageIcon />
	</button>
</template>

<script>
import MessageIcon from './assets/MessageIcon.vue';

export default {
	name: 'PricingSpaceMessage',
	components: {MessageIcon},

	methods: {
		openChat() {
			if (!this.isBp && window.jChatWidget) {
				if (window.jChatWidget.open && window.jChatWidget.close) {
					if (window.jChatWidget.isOpen) {
						window.jChatWidget.close();
					} else {
						window.jChatWidget.open();
					}
					window.jChatWidgetBtn.classList.toggle('hidden');
				} else {
					console.error('Chat widget methods open or close are not available');
				}
			}

		}
	}
}
</script>

<style scoped>
.price-space-button {
	background-color: #ededed;
	width: 40px;
	height: 40px;
	border-radius: 8px;
	padding: 8px;
}
.price-space-button:hover {
	background-color: #bbbbbb;
}
</style>
