export function useSnowEffect(){
	var snowEmmiter = document.getElementById('bpSnow')
	if(snowEmmiter) return

	const FLAKE_COUNT = 300
	const flakes = []

	snowEmmiter = document.createElement('canvas')
	snowEmmiter.id = 'bpSnow'
	document.body.appendChild(snowEmmiter)
	const ctx = snowEmmiter.getContext('2d')

	function onResize() {
		snowEmmiter.width = window.innerWidth
		snowEmmiter.height = window.innerHeight
	}

	const Snowflake = function () {
		this.x = 0
		this.y = 0
		this.vy = 0
		this.vx = 0
		this.r = 0
		this.o = 0

		this.reset()
		this.y = Math.random() * snowEmmiter.height
	}

	Snowflake.prototype.reset = function() {
		this.x = Math.random() * snowEmmiter.width
		this.y = Math.abs(Math.random() - 0.5) * snowEmmiter.height
		this.vy = (1 + Math.random() * 3) * 50
		this.vx = (0.5 - Math.random()) * 10
		this.r = 1 + Math.random() * 2
		this.o = 0.5 + Math.random() * 0.5
	}
	Snowflake.prototype.update = function(deltaTime) {
		this.x += this.vx * deltaTime
		this.y += this.vy * deltaTime
	}

	var prevTime = 0
	function animate(currentTime){
		ctx.clearRect(0, 0, snowEmmiter.width, snowEmmiter.height)

		currentTime *= 0.001
		const deltaTime = currentTime - prevTime
		prevTime = currentTime

		flakes.forEach(flake => {
			flake.update(deltaTime)

			ctx.globalAlpha = flake.o
			ctx.beginPath()
			ctx.arc(flake.x, flake.y, flake.r, 0, Math.PI * 2, false)
			ctx.closePath()
			ctx.fillStyle = '#ddd'
			ctx.fill()

			if (flake.y > snowEmmiter.height) {
				flake.reset()
			}
		})

		window.requestAnimationFrame(animate)
	}

	onResize()
	window.addEventListener('resize', onResize, false)
	for(let i = 0; i < FLAKE_COUNT; ++i){
		flakes.push(new Snowflake())
	}
	window.requestAnimationFrame(animate)
}
