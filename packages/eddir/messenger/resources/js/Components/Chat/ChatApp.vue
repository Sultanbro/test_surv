<template>
  <div class="chat-app" v-show="initialized">
    <ChatsList v-show="fullscreen"
               :chats="chats"
               @selected="startConversationWith"
               @search="searchChats"
               @leave-chat="leaveChat"
               @create-chat="createChat"
    />
    <Conversation v-show="fullscreen"
                  :messages="messages"
                  :chat="selectedChat"
                  :user="user"
                  :pinned-message="pinnedMessage"
                  @sent="saveNewMessage"
                  @delete="deleteMessage"
                  @close="toggleMessenger"
    />
    <SideChatsList v-show="!fullscreen"
                   :contacts="chats"
                   @contact-selected="startConversationWith"
                   @open="toggleMessenger"
    />
  </div>
</template>

<script>
import Conversation from './ChatConversation.vue';
import ChatsList from './ChatsList.vue';
import SideChatsList from './SideChatsList.vue';
import API from "./API.vue";

export default {
  components: {Conversation, ChatsList, SideChatsList},
  data() {
    return {
      initialized: false,
      fullscreen: false,
      selectedChat: null,
      user: {},
      messages: [],
      chats: [],
      pinnedMessage: null,
    };
  },
  mounted() {
    this.updateChats(() => {
      this.initialized = true;

      // new message notification
      window.Echo.channel(`messages.${this.user.id}`).listen('.newMessage', e => {
        this.handleIncomingMessage(e.message);
      });

      // pin message notification
      window.Echo.channel(`messages.${this.user.id}`).listen('.pinMessage', e => {
        this.handlePinnedMessage(e.message);
      });

      // toggleMessenger
      // this.toggleMessenger();

      // select first contact
      // this.startConversationWith(this.chats[0]);
    });
    this.setActiveStatus();
  },
  methods: {
    updateChats(callback = () => {}) {
      API.fetchChats(response => {
        this.chats = response.chats;
        this.user = response.user;
        callback(response);
      });
    },
    searchChats(search) {
      if (search.length > 0) {

        API.searchChats(search, chats => {
          this.chats = chats;
        });
      } else {
        this.updateChats();
      }
    },
    toggleMessenger() {
      this.fullscreen = !this.fullscreen;
    },
    startConversationWith(chat) {
      this.updateUnreadCount(chat.id, true);

      // Get chat info
      API.getChatInfo(chat.id, response => {
        this.selectedChat = response;
        this.handlePinnedMessage(response.pinned_message);
      });

      // API fetch messages
      API.fetchMessages(chat.id, messages => {
        this.messages = messages.reverse();

        // get messages ids
        let messagesIds = messages.map(message => message.id);

        // set messages as read
        API.setMessagesAsRead(messagesIds, chats => {
          this.chats = chats;
        });
      });
    },
    saveNewMessage(message) {
      this.messages.push(message);
    },
    handleIncomingMessage(message) {
      if (this.selectedChat && message['chat_id'] === this.selectedChat.id && message['sender_id'] !== this.user.id) {
        this.saveNewMessage(message);
        return;
      }

      this.updateUnreadCount(message['chat_id'], false);
    },
    handlePinnedMessage(message) {
      if (!message) {
        this.pinnedMessage = null;
        return;
      }
      if (this.selectedChat && message['chat_id'] === this.selectedChat.id) {
        this.pinnedMessage = message;
      }
    },
    updateUnreadCount(contact_id, reset) {
      this.chats = this.chats.map(single => {
        if (single.id !== contact_id) {
          return single;
        }

        if (reset) single.unseen = 0;
        else single.unseen += 1;

        return single;
      });
    },
    setActiveStatus() {
      // todo
    },
    createChat(items) {
      let members_ids = items.members.map(member => member.id);
      API.createChat(items.title, '', members_ids, () => {
        this.updateChats();
      });
    },
    deleteMessage(message) {
      API.deleteMessage(message.id, () => {
        this.messages = this.messages.filter(single => single.id !== message.id);
      });
    },
    leaveChat(chat) {
      API.leaveChat(chat.id, () => {
        // remove chat from list
        this.chats = this.chats.filter(
          contact => contact.id !== chat.id
        );
        if (this.selectedChat && this.selectedChat.id === chat.id) {
          this.selectedChat = null;
        }
      });
    },
  },
};
</script>

<style>
.chat-app {
  display: flex;
  position: fixed;
  width: 100%;
  justify-content: flex-end;
}
</style>
