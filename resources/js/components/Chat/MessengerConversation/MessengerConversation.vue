<template>
	<div class="messenger__col-messages">
		<ConversationHeader />
		<ConversationFeed />
		<ConversationFooter
			v-if="chat && (chat.id || userInChat || isPortalAdmin)"
		/>
		<ConversationSearch
			v-if="isDesktop"
			v-show="isChatSearchMode"
		/>
		<ConversationSearchMobile
			v-else
			v-show="isChatSearchMode"
		/>
	</div>
</template>

<script>
import ConversationHeader from './ConversationHeader/ConversationHeader.vue';
import ConversationFeed from './ConversationFeed/ConversationFeed.vue';
import ConversationFooter from './ConversationFooter/ConversationFooter.vue';
import ConversationSearch from './ConversationSearch/ConversationSearch.vue';
import ConversationSearchMobile from './ConversationSearch/ConversationSearchMobile.vue';
import {mapGetters} from 'vuex';

export default {
	name: 'MessengerConversation',
	components: {
		ConversationHeader,
		ConversationFeed,
		ConversationFooter,
		ConversationSearch,
		ConversationSearchMobile,
	},
	computed: {
		...mapGetters(['isChatSearchMode', 'chat', 'user']),
		isDesktop() {
			return this.$viewportSize.width > 670
		},
		userInChat(){
			if(!this.chat) return false
			return ~this.chat.users.findIndex(user => user.id === this.user.id)
		},
		isPortalAdmin(){
			/* global Laravel */
			return Laravel.is_admin
		}
	},
}
</script>

<style>
.messenger__col-messages {
  display: flex;
  height: 100%;
  flex: 1;
	align-items: flex-end;
  /* border: 1px solid #E1EBF9; */

  position: relative;
  overflow: hidden;
  flex-flow: column;
  flex-flow: column;
}
</style>
