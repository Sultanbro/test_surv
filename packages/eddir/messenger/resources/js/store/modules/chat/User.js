import API from "../../API.vue";

export default {
  actions: {
    async updateUser({commit, getters, dispatch}, user) {
      commit('setUser', user);
      if (!getters.initialized) {
        dispatch('init', true);
      }
    },
    async loadCompany({commit}) {
      await API.fetchCompany(company => {
        commit('setUsers', company.users);
        commit('setProfileGroups', company.profile_groups);
        commit('setPositions', company.positions);
      });
    },
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setUsers(state, users) {
      state.users = users;
    },
    setProfileGroups(state, groups) {
      state.profile_groups = groups;
    },
    setPositions(state, positions) {
      state.positions = positions;
    },
  },
  state: {
    user: [],
    users: [],
    profile_groups: [],
    positions: [],
  },
  getters: {
    user: state => state.user,
    users: state => state.users,
    profileGroups: state => state.profile_groups,
    positions: state => state.positions,
  }
}
