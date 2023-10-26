import {
	getRandomPerson,
	getRandomInt,
	getRandomArrayItem,
} from '@/composables/random'

export const tableFields = [
	{
		key: 'title',
		label: 'Реферер',
		tdClass: 'text-left RefStats-title',
		thClass: 'text-left RefStats-title',
	},
	{
		key: 'status',
		label: 'Статус'
	},
	{
		key: 'leads',
		label: 'Лидов'
	},
	{
		key: 'deals',
		label: 'Сделок'
	},
	{
		key: 'leadsToDealPercent',
		label: 'CV лид/сделка'
	},
	{
		key: 'accepted',
		label: 'Принято'
	},
	{
		key: 'dealToUserPercent',
		label: 'CV сделка/принят'
	},
	{
		key: 'total',
		label: 'Абсолютно ₸'
	},
	{
		key: 'month',
		label: 'За месяц'
	},
	{
		key: 'monthRef',
		label: 'От рефералов'
	},
	{
		key: 'monthPaid',
		label: 'Выплачено'
	},
]

export const subTableFields = [
	{
		key: 'title',
		label: 'Реферал',
		tdClass: 'text-left RefStatsReferals-title',
		thClass: 'text-left RefStatsReferals-title',
		rowspan: 2,
	},
	{
		key: 'status',
		label: 'Статус',
		rowspan: 2,
	}
]

for(let i = 0; i < 15; ++i){
	subTableFields.push({
		key: `${i + 1}`,
		label: `${i + 1}`,
		thClass: 'RefStatsReferals-referalValue',
		rowspan: 2,
	})
}
subTableFields.push({
	key: 'attest',
	label: 'Сдал аттестацию',
	rowspan: 2,
})
subTableFields.push({
	key: 'firstWeek',
	label: '1'
})
subTableFields.push({
	key: 'secondWeek',
	label: '2'
})
subTableFields.push({
	key: 'thirdWeek',
	label: '3'
})
subTableFields.push({
	key: 'fourthWeek',
	label: '4'
})
subTableFields.push({
	key: 'sixthWeek',
	label: '6'
})
subTableFields.push({
	key: 'eighthWeek',
	label: '8'
})
subTableFields.push({
	key: 'twelfthWeek',
	label: '12'
})

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
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		firstWeek: {
			value: Math.random() < 0.5 ? 10000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		secondWeek: {
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		thirdWeek: {
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		fourthWeek: {
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		sixthWeek: {
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		eighthWeek: {
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		twelfthWeek: {
			value: Math.random() < 0.5 ? 5000 : 0,
			paid: Math.random() < 0.5,
			comment: getRandomArrayItem(comments),
		},
		users: [],
	}
	for(let i = 0; i < 15; ++i){
		referal[i+1] = {
			value: Math.random() < 0.5 ? 1000 : 0,
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
		users: [],
	}
	for(let i = 0, l = getRandomInt(1, 5); i < l; ++i){
		referer.users.push(getFakeReferal())
	}
	return referer
}
