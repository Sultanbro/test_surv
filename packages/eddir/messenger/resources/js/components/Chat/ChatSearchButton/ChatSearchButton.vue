<template>
	<a
		href="#"
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
import {mapActions, mapGetters} from 'vuex';

// noinspection JSUnusedGlobalSymbols
export default {
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

</style>
