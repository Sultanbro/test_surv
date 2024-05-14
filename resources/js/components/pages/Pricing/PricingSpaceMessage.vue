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
	components: { MessageIcon },

	methods: {
		openChat() {
			if (!this.isBp && window.jChatWidget) {
				window.jChatWidget.open();
				window.jChatWidgetBtn.classList.toggle('hidden');
			}
		},
	},
};
</script>

<style scoped>
.price-space-button {
	background-color: #ededed;
	width: 40px;
	height: 40px;
	border-radius: 8px;
	padding: 8px;
}
</style>
