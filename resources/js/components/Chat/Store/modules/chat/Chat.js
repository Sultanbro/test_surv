import API from "../../API.vue";

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
      chat.unread_messages_count++;
    },
    removeChat(state, chat) {
      state.chats = state.chats.filter(c => c.id !== chat.id);
    },
    markChatAsSeen(state) {
      state.chat.unread_messages_count = 0;
      state.chats = state.chats.map(chat => {
        if (chat.id === state.chat.id) {
          chat.unread_messages_count = 0;
        }
        return chat;
      });
    },
    addMembers(state, members) {
      // update via Vue.set to make it reactive
      Vue.set(state.chat, 'users', state.chat.users.concat(members));
    },
    removeMembers(state, members) {
      // update via Vue.set to make it reactive
      Vue.set(state.chat, 'users', state.chat.users.filter(member => {
        return !members.includes(member);
      }));
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
    async escapeChat({commit, getters, dispatch}) {
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
    },
    async addMembers({commit, getters, dispatch}, members) {
      for (let member of members) {
        await API.addUserToChat(getters.chat.id, member.id);
      }
      commit('addMembers', members);
    },
    async removeMembers({commit, getters, dispatch}, members) {
      for (let member of members) {
        await API.removeUserFromChat(getters.chat.id, member.id);
      }
      commit('removeMembers', members);
    }
  }
}
