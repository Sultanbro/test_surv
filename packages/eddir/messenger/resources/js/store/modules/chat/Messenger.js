export default {
  actions: {
    async boot({commit, getters, dispatch}) {
      let debug = window.location.href.indexOf("rostkov.me") !== -1;
      commit('setDebugMode', debug);
      dispatch('loadChats');

      if (debug) {
        setTimeout(() => {
          dispatch('loadChat', getters.chats[0].id);
        }, 2000);
      }
    },
    async init({commit, getters, dispatch}) {

      window.Echo.connector.pusher.connection.bind_global(function (payload) {
        if (!getters.isSocketConnected && payload === 'message') {
          commit('setSocketConnected', true);
        } else if (payload === 'connected') {
          commit('setSocketConnected', true);
        } else if (payload === 'error' || payload === 'disconnected' || payload === 'connecting' || payload === 'unavailable') {
          commit('setSocketConnected', false);
        }
      });

      // new message notification
      window.Echo.private(`messages.${getters.user.id}`)
        .listen('.newMessage', e => {
          if (e.message.event) {
            dispatch('newServiceMessage', e.message);
          } else {
            dispatch('newMessage', e.message);
          }
        });
      commit('setInitialize', true);
    },
    async toggleMessenger({commit}) {
      commit('toggleMessenger');
    },
    async toggleInfoPanel({commit}) {
      commit('toggleInfoPanel');
    },
    async requestScroll({commit}, position) {
      commit('setScrolling', position);
    }
  },
  mutations: {
    setInitialize(state, initialized) {
      state.initialized = initialized;
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
    },
    setScrolling(state, position) {
      state.scrollingPosition = position;
    },
    setSocketConnected(state, status) {
      state.socketConnected = status;
    }
  },
  state: {
    initialized: false,
    fullscreen: false,
    open: false,
    infoPanel: false,
    debug: false,
    searchMode: false,
    scrollingPosition: -1,
    socketConnected: false
  },
  getters: {
    isInitialized: state => state.initialized,
    isOpen: state => state.open,
    isInfoPanel: state => state.infoPanel,
    isDebug: state => state.debug,
    isSearchMode: state => state.searchMode,
    scrollingPosition: state => state.scrollingPosition,
    isSocketConnected: state => state.socketConnected
  }
}
