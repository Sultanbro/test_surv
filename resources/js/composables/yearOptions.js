export function useYearOptions(start = 2020, end) {
	if(!end) end = new Date().getFullYear()
	const options = []
	for(let year = start; year <= end; ++year){
		options.push(year)
	}
	return options
}

export function useMonthOptions(){
	return [0,1,2,3,4,5,6,7,8,9,10,11]
}
