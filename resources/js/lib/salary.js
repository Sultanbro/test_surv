export function calcTaxes(total, taxes, rate = 1){
	let result = total
	let sum = total

	taxes.sort((a,b) => a.order - b.order).forEach(tax => {
		if(!tax.value) return
		let value
		if(!tax.is_percent) {
			value = Math.round(tax.value * rate)
		}
		else{
			value = tax.end_subtraction ? Math.round(sum * tax.value / 100) : Math.round(total * tax.value / 100)
		}
		sum -= value
		if(!tax.is_deduction) result -= value
	})

	return result
}
