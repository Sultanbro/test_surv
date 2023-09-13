export function resizeImage(image, width, type = 'image/jpeg'){
	return new Promise(resolve => {
		const reader = new FileReader()
		reader.onload = e => {
			const img = document.createElement('img');
			img.onload = () => {
				const w = img.naturalWidth
				const h = img.naturalHeight
				const aspectRatio = w > h ? w / h : h / w

				const canvas = document.createElement('canvas')
				const ctx = canvas.getContext('2d')

				ctx.drawImage(img, 0, 0, width, w / aspectRatio)
				resolve(canvas.toDataURL(type, 0.92))
			}
			img.src = e.target.result
		}
		reader.readAsDataURL(image)
	})
}
