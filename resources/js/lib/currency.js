export const symbols = {
	BYN: 'Br',
	EUR: '€',
	JPY: '¥',
	KZT: '₸',
	KRW: '₩',
	KGS: '⃀',
	RUB: '₽',
	USD: '$',
	UZS: 'UZS',
}
export function code2symbol(code){
	return symbols[code] || code
}
