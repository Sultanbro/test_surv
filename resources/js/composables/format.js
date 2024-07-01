export function separateNumber(number, separator = ' '){
	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator)
}

export function numberToCurrency(number){
	if(number - parseInt(number)) return Number(Number(number).toFixed(2))
	return number
}
