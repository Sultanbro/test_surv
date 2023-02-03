/** @module stores/api/hr */
import axios from 'axios'

/**
 * HR Analytics Recruitment Statistics
 * @param {ApiRequest.HRRecruitmentRequest} params
 * @return {ApiResponse.HRRecruitmentResponse}
 */
export async function fetchHRRecruitment(params){
	const { data } = await axios.get('/timetracking/getanalytics/recruitment-statictics', { params })
	return data
}

/**
 * HR Analytics Indicators/Synoptics
 * @param {ApiRequest.HRIndicatorsRequest} params
 * @return {ApiResponse.HRIndicatorsResponse}
 */
export async function fetchHRIndicators(params){
	const { data } = await axios.get('/timetracking/getanalytics/synoptics', { params })
	return data
}

/**
 * HR Analytics Trainees
 * @param {ApiRequest.HRTraineesRequest} params
 * @return {ApiResponse.HRTraineesResponse}
 */
export async function fetchHRTrainees(params){
	const { data } = await axios.get('/timetracking/getanalytics/trainees', { params })
	return data
}

/**
 * HR Analytics Internship Stage II
 * @param {ApiRequest.HRInternshipRequest} params
 * @return {ApiResponse.HRInternshipResponse}
 */
export async function fetchHRInternship(params){
	const { data } = await axios.get('/timetracking/getanalytics/internship-second-stage', { params })
	return data
}

/**
 * HR Analytics Funnels
 * @param {ApiRequest.HRFunnelsRequest} params
 * @return {ApiResponse.HRFunnelsResponse}
 */
export async function fetchHRFunnels(params){
	const { data } = await axios.get('/timetracking/getanalytics/funnel', { params })
	return data
}

/**
 * HR Analytics Dismiss Statistics
 * @param {ApiRequest.HRDismissRequest} params
 * @return {ApiResponse.HRDismissResponse}
 */
export async function fetchHRDismiss(params){
	const { data } = await axios.get('/timetracking/getanalytics/dismiss', { params })
	return data
}

/**
 * @typedef HRRecruitmentRequest
 * @type {object}
 * @property {number} day - 1-31
 * @property {number} month - 1-12
 * @property {number} year
 * @memberof ApiRequest
 */

/**
 * @typedef HRRecruitmentResponse
 * @type {object}
 * @property {?HRRecruiterStats[]} recruiter_stats
 * @property {?HRRecruiterRates} recruiter_stats_rates
 * @property {?HRRecruiterLead[]} recruiter_stats_leads
 * @property {?string} message
 * @property {?string} error
 * @memberof ApiResponse
 */

/**
 * @typedef HRRecruiterStats
 * @type {object}
 * @property {string} name
 * @property {string} agrees
 * @property {number} _agrees
 * @property {boolean} show
 * @property {number} profile
 * @property {number} user_id
 * @property {string} [0-31] - дни
 */

/**
 * @typedef HRRecruiterRates
 * @type {object}
 * @property {number} [1-31] - дни
 */

/**
 * @typedef HRRecruiterLead
 * @type {object}
 * @property {string} name -
 * @property {number} user_id -
 * @property {number} count -
 */

/**
 * @typedef HRIndicatorsRequest
 * @type {object}
 * @property {number} month - 1-12
 * @property {number} year
 * @memberof ApiRequest
 */

/**
 * @typedef HRIndicatorsResponse
 * @type {object}
 * @property {string} date - YYYY-MM-DD
 * @property {HRIndicators} indicators
 * @property {HRRecord[]} records
 * @property {Array<undefined>} hrs - ????
 * @memberof ApiResponse
 */

/**
 * @typedef HRIndicators
 * @type {object}
 * @property {HRIndicatorsInfo} info
 * @property {Array<undefined>} recruiters - ????
 * @property {HRIndicatorsOrder[]} orders
 * @property {string} today
 * @property {number} month
 */

/**
 * @typedef HRIndicatorsInfo
 * @type {object}
 * @property {number} trainees
 * @property {number} training
 * @property {number} applied
 * @property {number} remain_apply
 * @property {number} created
 * @property {number} processed
 * @property {number} converted
 * @property {number} fired
 * @property {number} applied_plan
 * @property {number} remain_days
 * @property {number} working
 */

/**
 * @typedef HRIndicatorsOrder
 * @type {object}
 * @property {string} group
 * @property {number} required
 * @property {string} fact
 */

/**
 * @typedef HRRecord
 * @type {object}
 * @property {string} headers
 * @property {?number} fact
 * @property {?number} plan
 * @property {?number} conversion
 */

/**
 * @typedef HRTraineesRequest
 * @type {object}
 * @property {?number} page - min 1
 * @property {?number} limit - 1-200, default: 40
 * @property {number} month - 1-12
 * @property {number} year
 * @memberof ApiRequest
 */

/**
 * @typedef HRTraineesResponse
 * @type {object}
 * @property {HRTraineesSkypes} skypes
 * @property {Object.<string, string>} invite_groups - key: id группы, value: название группы
 * @property {Object.<string, string>} segments - key: index|id??, value: название
 * @property {string} sgroups
 * @memberof ApiResponse
 */

/**
 * @typedef HRTraineesSkypes
 * @type {object}
 * @property {number} current_page
 * @property {HRTraineesSkypesData[]} data
 * @property {?string} first_page_url
 * @property {number} from
 * @property {number} last_page
 * @property {?string} last_page_url
 * @property {HRTraineesSkypesLink[]} links
 * @property {?string} next_page_url
 * @property {string} path
 * @property {number} per_page
 * @property {?string} prev_page_url
 * @property {number} to
 * @property {number} total
 */

/**
 * @typedef HRTraineesSkypesData
 * @type {object}
 * @property {number} id
 * @property {number} lead_id
 * @property {number} deal_id
 * @property {number} user_id
 * @property {string} resp_id
 * @property {number} segment
 * @property {?string} phone
 * @property {?string} phone_2
 * @property {?string} phone_3
 * @property {?string} skype
 * @property {string} project
 * @property {string} status
 * @property {string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} invite_at - (YYYY-MM-DD HH:MM)
 * @property {?string} skyped_old - (YYYY-MM-DD HH:MM:SS)
 * @property {?string} day_second - (YYYY-MM-DD HH:MM:SS)
 * @property {string} name
 * @property {?string} lang
 * @property {?string} house
 * @property {?string} net
 * @property {string} files - json Array<???>
 * @property {string} hash
 * @property {number} signed
 * @property {?string} time - (YYYY-MM-DD HH:MM:SS)
 * @property {?string} city
 * @property {?string} age
 * @property {?string} email
 * @property {?string} wishtime
 * @property {number} received_assessment
 * @property {number} invited
 * @property {?string} skyped - (YYYY-MM-DD HH:MM)
 * @property {?string} inhouse - (YYYY-MM-DD HH:MM:SS)
 * @property {number} invite_group_id
 * @property {?undefined} rating
 * @property {?string} rating_date - (????)
 * @property {?undefined} rating2
 * @property {?string} rating2_date - (????)
 * @property {number} received_fd
 * @property {string} user_type
 * @property {string} file
 * @property {string} invite_group
 * @property {number} os
 * @property {?string} country
 * @property {boolean} checked
 * @property {string} resp
 */

/**
 * @typedef HRTraineesSkypesLink
 * @type {object}
 * @property {?string} url
 * @property {?string} label
 * @property {boolean} active
 */

/**
 * @typedef HRTraineesGroup
 * @type {object}
 * @property {number} id
 * @property {string} name
 * @property {number} active
 * @property {string} users - json Array<number> - id пользователей
 * @property {?string} created_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} updated_at - (YYYY-MM-DDTHH:MM:SSZ)
 * @property {?string} work_start - (HH:MM:SS)
 * @property {?string} work_end - (HH:MM:SS)
 * @property {number} workdays
 * @property {?string} editors_id - json Array<number> - id пользователей
 * @property {?string} book_groups - json Array<???>
 * @property {number} required
 * @property {number} provided
 * @property {?string} head_id - json Array<number> - id пользователей
 * @property {?string} zoom_link
 * @property {?bp_link} bp_link
 * @property {?string} corp_books - json Array<???>
 * @property {?status} checktime - (YYYY-MM-DD HH:MM:SS)
 * @property {Array<undefined>} checktime_users
 * @property {?string} payment_terms
 * @property {number} salary_approved
 * @property {number} salary_approved_by
 * @property {?string} salary_approved_date - (YYYY-MM-DD HH:MM:SS)
 * @property {number} has_analytics
 * @property {?string} quality
 * @property {number} editable_time
 * @property {number} time_address
 * @property {Array<number>} time_exceptions
 * @property {number} paid_internship
 * @property {number} rentability_max
 * @property {number} show_payment_terms
 * @property {?string} archived_date - (YYYY-MM-DD)
 */

/**
 * @typedef HRInternshipRequest
 * @type {object}
 * @property {number} month - 1-12
 * @property {number} year
 * @memberof ApiRequest
 */

/**
 * @typedef HRInternshipResponse
 * @type {object}
 * @property {HRInternshipOcenka[]} ocenka_svod
 * @property {HRInternshipAbsent[]} absents_first
 * @property {HRInternshipAbsent[]} absents_second
 * @property {HRInternshipAbsent[]} absents_third
 * @property {Array<undefined>} trainee_report
 * @memberof ApiResponse
 */

/**
 * @typedef HRInternshipOcenka
 * @type {object}
 * @property {string} name
 * @property {number} sent
 * @property {number} working
 * @property {string} percent
 * @property {number} active
 * @property {number} required
 */

/**
 * @typedef HRInternshipAbsent
 * @type {object}
 * @property {string} cause
 * @property {number} count
 */

/**
 * @typedef HRFunnelsRequest
 * @type {object}
 * @property {number} month - 1-12
 * @property {number} year
 * @memberof ApiRequest
 */

/**
 * @typedef HRFunnelsResponse
 * @type {object}
 * @property {HRFunnels} funnels
 * @memberof ApiResponse
 */

/**
 * @typedef HRFunnels
 * @type {object}
 * @property {object} all
 * @property {HRFunnel[]} all.all
 * @property {HRFunnel[]} all.hh
 * @property {HRFunnel[]} all.insta
 * @property {Object<number, HRFunnelMonth>} month
 */

/**
 * @typedef HRFunnel
 * @description Числовые ключи могут быть отрицательными
 * @type {Object<number, number>}
 * @property {string} name
 * @property {string} totals
 * @property {string} show
 */

/**
 * @typedef HRFunnelMonth
 * @type {object}
 * @property {HRFunnel[]} hh
 * @property {HRFunnel[]} insta
 */

/**
 * @typedef HRDismissRequest
 * @type {object}
 * @property {number} month - 1-12
 * @property {number} year
 * @memberof ApiRequest
 */

/**
 * @typedef HRDismissResponse
 * @type {object}
 * @property {HRDismissCause[]} causes
 * @property {HRDismissQuiz[]} quiz
 * @property {HRDismissStaff[]} staff
 * @property {HRDismissStaff[]} staff_by_group
 * @property {HRDismissStaff[]} staff_longevity
 * @memberof ApiResponse
 */

/**
 * @typedef HRDismissStaff
 * @type {object}
 * @property {string} name
 * @property {number|string} m1
 * @property {number|string} m2
 * @property {number|string} m3
 * @property {number|string} m4
 * @property {number|string} m5
 * @property {number|string} m6
 * @property {number|string} m7
 * @property {number|string} m8
 * @property {number|string} m9
 * @property {number|string} m10
 * @property {number|string} m11
 * @property {number|string} m12
 */

/**
 * @typedef HRDismissQuiz
 * @type {object}
 * @property {string} q
 * @property {HRDismissAnswer[]} answers
 * @property {string} type
 * @property {number} order
 */

/**
 * @typedef HRDismissAnswer
 * @type {object}
 * @property {string} text
 * @property {?number} count
 * @property {?string} percent
 */

/**
 * @typedef HRDismissCause
 * @type {object}
 * @property {string} cause
 * @property {number} count
 */