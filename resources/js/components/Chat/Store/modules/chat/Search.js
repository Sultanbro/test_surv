/* eslint-disable camelcase */

import API from '../../API.vue';

export default {
	state: {
		contacts: [],
		newChatContacts: [],
		searchFocus: false,
		searchMessagesResults: [],
		chatSearchMessagesResults: [],
		chatSearchFilesResults: [],
		searchType: 'messages',
		searchFilesQueryId: 0,
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
		},
		setChatMessagesResults(state, messages) {
			state.chatSearchMessagesResults = messages;
		},
		setChatFilesResults(state, files) {
			state.chatSearchFilesResults = files;
		}
	},
	getters: {
		contacts: state => state.contacts,
		newChatContacts: state => state.newChatContacts,
		searchMessagesChatsResults: state => state.searchMessagesResults,
		chatSearchMessagesResults: state => state.chatSearchMessagesResults,
		chatSearchFilesResults: state => state.chatSearchFilesResults,
		searchType: state => state.searchType,
		isSearchFocus: state => state.searchFocus,
		chatSearchFilesFiles: state => state.chatSearchFilesResults.reduce((files, message) => files.concat(message.files), [])
	},
	actions: {
		async findContacts({commit}, username) {
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
		async findMessages({commit, getters}, text) {
			if (text.length > 1) {
				commit('setSearchMode', true);
				await API.searchMessages(text, null,  null,false, messages => {
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
		async findMessagesInChat({commit}, {text, chat_id, date, year, month}) {
			await API.searchMessages({
				search: text,
				chatId: chat_id,
				date,
				onlyFiles: false,
				year,
				month,
			}, messages => {
				commit('setChatMessagesResults', messages);
			});
		},
		async findFilesInChat({commit, state}, {text, chat_id}) {
			const queryId = ++state.searchFilesQueryId;
			if (text.length > 1) {
				await API.searchMessages({
					search: text,
					chatId: chat_id,
					onlyFiles: true,
				}, files => {
					if(queryId === state.searchFilesQueryId) commit('setChatFilesResults', files)
				});
			}
			else {
				const {data} = await API.allFiles(chat_id)
				if(queryId === state.searchFilesQueryId) commit('setChatFilesResults', data.data)
			}
		},
		async setCurrentChatContacts({commit}, contacts) {
			commit('setNewChatContacts', contacts);
		},
		async setSearchFocus({commit}, focus) {
			commit('setSearchFocus', focus);
		}
	}
}
