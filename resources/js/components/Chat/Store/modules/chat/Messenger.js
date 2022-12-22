export default {
  actions: {
    async boot({commit, getters, dispatch}) {
      let debug = window.location.href.indexOf("rostkov.me") !== -1;
      commit('setDebugMode', debug);
      dispatch('loadChats');

      if (debug) {
        setTimeout(() => {
          dispatch('loadChat', {chatId: getters.chats[0].id});
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
          dispatch('requestScroll', 0);
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
    },
    showGallery({commit}, {images, index}) {
      commit('prepareGallery', {images, index});
    },
    hideGallery({commit}) {
      commit('hideGallery');
    },
    toggleChatSearchMode({commit, getters}) {
      commit('setChatSearchMode', !getters.isChatSearchMode);
    },
    setLoading({commit}, value) {
      commit('setLoading', value);
    }
  },
  mutations: {
    setInitialize(state, initialized) {
      state.initialized = initialized;
    },
    toggleInfoPanel(state) {
      state.infoPanel = !state.infoPanel;
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
    setChatSearchMode(state, mode) {
      state.chatSearchMode = mode;
    },
    setScrolling(state, position) {
      state.scrollingPosition = position;
    },
    setSocketConnected(state, status) {
      state.socketConnected = status;
    },
    prepareGallery(state, {images, index}) {
      state.galleryImages = images;
      state.galleryIndex = index;
    },
    hideGallery(state) {
      state.galleryImages = [];
      state.galleryIndex = null;
    },
    setLoading(state, status) {
      state.loading = status;
    }
  },
  state: {
    initialized: false,
    fullscreen: false,
    open: false,
    infoPanel: false,
    debug: false,
    searchMode: false,
    chatSearchMode: false,
    scrollingPosition: -1,
    socketConnected: false,
    galleryIndex: null,
    galleryImages: [],
    loading: false
  },
  getters: {
    isInitialized: state => state.initialized,
    isOpen: state => state.open,
    isInfoPanel: state => state.infoPanel,
    isSearchMode: state => state.searchMode,
    isChatSearchMode: state => state.chatSearchMode,
    scrollingPosition: state => state.scrollingPosition,
    isSocketConnected: state => state.socketConnected,
    galleryIndex: state => state.galleryIndex,
    galleryImages: state => state.galleryImages,
    isLoading: state => state.loading
  }
}
