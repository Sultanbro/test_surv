import API from "../../API.vue";
import Vue from "vue";

import {MESSAGES_MAX_COUNT, MESSAGES_LOAD_COUNT, MESSAGES_LOAD_COUNT_ON_RESET} from "./constants.js";

export default {
  state: {
    messages: [],
    editMessage: null,
    pinnedMessage: null,
    messagesLoadMoreCount: MESSAGES_LOAD_COUNT,
    startMessageId: null,
    messagesOldEndReached: false,
    messagesNewEndReached: false,
    messagesLoading: false
  },
  actions: {
    async loadMessages({commit, getters, dispatch}, {reset = false, goto = 0} = {}) {
      if (getters.messagesLoading) {
        return;
      }
      commit('setMessagesLoading', true);

      let count, startMessageId, including;
      if (reset) {
        commit('resetMessages');
        count = MESSAGES_LOAD_COUNT_ON_RESET;
        startMessageId = null;
        including = false;
      } else if (goto > 0) {
        commit('resetMessages');
        count = -MESSAGES_MAX_COUNT;
        startMessageId = goto;
        including = true;
      } else {
        count = getters.messagesLoadMoreCount;
        startMessageId = getters.startMessageId;
        including = false;
      }

      return API.fetchMessages(getters.chat.id, count, startMessageId, including, messages => {
        if (messages.length === 0) {

          if (count < 0) {
            commit('setMessagesNewEndReached');
          } else {
            commit('setMessagesOldEndReached');
          }

        } else {

          messages = Object.keys(messages).map(key => messages[key]).reverse();
          dispatch('markMessagesAsRead', messages);

          if (getters.chat.unread_messages_count > 0 && getters.chat.unread_messages_count <= messages.length) {
            commit('markChatAsSeen');
          }

          if (reset) {
            commit('setMessages', messages);
            dispatch('requestScroll', 0);
          } else if (count > 0) {
            commit('prependMessages', messages);
          } else {
            commit('appendMessages', messages);
          }

        }
        commit('setMessagesLoading', false);
      });
    },
    async loadMoreOldMessages({commit, getters, dispatch}) {
      commit('setMessagesLoadMoreCount', MESSAGES_LOAD_COUNT);
      commit('setStartMessageId', getters.messages[0].id);
      dispatch('loadMessages');
    },
    async loadMoreNewMessages({commit, getters, dispatch}) {
      commit('setMessagesLoadMoreCount', -MESSAGES_LOAD_COUNT);
      commit('setStartMessageId', getters.messages[getters.messages.length - 1].id);
      dispatch('loadMessages');
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
      dispatch('requestScroll', 0);
      return API.sendMessage(getters.chat.id, message, response => {
        response.new_id = response.id;
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
        const isSender = getters.user.id === message.sender_id;
        commit('updateChatLastMessage', {chat, message, isSender});
      } else {
        await API.getChatInfo(message.chat_id, chat => {
          commit('addChat', chat);
        });
      }
    },
    async newServiceMessage({commit, getters, dispatch}, message) {
      switch (message.event.type) {
        case 'join':
          commit('addMembers', [message.event.payload.user]);
          break;
        case 'leave':
          // check if it is current chat
          if (getters.chat.id === message.chat_id) {
            commit('removeMembers', [message.event.payload.user]);
          }
          break;
        case 'delete':
          commit('deleteMessage', message.event.payload.message_id);
          break;
        case 'edit':
          commit('updateMessage', message.event.payload.message);
          break;
        case 'pin':
          commit('setPinnedMessage', message.event.payload.message);
          break;
        case 'unpin':
          commit('setPinnedMessage', null);
          break;
        case 'rename':
          commit('updateChat', message.event.payload.chat);
          break
        case 'online':
          if (getters.chat.private && getters.chat.users.find(member => member.id === message.sender.id)) {
            commit('setChatOnline', true);
          }
          return true;
        case 'offline':
          // if user is in current chat
          if (getters.chat.private && getters.chat.users.find(member => member.id === message.sender.id)) {
            commit('setChatOnline', false);
          }
          return true;
        case 'chat_created':
          commit('addChat', message.event.payload.chat);
          break;
        case 'read':
          commit('setMessageRead', {messageId: message.event.payload.message_id, reader: message.sender});
          return true;
      }

      if (getters.chat.id === message.chat_id) {
        commit('addMessage', message);
      }
    },
    async markMessagesAsRead({commit, getters, dispatch}, messages) {
      // exclude messages from user
      messages = messages.filter(message => message.sender_id !== getters.user.id);
      // exclude messages contains reader.id (current user)
      messages = messages.filter(message => !message.readers || !message.readers.find(reader => reader.id === getters.user.id));
      if (messages.length > 0) {
        await API.setMessagesAsRead(messages.map(message => message.id), (response) => {
          if (response.left === 0) {
            commit('markChatAsSeen');
          }
        });
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
    async uploadFiles({commit, getters, dispatch}, {files, caption = ""}) {
      const guid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
      let newMessage = {
        id: guid,
        body: "Загрузка файла...",
        created_at: new Date().toISOString(),
        chat_id: getters.chat.id,
        sender_id: getters.user.id
      }

      commit('addMessage', newMessage);

      return API.uploadFiles(getters.chat.id, files, caption, message => {
        message.new_id = message.id;
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
    appendMessages(state, messages) {
      state.messages.push(...messages.reverse());
    },
    prependMessages(state, messages) {
      state.messages.unshift(...messages);
    },
    addMessage(state, message) {
      if (!state.messages.find(m => m.id === message.id)) {
        message.failed = false;
        state.messages.push(message);
      }
    },
    updateMessage(state, message) {
      let index = state.messages.findIndex(m => m.id === message.id);

      if (index !== -1 && message.new_id) {
        message.id = message.new_id;
      }

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
    },
    setMessagesLoadMoreCount(state, messagesLoadMoreCount) {
      state.messagesLoadMoreCount = messagesLoadMoreCount;
    },
    setStartMessageId(state, startMessageId) {
      state.startMessageId = startMessageId;
    },
    resetMessages(state) {
      state.messages = [];
      state.messagesLoadMoreCount = MESSAGES_MAX_COUNT;
      state.startMessageId = null;
      state.messagesOldEndReached = false;
      state.messagesNewEndReached = false;
    },
    setMessagesOldEndReached(state) {
      state.messagesOldEndReached = true;
    },
    setMessagesNewEndReached(state) {
      state.messagesNewEndReached = true;
    },
    setMessagesLoading(state, messagesLoading) {
      state.messagesLoading = messagesLoading;
    },
    setMessageRead(state, {messageId, reader}) {
      let message = state.messages.find(m => m.id === messageId);
      if (message) {
        if (!message.readers) {
          message.readers = [];
        }
        message.readers.push(reader);
      }
    }
  },
  getters: {
    messages: state => state.messages,
    editMessage: state => state.editMessage,
    pinnedMessage: state => state.pinnedMessage,
    unreadCount: (state, getters) => getters.chats.reduce((sum, chat) => sum + chat.unread_messages_count, 0),
    messagesLoadMoreCount: state => state.messagesLoadMoreCount,
    startMessageId: state => state.startMessageId,
    messagesOldEndReached: state => state.messagesOldEndReached,
    messagesNewEndReached: state => state.messagesNewEndReached,
    messagesLoading: state => state.messagesLoading
  }
}
