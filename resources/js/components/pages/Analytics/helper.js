import {
	getRandomPerson,
	getRandomInt,
	getRandomArrayItem,
} from '@/composables/random'

export const tableFields = [
	{
		key: 'title',
		label: 'Реферер',
		tdClass: 'text-left RefStatsTable-title',
		thClass: 'text-left RefStatsTable-title',
	},
	{
		key: 'status',
		label: 'Статус',
		thClass: 'RefStatsTable-status',
		tdClass: 'RefStatsTable-status',
	},
	{
		key: 'leads',
		label: 'Лидов',
		thClass: 'RefStatsTable-leads',
	},
	{
		key: 'deals',
		label: 'Сделок',
		thClass: 'RefStatsTable-deals',
	},
	{
		key: 'leadsToDealPercent',
		label: 'CV лид/сделка',
		thClass: 'RefStatsTable-cv1',
	},
	{
		key: 'accepted',
		label: 'Принято',
		thClass: 'RefStatsTable-accepted',
	},
	{
		key: 'dealToUserPercent',
		label: 'CV сделка/принят',
		thClass: 'RefStatsTable-cv2',
	},
	{
		key: 'total',
		label: 'Абсолютно ₸',
		thClass: 'RefStatsTable-total',
	},
	{
		key: 'month',
		label: 'За месяц',
		thClass: 'RefStatsTable-month',
	},
	{
		key: 'monthRef',
		label: 'От рефералов',
		thClass: 'RefStatsTable-monthRef',
	},
	{
		key: 'monthPaid',
		label: 'Выплачено',
		thClass: 'RefStatsTable-paid',
	},
]

export const tableFieldsProfile = [
	{
		key: 'title',
		label: 'Реферер',
		tdClass: 'text-left RefStatsTable-title',
		thClass: 'text-left RefStatsTable-title',
	},
	{
		key: 'status',
		label: 'Статус',
		thClass: 'RefStatsTable-status',
		tdClass: 'RefStatsTable-status',
	},
	{
		key: 'leads',
		label: 'Кандидатов',
		thClass: 'RefStatsTable-profileLeads',
		hint: 'Оставили заявок по вашей реферальной ссылке',
	},
	{
		key: 'deals',
		label: 'На обучении',
		thClass: 'RefStatsTable-profileDeals',
		hint: 'Приступили к обучению из тех кто оставил заявку',
	},
	{
		key: 'leadsToDealPercent',
		label: 'CV кандидат/стажер',
		thClass: 'RefStatsTable-profileCV1',
	},
	{
		key: 'accepted',
		label: 'Принято',
		thClass: 'RefStatsTable-accepted',
	},
	{
		key: 'dealToUserPercent',
		label: 'CV стажер/принят',
		thClass: 'RefStatsTable-cv2',
	},
	{
		key: 'total',
		label: 'Абсолютно ₸',
		thClass: 'RefStatsTable-total',
	},
	{
		key: 'month',
		label: 'За месяц',
		thClass: 'RefStatsTable-month',
	},
	{
		key: 'monthRef',
		label: 'От рефералов',
		thClass: 'RefStatsTable-monthRef',
	},
	{
		key: 'monthPaid',
		label: 'Выплачено',
		thClass: 'RefStatsTable-paid',
	},
]

function getDaysFields(){
	const result = []
	for(let i = 0; i < 31; ++i){
		result.push({
			key: `${i + 1}`,
			label: `${i + 1}`,
			labelDialog: `${i + 1}й день`,
			thClass: 'RefStatsReferalsTable-referalValue',
			rowspan: 2,
			days: true,
		})
	}
	return result
}

export const subTableFields = [
	{
		key: 'title',
		label: 'Реферал',
		tdClass: 'text-left RefStatsReferalsTable-title',
		thClass: 'text-left RefStatsReferalsTable-title',
		rowspan: 2,
	},
	{
		key: 'status',
		label: 'Статус',
		rowspan: 2,
		thClass: 'RefStatsReferalsTable-status',
		tdClass: 'RefStatsReferalsTable-status',
	},
	...getDaysFields(),
	{
		key: 'attest',
		label: 'Сдал аттестацию',
		labelDialog: 'Сдал аттестацию',
		thClass: 'RefStatsReferalsTable-attest',
		tdClass: 'RefStatsReferalsTable-attest',
		rowspan: 2,
	},
	{
		key: 'firstWeek',
		label: '1',
		labelDialog: '1я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'secondWeek',
		label: '2',
		labelDialog: '2я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'thirdWeek',
		label: '3',
		labelDialog: '3я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'fourthWeek',
		label: '4',
		labelDialog: '4я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'sixthWeek',
		label: '6',
		labelDialog: '6я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'eighthWeek',
		label: '8',
		labelDialog: '8я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'twelfthWeek',
		label: '12',
		labelDialog: '12я неделя',
		thClass: 'RefStatsReferalsTable-week',
	},
	{
		key: 'spacer',
		label: '',
		thClass: 'RefStatsReferalsTable-spacer',
		tdClass: 'RefStatsReferalsTable-spacer',
		rowspan: 2,
	},
]

export const secondLayersFields = [
	{
		key: 'title',
		label: 'Реферал',
		tdClass: 'text-left RefStatsReferalsTable-title',
		thClass: 'text-left RefStatsReferalsTable-title',
		rowspan: 2,
	},
	{
		key: 'status',
		label: 'Статус',
		rowspan: 2,
		thClass: 'RefStatsReferalsTable-status',
		tdClass: 'RefStatsReferalsTable-status',
	},
	{
		key: 'firstWeek',
		label: 'Отработал 1 неделю',
		thClass: 'RefStatsReferalsTable-week1',
	},
	{
		key: 'spacer2',
		label: '',
		thClass: 'RefStatsReferalsTable-spacer2',
		tdClass: 'RefStatsReferalsTable-spacer2',
	},
]

let fakeId = 0;

export const status = [
	'promoter',
	'activist',
	'ambassador',
]

export const referalStatus = [
	'Стажер',
	'Работающий',
	'Уволен',
]

export const comments = [
	'',
	'test comment',
	'test comment\nsecond line',
]

const maxDepth = 3

export function getFakeReferal(depth = 0){
	const person = getRandomPerson()
	const referal = {
		id: ++fakeId,
		...person,
		title: `${person.name} ${person.lastName}`,
		status: getRandomArrayItem(referalStatus),
		attest: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		firstWeek: {
			sum: Math.random() < 0.5 ? 10000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		secondWeek: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		thirdWeek: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		fourthWeek: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		sixthWeek: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		eighthWeek: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		twelfthWeek: {
			sum: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		users: [],
	}
	const days = getRandomInt(0, 15)
	for(let i = 0; i < days; ++i){
		referal[i+1] = {
			sum: Math.random() < 0.5 ? 1000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		}
	}
	if(depth < maxDepth){
		for(let i = 0, l = getRandomInt(0, 3); i < l; ++i){
			referal.users.push(getFakeReferal(depth + 1))
		}
	}
	return referal
}

export function getFakeReferer(){
	const person = getRandomPerson()
	const referer = {
		id: ++fakeId,
		...person,
		title: `${person.name} ${person.lastName}`,
		status: getRandomArrayItem(status),
		leads: getRandomInt(0, 20),
		deals: getRandomInt(0, 20),
		leadsToDealPercent: getRandomInt(0, 100),
		accepted: getRandomInt(0, 20),
		dealToUserPercent: getRandomInt(0, 100),
		total: getRandomInt(0, 100) * 1000,
		month: getRandomInt(0, 30) * 1000,
		monthRef: getRandomInt(0, 20) * 1000,
		monthPaid: getRandomInt(0, 20) * 1000,
		avatar: 'https://placekitten.com/200/200',
		users: [],
	}
	for(let i = 0, l = getRandomInt(1, 5); i < l; ++i){
		referer.users.push(getFakeReferal())
	}
	return referer
}
