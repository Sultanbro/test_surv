export function copy2clipboard(text){
	const inp = document.createElement('input')
	inp.style = 'position: fixed; opacity: 0'
	inp.value = text
	document.body.appendChild(inp)
	inp.select()
	inp.setSelectionRange(0, 99999)
	document.execCommand('copy')
	document.body.removeChild(inp)
}
