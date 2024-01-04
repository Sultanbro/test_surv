export function arrayUnique(array){
	return Array.from(new Set(array))
}
export function arrayUniqueKey(array, key){
	return [...new Map(array.map(item => [item[key], item])).values()]
}
export function arrayUniqueFn(array, fn){
	return array.filter(item => fn(array, item))
}
