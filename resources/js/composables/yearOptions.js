export function useYearOptions(start = 2020, end) {
	if(!end) end = new Date().getFullYear()
	const options = []
	for(let year = start; year <= end; ++year){
		options.push(year)
	}
	return options
}