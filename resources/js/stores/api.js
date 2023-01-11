/** @module stores/api */
import axios from 'axios'


/**
 * Получение статуса работает/нет баланс и тд
 * @return {ProfileStatusResponse}
 */
export async function fetchProfileStatus(){
	// must be get
	const { data } = await axios.post('/timetracking/status', {})
	return data
}


/**
 * Получение информации о пользователе
 * @return {ApiResponse.ProfilePersonalInfoResponse}
 */
export async function fetchProfilePersonalInfo(){
	const { data } = await axios.get('/profile/personal-info')
	return data
}

/**
 * Получение начислений пользователя
 * @param {number} year - год
 * @param {number} month - месяц
 * @return {ApiResponse.ProfileSalaryResponse}
 */
export async function fetchProfileSalary(year, month){
	// must be get
	const { data } = await axios.post('/profile/salary/get', {
		year,
		month,
	})
	return data
}

/**
 * Получение доступных пользователю курсов
 * @param {boolean} progress - добавлить в ответ прогресс (дергаются разные api)
 * @return {string}
 */
export async function fetchProfileCourses(progress){
	// must be get
	if(progress){
		const { data } = await axios.get('/my-courses/get')
		return data.courses
	}
	const { data } = await axios.post('/profile/courses')
	return data
}

/**
 * ApiResponse
 * @namespace ApiResponse
 */

/**
 * @typedef ProfilePersonalInfoResponse
 * @type {object}
 * @property {ProfilePersonalInfoUser} user
 * @property {ProfilePersonalInfoPosition} position
 * @property {string} groups - html с названиями отделов
 * @property {string} salary - форматированная сумма без валюты
 * @property {string} workingDay - (пример: '5-2')
 * @property {string} workingTime - (пример: '8 часов')
 * @property {string} schedule - (пример: '09:00 - 19:00')
 * @property {string} currency - валюта upperCase (пример: KZT)
 * @property {object} currencies - currency: string (название валюты) - (пример: {KZT: 'Казахстанский тенге', RUB: 'Российский рубль'})
 * @memberof ApiResponse
 */

/**
 * @typedef ProfilePersonalInfoUser
 * @type {object}
 * @property {string} id - an ID.
 * @property {string} email - your name.
 * @property {string} remember_token - your age.
 * @property {string} name - your age.
 * @property {string} last_name - your age.
 * @property {number} UF_ADMIN - your age.
 * @property {number} position_id - your age.
 * @property {number} program_id - your age.
 * @property {number} full_time - your age.
 * @property {string} user_type - your age.
 * @property {string} phone - your age.
 * @property {?string} city - your age.
 * @property {?string} address - your age.
 * @property {?string} description - your age.
 * @property {?string} currency - your age.
 * @property {number} timezone - your age.
 * @property {number} segment - your age.
 * @property {number} working_day_id - your age.
 * @property {number} working_time_id - your age.
 * @property {string} work_start - your age.(HH:MM:SS)
 * @property {string} work_end - your age.(HH:MM:SS)
 * @property {string} birthday - (YYYY-MM-DDTHH:MM:SS)
 * @property {?string} last_group - json????
 * @property {?Object} read_corp_book_at -
 * @property {number} has_noti - your age.
 * @property {string} weekdays - "bitmap"(1 == dayoff),
 * @property {number} role_id - your age.
 * @property {number} is_admin - your age.
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} deleted_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} applied_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} notified_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?undefined} groups_all -
 * @property {?string} working_country -
 * @property {?string} working_city - city id (int)
 * @property {?string} img_url -
 * @property {?string} phone_1 -
 * @property {?string} phone_2 -
 * @property {?string} phone_3 -
 * @property {?string} phone_4 -
 * @property {?undefined} headphones_sum -
 * @property {number} active_status -
 * @property {?string} avatar -
 * @property {?Array.<undefined>} groups -
 * @property {?Array.<undefined>} roles -
 * @property {number} show_checklist -
 * @property {ProfilePersonalInfoUserWorkDay} working_day -
 * @property {ProfilePersonalInfoUserWorkTime} working_time -
*/

/**
 * @typedef ProfilePersonalInfoUserWorkDay
 * @type {object}
 * @property {number} id
 * @property {string} name
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 */

/**
 * @typedef ProfilePersonalInfoUserWorkTime
 * @type {object}
 * @property {number} id
 * @property {string} name
 * @property {number} time
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 */

/**
 * @typedef ProfilePersonalInfoPosition
 * @type {object}
 * @property {number} id
 * @property {string} position - название должности
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {string} book_groups - (json): Array<id: int>
 * @property {number} indexation
 * @property {number} sum
 */

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
 * @typedef ProfileSalaryResponse
 * @type {object}
 * @property {ProfileSalaryEarnings} user_earnings
 * @property {boolean} has_qp
 * @memberof ApiResponse
 */

/**
 * @typedef ProfileSalaryEarnings
 * @type {object}
 * @property {string} [0] - ???
 * @property {string} salary - форматированная сумма с валютой
 * @property {string} kpi - форматированная сумма с валютой
 * @property {string} bonus - форматированная сумма с валютой
 * @property {string} quarter_bonus - форматированная сумма с валютой
 * @property {string} currency - валюта
 * @property {number} oklad -
 * @property {number} sumSalary -
 * @property {number} sumKpi -
 * @property {number} sumBonuses -
 * @property {number} sumQuartalPremiums -
 * @property {number} sumNominations -
 * @property {number} salary_percent -
 * @property {number} kpi_percent -
 * @property {number} kpiMax -
 * @property {?undefined} editedBonus -
 * @property {?undefined} editedKpi -
 * @property {ProfileSalaryKPI[]} kpis -
 * @property {?undefined} bonusHistory -
 * @property {?string} potential_bonuses - (html)
 * @property {ProfileSalaryInfo} salary_info -
 */

/**
 * @typedef ProfileSalaryKPI
 * @type {object}
 * @property {number} id
 * @property {string} name
 * @property {string} work_start - (HH:MM:SS)
 * @property {string} work_end - (HH:MM:SS)
 * @property {number} has_analytics - (-1)
 */

/**
 * @typedef ProfileSalaryInfo
 * @type {object}
 * @property {number} worked_days
 * @property {number} indexation_sum
 * @property {number} days_before_indexation
 * @property {string} oklad - форматированная сумма с валютой
 */

/**
 * @typedef ProfileStatusResponse
 * @type {object}
 * @property {string} status - 'stopped' | 'started'
 * @property {Array<undefined>} groupsall
 * @property {Array<undefined>} orders
 * @property {string} zarplata - форматированная сумма с валютой
 * @property {string} bonus - форматированная сумма с валютой
 * @property {string} total_earned - форматированная сумма с валютой
 * @property {ProfileStatusBalance} balance -
 * @property {?undefined} corp_book -
 * @memberof ApiResponse
 */

/**
 * @typedef ProfileStatusBalance
 * @type {object}
 * @property {string} sum - форматированная сумма
 * @property {string} currency - валюта
 */
