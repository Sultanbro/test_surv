<template>
  <div class="messenger__col-messages">
    <ConversationHeader></ConversationHeader>
    <ConversationFeed></ConversationFeed>
    <ConversationFooter></ConversationFooter>
  </div>
</template>

<script>
import ConversationHeader from "./ConversationHeader/ConversationHeader.vue";
import ConversationFeed from "./ConversationFeed/ConversationFeed.vue";
import ConversationFooter from "./ConversationFooter/ConversationFooter.vue";

export default {
  name: "MessengerConversation",
  components: {
    ConversationHeader,
    ConversationFeed,
    ConversationFooter,
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
  border: 1px solid #c6c6c6;
}

</style>
