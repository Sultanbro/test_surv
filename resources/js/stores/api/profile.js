/** @module stores/api/profile */
/* global Laravel */
import axios from 'axios'

/**
 * Получение статуса работает/нет баланс и тд
 * @return {ApiResponse.ProfileStatusResponse}
 */
export async function fetchProfileStatus(){
	// must be get
	const { data } = await axios.post('/timetracking/status', {})
	return data
}

/**
 * Обновление статуса
 * @param {ApiRequest.ProfileStatusRequest} body -
 * @param {object} options - Axios Request Config
 * @return {ApiResponse.ProfileStatusResponse}
 */
export async function updateProfileStatus(body, options){
	const { data } = await axios.post('/timetracking/starttracking', body, options)
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
 * Получени информации по продвижению по карьере
 * @returns {ApiResponse.GetProfilePaymentTermsResponse}
 */
export async function fetchProfilePaymentTerms(){
	// must be get
	const { data } = await axios.post('/profile/payment-terms')
	return data
}

/**
 * Получени информации по стажировке
 * @returns {undefined}
 */
export async function fetchProfileTraineeReport(){
	// must be get
	const { data } = await axios.post('/profile/trainee-report')
	return data
}

/**
 * Получение сравнения показателей
 * @returns {ApiResponse.GetProfileActivitiesResponse}
 */
export async function fetchProfileActivities(){
	// must be get
	const { data } = await axios.post('/profile/activities')
	return data
}

/**
 * Получение баланса за месяц
 */
export async function fetchProfileBalance(year, month){
	const { data } = await axios.post('/timetracking/zarplata-table-new', {
		month: month + 1,
		year,
	})
	return data
}

/**
 * Получение kpi за месяц
 */
export async function fetchProfileKpi(year, month){
	const { data } = await axios.post('/statistics/kpi', {
		filters: {
			data_from: {
				month: month + 1,
				year,
			},
			user_id: Laravel.userId
		}
	})
	return data
}

/**
 * Получение премий за месяц
 */
export async function fetchProfilePremiums(year, month){
	const { data } = await axios.post('/statistics/quartal-premiums', {
		filters: {
			data_from: {
				month: month + 1,
				year,
			},
			user_id: Laravel.userId
		}
	})
	return data
}

/**
 * Получение бонусов за месяц
 */
export async function fetchProfileBonuses(year, month){
	const { data } = await axios.post('/bonuses', {
		month: month + 1,
		year,
	})
	return data
}

/**
 * Получение возможных бонусов
 */
export async function fetchPosibleBonuses(){
	const { data } = await axios.post('/bonus/user')
	return data.bonuses
}

/**
 * Получение наград текущего пользователя
 * @param {string} type - nomination | certificate | accrual
 */
export async function fetchProfileAwards(type){
	const { data } = await axios.get(`/awards/type?key=${type}`)
	return data.data
}

export async function setReadedKPI(){
	const { data } = await axios.put('/statistics/kpi/read')
	return data
}
export async function setReadedBonus(){
	const { data } = await axios.put('/bonus/read')
	return data
}
export async function setReadedPremium(){
	const { data } = await axios.put('/statistics/quartal-premiums/read')
	return data
}
export async function setReadedAward(){
	const { data } = await axios.get('/awards/read')
	return data
}

export async function fetchAvailableBonuses(){
	const { data } = await axios.post('/bonus/user')
	return data
}

/**
 * @typedef ProfilePersonalInfoResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {ProfilePersonalInfoUser} user
 * @property {ProfilePersonalInfoPosition} position
 * @property {string} groups - html с названиями отделов
 * @property {string} salary - форматированная сумма без валюты
 * @property {string} workingDay - (пример: '5-2')
 * @property {string} workingTime - (пример: '8 часов')
 * @property {string} schedule - (пример: '09:00 - 19:00')
 * @property {string} currency - валюта upperCase (пример: KZT)
 * @property {object} currencies - currency: string (название валюты) - (пример: {KZT: 'Казахстанский тенге', RUB: 'Российский рубль'})
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
 * @typedef ProfileSalaryResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {ProfileSalaryEarnings} user_earnings
 * @property {boolean} has_qp
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
 * @memberof ApiResponse
 * @property {string} status - 'stopped' | 'started'
 * @property {Array<undefined>} [groupsall]
 * @property {Array<undefined>} [orders]
 * @property {string} [zarplata] - форматированная сумма с валютой
 * @property {string} [bonus] - форматированная сумма с валютой
 * @property {string} [total_earned] - форматированная сумма с валютой
 * @property {ProfileStatusBalance} balance -
 * @property {?undefined} corp_book -
 */

/**
 * @typedef ProfileStatusRequest
 * @type {object}
 * @memberof ApiRequest
 * @property {string} [start] - (HH:MM:SS)
 * @property {string} [stop] - (HH:MM:SS)
 */

/**
 * @typedef ProfileStatusBalance
 * @type {object}
 * @property {string} sum - форматированная сумма
 * @property {string} currency - валюта
 */

/**
 * @typedef GetProfilePaymentTermsResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {GetProfilePaymentTermsGroup[]} groups - информания по отделам
 * @property {?GetProfilePaymentTermsPosition} position - информация по должности
 */

/**
 * @typedef GetProfilePaymentTermsGroup
 * @type {object}
 * @property {string} title - заголовок
 * @property {string} text - текст с \n\n лайнбрейками
 */

/**
 * @typedef GetProfilePaymentTermsPosition
 * @type {object}
 * @property {number} id -
 * @property {number} position_id -
 * @property {string} require - текст с \n\n лайнбрейками
 * @property {string} actions - текст с \n\n лайнбрейками
 * @property {string} time - текст с \n\n лайнбрейками
 * @property {string} salary - текст с \n\n лайнбрейками
 * @property {string} knowledge - текст с \n\n лайнбрейками
 * @property {string} next_step - текст с \n\n лайнбрейками
 * @property {number} show -
 */

/**
 * @typedef GetProfileActivitiesResponse
 * @type {object}
 * @memberof ApiResponse
 * @property {GetProfileActivitiesItem[]} items
 */

/**
 * @typedef GetProfileActivitiesItem
 * @type {object}
 * @property {GetProfileActivitiesGroup} group
 * @property {Array<GetProfileActivitiesActivity>} activities
 */

/**
 * @typedef GetProfileActivitiesGroup
 * @type {object}
 * @property {number} id
 * @property {string} name
 */

/**
 * @typedef GetProfileActivitiesActivity
 * @type {object}
 * @property {number} id
 * @property {string} name
 * @property {number} editable
 * @property {string} daily_plan - number
 * @property {number} order
 * @property {number} group_id
 * @property {string} plan_unit
 * @property {number} workdays
 * @property {number} weekdays
 * @property {string} type - 'default' | 'quality' | 'collection'
 * @property {number} view
 * @property {string} unit
 * @property {number} table
 * @property {number} price
 * @property {Array<(GetProfileActivitiesRecord | GetProfileActivitiesRecordQuality | GetProfileActivitiesRecordCollection)>} records
 */

/**
 * @typedef GetProfileActivitiesRecord
 * @type {object}
 * @property {string} name
 * @property {string} lastname
 * @property {string} fullname
 * @property {string} email
 * @property {number} full_time
 * @property {number} id
 * @property {number} fired
 * @property {boolean} is_trainee
 * @property {number} applied_from
 * @property {string} group
 * @property {number} plan
 * @property {number} ['1-31']
 */

/**
 * @typedef GetProfileActivitiesRecordQuality
 * @type {object}
 * @property {string} name
 * @property {number} [total]
 * @property {number} [avg]
 * @property {number} [avg1]
 * @property {number} [avg2]
 * @property {number} [avg3]
 * @property {number} [avg4]
 * @property {number} [avg5]
 * @property {number} [avg6]
 * @property {number} ['1-31']
 */

/**
 * @typedef GetProfileActivitiesRecordCollection
 * @type {object}
 * @property {string} name
 * @property {string} lastname
 * @property {string} fullname
 * @property {string} email
 * @property {number} full_time
 * @property {number} id
 * @property {number} fired
 * @property {boolean} is_trainee
 * @property {number} applied_from
 * @property {string} group
 * @property {number} plan
 * @property {undefined} ['1-31']
 */
