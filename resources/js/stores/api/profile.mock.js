/* eslint-disable camelcase */

import fetchProfilePersonalInfoData from './mock/profile/fetchProfilePersonalInfo.json'
import fetchProfileBalanceData from './mock/profile/fetchProfileBalance.json'
import fetchProfileKpiData from './mock/profile/fetchProfileKpi.json'
import fetchProfileBonusesData from './mock/profile/fetchProfileBonuses.json'
import fetchProfileAwards_nomination from './mock/profile/fetchProfileAwards_nomination.json'
import fetchProfileAwards_certificate from './mock/profile/fetchProfileAwards_certificate.json'
import fetchProfileAwards_accrual from './mock/profile/fetchProfileAwards_accrual.json'

const awards = {
	nomination: fetchProfileAwards_nomination,
	certificate: fetchProfileAwards_certificate,
	accrual: fetchProfileAwards_accrual,
}

export async function fetchProfileStatus(){
	return {
		status: 'stopped',
		balance: {
			currency: 'KZT'
		},
		corp_book: null
	}
}

export async function updateProfileStatus(/* body, options */){
	return {}
}

export async function fetchProfilePersonalInfo(){
	return fetchProfilePersonalInfoData
}

export async function fetchProfileSalary(/* year, month */){
	return {}
}

export async function fetchProfilePaymentTerms(){
	return {
		groups: [],
		position: null
	}
}

export async function fetchProfileTraineeReport(){
	return []
}

export async function fetchProfileActivities(){
	return {
		items: []
	}
}

export async function fetchProfileBalance(/* year, month */){
	return fetchProfileBalanceData
}

export async function fetchProfileKpi(/* year, month */){
	return fetchProfileKpiData
}

export async function fetchProfilePremiums(/* year, month */){
	return {
		data: [[], [], []],
		read: true,
	}
}

export async function fetchProfileBonuses(/* year, month */){
	return fetchProfileBonusesData
}

export async function fetchPosibleBonuses(){
	return []
}

export async function fetchProfileAwards(type){
	return awards[type]
}

export async function setReadedKPI(){
	return {status: 'success'}
}
export async function setReadedBonus(){
	return {status: 'success'}
}
export async function setReadedPremium(){
	return {status: 'success'}
}
export async function setReadedAward(){
	return {status: 'success'}
}

export async function fetchAvailableBonuses(){
	return {
		bonuses: [],
		read: true
	}
}
