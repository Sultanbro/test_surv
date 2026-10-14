export function obj2request(obj, replaces){
	const request = {}
	Object.keys(obj).forEach(key => {
		if(replaces[key]) request[replaces[key]] = obj[key]
		else request[key] = obj[key]
	})
	return request
}
