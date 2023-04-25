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
	return [{
		id: 1,
		title: 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tempore laboriosam nisi, quia repudiandae quaerat accusamus modi itaque repellendus odio consectetur qui voluptas id mollitia. Ipsum omnis sed repudiandae eum optio!',
		recipients: [
			{
				id: 5,
				type: 1
			},
			{
				id: 26,
				type: 2
			},
			{
				id: 47,
				type: 3
			}
		],
		date: {
			days: [1, 7],
			frequency: 'weekly'
		},
		time: '10:00',
		type_of_mailing: ['jobtron', 'bitrix'],
		created_at: '2023-04-25T04:26:22.302Z',
		updated_at: '2023-04-25T04:26:22.302Z',
		// deleted_at: '2023-04-25T04:26:22.302Z',
		created_by: {
			id: 5,
			name: 'Василий',
			last_name: 'Пупкин'
		}
	}]
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
