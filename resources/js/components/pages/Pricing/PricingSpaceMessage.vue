<template>
	<button
		class="price-space-button"
		@click="click"
	>
		<MessageIcon />
	</button>
</template>

<script>
import MessageIcon from './assets/MessageIcon.vue';
import {mapActions, mapGetters} from 'vuex';

export default {
	name: 'PricingSpaceMessage',
	components: {MessageIcon},
	computed: {
		...mapGetters(['isOpen'])
	},
	methods: {
		...mapActions([
			'toggleMessenger',
			'setSearchFocus'
		]),
		click(event) {
			if (!this.isOpen) {
				this.setSearchFocus(true);
			}
			event.stopPropagation();
			this.toggleMessenger();
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
</style>
