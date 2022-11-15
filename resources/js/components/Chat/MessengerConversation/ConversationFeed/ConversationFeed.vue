<template>
  <div class="messenger__container-scroll" ref="messengerContainer" @click="contextMenuVisible = false">
    <ContextMenu
      :show="contextMenuVisible"
      :x="contextMenuX"
      :y="contextMenuY"
      :parent-element="$refs.messengerContainer"
    >
      <a href="javascript:" @click="startEditMessage(contextMenuMessage)"
         v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id">Отредактировать</a>
      <a href="javascript:" @click="pinMessage(contextMenuMessage)">Закрепить</a>
      <a href="javascript:" @click="deleteMessage(contextMenuMessage)"
         v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id">Удалить</a>
    </ContextMenu>
    <div class="messenger__messages-container" id="messenger__messages">
      <div v-for="message in messages" :key="message.id" class="messenger__message-wrapper"
           @contextmenu.prevent="!message.type && showChatContextMenu(message, ...arguments)">
        <ConversationServiceMessage v-if="message.type" :message="message" />
        <ConversationMessage v-else :message="message" />
      </div>
    </div>
  </div>
</template>

<script>
import ConversationMessage from "./ConversationMessage/ConversationMessage.vue";
import {mapActions, mapGetters} from "vuex";
import ContextMenu from "../../ContextMenu/ContextMenu.vue";
import ConversationServiceMessage from "./ConversationServiceMessage/ConversationServiceMessage.vue";

export default {
  name: "ConversationFeed",
  components: {
    ConversationMessage,
    ConversationServiceMessage,
    ContextMenu
  },
  computed: {
    ...mapGetters(['messages', 'user'])
  },
  watch: {
    messages() {
      this.scrollToBottom();
    }
  },
  data() {
    return {
      contextMenuVisible: false,
      contextMenuTarget: null,
      contextMenuX: 0,
      contextMenuY: 0,
      contextMenuMessage: null
    }
  },
  methods: {
    ...mapActions(['startEditMessage', 'deleteMessage', 'pinMessage']),
    scrollToBottom() {
      this.$nextTick(function () {
        const lastMessage = document.querySelector('.messenger__message-wrapper:last-child');
        if (lastMessage) {
          // get last message
          // scroll to last message
          lastMessage.scrollIntoView({behavior: 'smooth'});
        }
      });
    },
    showChatContextMenu(message, event) {
      this.contextMenuVisible = true;
      this.contextMenuX = event.clientX;
      this.contextMenuY = event.clientY;
      this.contextMenuMessage = message;
    }
  }
}
</script>

<style scoped>

.messenger__container-scroll {
  background: #ffffff;
  flex: 1;
  overflow-y: auto;
  margin-right: 1px;
  margin-top: 60px;
  -webkit-overflow-scrolling: touch;
}

.messenger__messages-container {
  padding: 0 5px 5px;
}

/* total width */
.messenger__container-scroll::-webkit-scrollbar {
  background-color: #fff;
  width: 8px;
}
/* background of the scrollbar except button or resizer */
.messenger__container-scroll::-webkit-scrollbar-track {
  background-color: #bcbcbd;
  border-radius: 24px;
}
/* scrollbar itself */
.messenger__container-scroll::-webkit-scrollbar-thumb {
  background-color: #7e7e81;
  border-radius: 24px;
}
/* set button(top and bottom of the scrollbar) */
.messenger__container-scroll::-webkit-scrollbar-button {
  display: none;
}

</style>
