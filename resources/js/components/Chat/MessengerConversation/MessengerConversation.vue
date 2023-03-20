<template>
	<div class="messenger__col-messages">
		<ConversationHeader v-show="!isChatSearchMode" />
		<ConversationFeed v-show="!isChatSearchMode" />
		<ConversationFooter v-show="!isChatSearchMode" />
		<ConversationSearch v-show="isChatSearchMode" />
	</div>
</template>

<script>
import ConversationHeader from './ConversationHeader/ConversationHeader.vue';
import ConversationFeed from './ConversationFeed/ConversationFeed.vue';
import ConversationFooter from './ConversationFooter/ConversationFooter.vue';
import ConversationSearch from './ConversationSearch/ConversationSearch.vue';
import {mapGetters} from 'vuex';

export default {
	name: 'MessengerConversation',
	components: {
		ConversationHeader,
		ConversationFeed,
		ConversationFooter,
		ConversationSearch,
	},
	computed: {
		...mapGetters(['isChatSearchMode'])
	},
}
</script>

<style>
.messenger__col-messages {
  display: flex;
  height: 100%;
  flex: 1;
  /* border: 1px solid #E1EBF9; */

  position: relative;
  overflow: hidden;
  flex-flow: column;
  flex-flow: column;
}
</style>
