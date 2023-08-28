<script>
import DefaultLayout from '@/layouts/DefaultLayout'
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
	mounted() {
		this.getCoords()
	},
	methods: {
		async getCoords(){
			const {data} = await this.axios.get('/api/coordinates')
			const markers = [];
			data.data.forEach(coord => {
				if(!coord.users.length) return
				markers.push({
					count: coord.users.length,
					latLng: [coord.geo_lat, coord.geo_lon]
				});
			})
			this.markers = markers
		}
	},
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
