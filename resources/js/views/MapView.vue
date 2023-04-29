<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import {useAsyncPageData} from '@/composables/asyncPageData'
import {LMap, LTileLayer, LMarker, LIcon} from 'vue2-leaflet';
import 'leaflet/dist/leaflet.css';

export default {
	name: 'MapView',
	components: {
		DefaultLayout,
		LMap,
		LTileLayer,
		LMarker,
		LIcon
	},
	data() {
		return {
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
			zoom: 6,
			center: [50.416,69.258],
			markers: []
		}
	},
	methods: {},
	mounted() {
		useAsyncPageData('/maps').then(data => {
			const markers = [];
			for (const key in data.json) {
				markers.push({
					count: data.json[key].count,
					latLng: [data.json[key].geo_lat, data.json[key].geo_lon]
				});
			}
			this.markers = markers;
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<div class="old__content">
			<l-map
				style="height: 100vh"
				:zoom="zoom"
				:center="center"
			>
				<l-tile-layer
					:url="url"
					:attribution="attribution"
				/>
				<l-marker
					v-for="(marker, index) in markers"
					:key="index"
					:lat-lng="marker.latLng"
				>
					<l-icon>
						<span>{{ marker.count }}</span>
					</l-icon>
				</l-marker>
			</l-map>
		</div>
	</DefaultLayout>
</template>

<style lang="scss">
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
