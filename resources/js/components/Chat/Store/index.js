import Vue from 'vue'
import Vuex from 'vuex'
import message from './modules/chat/Message.js'
import search from './modules/chat/Search.js';
import user from './modules/chat/User.js';
import chat from './modules/chat/Chat.js';
import messenger from './modules/chat/Messenger.js';

Vue.use(Vuex)

const store = new Vuex.Store({
	modules: {
		messenger,
		message,
		chat,
		search,
		user
	},
})

export default store
