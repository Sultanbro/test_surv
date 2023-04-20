import API from '../../API.vue';
import Vue from 'vue';

import {MESSAGES_MAX_COUNT, MESSAGES_LOAD_COUNT, MESSAGES_LOAD_COUNT_ON_RESET} from './constants.js';

export default {
	state: {
		messages: [],
		editMessage: null,
		citedMessage: null,
		pinnedMessage: null,
		messagesLoadMoreCount: MESSAGES_LOAD_COUNT,
		startMessageId: null,
		messagesOldEndReached: false,
		messagesNewEndReached: false,
		messagesLoading: false
	},
	actions: {
		async loadMessages({commit, getters, dispatch}, {
			reset = false,
			goto = 0,
			callback = () => {}
		} = {}) {
			if (getters.messagesLoading) {
				return;
			}
			commit('setMessagesLoading', true);

			let count, startMessageId, including;
			if (reset) {
				count = MESSAGES_LOAD_COUNT_ON_RESET;
				startMessageId = null;
				including = !!goto;
			} else if (goto > 0) {

				count = MESSAGES_MAX_COUNT;
				startMessageId = goto;
				including = true;
			} else {
				count = getters.messagesLoadMoreCount;
				startMessageId = getters.startMessageId;
				including = false;
			}

			return API.fetchMessages(getters.chat.id, count, startMessageId, including, messages => {
				if (reset || goto) {
					commit('resetMessages');
				}

				if (messages.length === 0) {

					if (count < 0) {
						commit('setMessagesNewEndReached');
					} else {
						commit('setMessagesOldEndReached');
					}

				}
				else {

					messages = Object.keys(messages).map(key => messages[key]).reverse();
					dispatch('markMessagesAsRead', messages);

					if (reset) {
						commit('setMessages', messages);
						dispatch('requestScroll', 0);
					} else if (count > 0) {
						commit('prependMessages', messages);
					} else {
						commit('appendMessages', messages);
					}

					if (goto > 0) {

						// after 4 seconds
						setTimeout(() => {
							// next tick
							let mc = document.getElementById('messenger_container');
							let message = document.getElementById('messenger_message_' + goto);
							if (message) {
								mc.scrollTop = message.offsetTop - 100;
							}
						}, 1000);
					}

				}
				commit('setMessagesLoading', false);
				callback();
			});
		},
		async loadMoreOldMessages({commit, getters, dispatch}) {
			commit('setMessagesLoadMoreCount', MESSAGES_LOAD_COUNT);
			commit('setStartMessageId', getters.messages[0].id);
			dispatch('loadMessages');
		},
		async loadMoreNewMessages({commit, getters, dispatch}) {
			commit('setMessagesLoadMoreCount', -MESSAGES_LOAD_COUNT);
			commit('setStartMessageId', getters.messages[getters.messages.length - 1].id);
			dispatch('loadMessages');
		},
		async sendMessage({commit, getters, dispatch}, message) {
			let citedMessageId = getters.citedMessage ? getters.citedMessage.id : null;
			const guid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
			let newMessage = {
				id: guid,
				body: message,
				created_at: new Date().toISOString(),
				chat_id: getters.chat.id,
				sender_id: getters.user.id,
				parent: getters.citedMessage
			}
			commit('addMessage', newMessage);
			dispatch('requestScroll', 0);
			commit('setCitedMessage', null);
			return API.sendMessage(getters.chat.id, message, citedMessageId, response => {
				response.new_id = response.id;
				response.id = guid;
				commit('updateMessage', response);
			}, () => {
				newMessage.id = guid;
				newMessage.failed = true;
				commit('updateMessage', newMessage);
			});
		},
		async editMessageAction({commit, getters, dispatch}, text) {
			return API.editMessage(getters.editMessage.id, text, response => {
				response.id = getters.editMessage.id;
				commit('updateMessage', response);
				dispatch('cancelEditMessage');
			});
		},
		async newMessage({commit, getters, dispatch}, message) {
			if (getters.chat.id === message.chat_id && (getters.user.id !== message.sender_id)) {
				commit('addMessage', message);
				dispatch('markMessagesAsRead', [message]);
			}
			let chat = getters.chats.find(chat => chat.id === message.chat_id);
			// add chat if not exists
			if (chat) {
				const isSender = getters.user.id === message.sender_id;
				commit('updateChatLastMessage', {chat, message, isSender});
			} else {
				await API.getChatInfo(message.chat_id, chat => {
					commit('addChat', chat);
				});
			}
		},
		async newServiceMessage({commit, getters}, message) {
			switch (message.event.type) {
			case 'join':
				if (getters.chat && getters.chat.id === message.chat_id) {
					commit('addMembers', [message.event.payload.user]);
				} else if (getters.user.id === message.event.payload.user.id) {
					await API.getChatInfo(message.chat_id, chat => {
						commit('addChat', chat);
					});
				}
				break;
			case 'leave':
				// check if it is current chat
				if (getters.chat.id === message.chat_id) {
					commit('removeMembers', [message.event.payload.user]);
				}
				break;
			case 'delete':
				commit('deleteMessage', message.event.payload.message_id);
				break;
			case 'edit':
				commit('updateMessage', message.event.payload.message);
				break;
			case 'pin':
				commit('setPinnedMessage', message.event.payload.message);
				break;
			case 'unpin':
				commit('setPinnedMessage', null);
				break;
			case 'rename':
				commit('updateChat', message.chat);
				break
			case 'online':
				if (getters.chat && getters.chat.private && getters.chat.users.find(member => member.id === message.sender.id)) {
					commit('setChatOnline', true);
				}
				return true;
			case 'offline':
				// if user is in current chat
				if (getters.chat && getters.chat.private && getters.chat.users.find(member => member.id === message.sender.id)) {
					commit('setChatOnline', false);
				}
				return true;
			case 'chat_created':
				commit('addChat', message.event.payload.chat);
				break;
			case 'read':
				commit('setMessageRead', {messageId: message.event.payload.message_id, reader: message.sender});
				return true;
			case 'delete_chat':
				commit('removeChat', message.chat);
				commit('setPinnedMessage', null);
				break;
			case 'reaction':
				commit('addReaction', {
					messageId: message.event.payload.message_id,
					reaction: message.event.payload.reaction,
					user: message.sender
				});
				return;
			case 'chat_photo':
				commit('updateChat', message.chat);
				return;
			case 'chat_admin':
				commit('updateChat', message.chat);
				return;
			}

			if (getters.chat && getters.chat.id === message.chat_id) {
				commit('addMessage', message);
			}
		},
		async markMessagesAsRead({commit, getters}, messages) {
			// exclude messages from user
			messages = messages.filter(message => message.sender_id !== getters.user.id);
			// exclude messages contains reader.id (current user)
			messages = messages.filter(message => !message.readers || !message.readers.find(reader => reader.id === getters.user.id));
			if (messages.length > 0) {
				await API.setMessagesAsRead(messages.map(message => message.id), (response) => {
					commit('markChatAsSeen', response.left);
				});
			}
		},
		async deleteMessage({commit}, message) {
			return API.deleteMessage(message.id, () => {
				commit('deleteMessage', message);
			});
		},
		async startEditMessage({commit}, message) {
			commit('setEditMessage', message);
			document.getElementById('messengerMessageInput').focus();
		},
		async cancelEditMessage({commit}) {
			commit('setEditMessage', null);
		},
		async pinMessage({commit}, message) {
			return API.pinMessage(message.id, () => {
				commit('setPinnedMessage', message);
			});
		},
		async unpinMessage({commit, getters}) {
			return API.unpinMessage(getters.pinnedMessage.id, () => {
				commit('setPinnedMessage', null);
			});
		},
		async citeMessage({commit}, message) {
			commit('setCitedMessage', message);
		},
		async uploadFiles({commit, getters}, {files, caption = ''}) {
			const guid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
			let newMessage = {
				id: guid,
				body: 'Загрузка файла...',
				created_at: new Date().toISOString(),
				chat_id: getters.chat.id,
				sender_id: getters.user.id
			}

			commit('addMessage', newMessage);

			return API.uploadFiles(getters.chat.id, files, caption, message => {
				message.new_id = message.id;
				message.id = guid;
				commit('updateMessage', message);
			}, () => {
				newMessage.id = guid;
				newMessage.failed = true;
				newMessage.body = 'Ошибка загрузки файла';
				commit('updateMessage', newMessage);
			});
		},
		async reactMessage(context, {message, emoji_id}) {
			return API.reactMessage(message.id, emoji_id);
		},
	},
	mutations: {
		setMessages(state, messages) {
			state.messages = messages;
		},
		appendMessages(state, messages) {
			state.messages.push(...messages.reverse());
		},
		prependMessages(state, messages) {
			state.messages.unshift(...messages);
		},
		addMessage(state, message) {
			if (!state.messages.find(m => m.id === message.id)) {
				message.failed = false;
				state.messages.push(message);
			}
		},
		updateMessage(state, message) {
			let index = state.messages.findIndex(m => m.id === message.id);

			if (index !== -1 && message.new_id) {
				message.id = message.new_id;
			}

			Vue.set(state.messages, index, message);
		},
		deleteMessage(state, message) {
			let index = state.messages.findIndex(m => m.id === message.id);
			state.messages.splice(index, 1);
		},
		setEditMessage(state, message) {
			state.editMessage = message ? message : null;
		},
		setPinnedMessage(state, message) {
			state.pinnedMessage = message;
		},
		setMessagesLoadMoreCount(state, messagesLoadMoreCount) {
			state.messagesLoadMoreCount = messagesLoadMoreCount;
		},
		setStartMessageId(state, startMessageId) {
			state.startMessageId = startMessageId;
		},
		resetMessages(state) {
			state.messages = [];
			state.messagesLoadMoreCount = MESSAGES_MAX_COUNT;
			state.startMessageId = null;
			state.messagesOldEndReached = false;
			state.messagesNewEndReached = false;
		},
		setMessagesOldEndReached(state) {
			state.messagesOldEndReached = true;
		},
		setMessagesNewEndReached(state) {
			state.messagesNewEndReached = true;
		},
		setMessagesLoading(state, messagesLoading) {
			state.messagesLoading = messagesLoading;
		},
		setMessageRead(state, {messageId, reader}) {
			let message = state.messages.find(m => m.id === messageId);
			if (message && !(message.readers && message.readers.find(r => r.id === reader.id))) {
				let readers = message.readers || [];
				readers.push(reader);
				Vue.set(message, 'readers', readers);
			}
		},
		reactMessage(state, {message, user, emoji}) {
			if (!message.reactions) {
				message.reactions = [];
			}
			let reaction = message.reactions.find(r => r.emoji === emoji);
			if (reaction) {
				reaction.push(user);
			} else {
				message.reactions.push({emoji, users: [user]});
			}
		},
		addReaction(state, {messageId, reaction, user}) {
			let message = state.messages.find(m => m.id === messageId);
			if (message) {
				// update user reaction in readers or add if he didn't read
				user.pivot = {
					reaction: reaction,
					message_id: messageId,
					user_id: user.id
				};
				let reader = message.readers.find(r => r.id === user.id);
				if (reader) {
					reader.pivot = user.pivot;
					Vue.set(message.readers, message.readers.indexOf(reader), reader);
				} else {
					message.readers.push(user);
				}
			}
		},
		setCitedMessage(state, message) {
			state.citedMessage = message;
		}
	},
	getters: {
		messages: state => state.messages,
		messagesMap: (state, getters) => {
			const uniqueDates = [];
			const messagesMap = {};
			state.messages.forEach((message, index) => {
				const date = new Date(message.created_at);
				const dateKey = date.toLocaleDateString();
				if (!uniqueDates.includes(dateKey)) {
					uniqueDates.push(dateKey);
					messagesMap[dateKey] = [];
				}
				if(!state.messages[index + 1]){
					message.last = true
				}

				messagesMap[dateKey].push(message);
			}, {});

			Object.keys(messagesMap).forEach(dateKey => {
				let prevMsg = null
				let unread = false
				messagesMap[dateKey] = messagesMap[dateKey].map((message, i) => {
					const nextMsg = messagesMap[dateKey][i + 1]
					const isUserFirst = !prevMsg || !!prevMsg.event || prevMsg.sender_id !== message.sender_id
					const isUserLast = !nextMsg || !!nextMsg.event || nextMsg.sender_id !== message.sender_id
					const own = message.sender_id === getters.user.id
					const isMessageRead = message.readers || ~message.readers.findIndex(reader => reader.id === getters.user.id)
					let isUnreadFirst = false
					if(!message.event && !own && !unread && !isMessageRead){
						unread = true
						isUnreadFirst = true
					}
					prevMsg = message
					return {
						message,
						renderHelper: {
							isUserFirst,
							isUserLast,
							isUnreadFirst,
							unread,
							own,
							last: !nextMsg
						}
					}
				})
			})

			return messagesMap;
		},
		editMessage: state => state.editMessage,
		citedMessage: state => state.citedMessage,
		pinnedMessage: state => state.pinnedMessage,
		unreadCount: (state, getters) => getters.chats.reduce((sum, chat) => sum + chat.unread_messages_count, 0),
		messagesLoadMoreCount: state => state.messagesLoadMoreCount,
		startMessageId: state => state.startMessageId,
		messagesOldEndReached: state => state.messagesOldEndReached,
		messagesNewEndReached: state => state.messagesNewEndReached,
		messagesLoading: state => state.messagesLoading
	}
}
