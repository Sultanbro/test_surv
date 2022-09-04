<template>
  <div class="chat-app" v-show="initialized" :class="{'fullscreen' : fullscreen}">
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
                   :contacts="contacts"
                   @contact-selected="startConversationWith"
                   @open="toggleMessenger"
    />
  </div>
</template>

<script>
import Conversation from './ChatConversation.vue';
import ChatsList from './ChatsList.vue';
import SideChatsList from './SideChatsList.vue';
import API from "./Store/API.vue";

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
      contacts: [],
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
        // sort by last message date
        this.chats = this.sortChats(response.chats);
        this.user = response.user;

        // this.contacts as clone of this.chats
        this.contacts = this.chats.slice();

        callback(response);
      });
    },
    searchChats(search) {
      if (search.length > 0) {

        API.searchChats(search, chats => {
          chats.users.forEach(user => {
            user.id = 'user' + user.id;
            user.title = user.name;
            user.private = true;
          });

          this.chats = chats.users;
          this.chats = this.chats.concat(chats.chats);
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
        // if private set title to username
        if (response.private) {

          // if response contains users
          if (response.users) {
            // find user with current user id
            let user = response.users.find(user => user.id !== this.user.id);
            // if user found set title to username
            if (user) {
              response.title = user.name;
            }
          }
        }

        this.selectedChat = response;

        // API fetch messages
        API.fetchMessages(response.id, messages => {
          this.messages = messages.reverse();

          // get messages ids
          let messagesIds = messages.map(message => message.id);

          // set messages as read
          API.setMessagesAsRead(messagesIds, chats => {
            this.chats = this.sortChats(chats);
          });
        });


        this.handlePinnedMessage(response.pinned_message);
      });

    },
    sortChats(chats) {
      chats.sort((a, b) => {
        if (a.last_message === null) {
          return 1;
        }
        if (b.last_message === null) {
          return -1;
        }
        return new Date(b.last_message.created_at) - new Date(a.last_message.created_at);
      });
      // get chats as array of titles and last_message_date
      let chatsArray = chats.map(chat => {
        return {
          lmd: chat.last_message ? chat.last_message.created_at : null,
          title: chat.title,
          id: chat.id,
          private: chat.private,
        };
      });
      return chats;
    },
    saveNewMessage(message) {
      this.messages.push(message);
    },
    handleIncomingMessage(message) {
      // update last message
      this.chats.map(single => {
          // find chat with same id to update last message, exclude message from current user
          if (single.id !== message['chat_id'] || message['sender_id'] === this.user.id) {
            return single;
          }
          single.last_message = message;
          single.unread_messages_count = single.unread_messages_count + 1;
          return single;
        }
      );
      this.chats = this.sortChats(this.chats);

      if (this.selectedChat && message['chat_id'] === this.selectedChat.id && message['sender_id'] !== this.user.id) {
        this.saveNewMessage(message);
      }
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

        if (reset) single.unread_messages_count = 0;
        else single.unread_messages_count += 1;

        return single;
      });
    },
    setActiveStatus() {
      // todo
    },
    createChat(items) {
      let members_ids = items.members.map(member => member.id);
      API.createChat(items.title, '', members_ids, chat => {
        this.updateChats(() => {
          this.startConversationWith(chat);
        });
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
  height: 100vh;
  justify-content: flex-end;
}
</style>
