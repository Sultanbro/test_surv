<script>
/* global DG */

import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'

export default {
	name: 'MapView',
	components: {
		DefaultLayout,
	},
	data(){
		return {
			json: '',
		}
	},
	methods:{
		createMap(){
			const scriptTag = document.createElement('script')
			scriptTag.src = 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full'
			scriptTag.id = 'map-script'
			scriptTag.addEventListener('load', () => {
				const kis = this.json;

				DG.then(function() {
					const map = DG.map('map', {
						center: [42.885933,71.369987],
						zoom: 4.5
					})

					Object.keys(kis).forEach(i => {
						const count = kis[i]['count']
						const myDivIcon = DG.divIcon({
							iconSize: [30,30],
							html: `<b>${count}</b>`,
						})

						DG.marker([
							kis[i]['geo_lat'],
							kis[i]['geo_lon']
						], {
							icon: myDivIcon,
						}).addTo(map)
					});
				})
			})
			scriptTag.addEventListener('error', console.error)
			document.body.appendChild(scriptTag)
		},
		destroyMap(){
			const scriptTag = document.getElementById('map-script')
			if(!scriptTag) return
			scriptTag.remove()
			// remove map from coordinates-maps
		}
	},
	mounted(){
		useAsyncPageData('/maps').then(data => {
			this.json = data.json

			this.createMap()
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	},
	beforeUnmount(){
		this.destroyMap()
	}
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<!-- <script src="https://maps.api.2gis.ru/2.0/loader.js?pkg=full"></script> -->
		<div class="old__content">
			<div
				id="map"
				style="width:100%;height:1500px;"
			/>
		</div>
	</DefaultLayout>
</template>

<style lang="scss">
    #map{
        .leaflet-marker-icon{
            display: inline-flex;
            justify-content: center;
            align-items: center;
            z-index: 264!important;
            border-radius: 50%;
        }
    }
</style>