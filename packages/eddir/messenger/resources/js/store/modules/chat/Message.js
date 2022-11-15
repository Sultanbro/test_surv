import API from "../../API.vue";
import Vue from "vue";

export default {
  state: {
    messages: [],
    editMessage: null,
    pinnedMessage: null,
  },
  actions: {
    async loadMessages({commit, getters, dispatch}) {
      return API.fetchMessages(getters.chat.id, messages => {
        commit('setMessages', Object.keys(messages).map(key => messages[key]).reverse());
        dispatch('markMessagesAsRead', messages);

        if (getters.chat.unread_messages_count > 0 && getters.chat.unread_messages_count <= messages.length) {
          commit('markChatAsSeen');
        }
      });
    },
    async sendMessage({commit, getters, dispatch}, message) {
      const guid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
      let newMessage = {
        id: guid,
        body: message,
        created_at: new Date().toISOString(),
        chat_id: getters.chat.id,
        sender_id: getters.user.id
      }
      commit('addMessage', newMessage);
      return API.sendMessage(getters.chat.id, message, response => {
        response.id = guid;
        commit('updateMessage', response);
      }, () => {
        newMessage.id = guid;
        newMessage.failed = true;
        commit('updateMessage', newMessage);
      });
    },
    async editMessageAction({commit, getters, dispatch}, text) {
      return API.editMessage(getters.editMessage.id, text, response => {
        response.id = getters.editMessage.id;
        commit('updateMessage', response);
        dispatch('cancelEditMessage');
      });
    },
    async newMessage({commit, getters, dispatch}, message) {
      if (getters.chat.id === message.chat_id && (getters.user.id !== message.sender_id)) {
        commit('addMessage', message);
      }
      let chat = getters.chats.find(chat => chat.id === message.chat_id);
      // add chat if not exists
      if (chat) {
        commit('updateChatLastMessage', {chat, message});
      } else {
        await API.getChatInfo(message.chat_id, chat => {
          commit('addChat', chat);
        });
      }
    },
    async newServiceMessage({commit, getters, dispatch}, message) {
      switch (message.type) {
        case 'join':
          commit('addMembers', [message.payload.user]);
          break;
        case 'leave':
          // check if it is current chat
          if (getters.chat.id === message.chat_id) {
            commit('removeMembers', [message.payload.user]);
          }
          break;
        case 'delete':
          commit('deleteMessage', message.payload.message_id);
          break;
        case 'edit':
          commit('updateMessage', message.payload.message);
          break;
        case 'pin':
          // todo
          break;
        case 'unpin':
          // todo
          break;
        case 'rename':
          commit('updateChat', message.payload.chat);
          break;
      }

      if (getters.chat.id === message.chat_id) {
        commit('addMessage', message);
      }
    },
    async markMessagesAsRead({commit, getters, dispatch}, messages) {
      if (messages.length > 0) {
        await API.setMessagesAsRead(messages.map(message => message.id));
      }
    },
    async deleteMessage({commit, getters, dispatch}, message) {
      return API.deleteMessage(message.id, () => {
        commit('deleteMessage', message);
      });
    },
    async startEditMessage({commit}, message) {
      commit('setEditMessage', message);
    },
    async cancelEditMessage({commit}) {
      commit('setEditMessage', null);
    },
    async pinMessage({commit, getters, dispatch}, message) {
      return API.pinMessage(message.id, () => {
        commit('setPinnedMessage', message);
      });
    },
    async unpinMessage({commit, getters, dispatch}) {
      return API.unpinMessage(getters.pinnedMessage.id, () => {
        commit('setPinnedMessage', null);
      });
    },
    async uploadFile({commit, getters, dispatch}, file) {
      const guid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
      let newMessage = {
        id: guid,
        body: "Загрузка файла...",
        created_at: new Date().toISOString(),
        chat_id: getters.chat.id,
        sender_id: getters.user.id
      }
      commit('addMessage', newMessage);
      return API.uploadFile(getters.chat.id, "File", file, message => {
        message.id = guid;
        commit('updateMessage', message);
      }, () => {
        newMessage.id = guid;
        newMessage.failed = true;
        newMessage.body = "Ошибка загрузки файла";
        commit('updateMessage', newMessage);
      });
    }
  },
  mutations: {
    setMessages(state, messages) {
      state.messages = messages;
    },
    addMessage(state, message) {
      if (!state.messages.find(m => m.id === message.id)) {
        message.failed = false;
        state.messages.push(message);
      }
    },
    updateMessage(state, message) {
      let index = state.messages.findIndex(m => m.id === message.id);
      Vue.set(state.messages, index, message);
    },
    deleteMessage(state, message) {
      let index = state.messages.findIndex(m => m.id === message.id);
      state.messages.splice(index, 1);
    },
    setEditMessage(state, message) {
      state.editMessage = message ? message : null;
    },
    setPinnedMessage(state, message) {
      state.pinnedMessage = message;
    }
  },
  getters: {
    messages: state => state.messages,
    editMessage: state => state.editMessage,
    pinnedMessage: state => state.pinnedMessage,
    unreadCount: (state, getters) => getters.chats.reduce((sum, chat) => sum + chat.unread_messages_count, 0),
  }
}
