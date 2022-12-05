<template>
  <div ref="messengerChats"
       :class="!fullscreen ? 'messenger__chats-container messenger__collapsed' : 'messenger__chats-container messenger__fullscreen'">
    <ContextMenu
      :show="contextMenuVisible"
      :x="contextMenuX"
      :y="contextMenuY"
      :parent-element="$refs.messengerChats"
    >
      <template v-if="contextMenuChat">
        <a v-if="contextMenuChat.pinned" href="javascript:" @click="contextMenuVisible = false; unpinChat(contextMenuChat)">Открепить чат</a>
        <a v-else href="javascript:" @click="contextMenuVisible = false; pinChat(contextMenuChat)">Закрепить чат</a>
        <a v-if="contextMenuChat.owner_id === user.id" href="javascript:" @click="contextMenuVisible = false; remove(contextMenuChat)">Удалить чат</a>
        <a v-else href="javascript:" @click="contextMenuVisible = false; leftChat(contextMenuChat)">Покинуть чат</a>
      </template>
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
  components: {
    ContextMenu,
    ContactItem,
  },
  computed: {
    ...mapGetters([
      'sortedChats', 'chat', 'user',
      'contacts', 'searchMessagesChatsResults',
      'isSearchMode', 'isOpen'
    ])
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
    ...mapActions(['loadChat', 'toggleMessenger', 'leftChat', 'pinChat', 'unpinChat', 'removeChat', 'setLoading']),
    openChat(chat, event) {
      event.stopPropagation();
      this.setLoading(true);
      this.contextMenuVisible = false;
      if (!this.chat || this.chat.id !== chat.id) {
        this.loadChat({chatId: chat.id, callback: () => {
          this.setLoading(false);
        }});
      }
      if (!this.isOpen) {
        this.toggleMessenger();
      }
    },
    remove(chat) {
      this.$root.$emit('messengerConfirm', {
        title: 'Удалить чат?',
        message: 'Вы уверены, что хотите удалить чат ' + chat.title + '?',
        button: {
          yes: 'Удалить',
          no: 'Отмена'
        },
        callback: confirm => {
          if (confirm) {
            this.removeChat(chat);
          }
        }
      });
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
  min-width: 240px;
  max-width: 500px;
  position: relative;
  height: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
  background-color: #f4f6fa;
}

/*noinspection CssUnusedSymbol*/
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
  padding: 4px;
}

.messenger__chat-item:hover {
  background: #cbeefc;
}

.messenger__chats-list {
  flex: 1;
  position: relative;
  max-width: 100%;
  cursor: pointer;
  overflow-y: auto;
  margin: 20px 5px 0 0;
  padding-right: 10px;
}

/*noinspection CssUnusedSymbol*/
.messenger__chat-selected {
  color: #fff !important;
  background: #5d8ce7 !important;
}

/*noinspection CssUnusedSymbol*/
.messenger__chat-item {
  align-items: center;
  display: flex;
  flex: 1 1 100%;
  padding: 8px;
  position: relative;
  transition: background-color .3s cubic-bezier(.25, .8, .5, 1);
}

.messenger__collapsed .messenger__chats-list {
  margin: 0;
  padding-right: 0;
}

.messenger__chats-list::-webkit-scrollbar {
  width: 0;
}

</style>
