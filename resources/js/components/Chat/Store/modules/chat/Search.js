import API from "../../API.vue";

export default {
  state: {
    contacts: [],
    newChatContacts: [],
    searchFocus: false,
    searchMessagesResults: [],
    searchType: "messages"
  },
  mutations: {
    setContacts(state, contacts) {
      state.contacts = contacts;
    },
    setMessagesResults(state, messages) {
      state.searchMessagesResults = messages;
    },
    clearContacts(state) {
      state.contacts = [];
    },
    clearMessagesSearchResults(state) {
      state.searchMessagesResults = [];
    },
    setNewChatContacts(state, contacts) {
      state.newChatContacts = contacts;
    },
    setSearchFocus(state, focus) {
      state.searchFocus = focus;
    }
  },
  getters: {
    contacts: state => state.contacts,
    newChatContacts: state => state.newChatContacts,
    searchMessagesChatsResults: state => state.searchMessagesResults,
    searchType: state => state.searchType,
    isSearchFocus: state => state.searchFocus,
  },
  actions: {
    async findContacts({commit, getters, dispatch}, username) {
      if (username.length > 1) {
        commit('setSearchMode', true);
        await API.searchChats(username, contacts => {
          contacts.users.forEach(user => {
            user.id = 'user' + user.id;
            user.title = user.name + ' ' + user.last_name;
            user.private = true;
          });
          commit('setContacts', contacts.users.concat(contacts.chats));
        });
      } else {
        commit('setSearchMode', false);
        commit('clearContacts');
      }
    },
    async findMessages({commit, getters, dispatch}, text) {
      if (text.length > 1) {
        commit('setSearchMode', true);
        await API.searchMessages(text, messages => {
          // result_chats is an array of chats with messages
          let result_chats = [];
          messages.forEach(message => {
            // append chat of the message to the result_chats array
            let chat = getters.chats.find(chat => chat.id === message.chat_id);
            if (chat) {
              // use clone of the chat to avoid changing the original chat
              let chat_clone = JSON.parse(JSON.stringify(chat));
              chat_clone.last_message = message;
              result_chats.push(chat_clone);
            }
          });
          commit('setMessagesResults', result_chats);
        });
      } else {
        commit('setSearchMode', false);
        commit('clearMessagesSearchResults');
      }
    },
    async getUsers({commit}) {
      await API.fetchUsers(users => {
        users.forEach(user => {
          user.title = user.name;
          user.private = true;
        });
        commit('setContacts', users);
      });
    },
    async setCurrentChatContacts({commit, getters, dispatch}, contacts) {
      commit('setNewChatContacts', contacts);
    },
    async setSearchFocus({commit, getters, dispatch}, focus) {
      commit('setSearchFocus', focus);
    }
  }
}
