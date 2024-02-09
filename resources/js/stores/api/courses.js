/** @module stores/api/courses */
import axios from 'axios'

/**
 * Получение доступных пользователю курсов
 * @param {boolean} progress - добавлить в ответ прогресс (дергаются разные api)
 * @return {ApiResponse.ProfileCoursesResponse}
 */
export async function fetchProfileCourses(progress){
	if(progress){
		const { data } = await axios.get('/my-courses/get')
		return data.courses
	}
	// must be get
	const { data } = await axios.post('/profile/courses')
	return data
}

/**
 * Получение данных по курсу
 * @param {number} id -
 * @return {ApiResponse.GetMyCourseResponse}
 */
export async function fetchMyCourseInfo(id){
	const { data } = await axios.get(`/my-courses/get/${id}`)
	return data
}

/**
 * @typedef ProfileCoursesResponse
 * @type {ProfileCourse[]}
 * @memberof ApiResponse
 */

/**
 * @typedef ProfileCourse
 * @type {object}
 * @property {number} id
 * @property {number} user_id
 * @property {string} name - название курса
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} deleted_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {string} img - изображение курса
 * @property {string} text - описание курса
 * @property {number} order
 * @property {number} stages
 * @property {number} points
 * @property {number} award_id
 * @property {ProfileCourseResult} [course_results]
 */

/**
 * @typedef ProfileCourseResult
 * @type {object}
 * @property {number} id
 * @property {number} user_id
 * @property {number} course_id
 * @property {number} status
 * @property {number} progress - неправильный прогресс (96 при полном прохождении)
 * @property {number} points
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} started_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} ended_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {Object.<string, number>} weekly_progress - key: (YYYY-MM-DD)
 * @property {number} is_regressed
*/

/**
 * @typedef GetMyCourseResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {GetMyCourseInfo} course
 * @property {Array<GetMyCourseItem>} items
 * @property {number} all_stages
 * @property {number} completed_stages
 */

/**
 * @typedef GetMyCourseInfo
 * @type {object}
 * @property {number} id
 * @property {string} name
 * @property {number} user_id
 * @property {?string} created_at
 * @property {?string} updated_at
 * @property {?string} deleted_at
 * @property {string} img
 * @property {string} text
 * @property {number} order
 * @property {number} stages
 * @property {number} points
 * @property {number} award_id
 * @property {number} is_active
 * @property {Array<GetMyCourseItem>} items
 */

/**
 * @typedef GetMyCourseItem
 * @type {object}
 * @property {number} id
 * @property {number} course_id
 * @property {number} item_id
 * @property {string} item_model
 * @property {number} order
 * @property {?string} created_at
 * @property {?string} updated_at
 * @property {?string} deleted_at
 * @property {string} title
 * @property {number} items
 * @property {?number} last_item
 * @property {number} all_stages
 * @property {number} completed_stages
 * @property {number} status
 */


export async function fetchV2Courses(params){
	// paer_page
	// page
	const { data } = await axios.get('/v2/courses/index', { params })
	return data
}
export async function fetchV2Course(id){
	const { data } = await axios.get(`/v2/courses/get/${id}`)
	return data
}
export async function createV2Course(courseFormData){
	const { data } = await axios.post('/v2/courses/create', courseFormData)
	return data
}
export async function updateV2Course(id, courseFormData){
	const { data } = await axios.post(`/v2/courses/update/${id}`, courseFormData)
	return data
}
export async function deleteV2Course(id){
	const { data } = await axios.delete(`/v2/courses/delete/${id}`)
	return data
}
