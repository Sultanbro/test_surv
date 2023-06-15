export function getShiftDays(shift){
	if(!shift.work_charts_type) return 'Смена: ' + shift.name
	const type = typeof shift.work_charts_type === 'number' ? shift.work_charts_type : shift.work_charts_type.id
	if(type === 2) return 'Смена: ' + shift.name
	// const dayoffs = getBitCount(+shift.workdays)
	// return `Неделя: ${7 - dayoffs}-${dayoffs}`
	return `Неделя: ${shift.name}`
}

export function getBitCount(num, size = 7){
	let count = 0;
	let mask = 1;
	for (let i=0; i<=size; ++i) {
		if ((mask & num) != 0 ) {
			count++;
		}
		mask <<= 1;
	}
	return count;
}
