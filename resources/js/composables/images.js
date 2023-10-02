const ext = {
	'jpeg': 'jpg',
	'png': 'png',
	'pdf': 'pdf',
}

export function resizeImage(image, width, type = 'image/jpeg', isBlob = false){
	return new Promise(resolve => {
		const reader = new FileReader()
		reader.onload = e => {
			const img = document.createElement('img');
			img.onload = () => {
				const w = img.naturalWidth
				const h = img.naturalHeight
				const aspectRatio = w < h ? w / h : h / w
				const height = width * aspectRatio

				const canvas = document.createElement('canvas')
				const ctx = canvas.getContext('2d')
				canvas.width = width
				canvas.height = height

				ctx.drawImage(img, 0, 0, width, height)
				if(isBlob){
					canvas.toBlob(blob => {
						resolve(new File([blob], 'file.' + ext[type.split('/')[1]], { type }))
					}, type, 0.92)
				}
				else{
					resolve(canvas.toDataURL(type, 0.92))
				}
			}
			img.src = e.target.result
		}
		reader.readAsDataURL(image)
	})
}

export function resizeImageSrc(src, width, type = 'image/jpeg', isBlob = false){
	return new Promise(resolve => {
		const img = document.createElement('img');
		img.onload = () => {
			const w = img.naturalWidth
			const h = img.naturalHeight
			const aspectRatio = w < h ? w / h : h / w
			const height = width * aspectRatio

			const canvas = document.createElement('canvas')
			const ctx = canvas.getContext('2d')
			canvas.width = width
			canvas.height = height

			ctx.drawImage(img, 0, 0, width, height)
			if(isBlob){
				canvas.toBlob(blob => {
					resolve(new File([blob], 'file.' + ext[type.split('/')[1]], { type }))
				}, type, 0.92)
			}
			else{
				resolve(canvas.toDataURL(type, 0.92))
			}
		}
		img.src = src
	})
}
