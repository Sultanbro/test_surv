/** @module stores/api/settings */
import axios from 'axios'

/**
 * Получение настроек
 * @param {string} type - 'company' | 'book' | 'kb' | 'video'
 * @return {ApiResponse.GetSettingsResponse}
 */
export async function fetchSettings(type){
	// must be get
	const { data } = await axios.post('/settings/get', { type })
	return data
}

/**
 * Сохраннение настроек
 * @param {ApiRequest.UpdateSettingsRequest} body -
 * @param {object} options - Axios Request Config
 * @return {ApiResponse.UpdateSettingsResponse}
 */
export async function updateSettings(body, options){
	const { data } = await axios.post('/settings/save', body, options)
	return data
}

/**
 * @typedef GetSettingsResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {GetSettingsSettings} settings
 */

/**
 * @typedef GetSettingsSettings
 * @type {object}
 * @property {string} [logo] - ссылка на логотип
 */

/**
 * @typedef UpdateSettingsResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {string} [logo] - ссылка на логотип
 */

/**
 * @typedef UpdateSettingsRequest
 * @type {object}
 * @memberof ApiRequest
 * @property {string} type - 'company' | 'book' | 'kb' | 'video'
 * @property {string} [file] - blob
 */

