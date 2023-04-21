<script>
import axios from 'axios';

const REST_URI = '/messenger/api/v2/';

export default {
	name: 'API',

	/**
   * Fetch chats
   * @param {Function} callback
   */
	fetchChats(callback) {
		return axios.get(REST_URI + 'chats').then(response => {
			callback(response.data);
		});
	},

	/**
   * Fetch company
   * @param {Function} callback
   */
	fetchCompany(callback) {
		return axios.get(REST_URI + 'company').then(response => {
			callback(response.data);
		});
	},

	/**
   * Fetch users
   * @param {Function} callback
   */
	fetchUsers(callback) {
		axios.get(REST_URI + 'users').then(response => {
			callback(response.data);
		});
	},

	/**
   * Search chats
   * @param {String} search
   * @param {Function} callback
   * @return {Promise}
   */
	searchChats(search, callback) {
		return axios.get(REST_URI + 'search/chats', {
			params: {
				q: search,
			}
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Search messages
   * @param {String} search
   * @param chatId
   * @param date
   * @param onlyFiles
   * @param {Function} callback
   * @return {Promise}
   */
	searchMessages(search, chatId = null, date = null, onlyFiles = false, callback = () => {}) {
		return axios.get(REST_URI + 'search/messages', {
			params: {
				q: search,
				chat_id: chatId,
				only_files: onlyFiles,
				date: date,
			}
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Fetch messages
   * @param {Number} chatId
   * @param count
   * @param start_message_id
   * @param including
   * @param {Function} callback
   */
	fetchMessages(chatId, count, start_message_id, including, callback) {
		axios.get(REST_URI + 'chat/' + chatId + '/messages', {
			params: {
				count: count,
				start_message_id: start_message_id,
				including: including,
			}
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Set messages as read
   * Returns chats list with updated unread count
   * @param {array} messages - array of messages
   * @param {Function} callback
   * @return {Promise}
   */
	setMessagesAsRead(messages, callback = () => {}) {
		return axios.post(REST_URI + 'messages/read', {
			messages: messages,
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Get chat info
   * @param {Number} chatId
   * @param {Function} callback
   * @return {Promise}
   */
	getChatInfo(chatId, callback) {
		// check if chat id contains user and should be private
		let url = chatId.toString().includes('user') ? 'private/' + chatId.replace('user', '') : 'chat/' + chatId;
		axios.get(REST_URI + url).then(response => {
			callback(response.data);
		});
	},

	/**
   * Send message
   * @param {Number} chatId
   * @param {String} message
   * @param citedMessageId
   * @param successCallback
   * @param errorCallback
   */
	sendMessage(chatId, message, citedMessageId, successCallback = () => {}, errorCallback = () => {}) {
		axios.post(REST_URI + 'chat/' + chatId + '/messages', {
			message: message,
			cite_message_id: citedMessageId,
		}).then(response => {
			successCallback(response.data);
		}).catch(error => {
			console.log('Error sending message', error);
			errorCallback(error.response.data);
		});
	},

	/**
   * Edit message
   * @param {Number} messageId
   * @param {String} message
   * @param {Function} callback
   */
	editMessage(messageId, message, callback) {
		axios.post(REST_URI + 'message/' + messageId, {
			message: message,
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Delete message
   * @param {Number} messageId
   * @param {Function} callback
   */
	deleteMessage(messageId, callback) {
		axios.delete(REST_URI + 'message/' + messageId).then(response => {
			callback(response.data);
		});
	},

	/**
   * Pin message
   * @param {Number} messageId
   * @param {Function} callback
   */
	pinMessage(messageId, callback = () => {}) {
		axios.post(REST_URI + 'message/' + messageId + '/pin').then(response => {
			callback(response.data);
		});
	},

	/**
   * Unpin message
   * @param {Number} messageId
   * @param {Function} callback
   */
	unpinMessage(messageId, callback = () => {}) {
		axios.delete(REST_URI + 'message/' + messageId + '/pin').then(response => {
			callback(response.data);
		});
	},

	/**
   * Pin chat
   * @param {Number} chatId
   * @param {Function} callback
   */
	pinChat(chatId, callback = () => {}) {
		axios.post(REST_URI + 'chat/' + chatId + '/pin').then(response => {
			callback(response.data);
		});
	},

	/**
   * Unpin chat
   * @param {Number} chatId
   * @param {Function} callback
   */
	unpinChat(chatId, callback = () => {}) {
		axios.delete(REST_URI + 'chat/' + chatId + '/pin').then(response => {
			callback(response.data);
		});
	},

	/**
   * Create chat with title and description
   * @param {String} title
   * @param {String} description
   * @param {array} members
   * @return {Promise}
   */
	createChat(title, description, members) {
		return axios.post(REST_URI + 'chat', {
			title: title,
			description: description,
			members: members,
		});
	},

	/**
   * Remove chat
   * @param {Number} chatId
   * @param {Function} callback
   * @return {Promise}
   */
	removeChat(chatId, callback) {
		return axios.delete(REST_URI + 'chat/' + chatId).then(response => {
			callback(response.data);
		});
	},

	/**
   * Leave chat
   * @param {Number} chatId
   * @param {Function} callback
   * @return {Promise}
   */
	leaveChat(chatId, callback) {
		return axios.post(REST_URI + 'chat/' + chatId + '/leave').then(response => {
			callback(response.data);
		});
	},

	/**
   * Add user to chat
   * @param {Number} chatId
   * @param {Number} userId
   * @param {Function} callback
   * @return {Promise}
   */
	addUserToChat(chatId, userId, callback = () => {}) {
		return axios.post(REST_URI + 'chat/' + chatId + '/addUser', {
			user_id: userId,
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Remove user from chat
   * @param {Number} chatId
   * @param {Number} userId
   * @param {Function} callback
   * @return {Promise}
   */
	removeUserFromChat(chatId, userId, callback = () => {}) {
		return axios.post(REST_URI + 'chat/' + chatId + '/removeUser/' + userId).then(response => {
			callback(response.data);
		});
	},

	/**
   * Edit chat
   * @param {Number} chatId
   * @param {String} title
   * @param {String} description
   * @param {Function} callback
   * @return {Promise}
   */
	editChat(chatId, title, description, callback = () => {}) {
		return axios.post(REST_URI + 'chat/' + chatId + '/edit', {
			title: title,
			description: description,
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Upload file
   * @param chatId
   * @param caption
   * @param files
   * @param {Function} callback
   * @param callback_error
   * @return {Promise}
   */
	uploadFiles(chatId, files, caption, callback, callback_error) {
		let formData = new FormData();
		for (let i = 0; i < files.length; i++) {
			formData.append('files[]', files[i]);
		}
		formData.append('message', caption);

		return axios.post(REST_URI + 'chat/' + chatId + '/upload', formData, {
			headers: {
				'Content-Type': 'multipart/form-data'
			}
		}).then(response => {
			callback(response.data);
		}).catch(error => {
			callback_error(error.response.data);
		});
	},

	/**
   * Upload chat avatar
   * @param chatId
   * @param file
   */
	uploadChatAvatar(chatId, file) {
		let formData = new FormData();
		formData.append('avatar', file);

		return axios.post(REST_URI + 'chat/' + chatId + '/avatar', formData, {
			headers: {
				'Content-Type': 'multipart/form-data'
			}
		});
	},

	/**
   * Set emoji reaction to message
   * @param {Number} messageId
   * @param {String} emoji
   * @param {Function} callback
   */
	reactMessage(messageId, emoji, callback = () => {}) {
		axios.post(REST_URI + 'message/' + messageId + '/reaction', {
			emoji: emoji,
		}).then(response => {
			callback(response.data);
		});
	},

	/**
   * Set user as chat admin
   * @param {Number} chatId
   * @param {Number} userId
   * @param {Function} callback
   */
	setChatAdmin(chatId, userId, callback = () => {}) {
		axios.post(REST_URI + 'chat/' + chatId + '/setAdmin/' + userId).then(response => {
			callback(response.data);
		});
	},

	/**
   * Remove user from chat admin
   * @param {Number} chatId
   * @param {Number} userId
   * @param {Function} callback
   */
	unsetChatAdmin(chatId, userId, callback = () => {}) {
		axios.post(REST_URI + 'chat/' + chatId + '/unsetAdmin/' + userId).then(response => {
			callback(response.data);
		});
	},

	/**
   * Mute chat for current user
   * @param {Number} chatId
   */
	muteChat(chatId){
		return axios.post(`${REST_URI}chat/${chatId}/mute`)
	},

	/**
   * Unmute chat for current user
   * @param {Number} chatId
   */
	unmuteChat(chatId){
		return axios.delete(`${REST_URI}chat/${chatId}/unmute`)
	},

	allFiles(chatId){
		return axios.get(`${REST_URI}chat/${chatId}/files`)
	},
}
</script>
