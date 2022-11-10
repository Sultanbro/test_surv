<template>
  <div ref="messengerChats"
       :class="!isOpen ? 'messenger__chats-container messenger__collapsed' : 'messenger__chats-container'">
    <ContextMenu
      :show="contextMenuVisible"
      :x="contextMenuX"
      :y="contextMenuY"
      :parent-element="$refs.messengerChats"
    >
      <a href="javascript:" @click="leftChat(contextMenuChat)">Покинуть чат</a>
    </ContextMenu>
    <div class="messenger__chats-list">
      <div
        v-if="!isSearchMode"
        v-for="item in sortedChats"
        :class="(chat && chat.id === item.id) ? 'messenger__chat-item messenger__chat-selected' : 'messenger__chat-item'"
        @click="openChat(item)"
        @contextmenu.prevent="showChatContextMenu($event, item)"
      >
        <ContactItem :item="item"></ContactItem>
      </div>
      <template v-if="isSearchMessagesMode">
        <div
          v-if="isSearchMode"
          v-for="item in searchMessagesChatsResults"
          :class="'messenger__chat-item'"
          @click="openChat(item)"
        >
          <ContactItem :item="item"></ContactItem>
        </div>
      </template>
      <template v-if="isSearchContactsMode">
        <div
          v-if="isSearchMode"
          v-for="item in contacts"
          :class="(chat && chat.id === item.id) ? 'messenger__chat-item messenger__chat-selected' : 'messenger__chat-item'"
          :data-test-id="item.id"
          @click="openChat(item)"
          @contextmenu.prevent="showChatContextMenu($event, item)"
        >
          <ContactItem :item="item"></ContactItem>
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
    ...mapGetters(['sortedChats', 'chat', 'isOpen',
      'contacts', 'searchMessagesChatsResults',
      'isSearchMessagesMode', 'isSearchContactsMode',
      'isSearchMode'])
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
    ...mapActions(['loadChat', 'toggleMessenger', 'leftChat']),
    openChat(chat) {
      this.contextMenuVisible = false;
      if (!this.chat || this.chat.id !== chat.id) {
        this.loadChat(chat.id);
      }
      if (!this.isOpen) {
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

.messenger__chats-container {
  display: flex;
  flex-flow: column;
  flex: 0 0 25%;
  min-width: 280px;
  max-width: 500px;
  position: relative;
  background: #fff;
  height: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.messenger__collapsed {
  min-width: auto;
}

.messenger__collapsed .messenger__chat-item {
  padding: 5px 10px;
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
  margin: 20px 11px 0 14px;
  padding-right: 19px;
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
  padding: 0 14px;
  position: relative;
  min-height: 71px;
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

</style>
