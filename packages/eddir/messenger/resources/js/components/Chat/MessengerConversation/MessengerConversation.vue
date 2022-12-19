<template>
  <div class="messenger__col-messages">
    <ConversationHeader></ConversationHeader>
    <ConversationFeed v-show="!isChatSearchMode"></ConversationFeed>
    <ConversationFooter v-show="!isChatSearchMode"></ConversationFooter>
    <ConversationSearch v-show="isChatSearchMode"></ConversationSearch>
  </div>
</template>

<script>
import ConversationHeader from "./ConversationHeader/ConversationHeader.vue";
import ConversationFeed from "./ConversationFeed/ConversationFeed.vue";
import ConversationFooter from "./ConversationFooter/ConversationFooter.vue";
import ConversationSearch from "./ConversationSearch/ConversationSearch.vue";
import {mapGetters} from "vuex";

export default {
  name: "MessengerConversation",
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
  position: relative;
  height: 100%;
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-flow: column;
  border: 1px solid #e2e2e2;
}

</style>
