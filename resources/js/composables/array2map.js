export function array2map(array, key){
	return array.reduce((result, item) => {
		result[item[key]] = item
		return result
	}, {})
}
