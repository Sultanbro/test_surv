<script>
/* global ymaps */

import DefaultLayout from '@/layouts/DefaultLayout'
import { loadMapsApi } from '@/composables/ymapsLoader'

export default {
	name: 'MapView',
	components: {
		DefaultLayout,
	},
	data() {
		return {
			map: null,
			zoom: 5,
			center: [50.416, 69.258],
		}
	},
	created(){
		loadMapsApi()
	},
	mounted() {
		this.init()
	},
	methods: {
		init(){
			if(!window.ymaps) return setTimeout(this.init, 16)
			ymaps.ready(this.initMap)
		},
		initMap(){
			this.map = new ymaps.Map('MapView-map', {
				center: this.center,
				zoom: this.zoom,
				controls: [],
			})
			this.getCoords()
		},
		async getCoords(){
			const {data} = await this.axios.get('/api/coordinates')

			data.data.forEach(coord => {
				if(!coord.users.length) return
				this.map.geoObjects.add(new ymaps.Placemark([coord.geo_lat, coord.geo_lon], {
					iconContent: coord.users.length,
				}))
			})
		}
	},
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<div class="old__content">
			<div id="MapView-map" />
		</div>
	</DefaultLayout>
</template>

<style lang="scss">
	#MapView-map{
		height: 100vh;
		[class$="copyrights-promo"],
		[class$="gototech"]{
			display: none !important;
		}
	}
	.leaflet-marker-icon {
		span{
			border-radius: 50%;
			width: 35px;
			height: 35px;
			background-color: #fff;
			border: 2px solid #666;
			font-size: 14px;
			font-weight: 700;
			display: inline-flex;
			align-items: center;
			justify-content: center;
		}
	}
</style>
