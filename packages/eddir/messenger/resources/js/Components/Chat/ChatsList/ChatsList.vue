<template>
  <div ref="messengerChats"
       :class="!fullscreen ? 'messenger__chats-container messenger__collapsed' : 'messenger__chats-container messenger__fullscreen'">
    <ContextMenu
      :show="contextMenuVisible"
      :x="contextMenuX"
      :y="contextMenuY"
      :parent-element="$refs.messengerChats"
    >
      <a v-if="contextMenuChat && contextMenuChat.pinned" href="javascript:"
         @click="contextMenuVisible = false; unpinChat(contextMenuChat)">Открепить чат</a>
      <a v-else href="javascript:" @click="contextMenuVisible = false; pinChat(contextMenuChat)">Закрепить чат</a>
      <a href="javascript:" @click="contextMenuVisible = false; leftChat(contextMenuChat)">Покинуть чат</a>

    </ContextMenu>
    <div class="messenger__chats-list">
      <template v-if="!isSearchMode || !fullscreen">
        <div
          v-if="!isSearchMode || !fullscreen"
          v-for="item in sortedChats"
          :class="(chat && chat.id === item.id) ? 'messenger__chat-item messenger__chat-selected' : 'messenger__chat-item'"
          @click="openChat(item, $event)"
          @contextmenu.prevent="showChatContextMenu($event, item)"
        >
          <ContactItem :item="item" :fullscreen="fullscreen"></ContactItem>
        </div>
      </template>
      <template v-else-if="isSearchMode">
        <div
          v-for="item in contacts"
          :class="(chat && chat.id === item.id) ? 'messenger__chat-item messenger__chat-selected' : 'messenger__chat-item'"
          :data-test-id="item.id"
          @click="openChat(item, $event)"
          @contextmenu.prevent="showChatContextMenu($event, item)"
        >
          <ContactItem :item="item" :fullscreen="fullscreen"></ContactItem>
        </div>
        <div
          v-for="(item, index) in searchMessagesChatsResults"
          v-bind:key="index"
          :class="'messenger__chat-item'"
          @click="openChat(item, $event)"
        >
          <ContactItem :item="item" :fullscreen="fullscreen"></ContactItem>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import ContextMenu from "../ContextMenu/ContextMenu.vue";
import ContactItem from "./ContactItem/ContactItem.vue";

export default {
  name: "ChatsList",
  components: {ContextMenu, ContactItem},
  computed: {
    ...mapGetters(['sortedChats', 'chat',
      'contacts', 'searchMessagesChatsResults',
      'isSearchMode'])
  },
  props: {
    fullscreen: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      contextMenuVisible: false,
      contextMenuX: 0,
      contextMenuY: 0,
      contextMenuChat: null
    }
  },
  methods: {
    ...mapActions(['loadChat', 'toggleMessenger', 'leftChat', 'pinChat', 'unpinChat']),
    openChat(chat, event) {
      event.stopPropagation();
      this.contextMenuVisible = false;
      if (!this.chat || this.chat.id !== chat.id) {
        this.loadChat(chat.id);
      }
      if (!this.fullscreen) {
        this.toggleMessenger();
      }
    },
    showChatContextMenu(event, chat) {
      this.contextMenuVisible = true;
      this.contextMenuX = event.clientX;
      this.contextMenuY = event.clientY;
      this.contextMenuChat = chat;
    }
  }
}
</script>

<style>

/*noinspection CssUnusedSymbol*/
.messenger__chats-container {
  display: flex;
  flex-flow: column;
  flex: 0 0 25%;
  min-width: 200px;
  max-width: 500px;
  position: relative;
  height: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

@media only screen and (max-width: 670px) {
  .messenger__fullscreen {
    display: none;
  }
}

/*noinspection CssUnusedSymbol*/
.messenger__collapsed {
  min-width: auto;
}

.messenger__collapsed .messenger__chat-item {
  padding: 5px;
}

.messenger__chat-item:hover {
  background: #e5effa;
}

.messenger__chats-list {
  flex: 1;
  position: relative;
  max-width: 100%;
  cursor: pointer;
  overflow-y: auto;
  margin: 20px 5px 0 10px;
  padding-right: 10px;
}

/*noinspection CssUnusedSymbol*/
.messenger__chat-selected {
  color: #1976d2 !important;
  background: #e5effa !important;
}

/*noinspection CssUnusedSymbol*/
.messenger__chat-item {
  align-items: center;
  display: flex;
  flex: 1 1 100%;
  margin-bottom: 5px;
  padding: 10px;
  position: relative;
  transition: background-color .3s cubic-bezier(.25, .8, .5, 1);
}

.messenger__collapsed .messenger__chats-list {
  margin: 0;
  padding-right: 0;
}

/* total width */
.messenger__chats-list::-webkit-scrollbar {
  background-color: #fff;
  width: 8px;
}

/* background of the scrollbar except button or resizer */
.messenger__chats-list::-webkit-scrollbar-track {
  background-color: #bcbcbd;
  border-radius: 24px;
}

/* scrollbar itself */
.messenger__chats-list::-webkit-scrollbar-thumb {
  background-color: #7e7e81;
  border-radius: 24px;
}

/* set button(top and bottom of the scrollbar) */
.messenger__chats-list::-webkit-scrollbar-button {
  display: none;
}

.messenger__collapsed .messenger__chats-list::-webkit-scrollbar {
  width: 0;
  display: none;
}

</style>
