import API from "../../API.vue";
import {dispatch} from "alpinejs/src/utils/dispatch";

export default {
  state: {
    chats: [],
    chat: null
  },
  mutations: {
    setChats(state, chats) {
      state.chats = chats;
    },
    addChat(state, chat) {
      state.chats.push(chat);
    },
    setChat(state, chat) {
      state.chat = chat;
    },
    updateChatLastMessage(state, {chat, message}) {
      chat.last_message = message;
    },
    removeChat(state, chat) {
      state.chats = state.chats.filter(c => c.id !== chat.id);
    }
  },
  getters: {
    chats: state => state.chats,
    sortedChats: state => {
      return state.chats.sort((a, b) => {
        if (a.last_message === null) {
          return 1;
        }
        if (b.last_message === null) {
          return -1;
        }
        return new Date(b.last_message.created_at) - new Date(a.last_message.created_at);
      });
    },
    chat: state => state.chat
  },
  actions: {
    async loadChats({commit, getters, dispatch}) {
      return API.fetchChats(response => {
        commit('setChats', response.chats);
        commit('setContacts', response.chats);
        dispatch('updateUser', response.user);
      });
    },
    async loadChat({commit, getters, dispatch}, chatId) {
      await API.getChatInfo(chatId, chat => {
        commit('setChat', chat);
        commit('setPinnedMessage', chat.pinned_message);
        dispatch('loadMessages');
        dispatch('cancelEditMessage');
      });
    },
    async escapeChat({commit}) {
      commit('setChat', null);
      commit('setMessages', []);
      dispatch('cancelEditMessage');
    },
    async createChat({commit, getters, dispatch}, {title, description, members}) {
      await API.createChat(title, description, members).then(response => {
        dispatch('loadChat', response.data.id);
      });
    },
    async searchChats({commit}, search) {
      await API.searchChats(search).then(response => {
        let chats = response.data;
        chats.users.forEach(user => {
          user.id = 'user' + user.id;
          user.title = user.name;
          user.private = true;
        });
        commit('setContacts', chats.users.concat(chats.chats));
      });
    },
    async leftChat({commit, getters, dispatch}, chat) {
      await API.leaveChat(chat.id, () => {
        if (getters.chat && getters.chat.id === chat.id) {
          dispatch('escapeChat');
        }
        commit('removeChat', chat);
      });
    }
  }
}
