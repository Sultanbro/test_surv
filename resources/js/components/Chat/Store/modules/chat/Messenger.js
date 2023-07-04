export default {
	actions: {
		async boot({commit, getters, dispatch}) {
			let debug = window.location.href.indexOf('rostkov.me') !== -1;
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

			const domain = window.location.hostname;
			// new message notification
			window.Echo.private(`messages.${domain}.${getters.user.id}`)
				.listen('.newMessage', e => {
					if (e.message.event) {
						dispatch('newServiceMessage', e.message);
					} else {
						dispatch('newMessage', e.message);
					}
					dispatch('requestScroll', 0);
				});

			// Запрос на уведомления браузера
			if (Notification.permission === 'default') Notification.requestPermission()

			commit('setInitialize', true);
		},
		async toggleMessenger({dispatch, commit, getters}) {
			if(!getters.isOpen && getters.chat && getters.messages){
				dispatch('markMessagesAsRead', getters.messages)
			}
			commit('toggleMessenger');
		},
		async toggleInfoPanel({commit}) {
			commit('toggleInfoPanel');
		},
		async toggleAddUserDialog({commit}) {
			commit('toggleAddUserDialog');
		},
		async toggleNewChatDialog({commit}) {
			commit('toggleNewChatDialog');
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
		},
		sendNotification({dispatch}, { title, body, icon, data }){
			if (!('Notification' in window)) return
			if (Notification.permission === 'granted') {
				/* const notification = */ new Notification(title, { body, icon });
				// notification.addEventListener('click', () => {
				// 	if(data?.chatId) dispatch('loadChat', data)
				// })
			}
			else if (Notification.permission !== 'denied') {
				Notification.requestPermission().then(permission => {
					if (permission === 'granted') dispatch('sendNotification', { title, body, icon, data })
				})
			}
		}
	},
	mutations: {
		setInitialize(state, initialized) {
			state.initialized = initialized;
		},
		toggleInfoPanel(state) {
			state.infoPanel = !state.infoPanel;
		},
		toggleAddUserDialog(state) {
			state.addUserDialog = !state.addUserDialog;
		},
		toggleNewChatDialog(state) {
			state.newChatDialog = !state.newChatDialog;
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
		addUserDialog: false,
		newChatDialog: false,
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
		isAddUserDialog: state => state.addUserDialog,
		isNewChatDialog: state => state.newChatDialog,
		isSearchMode: state => state.searchMode,
		isChatSearchMode: state => state.chatSearchMode,
		scrollingPosition: state => state.scrollingPosition,
		isSocketConnected: state => state.socketConnected,
		galleryIndex: state => state.galleryIndex,
		galleryImages: state => state.galleryImages,
		isLoading: state => state.loading
	}
}
