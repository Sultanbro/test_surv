/** @module stores/api/partal */
import axios from 'axios'

/**
 * Получение уведомлений
 * @return {ApiResponse.FetchNotificationsResponse}
 */
export async function fetchNotifications(){
	const { data } = await axios.post('/notifications')
	return data
}

/**
 * @param {ApiRequest.SetNotificationsReadRequest} request
 * @return {ApiResponse.SetNotificationsReadResponse}
 */
export async function setNotificationsRead(request){
	const { data } = await axios.post('/notifications/set-read', request)
	return data
}

/**
 * @return {ApiResponse.SetNotificationsReadAllResponse}
 */
export async function setNotificationsReadAll(){
	const { data } = await axios.post('/notifications/set-read-all/', {})
	return data
}

/**
 * @typedef FetchNotificationsResponse
 * @memberof ApiResponse
 * @property {Array<Notification>} read
 * @property {Array<Notification>} unread
 * @property {Number} unreadQuantity
 */

/**
 * @typedef SetNotificationsReadRequest
 * @memberof ApiRequest
 * @property {Notification} id
 */

/**
 * @typedef SetNotificationsReadResponse
 * @memberof ApiResponse
 * ...
 */

/**
 * @typedef SetNotificationsReadAllResponse
 * @memberof ApiResponse
 * ...
 */
