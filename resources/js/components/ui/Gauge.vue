<template>
	<div id="foo" />
</template>
<script>
export default {
	name: 'UIGauge',
	props: {
		value: {
			type: Number,
			default: 0
		},
		max: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			stroke: '1256.6370614359173,2513.2741228718346',
			circle: 2513.2741228718346,
			start: 1256.6370614359173,
			percent: 0,
			radius: '400',
			gauge: null
		}
	},
	created() {
		this.percent = Number(this.value / this.max * 100).toFixed(0);
		// let result = this.start * (1 - this.percent / 100)

		var opts = {
			angle: -0.02, // The span of the gauge arc
			lineWidth: 0.2, // The line thickness
			radiusScale: 0.98, // Relative radius
			pointer: {
				length: 0.56, // // Relative to gauge radius
				strokeWidth: 0.035, // The thickness
				color: '#000000' // Fill color
			},
			limitMax: false,     // If false, max value increases automatically if value > maxValue
			limitMin: false,     // If true, the min value of the gauge will be fixed
			colorStart: '#6F6EA0',   // Colors
			colorStop: '#C0C0DB',    // just experiment with them
			strokeColor: '#EEEEEE',  // to see which ones work best for you
			generateGradient: true,
			highDpiSupport: true,     // High resolution support
		};
		var target = document.getElementById('foo'); // your canvas element

		// WTF is Gauger?!?!
		// eslint-disable-next-line no-undef
		this.gauge = new Gauger(target).setOptions(opts); // create sexy gauge!
		this.gauge.maxValue = 3000; // set max gauge value
		this.gauge.setMinValue(0);  // Prefer setter over gauge.minValue = 0
		this.gauge.animationSpeed = 50; // set animation speed (32 is default value)
		this.gauge.set(1675); // set actual value

	},
	methods: {
		rate() {

		}
	},
}
</script>

<style scoped lang="scss">

</style>
