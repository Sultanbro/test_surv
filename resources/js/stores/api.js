/** @module stores/api */
export * from './api/courses.js'
export * from './api/profile.js'
export * from './api/settings.js'
export * from './api/hr.js'
export * from './api/pricing.js'
export * from './api/kpi.js'
export * from './api/portal.js'

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
