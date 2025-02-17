/** @module stores/api */
import axios from 'axios'

export * from './api/courses.js'
export * from './api/profile.js'
export * from './api/settings.js'
export * from './api/hr.js'
export * from './api/pricing.js'
export * from './api/kpi.js'
export * from './api/portal.js'
export * from './api/notifications.js'
export * from './api/workChart.js'
export * from './api/company.js'
export * from './api/top.js'
export * from './api/analytics.js'
export * from './api/structure.js'
export * from './api/kb.js'

export async function updateOnlineStatus(){
	await axios.post('/online')
}
export async function checkVersion(){
	const current = window.appVersion
	const {data} = await axios.get('/mix-manifest.json')
	const server = (data['/js/app.js'] || '').split('?id=')[1]
	// eslint-disable-next-line
	window.appVersion = server
	return current && current !== server
}

/**
 * ApiResponse
 * @namespace ApiResponse
 */

/**
 * ApiRequest
 * @namespace ApiRequest
 */

/**
 * @typedef Paginator
 * @memberof ApiResponse
 * @template T
 * @property {T} data
 * @property {Array<PaginatorLink>} links
 * @property {number} current_page
 * @property {number} last_page
 * @property {number} per_page
 * @property {number} total
 */

/**
 * @typedef PaginatorLink
 * @memberof ApiResponse
 * @property {string | null} url
 * @property {string} label
 * @property {boolean} active
 */
