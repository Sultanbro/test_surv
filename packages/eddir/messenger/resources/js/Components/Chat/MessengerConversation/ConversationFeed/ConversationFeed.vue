<template>
  <div class="messenger__container-scroll" ref="messengerContainer" id="messenger_container" @click="contextMenuVisible = false"
       @scroll="onScroll">
    <ContextMenu
      :show="contextMenuVisible"
      :x="contextMenuX"
      :y="contextMenuY"
      :parent-element="$refs.messengerContainer"
    >
      <a href="javascript:" @click="startEditMessage(contextMenuMessage)"
         v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id && !contextMenuMessage.files">Отредактировать</a>
      <a href="javascript:" @click="pinMessage(contextMenuMessage)">Закрепить</a>
      <a href="javascript:" @click="deleteMessage(contextMenuMessage)"
         v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id">Удалить</a>
    </ContextMenu>
    <div class="messenger__messages-container" id="messenger__messages">
      <div v-for="(message, index) in messages" :key="message.id" class="messenger__message-wrapper"
           @contextmenu.prevent="!message.event && showChatContextMenu(message, ...arguments)">
        <ConversationServiceMessage v-if="message.event" :message="message"/>
        <ConversationMessage v-else-if="index === messages.length - 1" :message="message" @loadImage="scrollBottom"/>
        <ConversationMessage v-else :message="message"/>
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
    ...mapGetters(['messages', 'user', 'messagesOldEndReached', 'messagesNewEndReached', 'scrollingPosition']),
  },
  updated() {
    if (this.scrollingPosition !== -1) {
      this.scroll();
    }
  },
  data() {
    return {
      contextMenuVisible: false,
      contextMenuTarget: null,
      contextMenuX: 0,
      contextMenuY: 0,
      contextMenuMessage: null,
    }
  },
  methods: {
    ...mapActions([
      'startEditMessage',
      'deleteMessage', 'pinMessage',
      'loadMoreNewMessages', 'loadMoreOldMessages',
      'requestScroll'
    ]),
    scroll() {
      this.$nextTick(function () {
        let mc = document.getElementById('messenger_container');
        mc.scrollTop = mc.scrollHeight - this.scrollingPosition;
        this.requestScroll(-1);
      });
    },
    scrollBottom() {
      this.requestScroll(0);
      this.scroll();
    },
    onScroll({target: {scrollTop, scrollHeight, clientHeight}}) {
      if (scrollTop === 0 && !this.messagesOldEndReached) {
        if (this.messages.length > 0) {
          this.loadMoreOldMessages();
          this.requestScroll(scrollHeight - scrollTop);
        }
      }
      if (scrollHeight - scrollTop - clientHeight < 10) {
        if (this.messages.length > 0 && !this.messagesNewEndReached) {
          this.loadMoreNewMessages();
        }
      }
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
