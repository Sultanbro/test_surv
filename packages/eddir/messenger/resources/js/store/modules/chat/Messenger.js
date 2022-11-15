
export default {
  actions: {
    async boot({commit, getters, dispatch}) {
      let debug = window.location.href.indexOf("rostkov.me") !== -1;
      commit('setDebugMode', debug);
      dispatch('loadChats');

      if (debug) {
        setTimeout(() => {
          dispatch('loadChat', getters.chats[0].id);
        } , 2000);
      }
    },
    async init({commit, getters, dispatch}) {
      // new message notification
      window.Echo.channel(`messages.${getters.user.id}`).listen('.newMessage', e => {
        if (e.message.type) {
          dispatch('newServiceMessage', e.message);
        } else {
          dispatch('newMessage', e.message);
        }
      });

      commit('setInitialize', true);
    },
    async fullscreen({commit}) {
      commit('toggleFullscreen');
    },
    async online({commit}) {
      commit('setOnline', true);
    },
    async toggleMessenger({commit}) {
      commit('toggleMessenger');
    },
    async toggleInfoPanel({commit}) {
      commit('toggleInfoPanel');
    }
  },
  mutations: {
    setInitialize(state, initialized) {
      state.initialized = initialized;
    },
    toggleFullscreen(state) {
      state.fullscreen = !state.fullscreen;
    },
    toggleInfoPanel(state) {
      state.infoPanel = !state.infoPanel;
    },
    setOnline(state) {
      // todo
    },
    toggleMessenger(state) {
      state.open = !state.open;
    },
    setDebugMode(state, debug) {
      state.debug = debug;
      if (!state.initialized) {
        state.open = debug;
      }
    },
    setSearchMode(state, mode) {
      state.searchMode = mode;
    }
  },
  state: {
    initialized: false,
    fullscreen: false,
    open: false,
    infoPanel: false,
    debug: false,
    searchMode: false,
  },
  getters: {
    isInitialized: state => state.initialized,
    isFullscreen: state => state.fullscreen,
    isOpen: state => state.open,
    isInfoPanel: state => state.infoPanel,
    isDebug: state => state.debug,
    isSearchMode: state => state.searchMode
  }
}
