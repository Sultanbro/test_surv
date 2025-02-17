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
    updateChatLastMessage(state, {chat, message, isSender}) {
      chat.last_message = message;
      if (!isSender) {
        chat.unread_messages_count++;
      }
    },
    removeChat(state, chat) {
      state.chats = state.chats.filter(c => c.id !== chat.id);
    },
    markChatAsSeen(state, left = 0) {
      state.chat.unread_messages_count = left;
      state.chats = state.chats.map(chat => {
        if (chat.id === state.chat.id) {
          chat.unread_messages_count = left;
        }
        return chat;
      });
    },
    addMembers(state, members) {
      // check if members already exists
      let newMembers = members.filter(member => !state.chat.users.find(m => m.id === member.id));
      // update via Vue.set to make it reactive
      Vue.set(state.chat, 'users', state.chat.users.concat(newMembers));
    },
    removeMembers(state, members) {
      // update via Vue.set to make it reactive
      Vue.set(state.chat, 'users', state.chat.users.filter(member => {
        // member id is not in members array
        return !members.find(m => m.id === member.id);
      }));
    },
    updateChat(state, chat) {
      // find chat and update it title and description without changing the reference
      const chatFromList = state.chats.find(c => c.id === chat.id);
      chatFromList.title = chat.title;
      chatFromList.description = chat.description;
      chatFromList.pinned = chat.pinned;
      chatFromList.image = chat.image;
      chatFromList.users = chat.users;
      if (state.chat && state.chat.id === chat.id) {
        state.chat.title = chat.title;
        state.chat.description = chat.description;
        state.chat.pinned = chat.pinned;
        state.chat.image = chat.image;
        state.chat.users = chat.users;
      }
    },
    setChatOnline(state, online) {
      state.chat.isOnline = online;
    },
  },
  getters: {
    chats: state => state.chats,
    sortedChats: state => {
      return state.chats.sort((a, b) => {
        if (a.pinned && b.pinned) {
          return b.id - a.id;
        }
        if (a.pinned) {
          return -1;
        }
        if (b.pinned) {
          return 1;
        }
        let date1 = a.last_message ? a.last_message.created_at : a.created_at;
        let date2 = b.last_message ? b.last_message.created_at : b.created_at;
        return new Date(date2) - new Date(date1);
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
    async loadChat({commit, getters, dispatch}, {chatId, callback: callback = () => {}}) {
      await API.getChatInfo(chatId, chat => {
        commit('setChat', chat);
        commit('setPinnedMessage', chat.pinned_message);
        dispatch('loadMessages', {reset: true, callback: callback});
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
        dispatch('loadChat', {chatId: response.data.id});
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
        commit('setPinnedMessage', null);
      });
    },
    async removeChat({commit, getters, dispatch}, chat) {
      await API.removeChat(chat.id, () => {
        if (getters.chat && getters.chat.id === chat.id) {
          dispatch('escapeChat');
        }
        commit('removeChat', chat);
        commit('setPinnedMessage', null);
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
    },
    async editChatTitle({commit, getters, dispatch}) {
      await API.editChat(getters.chat.id, getters.chat.title, getters.chat.description);
      commit('updateChat', getters.chat);
    },
    async uploadChatAvatar({commit, getters, dispatch}, file) {
      await API.uploadChatAvatar(getters.chat.id, file);
    },
    async pinChat({commit, getters, dispatch}, chat) {
      await API.pinChat(chat.id);
      chat.pinned = true;
      commit('updateChat', chat);
    },
    async unpinChat({commit, getters, dispatch}, chat) {
      await API.unpinChat(chat.id);
      chat.pinned = false;
      commit('updateChat', chat);
    },
    async setChatAdmin({commit, getters, dispatch}, {chat, user}) {
      API.setChatAdmin(chat.id, user.id);
    },
    async unsetChatAdmin({commit, getters, dispatch}, {chat, user}) {
      API.unsetChatAdmin(chat.id, user.id);
    }
  }
}
