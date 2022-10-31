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
        commit('setMessages', messages.reverse());
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
        commit('updateMessage', {
          id: guid,
          response: response,
        });
      }, () => {
        newMessage.failed = true;
        commit('updateMessage', {
          id: guid,
          response: newMessage,
        });
      });
    },
    async editMessageAction({commit, getters, dispatch}, text) {
      return API.editMessage(getters.editMessage.id, text, response => {
        commit('updateMessage', {
          id: getters.editMessage.id,
          response: response,
        });
        dispatch('cancelEditMessage');
      });
    },
    async newMessage({commit, getters, dispatch}, message) {
      if (getters.chat.id === message.chat_id && (getters.user.id !== message.sender_id || message.files)) {
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
    async deleteMessage({commit, getters, dispatch}, message) {
      return API.deleteMessage(message.id, response => {
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
      return API.pinMessage(message.id, response => {
        commit('setPinnedMessage', message);
      });
    },
    async unpinMessage({commit, getters, dispatch}) {
      return API.unpinMessage(getters.pinnedMessage.id, response => {
        commit('setPinnedMessage', null);
      });
    },
    async uploadFile({commit, getters, dispatch}, file) {
      return API.uploadFile(getters.chat.id, "File", file, message => {
        commit('addMessage', message);
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
      Vue.set(state.messages, index, message.response);
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
    validMessages(state) {
      return state.messages.filter(p => {
        return p.title && p.body
      })
    },
    messages: state => state.messages,
    messagesCount: (state, getters) => getters.validMessages.length,
    editMessage: state => state.editMessage,
    pinnedMessage: state => state.pinnedMessage,
  }
}
