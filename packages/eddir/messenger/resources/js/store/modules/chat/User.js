export default {
  actions: {
    async updateUser({commit, getters, dispatch}, user) {
      commit('setUser', user);
      if (!getters.initialized) {
        dispatch('init', true);
      }
    }
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    }
  },
  state: {
    user: []
  },
  getters: {
    user: state => state.user
  }
}
