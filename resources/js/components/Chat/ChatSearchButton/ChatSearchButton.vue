<template>
	<a
		v-b-popover.hover.left.html="'Поиск по чату'"
		href="javascript:"
		class="header__right-icon loop"
		@click="click"
	>
		<img
			src="/images/dist/header-right-3.svg"
			alt="nav icon"
			class="header__icon-img"
		>
	</a>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

// noinspection JSUnusedGlobalSymbols
export default {
	computed: {
		...mapGetters(['isOpen']),
		isDesktop() {
			return this.$viewportSize.width > 670
		},
	},
	methods: {
		...mapActions([
			'toggleMessenger',
			'setSearchFocus',
			'unloadChat',
		]),
		click(event) {
			if (!this.isOpen) {
				this.setSearchFocus(true);
			}
			if(!this.isDesktop){
				this.unloadChat()
			}
			event.stopPropagation();
			this.toggleMessenger();
		},
	},
};
</script>

<style scoped>
</style>
