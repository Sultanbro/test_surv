export function calcTaxes(total, taxes, rate = 1){
	let result = total
	taxes.sort((a,b) => a.order - b.order).forEach(tax => {
		if(!tax.value) return
		if(!tax.is_percent) {
			result -= Math.round(tax.value * rate)
			return
		}
		result -= tax.end_subtraction ? Math.round(result * tax.value / 100) : Math.round(total * tax.value / 100)
	})
	return result
}
