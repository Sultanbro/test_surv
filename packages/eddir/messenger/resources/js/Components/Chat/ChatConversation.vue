<template>
  <div class="conversation">
    <ChatHeader :contact="chat" v-on="$listeners"></ChatHeader>
    <ChatFeed :contact="chat"
              :messages="messages"
              :user="user"
              :pinned-message="pinnedMessage"
              v-on="$listeners"
              @mode="changeMode"
    />
    <MessageInput v-if="chat"
                  @send="sendMessage"
                  @edit="editMessage"
                  :selected-message="selectedMessage"
                  :mode="mode"/>
  </div>
</template>

<script>
import ChatHeader from './ChatHeader.vue';
import ChatFeed from './ChatFeed.vue';
import MessageInput from './MessageInput.vue';

import axios from 'axios';
import API from "./API.vue";

export default {
  components: {ChatHeader, ChatFeed, MessageInput},
  props: {
    chat: {
      type: Object,
      default: null,
    },
    messages: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      required: true,
    },
    pinnedMessage: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      selectedMessage: {},
      mode: 'send',
    };
  },
  methods: {
    changeMode(mode, message) {
      this.selectedMessage = message;
      this.mode = mode;
    },
    sendMessage(text) {
      if (!this.chat) {
        return;
      }
      this.mode = 'send';

      API.sendMessage(this.chat.id, text, message => {
        this.$emit('sent', message);
      });
    },
    editMessage(message) {
      API.editMessage(this.selectedMessage.id, message, message => {
          this.selectedMessage.body = message.body;
          this.mode = 'send';
        });
    },
  },
};
</script>

<style>
.conversation {
  display: flex;
  flex: 5;
  flex-direction: column;
  justify-content: space-between;
  background-color: white;
}

.conversation h1 {
  font-size: 20px;
  padding: 10px;
  margin: 0;
}

h1 span {
  font-size: 16px;
  color: #999;
  margin-left: 5px;
}
</style>
