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
	}
]

for(let i = 0; i < 15; ++i){
	subTableFields.push({
		key: `${i + 1}`,
		label: `${i + 1}`,
		thClass: 'RefStatsReferalsTable-referalValue',
		rowspan: 2,
		days: true,
	})
}
subTableFields.push({
	key: 'attest',
	label: 'Сдал аттестацию',
	thClass: 'RefStatsReferalsTable-attest',
	rowspan: 2,
})
subTableFields.push({
	key: 'firstWeek',
	label: '1',
	thClass: 'RefStatsReferalsTable-week',
})
subTableFields.push({
	key: 'secondWeek',
	label: '2',
	thClass: 'RefStatsReferalsTable-week',
})
subTableFields.push({
	key: 'thirdWeek',
	label: '3',
	thClass: 'RefStatsReferalsTable-week',
})
subTableFields.push({
	key: 'fourthWeek',
	label: '4',
	thClass: 'RefStatsReferalsTable-week',
})
subTableFields.push({
	key: 'sixthWeek',
	label: '6',
	thClass: 'RefStatsReferalsTable-week',
})
subTableFields.push({
	key: 'eighthWeek',
	label: '8',
	thClass: 'RefStatsReferalsTable-week',
})
subTableFields.push({
	key: 'twelfthWeek',
	label: '12',
	thClass: 'RefStatsReferalsTable-week',
})

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
	}
]

let fakeId = 0;

export const status = [
	'Promoter',
	'Activist',
	'Ambassador',
]

export const referalStatus = [
	'Стажер',
	'Работающий',
	'Уволен',
]

export const comments = [
	'',
	'test comment'
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
	for(let i = 0; i < 15; ++i){
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
