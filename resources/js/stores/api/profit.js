// import axios from 'axios'

const localData = {
	KEY: 'profit_edited_rev',
	getKey(year, month, day){
		return `${localData.KEY}_${year}_${month}_${day}`
	},
	getValues(year, month, day){
		return JSON.parse(localStorage.getItem(localData.getKey(year, month, day)) || '{}')
	},
	setValue(year, month, day, key, value){
		const current = localData.getValues(year, month, day)
		current[key] = value
		localStorage.setItem(localData.getKey(year, month, day), JSON.stringify(current))
	}
}

export async function fetchEditedRevenue({year, month, day}){
	return localData.getValues(year, month, day)
}
export async function updateEditedRevenue({year, month, day, key, value}){
	return localData.setValue(year, month, day, key, value)
}
