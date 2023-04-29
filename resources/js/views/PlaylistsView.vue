<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const Playlists = () => import(/* webpackChunkName: "PlaylistsPage" */ '@/pages/Playlists')

export default {
	name: 'PlaylistsView',
	components: {
		DefaultLayout,
		Playlists,
	},
	data(){
		return {
			token: '',
			can_edit: false,
			category: 0,
			playlist: 0,
			video: 0,
		}
	},
	mounted(){
		useAsyncPageData('/video_playlists').then(data => {
			this.token = '' + data.token
			this.can_edit = !!data.can_edit
			this.category = +data.category
			this.playlist = +data.playlist
			this.video = +data.video
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<div class="old__content my-course-content">
			<Playlists
				v-show="token"
				:token="token"
				:can_edit="can_edit"
				:category="category"
				:playlist="playlist"
				:video="video"
			/>
		</div>
	</DefaultLayout>
</template>

<style scoped>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 7rem !important;
        padding-right: 6rem !important;
    }
}
</style>
