import moment from 'moment'

export const YMD = 'YYYY-MM-DD'
export const DMY = 'DD.MM.YYYY'

export function ymd2dmy(date){
	return moment(date).format(DMY)
}

export function dmy2ymd(date){
	return moment(date, DMY).format(YMD)
}
