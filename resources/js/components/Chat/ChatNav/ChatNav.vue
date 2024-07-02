<template>
	<div
		class="messenger__nav"
		:class="(isDesktop ? !fullscreen : !chat) ? 'messenger__nav-collapsed' : 'messenger__nav-fullscreen'"
	>
		<ChatHeader v-if="isOpen" />
		<ChatsList
			class="messenger__nav-chats"
			:fullscreen="fullscreen"
		/>
	</div>
</template>

<script>
import {mapGetters} from 'vuex'
import ChatHeader from '../ChatHeader/ChatHeader.vue'
import ChatsList from '../ChatsList/ChatsList.vue'

export default {
	name: 'ChatNav',
	components: {
		ChatHeader,
		ChatsList,
	},
	props: {
		fullscreen: {
			type: Boolean,
			default: false
		}
	},
	// data() {
	// 	return {}
	// },
	computed: {
		...mapGetters([
			'isOpen',
			'chat',
		]),
		isDesktop() {
			return this.$viewportSize.width > 670
		},
	},
	// methods: {}
}
</script>

<style lang="scss">
/*noinspection CssUnusedSymbol*/
.messenger__nav {
  display: flex;
  flex-flow: column;
  flex: 0 0 25%;
  min-width: 240px;
  max-width: 500px;
  height: 100%;
  position: relative;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
  /* background-color: #f4f6fa; */
	border-right: 1px solid #E1EBF9;
}

/*noinspection CssUnusedSymbol*/
@media only screen and (max-width: 670px) {
  .messenger__nav-fullscreen {
    display: none;
  }
	.messenger__nav-collapsed{
		flex: 0 0 100%;
		max-width: 100%;
		.messenger__box-search__input{
			max-width: 100%;
		}
	}
}

/*noinspection CssUnusedSymbol*/
.messenger__nav-collapsed {
  min-width: auto;
}

.messenger__nav-collapsed
.messenger__chat-item {
  padding: 4px;
}

.messenger__nav-chats {
  max-width: 100%;
  flex: 1;
  position: relative;
  cursor: pointer;
  overflow-y: auto;
}

.messenger__nav-collapsed
.messenger__nav-chats {
  padding-right: 0;
  margin: 0;
}

.messenger__nav-chats::-webkit-scrollbar {
  width: 0;
}
</style>
