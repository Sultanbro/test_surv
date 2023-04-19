/** @module stores/api/kpi */
import axios from 'axios'

/**
 * Статистика kpi за год
 * @param {ApiRequest.KPIStatYearRequest} params
 * @return {ApiResponse.KPIStatYearResponse}
 */
export async function fetchKPIStatYear(params){
	const { data } = await axios.get('/statistics/kpi/annual-statistics', { params })
	return data
}

// mock fetchKPIStatYear
// export async function fetchKPIStatYear(){
// 	await new Promise(resolve => resolve())
// 	const data = require('./mock/fetchKPIStatYear.json')
// 	return data
// }

/**
 * @typedef KPIStatYearRequest
 * @type {object}
 * @memberof ApiRequest
 * @property {number} year
 * @property {number} page
 * @property {number} limit
 */

/**
 * @typedef KPIStatYearResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {ApiResponse.Paginator<KPIStatItem>} paginator
 */

/**
 * @typedef KPIStatItem
 * @property {number} id
 * @property {number} targetable_id
 * @property {string} targetable_type
 * @property {number} completed_80
 * @property {number} completed_100
 * @property {number} lower_limit
 * @property {number} upper_limit
 * @property {string} colors
 * @property {string?} deleted_at
 * @property {string?} created_at
 * @property {string?} updated_at
 * @property {number} created_by
 * @property {number} updated_by
 * @property {Array<unknown>} kpi_items
 * @property {Array<KPIStatItemItem>} items
 * @property {Array<KPIStatItemUser>} users
 * @property {number} avg
 * @property {KPIStatItemTarget} target
 * @property {boolean} expanded
 * @property {KPIStatItemHistory} histories_latest
 */

/**
 * @typedef KPIStatItemItem
 */

/**
 * @typedef KPIStatItemUser
 */

/**
 * @typedef KPIStatItemTarget
 */

/**
 * @typedef KPIStatItemHistory
 */
