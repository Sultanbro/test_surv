import API from "../../API.vue";

export default {
  state: {
    contacts: [],
    newChatContacts: [],
  },
  mutations: {
    setContacts(state, contacts) {
      state.contacts = contacts;
    },
    clearContacts(state) {
      state.contacts = [];
    },
    setNewChatContacts(state, contacts) {
      state.newChatContacts = contacts;
    }
  },
  getters: {
    contacts: state => state.contacts,
    newChatContacts: state => state.newChatContacts,
  },
  actions: {
    async searchContacts({commit, getters, dispatch}, username) {
      if (username.length > 1) {
        commit('setSearchMode', true);
        await API.searchChats(username, contacts => {
          contacts.users.forEach(user => {
            user.id = 'user' + user.id;
            user.title = user.name;
            user.private = true;
          });
          commit('setContacts', contacts.users.concat(contacts.chats));
        });
      } else {
        commit('setSearchMode', false);
        commit('clearContacts');
      }
    },
    async getUsers({commit, getters, dispatch}) {
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
    }
  }
}
