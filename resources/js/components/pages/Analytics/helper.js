import {
	getRandomPerson,
	getRandomInt,
} from '@/composables/random'

export const tableFields = [
	{
		key: 'switch',
		label: ''
	},
	{
		key: 'title',
		label: 'Реферер'
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
		label: 'Абсолютно $'
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
		key: 'monthPayd',
		label: 'Выплачено'
	},
]

export const subTableFields = [
	{
		key: 'title',
		label: 'Реферал'
	},
	{
		key: 'status',
		label: 'Статус'
	}
]

for(let i = 0; i < 15; ++i){
	subTableFields.push({
		key: i + 1,
		label: `${i + 1}`
	})
}
subTableFields.push({
	key: 'attest',
	label: 'Сдал аттестацию'
})
subTableFields.push({
	key: 'firstWeek',
	label: 'Отработал 1ю неделю'
})
subTableFields.push({
	key: 'secondWeek',
	label: 'вторую'
})
subTableFields.push({
	key: 'thirdWeek',
	label: 'третью'
})
subTableFields.push({
	key: 'fourthWeek',
	label: 'четвертую'
})
subTableFields.push({
	key: 'sixthWeek',
	label: '6ю'
})
subTableFields.push({
	key: 'eighthWeek',
	label: '8ю'
})
subTableFields.push({
	key: 'twelfthWeek',
	label: '12ю'
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

const maxDepth = 3

export function getFakeReferal(depth = 0){
	const person = getRandomPerson()
	const referal = {
		id: ++fakeId,
		...person,
		title: `${person.name} ${person.lastName}`,
		status: referalStatus[getRandomInt(0, referalStatus.length)],
		attest: Math.random() < 0.5 ? 5000 : 0,
		firstWeek: Math.random() < 0.5 ? 10000 : 0,
		secondWeek: Math.random() < 0.5 ? 5000 : 0,
		thirdWeek: Math.random() < 0.5 ? 5000 : 0,
		fourthWeek: Math.random() < 0.5 ? 5000 : 0,
		sixthWeek: Math.random() < 0.5 ? 5000 : 0,
		eighthWeek: Math.random() < 0.5 ? 5000 : 0,
		twelfthWeek: Math.random() < 0.5 ? 5000 : 0,
		users: [],
	}
	for(let i = 0; i < 15; ++i){
		referal[i+1] = Math.random() < 0.5 ? 1000 : 0
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
		status: status[getRandomInt(0, status.length)],
		leads: getRandomInt(0, 20),
		deals: getRandomInt(0, 20),
		leadsToDealPercent: getRandomInt(0, 100),
		accepted: getRandomInt(0, 20),
		dealToUserPercent: getRandomInt(0, 100),
		total: getRandomInt(0, 100) * 1000,
		month: getRandomInt(0, 30) * 1000,
		monthRef: getRandomInt(0, 20) * 1000,
		monthPayd: getRandomInt(0, 20) * 1000,
		users: [],
	}
	for(let i = 0, l = getRandomInt(1, 5); i < l; ++i){
		referer.users.push(getFakeReferal())
	}
	return referer
}
