<script>
import axios from 'axios';

const REST_URI = '/messenger/api/v2/';

export default {
  name: "API",

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
   * @param {Function} callback
   * @return {Promise}
   */
  searchMessages(search, callback) {
    return axios.get(REST_URI + 'search/messages', {
      params: {
        q: search,
      }
    }).then(response => {
      callback(response.data);
    });
  },

  /**
   * Fetch messages
   * @param {Number} chatId
   * @param {Function} callback
   */
  fetchMessages(chatId, callback) {
    axios.get(REST_URI + 'chat/' + chatId + '/messages').then(response => {
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
   * @param successCallback
   * @param errorCallback
   */
  sendMessage(chatId, message, successCallback = () => {}, errorCallback = () => {}) {
    axios.post(REST_URI + 'chat/' + chatId + '/messages', {
      message: message,
    }).then(response => {
      successCallback(response.data);
    }).catch(error => {
      console.log("Error sending message", error);
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
   * @param {File} file
   * @param {Function} callback
   * @param callback_error
   * @return {Promise}
   */
  uploadFile(chatId, caption, file, callback, callback_error) {
    let formData = new FormData();
    formData.append('file', file);
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
  }
}
</script>
