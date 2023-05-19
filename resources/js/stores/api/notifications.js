/** @module stores/api/partal */
/// <reference path="./notifications.d.ts" />
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
	const { data } = await axios.post('/notifications/read', request)
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
 * Получение вариантов уведомлений для настроек
 */
export async function fetchNotificationVariants(){
	const { data } = await axios.get('/mailing')
	return data.data
}

export async function createNotification(notification){
	const { data } = await axios.post('/mailing', notification)
	return data
}

export async function fetchNotification(id){
	const { data } = await axios.get(`/mailing/find/${id}`)
	return data
}

export async function updateNotification(notification){
	const { data } = await axios.put('/mailing', notification)
	return data
}

export async function deleteNotification(id){
	const { data } = await axios.delete(`/mailing/${id}`)
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
